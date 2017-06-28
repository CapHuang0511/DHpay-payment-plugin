<?php
require_once 'dhpay/dhpay_front_core.php';
require_once 'dhpay/dhpay_admin_core.php';
require_once 'dhpay/dhpay_ipn.php';

class ModelExtensionPaymentDhpay extends Model {


    public function initConfig(){
        $config = array();
        foreach(Dhpay_Admin_Core::$field as $field){
            $key = strtolower($field);
            $config[$field] = $this->config->get($key);

        }
        Dhpay_Admin_Core::initConfig($config);
    }

    /**
     *
     * 该方法必须有，payment_method.php中调用获取是否显示该支付方式.
     *
     * @param $address 地址
     * @param $total   支付金额
     *
     * @return array
     */
	public function getMethod($address, $total) {
        $this->initConfig();

        // 获取交易订单信息 加载订单model
        
        if(!isset($this->session->data['order_id'])){
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $show = Dhpay_Front_Core::show($order_info['currency_code']);
            $method_data = array();
            if ($show){
                $method_data = array(
                    'code' => 'dhpay',
                    'title' => '<img src="https://www.dhpay.com/merchantaccount/zh_CN/v2/image/download/pay-3.jpg" />' . $this->config->get('dhpay_title'),
                    'terms'      => '',
                    'sort_order' => $this->config->get('dhpay_sort_order')
                );
            }
            return $method_data;
        } else {
            $method_data = array(
                'code' => 'dhpay',
                'title' => '<img src="https://www.dhpay.com/merchantaccount/zh_CN/v2/image/download/pay-3.jpg" />' . $this->config->get('dhpay_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('dhpay_sort_order')
            );
            return $method_data;
        }
	}



    public function ipn($data){
        $this->initConfig();
        $ipn = new DhpayReturn($this->registry);
        return $ipn->init($data, DHPAY_ORDER_STATUS_PROCESSING_ID, DHPAY_ORDER_STATUS_FAIL_ID,DHPAY_ORDER_STATUS_ID);
    }

}