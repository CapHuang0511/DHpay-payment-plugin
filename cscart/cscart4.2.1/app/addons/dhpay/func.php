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

use Tygh\Registry;
use Tygh\Settings;
use Tygh\Http;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

require_once dirname(__FILE__) . "/dhpay.functions.php";

function fn_dhpay_delete_payment_processors()
{
    db_query("DELETE FROM ?:payment_descriptions WHERE payment_id IN (SELECT payment_id FROM ?:payments WHERE processor_id IN (SELECT processor_id FROM ?:payment_processors WHERE processor_script IN ('dhpay.php', 'dhpay_pro.php', 'payflow_pro.php', 'dhpay_express.php', 'dhpay_advanced.php')))");
    db_query("DELETE FROM ?:payments WHERE processor_id IN (SELECT processor_id FROM ?:payment_processors WHERE processor_script IN ('dhpay.php', 'dhpay_pro.php', 'payflow_pro.php', 'dhpay_express.php', 'dhpay_advanced.php'))");
    db_query("DELETE FROM ?:payment_processors WHERE processor_script IN ('dhpay.php', 'dhpay_pro.php', 'payflow_pro.php', 'dhpay_express.php', 'dhpay_advanced.php')");
}

function fn_dhpay_get_checkout_payment_buttons(&$cart, &$cart_products, &$auth, &$checkout_buttons, &$checkout_payments, &$payment_id)
{
    $processor_data = fn_get_processor_data($payment_id);
    if (!empty($processor_data) && empty($checkout_buttons[$payment_id]) && Registry::get('runtime.mode') == 'cart') {
        $checkout_buttons[$payment_id] = '
            <form name="dhpay_express" action="'. fn_payment_url('current', 'dhpay_express.php') . '" method="post">
            <input name="payment_id" value="' . $payment_id . '" type="hidden" />
            <input src="https://www.dhpayobjects.com/webstatic/en_US/i/buttons/checkout-logo-small.png" type="image" />
            <input name="mode" value="express" type="hidden" />
            </form>';
    }
}

function fn_dhpay_payment_url(&$method, &$script, &$url, &$payment_dir)
{
    if (strpos($script, 'dhpay_express.php') !== false) {
        $payment_dir = '/app/addons/dhpay/payments/';
    }
}

function fn_update_dhpay_settings($settings)
{
    if (isset($settings['dhpay_statuses'])) {
        $settings['dhpay_statuses'] = serialize($settings['dhpay_statuses']);
    }

    if (isset($settings['dhpay_config_data'])){
        $settings['dhpay_config_data'] = serialize($settings['dhpay_config_data']);
    }

    foreach ($settings as $setting_name => $setting_value) {
        Settings::instance()->updateValue($setting_name, $setting_value);
    }
}

function fn_get_dhpay_settings($lang_code = DESCR_SL)
{
    $dhpay_settings = Settings::instance()->getValues('dhpay', 'ADDON');

    if (!empty($dhpay_settings['general']['dhpay_statuses'])) {
        $dhpay_settings['general']['dhpay_statuses'] = unserialize($dhpay_settings['general']['dhpay_statuses']);
    }

    if (!empty($dhpay_settings['general']['dhpay_config_data'])) {
        $dhpay_settings['general']['dhpay_config_data'] = unserialize($dhpay_settings['general']['dhpay_config_data']);
    }

    $dhpay_settings['general']['main_pair'] = fn_get_image_pairs(fn_dhpay_get_logo_id(), 'dhpay_logo', 'M', false, true, $lang_code);

    return $dhpay_settings['general'];
}

function fn_dhpay_get_logo_id()
{
    if (Registry::get('runtime.simple_ultimate')) {
        $logo_id = 1;
    } elseif (Registry::get('runtime.company_id')) {
        $logo_id = Registry::get('runtime.company_id');
    } else {
        $logo_id = 0;
    }

    return $logo_id;
}

function fn_dhpay_update_payment_pre(&$payment_data, &$payment_id, &$lang_code, &$certificate_file, &$certificates_dir)
{
    if (!empty($payment_data['processor_id']) && db_get_field("SELECT processor_id FROM ?:payment_processors WHERE processor_id = ?i AND processor_script IN ('dhpay.php', 'dhpay_pro.php', 'payflow_pro.php', 'dhpay_express.php', 'dhpay_advanced.php')", $payment_data['processor_id'])) {
        $p_surcharge = floatval($payment_data['p_surcharge']);
        $a_surcharge = floatval($payment_data['a_surcharge']);
        if (!empty($p_surcharge) || !empty($a_surcharge)) {
            $payment_data['p_surcharge'] = 0;
            $payment_data['a_surcharge'] = 0;
            fn_set_notification('E', __('error'), __('text_dhpay_surcharge'));
        }
    }
}

function fn_dhpay_rma_update_details_post(&$data, &$show_confirmation_page, &$show_confirmation, &$is_refund, &$_data, &$confirmed)
{
    $change_return_status = $data['change_return_status'];
    if (($show_confirmation == false || ($show_confirmation == true && $confirmed == 'Y')) && $is_refund == 'Y') {
        $order_info = fn_get_order_info($change_return_status['order_id']);
        $amount = 0;
        $st_inv = fn_get_statuses(STATUSES_RETURN);
        if ($change_return_status['status_to'] != $change_return_status['status_from'] && $st_inv[$change_return_status['status_to']]['params']['inventory'] != 'D') {
            if (!empty($order_info['payment_method']) && !empty($order_info['payment_method']['processor_params']) && !empty($order_info['payment_info']) && !empty($order_info['payment_info']['transaction_id'])) {
                if (!empty($order_info['payment_method']['processor_params']['username']) && !empty($order_info['payment_method']['processor_params']['password'])) {
                    $request_data = array(
                        'METHOD' => 'RefundTransaction',
                        'VERSION' => '94',
                        'TRANSACTIONID' => $order_info['payment_info']['transaction_id']
                    );
                    if (!empty($order_info['returned_products'])) {
                        foreach ($order_info['returned_products'] as $product) {
                            $amount += $product['subtotal'];
                        }
                    } elseif (!empty($order_info['products'])) {
                        foreach ($order_info['products'] as $product) {
                            if (isset($product['extra']['returns'])) {
                                foreach ($product['extra']['returns'] as $return_id => $return_data)  {
                                    $amount += $return_data['amount'] * $product['subtotal'];
                                }
                            }
                        }
                    }

                    if ($amount != $order_info['subtotal'] || fn_allowed_for('MULTIVENDOR')) {
                        $request_data['REFUNDTYPE'] = 'Partial';
                        $request_data['AMT'] = $amount;
                        $request_data['CURRENCYCODE'] = isset($order_info['payment_method']['processor_params']['currency']) ? $order_info['payment_method']['processor_params']['currency'] : 'USD';
                        $request_data['NOTE'] = !empty($_REQUEST['comment']) ? $_REQUEST['comment'] : '';
                    } else {
                        $request_data['REFUNDTYPE'] = 'Full';
                    }
                    fn_dhpay_build_request($order_info['payment_method'], $request_data, $post_url, $cert_file);
                    $result = fn_dhpay_request($request_data, $post_url, $cert_file);
                }
            }
        }
    }
}

function fn_validate_dhpay_order_info($data, $order_info)
{
    if (empty($data) || empty($order_info)) {
        return false;
    }
    $errors = array();
    if (!isset($data['num_cart_items']) || count($order_info['products']) != $data['num_cart_items']) {
        if (isset($order_info['payment_method']) && isset($order_info['payment_method']['processor_id']) && 'dhpay.php' == db_get_field("SELECT processor_script FROM ?:payment_processors WHERE processor_id = ?i", $order_info['payment_method']['processor_id'])) {
            list(, $count) = fn_dhpay_standart_prepare_products($order_info);

            if ($count != $data['num_cart_items']) {
                $errors[] = __('dhpay_product_count_is_incorrect');
            }
        }
    }
    if (!isset($order_info['payment_method']['processor_params']) || !isset($order_info['payment_method']['processor_params']['currency']) || !isset($data['mc_currency']) || $data['mc_currency'] != $order_info['payment_method']['processor_params']['currency']) {
        //if cureency defined in dhpay settings do not match currency in IPN
        $errors[] = __('dhpay_currency_is_incorrect');
    } elseif (!isset($data['mc_gross']) || !isset($order_info['total']) || (float)$data['mc_gross'] != (float)$order_info['total']) {
        //if currency is ok, check totals
        $errors[] = __('dhpay_total_is_incorrect');
    }

    if (!empty($errors)) {
        $dhpay_response['ipn_errors'] = implode('; ', $errors);
        fn_update_order_payment_info($order_info['order_id'], $dhpay_response);
        return false;
    }
    return true;
}

function fn_dhpay_get_customer_info($data)
{
    $user_data = array();
    if (!empty($data['address_street'])) {
        $user_data['b_address'] = $user_data['s_address'] = $data['address_street'];
    }
    if (!empty($data['address_city'])) {
        $user_data['b_city'] = $user_data['s_city'] = $data['address_city'];
    }
    if (!empty($data['address_state'])) {
        $user_data['b_state'] = $user_data['s_state'] = $data['address_state'];
    }
    if (!empty($data['address_country'])) {
        $user_data['b_country'] = $user_data['s_country'] = $data['address_country'];
    }
    if (!empty($data['address_zip'])) {
        $user_data['b_zipcode'] = $user_data['s_zipcode'] = $data['address_zip'];
    }
    if (!empty($data['contact_phone'])) {
        $user_data['b_phone'] = $user_data['s_phone'] = $data['contact_phone'];
    }
    if (!empty($data['address_country_code'])) {
        $user_data['b_country'] = $user_data['s_country'] = $data['address_country_code'];
    }
    if (!empty($data['first_name'])) {
        $user_data['firstname'] = $data['first_name'];
    }
    if (!empty($data['last_name'])) {
        $user_data['lastname'] = $data['last_name'];
    }
    if (!empty($data['address_name'])) {
        //When customer set a shipping name we should use it
        $_address_name = explode(' ', $data['address_name']);
        $user_data['s_firstname'] = $_address_name[0];
        $user_data['s_lastname'] = $_address_name[1];
    }
    if (!empty($data['payer_business_name'])) {
        $user_data['company'] = $data['payer_business_name'];
    }
    if (!empty($data['payer_email'])) {
        $user_data['email'] = $data['payer_email'];
    }
    if (!empty($user_data) && isset($data['charset'])) {
        array_walk($user_data, 'fn_dhpay_convert_encoding', $data['charset']);
    }

    return $user_data;
}

function fn_dhpay_convert_encoding(&$value, $key, $enc_from = 'windows-1252')
{
    $value = fn_convert_encoding($enc_from, 'UTF-8', $value);
}

function fn_process_dhpay_ipn($order_id, $data)
{
    $order_info = fn_get_order_info($order_id);
    if (!empty($order_info)) {
        $dhpay_settings = fn_get_dhpay_settings();
        fn_clear_cart($cart, true);
        $customer_auth = fn_fill_auth(array(), array(), false, 'C');
        fn_form_cart($order_id, $cart, $customer_auth);

        if (@$dhpay_settings['override_customer_info'] == 'Y') {
            $cart['user_data'] = fn_dhpay_get_customer_info($data);
        }

        $cart['order_id'] = $order_id;
        $cart['payment_info'] = $order_info['payment_info'];
        $cart['payment_info']['protection_eligibility'] = '';
        $cart['payment_id'] = $order_info['payment_id'];

        //Sometimes, for some reasons cart_id in product products calculated incorrectle, so we need recalculate it.
        $cart['change_cart_products'] = true;
        fn_calculate_cart_content($cart, $customer_auth);
        $cart['payment_info']['order_status'] = $dhpay_settings['dhpay_statuses'][strtolower($data['payment_status'])];
        list($order_id, ) = fn_update_order($cart, $order_id);

        if ($order_id) {
            fn_change_order_status($order_id, $dhpay_settings['dhpay_statuses'][strtolower($data['payment_status'])]);
            if (in_array($dhpay_settings['dhpay_statuses'][strtolower($data['payment_status'])], fn_get_order_paid_statuses())) {
                db_query('DELETE FROM ?:user_session_products WHERE order_id = ?i AND type = ?s', $order_id, 'C');
            }
            if (fn_allowed_for('MULTIVENDOR')) {
                $child_order_ids = db_get_fields("SELECT order_id FROM ?:orders WHERE parent_order_id = ?i", $order_id);
                if (!empty($child_order_ids)) {
                    foreach ($child_order_ids as $child_order_id) {
                        fn_update_order_payment_info($child_order_id, $cart['payment_info']);
                    }
                }
            }
        }

        return true;
    }
}

function fn_dhpay_get_ipn_order_ids($data)
{
    $order_ids = (array)(int)$data['custom'];
    fn_set_hook('dhpay_get_ipn_order_ids', $data, $order_ids);

    return $order_ids;
}

function fn_dhpay_prepare_checkout_payment_methods(&$cart, &$auth, &$payment_groups)
{
    if (isset($cart['payment_id'])) {
        foreach ($payment_groups as $tab => $payments) {
            foreach ($payments as $payment_id => $payment_data) {
                if (isset($_SESSION['dhpay_express_details'])) {
                    if ($payment_id != $cart['payment_id']) {
                        unset($payment_groups[$tab][$payment_id]);
                    } else {
                        $_tab = $tab;
                    }
                }
            }
        }
        if (isset($_tab)) {
            $_payment_groups = $payment_groups[$_tab];
            $payment_groups = array();
            $payment_groups[$_tab] = $_payment_groups;
        }
    }
}

function fn_dhpay_standart_prepare_products($order_info, $dhpay_currency = '', $max_dhpay_products = MAX_DHPAY_PRODUCTS)
{
    $post_data = array();
    $product_count = 1;

    if (empty($dhpay_currency)) {
        $dhpay_currency = !empty($order_info['payment_method']['processor_params']['currency']) ? $order_info['payment_method']['processor_params']['currency'] : CART_PRIMARY_CURRENCY;
    }

    $dhpay_shipping = fn_order_shipping_cost($order_info);
    $dhpay_total = fn_format_price($order_info['total'] - $dhpay_shipping, $dhpay_currency);

    if (empty($order_info['use_gift_certificates']) && !floatval($order_info['subtotal_discount']) && empty($order_info['points_info']['in_use']) && count($order_info['products']) < MAX_DHPAY_PRODUCTS) {
        $i = 1;
        if (!empty($order_info['products'])) {
            foreach ($order_info['products'] as $k => $v) {
                $suffix = '_'.($i++);
                $v['product'] = htmlspecialchars(strip_tags($v['product']));
                $v['price'] = fn_format_price(($v['subtotal'] - fn_external_discounts($v)) / $v['amount'], $dhpay_currency);
                $post_data["item_name$suffix"] = $v['product'];
                $post_data["amount$suffix"] = $v['price'];
                $post_data["quantity$suffix"] = $v['amount'];
                if (!empty($v['product_options'])) {
                    foreach ($v['product_options'] as $_k => $_v) {
                        $_v['option_name'] = htmlspecialchars(strip_tags($_v['option_name']));
                        $_v['variant_name'] = htmlspecialchars(strip_tags($_v['variant_name']));
                        $post_data["on$_k$suffix"] = $_v['option_name'];
                        $post_data["os$_k$suffix"] = $_v['variant_name'];
                    }
                }
            }
        }

        if (!empty($order_info['taxes']) && Registry::get('settings.General.tax_calculation') == 'subtotal') {
            foreach ($order_info['taxes'] as $tax_id => $tax) {
                if ($tax['price_includes_tax'] == 'Y') {
                    continue;
                }
                $suffix = '_' . ($i++);
                $item_name = htmlspecialchars(strip_tags($tax['description']));
                $item_price = fn_format_price($tax['tax_subtotal'], $dhpay_currency);
                $post_data["item_name$suffix"] = $item_name;
                $post_data["amount$suffix"] = $item_price;
                $post_data["quantity$suffix"] = '1';
            }
        }

        // Gift Certificates
        if (!empty($order_info['gift_certificates'])) {
            foreach ($order_info['gift_certificates'] as $k => $v) {
                $suffix = '_' . ($i++);
                $v['gift_cert_code'] = htmlspecialchars($v['gift_cert_code']);
                $v['amount'] = (!empty($v['extra']['exclude_from_calculate'])) ? 0 : fn_format_price($v['amount'], $dhpay_currency);
                $post_data["item_name$suffix"] = $v['gift_cert_code'];
                $post_data["amount$suffix"] = $v['amount'];
                $post_data["quantity$suffix"] = '1';
            }
        }

        if (fn_allowed_for('MULTIVENDOR') && fn_take_payment_surcharge_from_vendor('')) {
            $take_surcharge = false;
        } else {
            $take_surcharge = true;
        }

        // Payment surcharge
        if ($take_surcharge && floatval($order_info['payment_surcharge'])) {
            $suffix = '_' . ($i++);
            $name = __('surcharge');
            $payment_surcharge_amount = fn_format_price($order_info['payment_surcharge'], $dhpay_currency);
            $post_data["item_name$suffix"] = $name;
            $post_data["amount$suffix"] = $payment_surcharge_amount;
            $post_data["quantity$suffix"] = '1';
        }
        $product_count = $i - 1;
    } elseif ($dhpay_total <= 0) {
        $post_data['item_name_1'] = __('total_product_cost');;
        $post_data['amount_1'] = fn_format_price($order_info['total'], $dhpay_currency);
        $post_data['quantity_1'] = '1';
        $post_data['amount'] = fn_format_price($order_info['total'], $dhpay_currency);;
        $post_data['shipping_1'] = 0;
    } else {
        $post_data['item_name_1'] = __('total_product_cost');;
        $post_data['amount_1'] = $dhpay_total;
        $post_data['quantity_1'] = '1';
    }

    return array($post_data, $product_count);
}

function fn_dhpay_save_mode($order_info)
{
    $data['dhpay_mode'] = 'test';
    if (!empty($order_info['payment_method']) && !empty($order_info['payment_method']['processor_params']) && !empty($order_info['payment_method']['processor_params']['mode'])) {
        $data['dhpay_mode'] = $order_info['payment_method']['processor_params']['mode'];
    }
    fn_update_order_payment_info($order_info['order_id'], $data);

    return true;
}

function fn_dhpay_get_mode($order_id)
{
    $result = 'test';
    $payment_info = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = 'P'", $order_id);
    if (!empty($payment_info)) {
        $payment_info = unserialize(fn_decrypt_text($payment_info));
        if (!empty($payment_info['dhpay_mode'])) {
            $result = $payment_info['dhpay_mode'];
        }
    }

    return $result;
}

function fn_dhpay_request_hash($data, $private_key)
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

function fn_dhpay_resonse_hash($data, $private_key)
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

