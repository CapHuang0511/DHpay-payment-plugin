<?php
/**
 * Copyright © 2016 Payssion All rights reserved.
 */

namespace Pay\Dhpay\Controller\Checkout;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Checkout\Model\Session;

class Finish extends \Magento\Framework\App\Action\Action
{
    /**
     *
     * @var \Payssion\Payment\Model\Config
     */
    protected $_config;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code='pay_dhpay';
    
    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;
    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Payssion\Payment\Model\Config $config
     * @param Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Pay\Dhpay\Model\Config $config,
        Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger,
    	PaymentHelper $paymentHelper
    )
    {
        $this->_config = $config;
        $this->_checkoutSession = $checkoutSession;
        $this->_logger = $logger;
        $this->_paymentHelper = $paymentHelper;

        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $params = $this->getRequest()->getParams();

        if(!isset($params['order_no'])){
            $this->messageManager->addNotice(__('Invalid return, no transactionId specified'));
            $this->_logger->critical('Invalid return, no transactionId specified', $params);
            $resultRedirect->setPath('checkout/cart');
            return $resultRedirect;
        }
        
        $order_no= $params["order_no"];
        $orderModel = $this->_objectManager->get('Magento\Sales\Model\Order');
        if(isset($params['order_no'])) {
        	$order = $orderModel->loadByIncrementId($order_no);
        }
        
        if (empty($order)) {
        	$this->messageManager->addNotice(__('Invalid return, no transactionId specified'));
        	$this->_logger->critical('Invalid return, no transactionId specified', $params);
        	$resultRedirect->setPath('checkout/cart');
        } else {
        	$hash 			= (isset($params["hash"]))?$params["hash"]:'';
        	$amount 		= (isset($params["amount"]))?$params["amount"]:'';
        	$failure_reason = (isset($params["failure_reason"]))?$params["failure_reason"]:''; 
        	$trans_date 	= (isset($params["trans_date"]))?$params["trans_date"]:'';
        	$trans_time 	= (isset($params["trans_time"]))?$params["trans_time"]:'';
        	$status 		= (isset($params["status"]))?$params["status"]:'';
        	$ref_no 		= (isset($params["ref_no"]))?$params["ref_no"]:'';
        	$invoice_id 	= (isset($params["invoice_id"]))?$params["invoice_id"]:'';
        	$merchant_id 	= (isset($params["merchant_id"]))?$params["merchant_id"]:'';
        	$order_no 		= (isset($params["order_no"]))?$params["order_no"]:'';
        	$currency 		= (isset($params["currency"]))?$params["currency"]:''; 
        	//md5
            $Helper 		= $this->_paymentHelper->getMethodInstance($this->_code);
        	$MD5key 		= $Helper->getConfigData('md5_msg');

            $jmyc =  $MD5key.$amount.$currency.$failure_reason.$invoice_id.$merchant_id.$order_no.$ref_no.$status.$trans_date.$trans_time;
            $jmh = hash('sha256', $jmyc);
            
            if (strtoupper($jmh) == $hash){
            	$this->_getCheckoutSession()->start();
            	if ($status == '01'){
            		if ($order->getState() <> $Helper->getConfigData('order_status_payment_success')){
            			$order->addStatusToHistory(
            					$Helper->getConfigData('order_status_payment_success'),//$order->getStatus(),
            					'Payment success by dhpay!'
            			);
            			$order->setState($Helper->getConfigData('order_status_payment_success'));
            			$order->save();
            		}
            		$this->saveInvoice($order);
            		$resultRedirect->setPath('checkout/onepage/success');
            	}
            	if ($status == '02'){
            		if ($order->getState() != \Magento\Sales\Model\Order::STATE_CANCELED){
            			$order->addStatusToHistory(
            					\Magento\Sales\Model\Order::STATE_CANCELED,//$order->getStatus(),
            					'Payment failed by dhpay!reason:'.$failure_reason
            			);
            			$order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
            			$order->save();
            		}
            		$resultRedirect->setPath('checkout/cart');
            	}
            	if ($status == '00'){
            		$resultRedirect->setPath('checkout/onepage/success');
            	}
            }else{
            	$resultRedirect->setPath('checkout/cart');
            }
        }
        return $resultRedirect;
    }

    /**
     * Return checkout session object
     *
     * @return Session
     */
    protected function _getCheckoutSession()
    {
        return $this->_checkoutSession;
    }
       
    public function saveInvoice(\Magento\Sales\Model\Order $order)
    {
    	if ($order->canInvoice() && !$order->hasInvoices()) {
    		$invoice = $this->_objectManager->create('Magento\Sales\Model\Service\InvoiceService')->prepareInvoice($order);
    		if (!$invoice->getTotalQty()) {
    			throw new \Magento\Framework\Exception\LocalizedException(
    					__('You can\'t create an invoice without products.')
    			);
    		}
    		$invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::NOT_CAPTURE)->register();
    		$transactionSave = $this->_objectManager->create('Magento\Framework\DB\Transaction')
    							->addObject($invoice)
    							->addObject($invoice->getOrder());
    		$transactionSave->save();
    		$order->addStatusHistoryComment(__('Created invoice #%1.', $invoice->getId()))->setIsCustomerNotified(true)->save();
    		return true;
    	}
    	return false; 
    }
}