<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Phoenix
 * @package     Idev_Dhpay
 * @copyright   Copyright (c) 2013 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class Idev_Dhpay_Model_Abstract extends Mage_Payment_Model_Method_Abstract
{
    /**
     * unique internal payment method identifier
     */
    protected $_code = 'dhpay_abstract';

    protected $_formBlockType = 'dhpay/form';
    protected $_infoBlockType = 'dhpay/info';

    /**
     * Availability options
     */
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canVoid = false;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;

    protected $_paymentMethod = 'abstract';
    protected $_defaultLocale = 'en';
    protected $_supportedLocales = array('cn', 'cz', 'da', 'en', 'es', 'fi', 'de', 'fr', 'gr', 'it', 'nl', 'ro', 'ru', 'pl', 'sv', 'tr');
    protected $_hidelogin = '1';

    protected $_order;

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = $this->getInfoInstance()->getOrder();
        }
        return $this->_order;
    }

    /**
     * Return url for redirection after order placed
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('dhpay/processing/payment');
    }

    /**
     * Capture payment through Dhpay api
     *
     * @param Varien_Object $payment
     * @param decimal       $amount
     *
     * @return Idev_Dhpay_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setTransactionId($this->getTransactionId())
            ->setIsTransactionClosed(0);

        return $this;
    }

    /**
     * Camcel payment
     *
     * @param Varien_Object $payment
     *
     * @return Idev_Dhpay_Model_Abstract
     */
    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
            ->setTransactionId($this->getTransactionId())
            ->setIsTransactionClosed(1);

        return $this;
    }

    /**
     * Return url of payment method
     *
     * @return string
     */
    public function getUrl()
    {
        $payflow_url = 'https://www.dhpay.com/merchant/web/cashier';
        if (Mage::getStoreConfig(Idev_Dhpay_Helper_Data::XML_PATH_CHECKOUT_METHOD) == 'Iframe') {
            $payflow_url = 'https://www.dhpay.com/merchant/web/cashier/iframe/before';
        }

        if (Mage::getStoreConfig(Idev_Dhpay_Helper_Data::XML_PATH_MODE) == 'Test') {
            $payflow_url .= '?env=dhpaysandbox';
        }

        return $payflow_url;
    }

    public function getFormMethod(){
        $method = 'POST';
        if (Mage::getStoreConfig(Idev_Dhpay_Helper_Data::XML_PATH_CHECKOUT_METHOD) == 'Iframe') {
            $method = 'GET';
        }
        return $method;
    }


    /**
     * Return url of payment method
     *
     * @return string
     */
    public function getLocale()
    {
        $locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
        if (is_array($locale) && !empty($locale) && in_array($locale[0], $this->_supportedLocales)) {
            return $locale[0];
        }
        return $this->getDefaultLocale();
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields()
    {
        $order_id = $this->getOrder()->getRealOrderId();
        $billing = $this->getOrder()->getBillingAddress();
        $shipping = $this->getOrder()->getShippingAddress();

        if ($this->getOrder()->getBillingAddress()->getEmail()) {
            $email = $this->getOrder()->getBillingAddress()->getEmail();
        } else {
            $email = $this->getOrder()->getCustomerEmail();
        }

        $productList = $this->getOrder()->getAllItems();
        if ($productList){
            $product = $productList[0];
            $product_name = $product->getName();
            $product_price = $product->getPrice();
            $product_qty = $product->getQtyOrdered();
        }
        $params = array(
            'merchant_id' => Mage::getStoreConfig(Idev_Dhpay_Helper_Data::XML_PATH_MERCHANT_ID),
            'invoice_id' => $order_id,
            'order_no' => $order_id,//
            'currency' => $this->getOrder()->getOrderCurrencyCode(),
            'amount' => sprintf('%.2f', $this->getOrder()->getGrandTotal()),
            'buyer_email' => $email,
            'first_name' => $billing->getFirstname(),
            'last_name' => $billing->getLastname(),
            'country' => $billing->getCountryModel()->getIso2Code(),
            'state' => $billing->getRegion(),
            'city' => $billing->getCity(),
            'address_line' => $billing->getStreet(-1),
            'zipcode' => $billing->getPostcode(),
            'product_name' => $product->getName(),
            'product_price' => sprintf("%.2f", $product_price),
            'product_quantity' => intval($product_qty),
            'return_url' => Mage::getUrl('dhpay/processing/success'),
            'remark' => '',
            'hash' => '',

            'shipping_country' => $shipping->getCountryModel()->getIso2Code(),
            'shipping_first_name' => $shipping->getFirstname(),
            'shipping_last_name' => $shipping->getLastname(),
            'shipping_state' => $shipping->getRegion(),
            'shipping_city' => $shipping->getCity(),
            'shipping_address_line' => $shipping->getStreet(-1),
            'shipping_zipcode' => $shipping->getPostcode(),
            'shipping_email' => $shipping->getEmail(),
            'shipping_phone' => $shipping->getTelephone(),

            'body_style' => Mage::getStoreConfig(Idev_Dhpay_Helper_Data::XML_PATH_STYLE_BODY),
            'title_style' => Mage::getStoreConfig(Idev_Dhpay_Helper_Data::XML_PATH_STYLE_TITLE),
            'layout' => strtolower(Mage::getStoreConfig(Idev_Dhpay_Helper_Data::XML_PATH_STYLE_LAYOUT)),
            'button_style' => Mage::getStoreConfig(Idev_Dhpay_Helper_Data::XML_PATH_STYLE_BUTTON),
        );

        if(Mage::getStoreConfig(Idev_Dhpay_Helper_Data::XML_PATH_MODE) == 'Test'){
            $params['env'] = 'dhpaysandbox';
        }

        $params['hash'] = $this->request_hash($params, Mage::getStoreConfig(Idev_Dhpay_Helper_Data::XML_PATH_PRIVATE_KEY));


        return $params;
    }

    /**
     * Get initialized flag status
     *
     * @return true
     */
    public function isInitializeNeeded()
    {
        return true;
    }

    /**
     * Instantiate state and set it to state onject
     * //@param
     * //@param
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setIsNotified(false);
    }

    /**
     * Get config action to process initialization
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        $paymentAction = $this->getConfigData('payment_action');
        return empty($paymentAction) ? true : $paymentAction;
    }

    /**
     * 加密
     *
     * @param $data
     * @param $private_key
     *
     * @return string
     */
    public function request_hash($data, $private_key)
    {
        // 签名的表单字段名
        $hash_src = '';
        $hash_key = array('amount', 'currency', 'invoice_id', 'merchant_id');
        // 按 key 名进行顺序排序
        sort($hash_key);
        foreach ($hash_key as $key) {
            $hash_src .= $data[$key];
        }
        // 密钥放最前面
        $hash_src = $private_key . $hash_src;
        // sha256 算法
        $hash = hash('sha256', $hash_src);

        return $hash;
    }
}
