<?php
/**
 * Copyright 漏 2016 Payssion All rights reserved.
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
    	$merchant_id 		= $this->getConfigData('merchantid');//合作伙伴ID
    	$order_no 			= $invoice_id = $orderIncrementId;
    	$currency 			= $order->getOrderCurrencyCode();//支付币种
    	$amount 			= sprintf('%.2f', $order->getGrandTotal());//交易金额
		$storeCurrency 		= $objectManager->get('Magento\Directory\Model\Currency')->load($currency);
    	$amount 			= sprintf('%.2f', $storeCurrency->convert($order->getGrandTotal(), 'USD'));
    	$buyer_email 		= $order->getData('customer_email' ); //账单地址用户邮箱
    	$return_url 		= $errorNotifyUrl = $objectManager->get( 'Magento\Framework\UrlInterface' )->getUrl('pay/checkout/finish/',['_secure' => true]);
    	$notify_url 		= $this->getConfigData('notifyUrl');
    	$config 			= new Config($this->_scopeConfig);
    	$remark 			= $config->getRemark().":".$order_no;
    	$billingAddress 	= $order->getBillingAddress();
    	$shipping_country 	= $billingAddress->getCountryId();//账单地址国家
    	$first_name 		= trim($billingAddress->getFirstname());//	账单地址用户姓
    	$last_name  		= trim($billingAddress->getLastname());//账单地址用户名
    	$zipcode  			= $billingAddress->getPostcode();//账单邮编
    	$product_name 		= 'product-'.$order_no;//商品名称
    	$product_quantity 	= 1;//商品数量
    	$product_price		= $amount;//商品单价
    	$address_line 		= trim(implode(' ', $billingAddress->getStreet()));//账单地址街道一
    	$city 				= trim($billingAddress->getCity());//账单地址城市
    	$country 			= $shipping_country;
    	$state 				= trim($billingAddress->getRegion());//账单地址州
    	$billToState 		= trim($billingAddress->getRegion());//账单地址国家
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