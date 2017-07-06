<?php
/**
 * dhpay1.php.
 *
 * @author zhuna<zhuna@yonyou.com>
 * @date   16/1/5
 */
// $_GET['currency'] &  $_GET['language'] 会引发跳转，includes/init_includes/init_currencies.php
if (!empty($_GET) && empty($_POST)) {
    $_POST = $_GET;
}
unset($_GET);

require('includes/dhpay_application_top.php');
require_once DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/dhpay/dhpay_front_core.php';

class DhpayReturn{

    public $merchantId;
    public $orderId;
    public $amount;
    public $currency;
    public $transactionDate;
    public $transactionTime;
    public $status;
    public $refNo;
    public $hash;
    public $dhReturn;
    public $failReason;

    public $data;

    public $order_status_fail;
    public $order_status_success;
    public $order_status_process;

    public function __construct($data, $order_status_success, $order_status_fail, $order_status_process){
        $this->data = $data;

        $this->merchantId = $data['merchant_id'];
        $this->orderId = $data['order_no'];
        $this->transactionDate = $data['trans_date'];
        $this->currency = $data['currency'];
        $this->amount = $data['amount'];
        $this->failReason = $data['failure_reason'];//失败原因
        $this->transactionTime = $data['trans_time'];
        $this->status = $data['status'];//交易返回状态00处理中，01成功，02失败
        $this->refNo = $data['ref_no'];//DHpay 参考号
        $this->hash = $data['hash'];//交易的签名
        $this->dhReturn = $data['dh_rt'];//ipn,real_time

        $this->order_status_fail = $order_status_fail;
        $this->order_status_success = $order_status_success;
        $this->order_status_process = $order_status_process;
        if (method_exists($this, $this->dhReturn)){
            $this->{$this->dhReturn}();
        }
    }

    public function ipn(){
        $flag = Dhpay_Front_Core::response($this->data);
        $method = 'ipn_' . $flag;
        if (method_exists($this, $method)){
            $this->$method();
        }
        echo 'success';
    }

    public function real_time(){
        $flag = Dhpay_Front_Core::response($this->data);
        $method = 'real_time_' . $flag;
        if (method_exists($this, $method)){
            echo $this->$method();
        }
    }

    protected function ipn_success(){
        global $db;
        require(DIR_WS_CLASSES . 'order.php');
        $order = new order($this->orderId);

        // //判断历史库中是否已经有该订单DH的通知的记录（避免重复写入）
        // $orders_query = "SELECT count(*) as counter FROM " . TABLE_ORDERS_STATUS_HISTORY . "
        //          WHERE orders_id = :orderId and comments like '%TransactionNo%' LIMIT 1 ";
        // $orders_query = $db->bindVars($orders_query, ':orderId', $this->orderId, 'integer');
        // $orderHistory = $db->Execute($orders_query);
        // $counter = $orderHistory->fields['counter'];
        // if ($counter > 0 ){
        //     return;
        // }

        //发成功邮件
        $lang_file = DIR_WS_LANGUAGES . $_SESSION['language'] . '/checkout_process.php';
        if (file_exists($lang_file)) {
            require_once($lang_file);
        } else {
            require_once('includes/languages/english/checkout_process.php');
        }
        $order->send_order_email($this->orderId, 2);

        //更新订单状态以及添加订单状态历史记录
        $comment = 'Order payment successful! TransactionNo:' . $this->orderId;
        $sql_data_array = array(
            'orders_id' => $this->orderId,
            'orders_status_id' => $this->order_status_success,
            'date_added' => 'now()',
            'comments' => $comment,
            'customer_notified' => '1');
        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

        $sql_data_array = array(
            'orders_status' => $this->order_status_success,
            'orders_date_finished' => 'now()');

        zen_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = ' . (int)$this->orderId);//更新订单状态

    }

    protected function ipn_fail(){
        $comment = 'Order payment Fail!Error Msg:' . $this->failReason . '. TransactionNo:' . $this->orderId;



        $sql_data_array = array(
            'orders_id' => $this->orderId,
            'orders_status_id' => $this->order_status_fail,
            'date_added' => 'now()',
            'comments' => $comment,
            'customer_notified' => '1');
        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

        $sql_data_array = array('orders_status' => $this->order_status_fail,
                                'orders_date_finished' => 'now()',
        );

        //判断历史库中是否已经有该订单DH的成功通知记录通知的记录（避免重复写入）
        $orders_query = "SELECT count(*) as counter FROM " . TABLE_ORDERS_STATUS_HISTORY . "
                 WHERE orders_id = :orderId and comments like '%Order payment successful! TransactionNo%' LIMIT 1 ";
        $orders_query = $db->bindVars($orders_query, ':orderId', $this->orderId, 'integer');
        $orderHistory = $db->Execute($orders_query);
        $counter = $orderHistory->fields['counter'];
        if ($counter > 0 ){
            return;
        }
        zen_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = ' . (int)$this->orderId);//更新订单状态

    }

    protected function ipn_process(){
        //更新订单状态以及添加订单状态历史记录
        $comment = 'Order payment is under process!';
        $sql_data_array = array(
            'orders_id' => $this->orderId,
            'orders_status_id' => $this->order_status_process,
            'date_added' => 'now()',
            'comments' => $comment,
            'customer_notified' => '1');
        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

        $sql_data_array = array(
            'orders_status' => $this->order_status_process,
            'orders_date_finished' => 'now()');
        zen_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = ' . (int)$this->orderId);//更新订单状态
    }

    protected function real_time_success(){
        $_SESSION['messageStack'] = "Pay Result: Successful";
        $_SESSION['cart']->reset(true);

        unset($_SESSION['order_id']);
        unset($_SESSION['old_cur']);

        $this->ipn_success();

        return  '<script type="text/javascript">parent.location.href="' .
            zen_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true, false) . '"</script>';
    }

    protected function real_time_fail(){
        global $messageStack;

        $_SESSION['messageStack'] = "Pay Result: Failed";
        $messageStack->add_session('checkout_payment', 'Your payment transaction failed!Please check your credit card info and try again!', 'error');

        $this->ipn_fail();

        return '<script type="text/javascript">parent.location.href="' .
        zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false) . '"</script>';
    }

    protected function real_time_process(){
        $_SESSION['messageStack'] = "Pay Result: Processing";
        $_SESSION['cart']->reset(true);
        unset($_SESSION['order_id']);
        unset($_SESSION['old_cur']);
        return '<script type="text/javascript">parent.location.href="' .
        zen_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true, false) . '"</script>';
    }

}

new DhpayReturn($_POST, DHPAY_ORDER_STATUS_PROCESSING_ID, DHPAY_ORDER_STATUS_FAIL_ID,DHPAY_ORDER_STATUS_ID);
exit;
