<?php
class ControllerExtensionPaymentDhpay extends Controller {
	private $error = array();

    public function index() {
        $this->load->language('extension/payment/dhpay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('dhpay', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_authorization'] = $this->language->get('text_authorization');
        $data['text_sale'] = $this->language->get('text_sale');
        $data['text_iframe'] = $this->language->get('text_iframe');
        $data['text_redirect'] = $this->language->get('text_redirect');
        $data['text_vertical'] = $this->language->get('text_vertical');
        $data['text_horizontal'] = $this->language->get('text_horizontal');
        $data['text_live'] = $this->language->get('text_live');
        $data['text_test'] = $this->language->get('text_test');

        $data['entry_dhpay_merchant_id'] = $this->language->get('entry_dhpay_merchant_id');
        $data['entry_dhpay_private_key'] = $this->language->get('entry_dhpay_private_key');
        $data['entry_dhpay_mode'] = $this->language->get('entry_dhpay_mode');
        $data['entry_dhpay_status'] = $this->language->get('entry_dhpay_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_dhpay_payment_method'] = $this->language->get('entry_dhpay_payment_method');
        $data['entry_dhpay_order_status_id'] = $this->language->get('entry_dhpay_order_status_id');
        $data['entry_dhpay_order_status_fail_id'] = $this->language->get('entry_dhpay_order_status_fail_id');
        $data['entry_dhpay_order_status_processing_id'] = $this->language->get('entry_dhpay_order_status_processing_id');
        $data['entry_dhpay_style_body'] = $this->language->get('entry_dhpay_style_body');
        $data['entry_dhpay_style_title'] = $this->language->get('entry_dhpay_style_title');
        $data['entry_dhpay_style_button'] = $this->language->get('entry_dhpay_style_button');
        $data['entry_dhpay_style_layout'] = $this->language->get('entry_dhpay_style_layout');
        $data['entry_dhpay_title'] = $this->language->get('entry_dhpay_title');
        $data['entry_status'] = $this->language->get('entry_status');


        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['action'] = $this->url->link('extension/payment/dhpay', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

        $data['breadcrumbs'] = array(
            array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
            ),
            array(
                'text' => $this->language->get('text_payment'),
                'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            ),
            array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/payment/dhpay', 'token=' . $this->session->data['token'], 'SSL'),
            )
        );

        $data['error_dhpay_merchant_id'] = isset($this->error['dhpay_merchant_id']) ? $this->error['dhpay_merchant_id'] : '';
        $data['error_dhpay_private_key'] = isset($this->error['dhpay_private_key']) ? $this->error['dhpay_private_key'] : '';
        $data['error_dhpay_title'] = isset($this->error['dhpay_title']) ? $this->error['dhpay_title'] : '';
        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        $data['dhpay_title'] = isset($this->request->post['dhpay_title']) ? $this->request->post['dhpay_title'] : $this->config->get('dhpay_title');
        $data['dhpay_merchant_id'] = isset($this->request->post['dhpay_merchant_id']) ? $this->request->post['dhpay_merchant_id'] : $this->config->get('dhpay_merchant_id');
        $data['dhpay_private_key'] = isset($this->request->post['dhpay_private_key']) ? $this->request->post['dhpay_private_key'] : $this->config->get('dhpay_private_key');
        $data['dhpay_mode'] = isset($this->request->post['dhpay_mode']) ? $this->request->post['dhpay_mode'] : $this->config->get('dhpay_mode');
        $data['dhpay_style_body'] = isset($this->request->post['dhpay_style_body']) ? $this->request->post['dhpay_style_body'] : $this->config->get('dhpay_style_body');
        $data['dhpay_style_title'] = isset($this->request->post['dhpay_style_title']) ? $this->request->post['dhpay_style_title'] : $this->config->get('dhpay_style_title');
        $data['dhpay_style_button'] = isset($this->request->post['dhpay_style_button']) ? $this->request->post['dhpay_style_button'] : $this->config->get('dhpay_style_button');
        $data['dhpay_style_layout'] = isset($this->request->post['dhpay_style_layout']) ? $this->request->post['dhpay_style_layout'] : $this->config->get('dhpay_style_layout');
        $data['dhpay_status'] = isset($this->request->post['dhpay_status']) ? $this->request->post['dhpay_status'] : $this->config->get('dhpay_status');
        $data['dhpay_sort_order'] = isset($this->request->post['dhpay_sort_order']) ? $this->request->post['dhpay_sort_order'] : $this->config->get('dhpay_sort_order');
        $data['dhpay_payment_method'] = isset($this->request->post['dhpay_payment_method']) ? $this->request->post['dhpay_payment_method'] : $this->config->get('dhpay_payment_method');

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['dhpay_order_status_id'] = isset($this->request->post['dhpay_order_status_id']) ? $this->request->post['dhpay_order_status_id'] : $this->config->get('dhpay_order_status_id');
        $data['dhpay_order_status_processing_id'] = isset($this->request->post['dhpay_order_status_processing_id']) ? $this->request->post['dhpay_order_status_processing_id'] : $this->config->get('dhpay_order_status_processing_id');
        $data['dhpay_order_status_fail_id'] = isset($this->request->post['dhpay_order_status_fail_id']) ? $this->request->post['dhpay_order_status_fail_id'] : $this->config->get('dhpay_order_status_fail_id');

//        $data['post_url'] = HTTPS_CATALOG . 'index.php?route=payment/dhpay/paymentipn';
//        $data['return_url'] = HTTPS_CATALOG . 'index.php?route=payment/dhpay/paymentreturn';

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/dhpay', $data));
    }

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/dhpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['dhpay_merchant_id']) {
			$this->error['dhpay_merchant_id'] = $this->language->get('error_dhpay_merchant_id');
		}

		if (!$this->request->post['dhpay_private_key']) {
			$this->error['dhpay_private_key'] = $this->language->get('error_dhpay_private_key');
		}

        if (!$this->request->post['dhpay_title']) {
            $this->error['dhpay_title'] = $this->language->get('error_dhpay_title');
        }
		return !$this->error;
	}

	public function install(){
        return true;
	}

    public function uninstall(){
        return true;
    }
}