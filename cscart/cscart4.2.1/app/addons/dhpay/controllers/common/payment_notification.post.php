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

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($mode == 'dhpay_ipn') {
        $settings = fn_get_dhpay_settings();
        $hash = fn_dhpay_resonse_hash($_REQUEST, $settings['dhpay_config_data']['private_key']);
        if($_REQUEST['hash'] != $hash){
            exit;
        }

        fn_define('ORDER_MANAGEMENT', true);
        if ($_REQUEST['status'] == '02'){
            $data['payment_status'] = 'Failed';
            fn_process_dhpay_ipn($_REQUEST['order_no'], $data);
        }elseif($_REQUEST['status'] == '01'){
            $data['payment_status'] = 'Completed';
            fn_process_dhpay_ipn($_REQUEST['order_no'], $data);
        }

        echo 'success';
        exit;
    }
}
