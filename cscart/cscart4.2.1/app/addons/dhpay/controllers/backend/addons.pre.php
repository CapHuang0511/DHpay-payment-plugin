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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update' && $_REQUEST['addon'] == 'dhpay' && (!empty($_REQUEST['dhpay_settings']) )) {
        $dhpay_settings = isset($_REQUEST['dhpay_settings']) ? $_REQUEST['dhpay_settings'] : array();
        fn_update_dhpay_settings($dhpay_settings);
    }
}

if ($mode == 'update') {
    if ($_REQUEST['addon'] == 'dhpay') {
        //var_dump(fn_get_dhpay_settings());exit;
        Tygh::$app['view']->assign('dhpay_settings', fn_get_dhpay_settings());
    }
}
