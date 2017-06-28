<?php
class DhpayReturn extends Model{

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

    public function init($data, $order_status_success, $order_status_fail, $order_status_process){
        $this->data = $data;

        $this->merchantId = $data['merchant_id'];
        $this->orderId = $data['order_no'];
        $this->transactionDate = $data['trans_date'];
        $this->currency = $data['currency'];
        $this->amount = $data['amount'];
        $this->failReason = isset($data['failure_reason']) ? $data['failure_reason'] : '';//失败原因
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
        $comment = 'Order payment successful! TransactionNo:' . $this->orderId;
        $order_info = $this->model_checkout_order->getOrder($this->orderId);
        if ($order_info){
            $this->model_checkout_order->addOrderHistory($this->orderId, $this->order_status_success, $comment);
        }

    }

    protected function ipn_fail(){
        $comment = 'Order payment Fail!Error Msg:' . $this->failReason . '. TransactionNo:' . $this->orderId;
        $order_info = $this->model_checkout_order->getOrder($this->orderId);
        if ($order_info && $order_info['order_status_id'] == 0){
            $this->model_checkout_order->addOrderHistory($this->orderId, $this->order_status_fail, $comment);
        }
    }

    protected function ipn_process(){
        //更新订单状态以及添加订单状态历史记录
        $comment = 'Order payment is under process!';
        $order_info = $this->model_checkout_order->getOrder($this->orderId);
        if ($order_info && $order_info['order_status_id'] == 0){
            $this->model_checkout_order->addOrderHistory($this->orderId, $this->order_status_process, $comment);
        }
    }

    protected function real_time_success(){
        $this->ipn_success();

        return  '<script type="text/javascript">parent.location.href="' .
        $this->url->link('checkout/success') . '"</script>';
    }

    protected function real_time_fail(){
        $this->ipn_fail();

        return '<script type="text/javascript">parent.location.href="' .
        $this->url->link('checkout/failure') . '"</script>';
    }

    protected function real_time_process(){
        return '<script type="text/javascript">parent.location.href="' .
        $this->url->link('checkout/success') . '"</script>';
    }

}
 