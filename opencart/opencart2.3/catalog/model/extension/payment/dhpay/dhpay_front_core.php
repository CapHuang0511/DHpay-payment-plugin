<?php

/**
 * dhapy_core.php.
 *
 * @author zhuna<zhuna@yonyou.com>
 * @date   15/12/23
 */
class Dhpay_Front_Core
{

    const DHPAY_REDIRECT_URL = 'https://www.dhpay.com/merchant/web/cashier';
    const DHPAY_IFRAME_URL = 'https://www.dhpay.com/merchant/web/cashier/iframe/before?';

    static $fields = array(
        'product_name',
        'product_quantity',
        'product_price',
        'merchant_id',
        'invoice_id',
        'order_no',
        'currency',
        'amount',
        'return_url',
        'remark',
        'first_name',
        'last_name',
        'address_line',
        'country',
        'state',
        'city',
        'buyer_email',
        'zipcode',
        'shipping_country',
        'hash',
        'body_style',
        'title_style',
        'language',
        'layout',
        'button_style',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_state',
        'shipping_phone',
        'shipping_city',
        'shipping_address_line',
        'shipping_zipcode',
        'shipping_email'
    );

    public static $supportCurrency = array('CAD', 'GBP', 'EUR', 'USD', 'CNY', 'SEK', 'SGD', 'RUB', 'JPY', 'AUD');
    public static $supportLanguage = array('de', 'fr', 'it', 'es', 'pt', 'en');

    public static function show($currency)
    {
        if (DHPAY_STATUS != 'Active' || !in_array($currency, self::$supportCurrency)) {
            return false;
        }
        return true;
    }

    public static function env($params)
    {
        if (DHPAY_MODE == 'Test') {
            $params['env'] = 'dhpaysandbox';
        }
        return $params;
    }

    public static function language($params)
    {
        if (isset($params['language']) && in_array($params['language'], self::$supportLanguage)) {
            return $params;
        }

        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
        if (preg_match("/en/i", $lang))
            $strlang = 'en';//英文
        elseif (preg_match("/fr/i", $lang))
            $strlang = 'fr';//法语
        elseif (preg_match("/de/i", $lang))
            $strlang = 'de';//德语
        elseif (preg_match("/ja/i", $lang))
            $strlang = 'ja';//日语
        elseif (preg_match("/ko/i", $lang))
            $strlang = 'en';//'ko-kr';//韩语
        elseif (preg_match("/es/i", $lang))
            $strlang = 'es';//西班牙语
        elseif (preg_match("/it/i", $lang))
            $strlang = 'en';//'it-it';//意大利语
        else
            $strlang = 'en';//英文

        $params['language'] = $strlang;
        return $params;
    }

    public static function account($params)
    {
        $params['merchant_id'] = DHPAY_MERCHANT_ID;
        return $params;
    }

    public static function style($params)
    {
        $params['body_style'] = DHPAY_STYLE_BODY;
        $params['layout'] = strtolower(DHPAY_STYLE_LAYOUT);
        $params['button_style'] = DHPAY_STYLE_BUTTON;
        $params['title_style'] = DHPAY_STYLE_TITLE;
        return $params;
    }

    public static function data($data)
    {
        $params = array();
        foreach ($data as $key => $row) {
            if (in_array($key, self::$fields)) {
                switch ($key) {
                    case 'product_price':
                    case 'amount':
                        $row = sprintf('%.2f', $row);
                        break;
                    default:
                        $row = self::filter($row);
                }

                $params[$key] = $row;
            }

        }
        $params = self::account($params);
        $params = self::env($params);
        $params = self::language($params);
        $params = self::style($params);
        $params = self::request_hash($params);
        return $params;

    }

    public static function form($data, $url, $method, $target = '_self')
    {

        $params = self::data($data);

        $html = array('<form action="' . $url . '" method="' . $method . '" id="dhpay_payment_form" target="' . $target . '">');
        foreach ($params as $name => $val) {
            $html[] = '<input type="hidden" name="' . $name . '" value="' . $val . '" />';
        }
        $html[] = self::button();
        $html[] = '</form>';
        $html[] = '<script type="text/javascript">function dhpay_iframe_submit(){document.getElementById("dhpay_payment_form").submit()}</script>';
        return join('', $html);
    }

    public static function redirect($data)
    {
        $url = self::DHPAY_REDIRECT_URL;
        if (DHPAY_MODE == 'Test') {
            $url .= '?env=dhpaysandbox';
        }
        return self::form($data, $url, 'post');
    }

    public static function iframe($data, $width = '95%', $height = '95%')
    {
        $html = '<iframe frameborder="0" width="' . $width . '" height="' . $height . '" scrolling="no" name="dhpay_payment_iframe"></iframe>';
        $html .= self::form($data, self::DHPAY_IFRAME_URL, 'get', 'dhpay_payment_iframe');
        return $html;
    }

    public static function request_hash($data)
    {
        $hash_src = '';
        $hash_key = array('amount', 'currency', 'invoice_id', 'merchant_id');
        // 按 key 名进行顺序排序
        sort($hash_key);
        foreach ($hash_key as $key) {
            $hash_src .= $data[$key];
        }
        // 密钥放最前面
        $hash_src = DHPAY_PRIVATE_KEY . $hash_src;
        // sha256 算法
        $hash = hash('sha256', $hash_src);

        $data['hash'] = strtoupper($hash);
        return $data;
    }

    public static function response_hash($data)
    {
        $hash_src = '';
        $hash_key = array(
            'amount', 'currency', 'invoice_id', 'merchant_id',
            'trans_time', 'trans_date', 'status', 'ref_no', 'order_no'
        );
        if ($data['status'] == '02') {
            $hash_key[] = 'failure_reason';
        }
        // 按 key 名进行顺序排序
        sort($hash_key);
        foreach ($hash_key as $key) {
            $hash_src .= isset($data[$key]) ? $data[$key] : '';
        }
        // 密钥放最前面
        $hash_src = DHPAY_PRIVATE_KEY . $hash_src;
        // sha256 算法
        $hash = hash('sha256', $hash_src);
        return strtoupper($hash);
    }

    public static function getFormUrl()
    {
        $url = DHPAY_PAYMENT_METHOD == 'Redirect' ? self::DHPAY_REDIRECT_URL : self::DHPAY_IFRAME_URL;
        if (DHPAY_MODE == 'Test') {
            $url .= '?env=dhpaysandbox';
        }
        return $url;
    }

    public static function request($data)
    {
        $method = DHPAY_PAYMENT_METHOD;
        if (method_exists('Dhpay_Front_Core', $method)) {
            return call_user_func_array(array('Dhpay_Front_Core', $method), array($data));
        } else {
            echo 'Payment Method is invalid.';
            exit;
        }
    }

    public static function response($data)
    {
        if ($data['hash'] != self::response_hash($data)) {
            return 'fail';
        }

        if ($data['status'] == '01') { // 成功
            return 'success';
        } else if ($data['status'] == '02') { // 失败
            return 'fail';
        } else {
            return 'process';
        }
    }

    public static function filter($str)
    {
        $str = trim($str);
        $str = str_replace("&", "&amp;", $str);
        $str = str_replace("\"", "&quot;", $str);
        $str = str_replace("<", "&lt;", $str);
        $str = str_replace(">", "&gt;", $str);
        $str = str_replace("'", "&#39;", $str);
        return $str;
    }

    public static function button($html = ''){
        static $button = '';
        if ($html){
            $button = $html;
        }
        return $button;
    }
}