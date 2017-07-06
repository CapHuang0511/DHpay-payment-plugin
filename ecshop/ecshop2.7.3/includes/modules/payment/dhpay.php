<?php

/**
 * ECSHOP 快钱插件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: kuaiqian.php 17217 2011-01-19 06:29:08Z liubo $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/payment/dhpay.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/**
 * 模块信息
 */
if (isset($set_modules) && $set_modules == true)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code'] = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc'] = 'dhpay_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod'] = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online'] = '1';

    /* 作者 */
    $modules[$i]['author']  = 'DHPAY TEAM';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.dhgate.com';

    /* 版本号 */
    $modules[$i]['version'] = '1.0';

    /* 配置信息 */
    $modules[$i]['config'] = array(
        array('name' => 'dhpay_account', 'type' => 'text', 'value' => ''),
        array('name' => 'dhpay_key', 'type' => 'text', 'value' => ''),
        array('name' => 'dhpay_currency', 'type' => 'select', 'value' => 'USD'),
        array('name' => 'dhpay_way', 'type' => 'select', 'value' => 'redirect'),
        array('name' => 'dhpay_test', 'type' => 'select', 'value' => 'live'),
        array('name' => 'dhpay_style_layout', 'type' => 'select', 'value' => 'vertical'),
        array('name' => 'dhpay_style_body', 'type' => 'text', 'value' => ''),
        array('name' => 'dhpay_style_title', 'type' => 'text', 'value' => ''),
        array('name' => 'dhpay_style_button', 'type' => 'text', 'value' => ''),
    );

    return;

}

class dhpay
{
    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */

    function dhpay()
    {
    }

    function __construct()
    {
        $this->dhpay();
    }

   /**
     * 生成支付代码
     * @param   array   $order  订单信息
     * @param   array   $payment    支付方式信息
     */
   function get_code($order, $payment)
   {
       global $_CFG;
       $sql = "SELECT *  FROM " . $GLOBALS['ecs']->table('order_goods') .
           " WHERE order_id = '{$order['order_id']}'";
       $order_goods = $GLOBALS['db']->getRow($sql);

       $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('region') .
           "WHERE region_id = '{$order['province']}'";
       $provinceinfo = $GLOBALS['db']->getRow($sql);

       $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('region') .
           "WHERE region_id = '{$order['city']}'";
       $cityinfo = $GLOBALS['db']->getRow($sql);

       $strlang = 'en';

       $merchant_acctid    = trim($payment['dhpay_account']);                 //人民币账号 不可空
       $private_key        = trim($payment['dhpay_key']);
       $page_url           = return_url(basename(__FILE__, '.php'));
       $bg_url             = '';
       $language           = 1;
       $order_id           = $order['order_sn'];                                    //商户订单号 不可空
       $order_amount       = $order['order_amount'];                                //商户订单金额 不可空
       $order_time         = local_date('YmdHis', $order['add_time']);            //商户订单提交时间 不可空 14位
       $currency           = $payment['dhpay_currency'];
       $return_url         = return_url(basename(__FILE__, '.php'));

       $payflow_url = 'https://www.dhpay.com/merchant/web/cashier';
       if ($payment['dhpay_test'] == 'test') {
           $payflow_url .= '?env=dhpaysandbox';
       }
       if ($payment['dhpay_way'] == 'iframe') {
           $payflow_url = $GLOBALS['ecs']->url() . 'dhpayiframe.php?code=dhpay';
       }


        /* 生成加密签名串 请务必按照如下顺序和规则组成加密串！*/
       // 签名的表单字段名
       $hash_src = '';
       $data = array(
           'amount'=>$order_amount,
           'currency'=>$currency,
           'invoice_id'=>$order_id,
           'merchant_id'=>$merchant_acctid);
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

       $def_url  = '<div style="text-align:center"><form name="dhpayPay" style="text-align:center;" method="post" action="'. $payflow_url . '" target="_blank">';
       $def_url .= $process_button_string = '<input type="hidden" name="product_name" value="' . $order_goods['goods_name'] . '" />' .
           '<input type="hidden" name="product_quantity" value="' . $order_goods['goods_number'] . '" />' .
           '<input type="hidden" name="product_price" value="' . $order_goods['goods_price'] . '" />' .
           '<input type="hidden" name="merchant_id" value="' . $merchant_acctid . '" />' .
           '<input type="hidden" name="invoice_id" value="' . $order_id . '" />' .
           '<input type="hidden" name="order_no" value="' . $order['log_id'] . '" />' .
           '<input type="hidden" name="currency" value="' . $currency . '" />' .
           '<input type="hidden" name="amount" value="' . $order_amount . '" />' .
           '<input type="hidden" name="return_url" value="' . $return_url . '" />' .
           '<input type="hidden" name="remark" value="" />' .
           '<input type="hidden" name="first_name" value="' . $order['consignee'] . '" />' .
           '<input type="hidden" name="last_name" value="" />' .
           '<input type="hidden" name="address_line" value="' . $order['address'] . '" />' .
           '<input type="hidden" name="country" value="CN" />' .
           '<input type="hidden" name="state" value="' . $provinceinfo['region_name'] . '" />' .
           '<input type="hidden" name="city" value="' . $cityinfo['region_name'] . '" />' .
           '<input type="hidden" name="buyer_email" value="' . $order['email'] . '" />' .
           '<input type="hidden" name="zipcode" value="' . $order['zipcode'] . '" />' .
           '<input type="hidden" name="shipping_country" value="CN" />' .
           '<input type="hidden" name="body_style" value="' . $payment['dhpay_style_body'] . '" />' .
           '<input type="hidden" name="title_style" value="' . $payment['dhpay_style_title'] . '" />' .
           '<input type="hidden" name="button_style" value="' . $payment['dhpay_style_button'] . '" />' .
           '<input type="hidden" name="language" value="' . $strlang . '" />' .
           '<input type="hidden" name="layout" value="' . $payment['dhpay_style_layout'] . '" />' .
           '<input type="hidden" name="hash" value="' . $hash . '" />' .
           '<input type="hidden" name="shipping_first_name" value="' . $order['consignee'] . '"> ' .
           '<input type="hidden" name="shipping_last_name" value="">' .
           '<input type="hidden" name="shipping_state" value="' . $provinceinfo['region_name'] . '">' .
           '<input type="hidden" name="shipping_city" value="' . $cityinfo['region_name'] . '">' .
           '<input type="hidden" name="shipping_address_line" value="' . $order['address'] . '">'.
           '<input type="hidden" name="shipping_zipcode" value="' . $order['zipcode'] . '">' .
           '<input type="hidden" name="shipping_email" value="' . $order['email'] . '">' .
           '<input type="hidden" name="shipping_phone" value="' . $order['mobile'] . '">';

       $def_url .= "<input type='submit' name='submit' value='" . $GLOBALS['_LANG']['pay_button'] . "' />";
       $def_url .= "</form></div></br>";

       return $def_url;
    }

    /**
     * 响应操作
     */
    function respond()
    {
        $payment             = get_payment($_GET['code']);
        $merchant_acctid     = $payment['dhpay_account'];                 //人民币账号 不可空
        $private_key        = trim($payment['dhpay_key']);
        $data = $_REQUEST;

        //首先对获得的商户号进行比对
        if ($_REQUEST['merchant_id'] != $merchant_acctid)
        {
            //商户号错误
            return false;
        }

        //生成加密串。必须保持如下顺序。
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

        if (strtoupper($hash) != $_REQUEST['hash']){
            //校验码错误
            return false;
        }

        if ($_REQUEST['status'] == '01')
        {
            order_paid($_REQUEST['order_no']);
            return true;
        }
        else
        {
            //'支付结果失败';
            return false;
        }

    }

    /**
    * 将变量值不为空的参数组成字符串
    * @param   string   $strs  参数字符串
    * @param   string   $key   参数键名
    * @param   string   $val   参数键对应值
    */
    function append_param($strs,$key,$val)
    {
        if($strs != "")
        {
            if($key != '' && $val != '')
            {
                $strs .= '&' . $key . '=' . $val;
            }
        }
        else
        {
            if($val != '')
            {
                $strs = $key . '=' . $val;
            }
        }
            return $strs;
    }

}

?>