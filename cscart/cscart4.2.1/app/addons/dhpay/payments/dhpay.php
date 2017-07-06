<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

use Tygh\Http;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

// Return from dhpay website
if (defined('PAYMENT_NOTIFICATION')) {
    if ($mode == 'iframereturn') {
        $order_id = $_REQUEST['order_id'];
        $params = $_REQUEST;
        $url = fn_url("payment_notification.return?payment=dhpay&order_id=$order_id", AREA, 'current');
        unset($params['dispatch']);
        unset($params['order_id']);
        $url .= '&' . http_build_query($params, '', "&");
        echo '<script type="text/javascript">top.location.href="' . $url . '"</script>';exit;
    }elseif ($mode == 'return') {
        //if ()
        if (fn_check_payment_script('dhpay.php', $_REQUEST['order_id'])) {
            $order_info = fn_get_order_info($_REQUEST['order_id'], true);
            fn_dhpay_save_mode($order_info);

            if ($order_info['status'] == 'O') {
                $edp_data = fn_generate_ekeys_for_edp(array('status_from' => STATUS_INCOMPLETED_ORDER, 'status_to' => 'O'), $order_info);
                fn_order_notification($order_info, $edp_data);
            }

            if (fn_allowed_for('MULTIVENDOR')) {
                if ($order_info['status'] == STATUS_PARENT_ORDER) {
                    $child_orders = db_get_hash_single_array("SELECT order_id, status FROM ?:orders WHERE parent_order_id = ?i", array('order_id', 'status'), $_REQUEST['order_id']);
                    foreach ($child_orders as $order_id => $order_status) {
                        if ($order_status == 'O') {
                            $order_info = fn_get_order_info($order_id, true);
                            $edp_data = fn_generate_ekeys_for_edp(array('status_from' => STATUS_INCOMPLETED_ORDER, 'status_to' => 'O'), $order_info);
                            fn_order_notification($order_info, $edp_data);
                        }
                    }
                }
            }
        }

        $settings = fn_get_dhpay_settings();
        $hash = fn_dhpay_resonse_hash($_REQUEST, $settings['dhpay_config_data']['private_key']);
        if($_REQUEST['hash'] == $hash){
            fn_define('ORDER_MANAGEMENT', true);
            if ($_REQUEST['status'] == '02'){
                $data['payment_status'] = 'Failed';
            }elseif($_REQUEST['status'] == '01'){
                $data['payment_status'] = 'Completed';
            }
            fn_process_dhpay_ipn($_REQUEST['order_no'], $data);
        }else{
            fn_set_notification('E', '', __('text_order_placed_error'));
            fn_redirect('checkout.checkout');
            exit;
        }

        fn_order_placement_routines('route', $_REQUEST['order_id'],  false);

    } elseif ($mode == 'cancel') {
        $order_info = fn_get_order_info($_REQUEST['order_id']);
        fn_dhpay_save_mode($order_info);

        $dhpay_response['order_status'] = 'N';
        $dhpay_response["reason_text"] = __('text_transaction_cancelled');

        if (!empty($_REQUEST['payer_email'])) {
            $dhpay_response['customer_email'] = $_REQUEST['payer_email'];
        }
        if (!empty($_REQUEST['payer_id'])) {
            $dhpay_response['client_id'] = $_REQUEST['payer_id'];
        }
        if (!empty($_REQUEST['memo'])) {
            $dhpay_response['customer_notes'] = $_REQUEST['memo'];
        }
        fn_finish_payment($_REQUEST['order_id'], $dhpay_response);
        fn_order_placement_routines('route', $_REQUEST['order_id']);
    }

} else {
    if (defined('IFRAME_MODE')){
        Tygh::$app['view']->display('views/checkout/processors/dhpay_placeorder.tpl');
        exit;
    }
    $settings = fn_get_dhpay_settings();
    $dhpay_account = $settings['dhpay_config_data']['merchant_id'];

    $dhpay_url = 'https://www.dhpay.com/merchant/web/cashier';
    if ($settings['dhpay_config_data']['checkout_method'] == 'Iframe') {
        $dhpay_url = 'https://www.dhpay.com/merchant/web/cashier/iframe/before';
    }

    if ($settings['dhpay_config_data']['test_mode'] == 'Test') {
        $dhpay_url .= '?env=dhpaysandbox';
    }

    $dhpay_currency = $processor_data['processor_params']['currency'];
    $dhpay_item_name = $processor_data['processor_params']['item_name'];
    //Order Total
    $dhpay_shipping = fn_order_shipping_cost($order_info);
    $dhpay_total = fn_format_price($order_info['total'], $dhpay_currency, 2, false);
    $dhpay_shipping = fn_format_price($dhpay_shipping, $dhpay_currency);
    $dhpay_order_id = $processor_data['processor_params']['order_prefix']. $order_id;

    $return_url = fn_url("payment_notification.return?payment=dhpay&order_id=$order_id", AREA, 'current');
    $cancel_url = fn_url("payment_notification.cancel?payment=dhpay&order_id=$order_id", AREA, 'current');
    $notify_url = fn_url("payment_notification.dhpay_ipn", AREA, 'current');
    $iframe_return_url = fn_url("payment_notification.iframereturn?payment=dhpay&order_id=$order_id", AREA, 'current');

    foreach($order_info['products'] as $order_product){
        break;
    }

    $post_data = array(
        'merchant_id' => $dhpay_account,
        'invoice_id' => $dhpay_order_id,
        'order_no' => $order_id,//
        'currency' => $dhpay_currency,
        'amount' => $dhpay_total,
        'buyer_email' => $order_info['email'],
        'shipping_country' => $shipping_country['iso_code_2'],
        'first_name' => $order_info['b_firstname'],
        'last_name' => $order_info['b_lastname'],
        'country' => $order_info['b_country'],
        'state' => $order_info['b_state'],
        'city' => $order_info['b_city'],
        'address_line' => $order_info['b_address'] . "\n" . $order_info['b_address_2'],
        'zipcode' => $order_info['b_zipcode'],
        'product_name' => $order_product['product'],
        'product_price' => sprintf("%.2f", $order_product['price']),
        'product_quantity' => $order_product['amount'],
        'return_url' => ($settings['dhpay_config_data']['checkout_method'] == 'Iframe') ? $iframe_return_url : $return_url,
        'remark' => '',
        'hash' => '',

        'shipping_first_name'=> $order_info['s_firstname'],
        'shipping_last_name'=>$order_info['s_lastname'],
        'shipping_state'=>$order_info['s_state'],
        'shipping_city'=>$order_info['s_city'],
        'shipping_address_line'=>$order_info['s_address']. "\n" . $order_info['s_address_2'],
        'shipping_zipcode'=>$order_info['s_zipcode'],
        'shipping_email'=>$order_info['email'],
        'shipping_phone'=>$order_info['s_phone'],
                       
                       'body_style' => $settings['dhpay_config_data']['style_body'],
                       'layout' => strtolower($settings['dhpay_config_data']['style_layout']),
                       'button_style' => $settings['dhpay_config_data']['style_button'],
                       'title_style' => $settings['dhpay_config_data']['style_title'],
    );

    $post_data['hash'] = fn_dhpay_request_hash($post_data, $settings['dhpay_config_data']['private_key']);

    if ($order_info['status'] == STATUS_INCOMPLETED_ORDER) {
        fn_change_order_status($order_id, 'O', '', false);
    }

    if (fn_allowed_for('MULTIVENDOR')) {
        if ($order_info['status'] == STATUS_PARENT_ORDER) {
            $child_orders = db_get_hash_single_array("SELECT order_id, status FROM ?:orders WHERE parent_order_id = ?i", array('order_id', 'status'), $order_id);

            foreach ($child_orders as $order_id => $order_status) {
                if ($order_status == STATUS_INCOMPLETED_ORDER) {
                    fn_change_order_status($order_id, 'O', '', false);
                }
            }
        }
    }
    fn_dhpay_save_mode($order_info);

    if ($settings['dhpay_config_data']['checkout_method'] == 'Iframe'){
        $iframe_url = $dhpay_url .
            ($settings['dhpay_config_data']['test_mode'] == 'Test' ? '&' : '?') .
            http_build_query($post_data, '', "&");

        Tygh::$app['view']->assign('iframe_url', $iframe_url);
        Tygh::$app['view']->assign('cancel_url', $cancel_url);

        Tygh::$app['view']->display('views/checkout/processors/dhpay_iframe.tpl');
    }else{
        echo <<<EOT
        <form method="post" action="$dhpay_url" name="process">
EOT;

        foreach ($post_data as $name => $value) {
            echo('<input type="hidden" name="' . htmlentities($name, ENT_QUOTES, 'UTF-8') . '" value="' . htmlentities($value, ENT_QUOTES, 'UTF-8') . '" />' . "\n");
        }
        echo <<<EOT
        </form>
        <script type="text/javascript">
             document.process.submit();
        </script>
EOT;

    }
    exit;
}

