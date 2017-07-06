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
 * @category    Idev
 * @package     Idev_DHPAY
 * @copyright   Copyright (c) 2013 Dhpay Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DHPAY notification processor model
 */
class Idev_Dhpay_Model_Event
{
    const DHPAY_STATUS_FAIL = '02';
    const DHPAY_STATUS_CANCEL = -1;
    const DHPAY_STATUS_PENDING = '00';
    const DHPAY_STATUS_SUCCESS = '01';

    /*
     * @param Mage_Sales_Model_Order
     */
    protected $_order = null;

    /**
     * Event request data
     * @var array
     */
    protected $_eventData = array();

    /**
     * Enent request data setter
     * @param array $data
     * @return Dhpay_DHPAY_Model_Event
     */
    public function setEventData(array $data)
    {
        $this->_eventData = $data;
        return $this;
    }

    /**
     * Event request data getter
     * @param string $key
     * @return array|string
     */
    public function getEventData($key = null)
    {
        if (null === $key) {
            return $this->_eventData;
        }
        return isset($this->_eventData[$key]) ? $this->_eventData[$key] : null;
    }

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Process status notification from Monebookers server
     *
     * @return String
     */
    public function processStatusEvent()
    {
        try {
            $params = $this->_validateEventData();
            $msg = '';
            switch($params['status']) {
                case self::DHPAY_STATUS_FAIL: //fail
                    $msg = Mage::helper('dhpay')->__('Payment failed.');
                    $this->_processCancel($msg);
                    break;
                case self::DHPAY_STATUS_CANCEL: //cancel
                    $msg = Mage::helper('dhpay')->__('Payment was canceled.');
                    $this->_processCancel($msg);
                    break;
                case self::DHPAY_STATUS_PENDING: //pending
                    $msg = Mage::helper('dhpay')->__('Pending bank transfer created.');
                    $this->_processSale($params['status'], $msg);
                    break;
                case self::DHPAY_STATUS_SUCCESS: //ok
                    $msg = Mage::helper('dhpay')->__('The amount has been authorized and captured by dhpay.');
                    $this->_processSale($params['status'], $msg);
                    break;
            }
            return 'success';
        } catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        } catch(Exception $e) {
            Mage::logException($e);
        }
        return;
    }

    /**
     * Process cancelation
     */
    public function cancelEvent() {
        try {
            $this->_validateEventData(false);
            $this->_processCancel('Payment was canceled.');
            return Mage::helper('dhpay')->__('The order has been canceled.');
        } catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        } catch(Exception $e) {
            Mage::logException($e);
        }
        return '';
    }

    /**
     * Validate request and return QuoteId
     * Can throw Mage_Core_Exception and Exception
     *
     * @return int
     */
    public function successEvent(){
        $this->_validateEventData();
        $this->processStatusEvent();
        return $this->_order->getQuoteId();
    }

    /**
     * Processed order cancelation
     * @param string $msg Order history message
     */
    protected function _processCancel($msg)
    {
        $this->_order->cancel();
        $this->_order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $msg);
        $this->_order->save();
    }

    /**
     * Processes payment confirmation, creates invoice if necessary, updates order status,
     * sends order confirmation to customer
     * @param string $msg Order history message
     */
    protected function _processSale($status, $msg)
    {
        switch ($status) {
            case self::DHPAY_STATUS_SUCCESS:
                $this->_createInvoice();
                $this->_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $msg);

                // save transaction ID
                $this->_order->getPayment()->setLastTransId($this->getEventData('mb_transaction_id'));
                $this->_order->save();

                // send new order email
                $this->_order->sendNewOrderEmail();
                $this->_order->setEmailSent(true);
                break;
            case self::DHPAY_STATUS_PENDING:
                $this->_order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, $msg);
                // save transaction ID
                $this->_order->getPayment()->setLastTransId($this->getEventData('mb_transaction_id'));
                $this->_order->save();
                break;
        }

    }

    /**
     * Builds invoice for order
     */
    protected function _createInvoice()
    {
        if (!$this->_order->canInvoice()) {
            return;
        }
        $invoice = $this->_order->prepareInvoice();
        $invoice->register()->capture();
        $this->_order->addRelatedObject($invoice);
    }

    /**
     * Checking returned parameters
     * Thorws Mage_Core_Exception if error
     * @param bool $fullCheck Whether to make additional validations such as payment status, transaction signature etc.
     *
     * @return array  $params request params
     */
    protected function _validateEventData($fullCheck = true)
    {
        // get request variables
        $params = $this->_eventData;
        if (empty($params)) {
            Mage::throwException('Request does not contain any elements.');
        }

        $hash = $this->response_hash($params, Mage::getStoreConfig(Idev_Dhpay_Helper_Data::XML_PATH_PRIVATE_KEY));
        if($hash != $params['hash']){
            Mage::throwException('Request is invalid.');
        }

        // check payment status
        if (empty($params['status']) || $params['status'] != '01') {
            Mage::throwException('Payment Failed.');
        }

        // check order ID
        if (empty($params['order_no'])
            || ($fullCheck == false && $this->_getCheckout()->getDhpayRealOrderId() != $params['order_no'])
        ) {
            Mage::throwException('Missing or invalid order ID.');
        }
        // load order for further validation
        $this->_order = Mage::getModel('sales/order')->loadByIncrementId($params['order_no']);
        if (!$this->_order->getId()) {
            Mage::throwException('Order not found.');
        }

        if (0 !== strpos($this->_order->getPayment()->getMethodInstance()->getCode(), 'dhpay_')) {
            Mage::throwException('Unknown payment method.');
        }

        return $params;
    }


    public function response_hash($data, $private_key)
    {
        $hash_src = '';
        $hash_key = array('amount','currency', 'invoice_id', 'merchant_id',
            'trans_time', 'trans_date', 'status', 'ref_no', 'order_no');
        if ($data['status'] == '02') {
            $hash_key[] = 'failure_reason';
        }
        // 按 key 名进行顺序排序
        sort($hash_key);
        foreach ($hash_key as $key) {
            $hash_src .= $data[$key];
        }
        // 密钥放最前面
        $hash_src = $private_key . $hash_src;
        // sha256 算法
        $hash = hash('sha256', $hash_src);
        return strtoupper($hash);
    }
}
