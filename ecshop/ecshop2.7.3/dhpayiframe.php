<?php

/**
 * ECSHOP 支付响应页面
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: respond.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_payment.php');
require(ROOT_PATH . 'includes/lib_order.php');
/* 支付方式代码 */
$pay_code = !empty($_REQUEST['code']) ? trim($_REQUEST['code']) : '';

/* 参数是否为空 */
if (empty($pay_code))
{
    $msg = $_LANG['pay_not_exist'];
}
else
{
    /* 检查code里面有没有问号 */
    if (strpos($pay_code, '?') !== false)
    {
        $arr1 = explode('?', $pay_code);
        $arr2 = explode('=', $arr1[1]);

        $_REQUEST['code']   = $arr1[0];
        $_REQUEST[$arr2[0]] = $arr2[1];
        $_GET['code']       = $arr1[0];
        $_GET[$arr2[0]]     = $arr2[1];
        $pay_code           = $arr1[0];
    }

    /* 判断是否启用 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('payment') . " WHERE pay_code = '$pay_code' AND enabled = 1";
    if ($db->getOne($sql) == 0)
    {
        $msg = $_LANG['pay_disabled'];
    }else{
        $payment = get_payment($pay_code);
        if ($payment['dhpay_way'] == 'iframe') {
            $payflow_url = 'https://www.dhpay.com/merchant/web/cashier/iframe/before';
        }else{
            $msg = $_LANG['pay_disabled'];
        }

        $_POST['return_url'] = $GLOBALS['ecs']->url() . 'dhpayiframerespond.php?code=' . $pay_code;
        if ($payment['dhpay_test'] == 'test') {
            $payflow_url .= '?env=dhpaysandbox';
        }

        $payflow_url .= ($payment['dhpay_test'] == 'test' ? '&' : '?' ) . http_build_query($_POST, '', '&');
        $smarty->assign('payflow_url', $payflow_url);

    }
}

assign_template();
$position = assign_ur_here();
$smarty->assign('page_title', $position['title']);   // 页面标题
$smarty->assign('ur_here',    $position['ur_here']); // 当前位置
$smarty->assign('page_title', $position['title']);   // 页面标题
$smarty->assign('ur_here',    $position['ur_here']); // 当前位置
$smarty->assign('helps',      get_shop_help());      // 网店帮助

$payment_lang = ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/payment/dhpay.php';
include_once($payment_lang);

$smarty->assign('dhpay_nav',    $_LANG['dhpay_nav']);
$smarty->assign('message',    $msg);
$smarty->assign('shop_url',   $ecs->url());


$smarty->display('dhpayiframe.dwt');

?>