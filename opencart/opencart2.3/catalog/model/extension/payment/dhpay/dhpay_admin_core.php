<?php

/**
 * dhapy_core.php.
 *
 * @author zhuna<zhuna@yonyou.com>
 * @date   15/12/23
 */
class Dhpay_Admin_Core
{
    static $field = array(
        'DHPAY_MERCHANT_ID',
        'DHPAY_PRIVATE_KEY',
        'DHPAY_STYLE_TITLE',
        'DHPAY_STYLE_BUTTON',
        'DHPAY_STYLE_LAYOUT',
        'DHPAY_STYLE_BODY',
        'DHPAY_MODE',
        'DHPAY_PAYMENT_METHOD',
        'DHPAY_STATUS',
        'DHPAY_ORDER_STATUS_ID',
        'DHPAY_ORDER_STATUS_FAIL_ID',
        'DHPAY_ORDER_STATUS_PROCESSING_ID'
    );

    public static function initConfig($config)
    {
        foreach ($config as $key => $row) {
            if (in_array($key, self::$field)) {
                defined($key) || define($key, $row);
            }
        }
    }
}