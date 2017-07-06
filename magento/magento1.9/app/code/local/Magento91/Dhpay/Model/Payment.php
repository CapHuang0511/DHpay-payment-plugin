<?php
/**
 * E: jj632293@gmail.com
 * W:www.91magento.net
 */
class Magento91_Dhpay_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'dhpay_payment';
    protected $_formBlockType = 'dhpay/form';


    // Order instance
    protected $_order = null;

public function writeLog($file,$msg){
   $file = @fopen($file,"a+");
   @fputs($file,$msg. "\n");
   @fclose($file);
}

 public function canUseForCurrency($currencyCode)
	{
		
		return true;
	}
	
		/**
	 * Return Order Place Redirect URL
	 *
	 * @return	  string Order Redirect URL
	 */
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('dhpay/payment/redirect', array('_secure' => true));
	}
public function generateErrorResponse(){
	echo "error";
	exit;
}
  /**
	 * Return Standard Checkout Form Fields for request to 95EPAY
	 *
	 * @return	  array Array of hidden form fields
	 */
	public function getStandardCheckoutFormFields() {
		$session = Mage::getSingleton ( 'checkout/session' );
		$orderIncrementId = $session->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		if (!($order instanceof Mage_Sales_Model_Order)) {
			Mage::throwException ( $this->_getHelper ()->__ ( 'Cannot retrieve order object' ) );
		}
		$sourcestr = "";
		
		$merchant_id = $this->getConfigData ('merchantid');//合作伙伴ID
		







		$order_no = $invoice_id = $orderIncrementId;	

$currency = $order->getOrderCurrencyCode();//支付币种
		
		
		
		$amount = sprintf('%.2f', $order->getGrandTotal());//交易金额 
		
	   $storeCurrency = Mage::getSingleton('directory/currency')->load($order->order_currency_code);
	   $amount = sprintf('%.2f', $storeCurrency->convert($order->getGrandTotal(), 'USD'));
	   $buyer_email =  $order->getData ('customer_email' ); //账单地址用户邮箱
			
		
		$return_url =$errorNotifyUrl = Mage::getUrl ( 'dhpay/payment/return', array ('_secure' => true ));
		$remark = Mage::getStoreConfig("web/unsecure/base_url").":".$order_no;
		//$this->getConfigData ("notifyurl");//页面跳转同步通知页面路
		//$errorNotifyUrl = Mage::getUrl ( 'dhpay/payment/return', array ('_secure' => true ));	//请求出错时的通知页面路径
		//$notifyUrl = Mage::getUrl ( 'dhpay/payment/notify', array ('_secure' => true ));	//服务器异步通知页面路径

		$billingAddress = $order->getBillingAddress();

		$shipping_country = $billingAddress->getCountry();//账单地址国家
		$first_name =  trim($billingAddress->getFirstname());//	账单地址用户姓
		$last_name  = trim($billingAddress->getLastname ());//账单地址用户名 
		$zipcode  =   $billingAddress->getPostcode ();//账单邮编
		//foreach ($order->getAllItems () as $item ) {
			
			$product_name = 'product-'.$order_no;//商品名称
			//$product_name = str_replace('"',"",$product_name);//商品名称
			//$product_name = str_replace('&',"",$product_name);//商品名称
			
			$product_quantity = 1;//商品数量
			$product_price=$amount;//商品单价
			//break;
		//} 
		
	
		$address_line = trim($billingAddress->getStreetFull());//账单地址街道一
		$city = trim($billingAddress->getCity());//账单地址城市
		$country = $shipping_country;
		$state = trim($billingAddress->getRegion());//账单地址州
		$billToState = trim($billingAddress->getRegion());//账单地址国家
		
		$md5 = $this->getConfigData ('md5_msg');
		//$sourcestr = $md5 . $address_line . $amount . $buyer_email . $city . $country . $currency . $first_name . $invoice_id . $last_name . $merchant_id . $order_no . $product_name . $product_price . $product_quantity . $remark . $return_url . $shipping_country . $state . $zipcode;

$sourcestr = $md5 . $amount . $currency . $invoice_id . $merchant_id;
		//echo "加密串:<input type=text value='".$sourcestr ."' />";
		//echo $sourcestr;
		//echo "<br>";
		//echo "md5:<input type=text value='". $this->getConfigData ('md5_msg') ."' />";
		//exit;
		$hash = hash('sha256', $sourcestr);
		
		$submitdatas['merchant_id'] = $merchant_id;
		$submitdatas["invoice_id"] = $invoice_id;
		$submitdatas["order_no"] = $order_no;
		$submitdatas["currency"] = $currency;
		$submitdatas["amount"] = $amount;
		$submitdatas["buyer_email"] = $buyer_email;
		$submitdatas["return_url"] = $return_url;
		$submitdatas["remark"] = $remark;
		$submitdatas["shipping_country"] = $shipping_country;
		$submitdatas["first_name"] = $first_name;
		$submitdatas["last_name"] = $last_name;
		$submitdatas["product_name"] = $product_name;
		$submitdatas["product_price"] = $product_price;
		$submitdatas["product_quantity"] = $product_quantity;
		$submitdatas["address_line"] = $address_line;
		$submitdatas["city"] = $city;
		$submitdatas["country"] = $country;
		$submitdatas["state"] = $state;
		$submitdatas["zipcode"] = $zipcode;
		$submitdatas["hash"] = strtoupper($hash);
		return $submitdatas;
	}
}