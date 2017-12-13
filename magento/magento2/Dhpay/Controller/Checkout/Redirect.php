<?php
/**
 * Copyright © 2016 Payssion All rights reserved.
 */

namespace Pay\Dhpay\Controller\Checkout;

use Magento\Payment\Helper\Data as PaymentHelper;
use Pay\Error\Error;

/**
 * Description of Redirect
 *
 * @author Payssion Technical <technical@payssion.com>
 */
class Redirect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Payssion\Payment\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;
    
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
//     protected $layoutFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Payssion\Payment\Model\Config $config
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Pay\Dhpay\Model\Config $config,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger,
        PaymentHelper $paymentHelper
//     	\Magento\Framework\View\LayoutFactory $layoutFactory
    )
    {
        $this->_config = $config; // Payssion config helper
        $this->_checkoutSession = $checkoutSession;
        $this->_logger = $logger;
        $this->_paymentHelper = $paymentHelper;
//         $this->layoutFactory = $layoutFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $order = $this->_getCheckoutSession()->getLastRealOrder();
            $order->addStatusToHistory(
            		$order->getStatus(),
            		'Customer was redirected to dhpay'
            );
            $order->save();
            $method = $order->getPayment()->getMethod();
            $methodInstance = $this->_paymentHelper->getMethodInstance($method);
            
            if ($methodInstance instanceof \Pay\Dhpay\Model\Paymentmethod\Paymentmethod) {
            	$FormFields = $methodInstance->getStandardCheckoutFormFields();
            	$html = '<html><body>';
				$html .= "<form action='".$methodInstance->getNameData('gateway')."' id='dhpay_payment_checkout' method='POST' name='dhpay_payment_checkout'>";
				foreach ($FormFields as $field => $value) {
				    $html.= "<input type='hidden' name='". $field."' value='".$value."' />";
				}
				$html.= "</form>";					
				$html.= $methodInstance->getNameData('redirectmsg');
				$html.= '<script type="text/javascript">document.getElementById("dhpay_payment_checkout").submit();</script>';
				$html.= '</body></html>';
            	echo $html;             
            } else {
                throw new Error('Method is not a dhpay payment method');
            }

        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong, please try again later'));
            $this->_logger->critical($e);
            $this->_getCheckoutSession()->restoreQuote();
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Return checkout session object
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckoutSession()
    {
        return $this->_checkoutSession;
    }
}