<?php

class ControllerPaymentDhpay extends Controller
{
    public function index()
    {
        $this->load->model('checkout/order');
        $this->load->model('payment/dhpay');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $this->load->model('account/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $shipping_country = $this->model_localisation_country->getCountry($order_info['shipping_country_id']);
        $payment_country = $this->model_localisation_country->getCountry($order_info['payment_country_id']);
        $payment_zone = $this->model_localisation_zone->getZone($order_info['payment_zone_id']);
        $shipping_zone = $this->model_localisation_zone->getZone($order_info['shipping_zone_id']);
        $order_product = $this->model_account_order->getOrderProducts($order_info['order_id']);

        $this->model_payment_dhpay->initConfig();

        $this->data['dhpay_params'] = array(
            'product_name'=>$order_product[0]['name'],
            'product_quantity'=>$order_product[0]['quantity'],
            'product_price'=>$this->currency->format($order_product[0]['price'], $order_info['currency_code'], false, false),
            'invoice_id'=>$order_info['order_id'],
            'order_no'=>$order_info['order_id'],
            'currency'=>$order_info['currency_code'],
            'amount'=>$this->currency->format($order_info['total'], $order_info['currency_code'], false, false),
            'return_url'=>HTTPS_SERVER . 'index.php?route=payment/dhpay/ipn&dh_rt=real_time',
            'remark'=>'',
            'first_name'=>$order_info['payment_firstname'],
            'last_name'=>$order_info['payment_lastname'],
            'address_line'=>$order_info['payment_address_1'] . "\n" . $order_info['payment_address_2'],
            'country'=>$payment_country['iso_code_2'],
            'state'=>$payment_zone['code'],
            'city'=> $order_info['payment_city'],
            'buyer_email'=>$order_info['email'],
            'zipcode'=>$order_info['payment_postcode'],
            'shipping_country'=>@$shipping_country['iso_code_2'],
            'shipping_first_name'=>$order_info['shipping_firstname'],
            'shipping_last_name'=>$order_info['shipping_lastname'],
            'shipping_state'=>$shipping_zone['code'],
            'shipping_phone'=>$order_info['telephone'],
            'shipping_city'=>$order_info['shipping_city'],
            'shipping_address_line'=>$order_info['shipping_address_1'] . "\n" . $order_info['shipping_address_2'],
            'shipping_zipcode'=>$order_info['shipping_postcode'],
            'shipping_email'=>$order_info['email']
        );
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/dhpay.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/dhpay.tpl';
        } else {
            $this->template = 'default/template/payment/dhpay.tpl';
        }
        $this->render();
    }

    public function ipn()
    {
        $this->load->model('payment/dhpay');
        $this->load->model('checkout/order');

        $this->model_payment_dhpay->ipn($this->request->get);
    }

    public function failure(){
        if (isset($this->session->data['order_id'])) {
            $this->cart->clear();

            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['guest']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);
        }

        //$this->language->load('checkout/success');
        $this->language->load('payment/dhpay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href'      => $this->url->link('common/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'href'      => $this->url->link('checkout/cart'),
            'text'      => $this->language->get('text_basket'),
            'separator' => '::'
        );

        $this->data['breadcrumbs'][] = array(
            'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
            'text'      => $this->language->get('text_checkout'),
            'separator' => '::'
        );

        $this->data['breadcrumbs'][] = array(
            'href'      => $this->url->link('checkout/success'),
            'text'      => $this->language->get('text_failure'),
            'separator' => '::'
        );

        $this->data['heading_title'] = $this->language->get('text_failure');

        $this->data['text_message'] = sprintf($this->language->get('text_message'), $this->url->link('information/contact'));

        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['continue'] = $this->url->link('common/home');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/success.tpl';
        } else {
            $this->template = 'default/template/common/success.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
}