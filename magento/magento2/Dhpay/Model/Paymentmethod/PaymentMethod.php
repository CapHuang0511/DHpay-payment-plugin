<?php
/**
 * Copyright © 2016 Payssion All rights reserved.
 */

namespace Pay\Dhpay\Model\Paymentmethod;

use Magento\Framework\UrlInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order;
use Pay\Dhpay\Model\Config;

/**
 * Description of AbstractPaymentMethod
 *
 * @author Payssion Technical <technical@payssion.com>
 */
abstract class PaymentMethod extends AbstractMethod
{
    protected $_isInitializeNeeded = true;

    protected $_canRefund = false;
    
    protected $_code;
    
    /**
     * Get payment instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

    public function initialize($paymentAction, $stateObject)
    {
        $state = $this->getConfigData('order_status');
        $stateObject->setState($state);
        $stateObject->setStatus($state);
        $stateObject->setIsNotified(false);  
    }
    
    public function getStandardCheckoutFormFields(){ 
    	$objectManager 		= \Magento\Framework\App\ObjectManager::getInstance();
    	$orderIncrementId   = $objectManager->get('Magento\Checkout\Model\Session')->getLastRealOrderId();
    	$order 				= $objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($orderIncrementId);
    	$sourcestr 			= "";
    	$merchant_id 		= $this->getConfigData('merchantid');//�������ID
    	$order_no 			= $invoice_id = $orderIncrementId;
    	$currency 			= $order->getOrderCurrencyCode();//֧������
    	$amount 			= sprintf('%.2f', $order->getGrandTotal());//���׽��
		$storeCurrency 		= $objectManager->get('Magento\Directory\Model\Currency')->load($currency);
    	$amount 			= sprintf('%.2f', $storeCurrency->convert($order->getGrandTotal(), 'USD'));
    	$buyer_email 		= $order->getData('customer_email' ); //�˵���ַ�û�����
    	$return_url 		= $errorNotifyUrl = $objectManager->get( 'Magento\Framework\UrlInterface' )->getUrl('pay/checkout/finish/',['_secure' => true]);
    	$notify_url 		= $this->getConfigData('notifyUrl');
    	$config 			= new Config($this->_scopeConfig);
    	$remark 			= $config->getRemark().":".$order_no;
    	$billingAddress 	= $order->getBillingAddress();
    	$shipping_country 	= $billingAddress->getCountryId();//�˵���ַ����
    	$first_name 		= trim($billingAddress->getFirstname());//	�˵���ַ�û���
    	$last_name  		= trim($billingAddress->getLastname());//�˵���ַ�û���
    	$zipcode  			= $billingAddress->getPostcode();//�˵��ʱ�
    	$product_name 		= 'product-'.$order_no;//��Ʒ����
    	$product_quantity 	= 1;//��Ʒ����
    	$product_price		= $amount;//��Ʒ����
    	$address_line 		= trim(implode(' ', $billingAddress->getStreet()));//�˵���ַ�ֵ�һ
    	$city 				= trim($billingAddress->getCity());//�˵���ַ����
    	$country 			= $shipping_country;
    	$state 				= trim($billingAddress->getRegion());//�˵���ַ��
    	$billToState 		= trim($billingAddress->getRegion());//�˵���ַ����
    	$md5 				= $this->getConfigData('md5_msg');
    	$sourcestr 			= $md5 . $amount . $currency . $invoice_id . $merchant_id;
    	$hash 				= hash('sha256', $sourcestr);
    	
    	$submitdatas['merchant_id'] 		= $merchant_id;
    	$submitdatas["invoice_id"] 			= $invoice_id;
    	$submitdatas["order_no"] 			= $order_no;
    	$submitdatas["currency"] 			= $currency;
    	$submitdatas["amount"] 				= $amount;
    	$submitdatas["buyer_email"] 		= $buyer_email;
    	$submitdatas["return_url"] 			= $return_url;
    	$submitdatas["notify_url"] 			= $notify_url;
    	$submitdatas["remark"] 				= $remark;
    	$submitdatas["shipping_country"] 	= $shipping_country;
    	$submitdatas["first_name"] 			= $first_name;
    	$submitdatas["last_name"]			= $last_name;
    	$submitdatas["product_name"] 		= $product_name;
    	$submitdatas["product_price"] 		= $product_price;
    	$submitdatas["product_quantity"] 	= $product_quantity;
    	$submitdatas["address_line"] 		= $address_line;
    	$submitdatas["city"] 				= $city;
    	$submitdatas["country"] 			= $country;
    	$submitdatas["state"] 				= $state;
    	$submitdatas["zipcode"] 			= $zipcode;
    	$submitdatas["hash"] 				= strtoupper($hash);
    	return $submitdatas;
    }
    
    
    
    private function getPMID() {
    	return substr($this->_code, strlen('payssion_payment_'));
    }
    
    public function getNameData($name){ 
    	return $this->getConfigData($name);
    }
}