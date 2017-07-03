# PHP 版本接入示例

#### 商户下单页面

```html
<!DOCTYPE  html  PUBLIC  "-//W3C//DTD  HTML  4.01  Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="敦煌网, DHpay">
    <meta name="description" content="敦煌网, DHpay">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link type="text/css" rel="stylesheet" href="css/common.css">
    <link type="text/css" rel="stylesheet" href="css/payment.css">
    <title>商户/订单信息</title>
</head>

<body>
    <div style="padding-left:305px">
        <p style="width:500px;font-size:18px;margin:20px  0  20px 0;padding-bottom:10px;border-bottom:1px solid gray;">DHPay Demo -- PHP 版本</p>
        <form action="dhpay_select_payment.php" method="post">
            <table>
                <tr>
                    <td>商户/订单信息</td>
                </tr>
                <tr>
                    <td width="120">merchant_id</td>
                    <td>
                        <input id="merchant_id" name="merchant_id" value="1495400" /> 商户号，由 DHpay颁发</td>
                </tr>
                <tr>
                    <td>invoice_id</td>
                    <td>
                        <input id="invoice_id" name="invoice_id" value="14098277532431500" /> 交易号，由商户生成，本次交易的唯一标示, 以数字 0-9，大小写字母组成 </td>
                </tr>
                <tr>
                    <td>order_no</td>
                    <td>
                        <input id="order_no" name="order_no" value="" /> 订单号</td>
                </tr>
                <tr>
                    <td>currency</td>
                    <td>
                        <input id="currency" name="currency" value="USD" /> 币种</td>
                </tr>
                <tr>
                    <td>amount</td>
                    <td>
                        <input id="amount" name="amount" value="1.02" /> 交易金额</td>
                </tr>
                <tr>
                    <td>buyer_email</td>
                    <td>
                        <input id="buyer_email" name="buyer_email" value="" /> 买家邮件地址，接入 AE 卡必须传输</td>
                </tr>
                <tr>
                    <td>return_url</td>
                    <td>
                        <input id="return_url" name="return_url" value="http://localhost/dhpay_callback.php" style="width:350px;" /> 支付完成返回地址，如果不填则以商户设置为准</td>
                </tr>
                <tr>
                    <td>remark</td>
                    <td>
                        <input id="remark" name="remark" value="beizhu" /> 备注</td>
                </tr>
                <tr>
                    <td>shipping country</td>
                    <td>
                        <input id="shipping_country" name="shipping_country" value="US" /> 订单货运国家二维代码，风控要素，见附录国家代码</td>
                </tr>
                <tr>
                    <td>first name</td>
                    <td>
                        <input id="first_name" name="first_name" value="wukong" /> Billing 名</td>
                </tr>
                <tr>
                    <td>last name </td>
                    <td>
                        <input id="last_name" name="last_name" value="sun" /> Billing 姓</td>
                </tr>
                <tr>
                    <td>商品名</td>
                    <td>
                        <input id="product_name" name="product_name" value="book" /> billing 账单 商品名</td>
                </tr>
                <tr>
                    <td>商品价格</td>
                    <td>
                        <input id="product_price" name="product_price" value="1.02" /> billing 账单 商品价格</td>
                </tr>
                <tr>
                    <td>商品数量</td>
                    <td>
                        <input id="product_quantity" name="product_quantity" value="3" /> billing 账单 商品数量</td>
                </tr>
                <tr>
                    <td>billing 账单 地址</td>
                    <td>
                        <input id="address_line" name="address_line" value="111111" />billing 账单 地址</td>
                </tr>
                <tr>
                    <td>city</td>
                    <td>
                        <input id="city" name="city" value="ca" /> city</td>
                </tr>
                <tr>
                    <td>country</td>
                    <td>
                        <input id="country" name="country" value="US" /> country</td>
                </tr>
                <tr>
                    <td>state</td>
                    <td>
                        <input id="state" name="state" value="ca" /> state</td>
                </tr>
                <tr>
                    <td>zipcode</td>
                    <td>
                        <input id="zipcode" name="zipcode" value="1234" /> zipcode</td>
                </tr>
                <tr height="50">
                    <td></td>
                    <td>
                        <input type="submit" name="submit" value="下一步:  选择付款方式" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>

</html>

```
#### 选择支付方式页面

```php
<?php 
/**   * dhpay php demo   *   * select payment method   *   * author: lijie <lijierd@dhgate.com>   * date:      2014.09.05   */
  //  密钥 
$private_key = '111111';  
//  签名的表单字段名 
$hash_key = array('amount','currency', 'invoice_id', 'merchant_id');  
//  按 key 名进行顺序排序 
sort($hash_key); 
foreach ($hash_key as $key) {
     $hash_src .= $_POST[$key];
}  
//  密钥放最前面 
$hash_src = $private_key . $hash_src;  
// sha256 算法 
$hash = hash('sha256', $hash_src);  
?>


<html>

<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="敦煌网, DHpay">
    <meta name="description" content="敦煌网, DHpay">
    <link type="text/css" rel="stylesheet" href="css/common.css">
    <link type="text/css" rel="stylesheet" href="css/payment.css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>选择付款方式</title>
</head>

<body>
    <div class="page-wrap">
        <p style="width:500px;font-size:18px;margin:-10px  0  50px 0;padding-bottom:10px;border-bottom:1px solid gray;">DHPay Demo -- PHP 版本</p>
        <form action="https://www.dhpay.com/merchant/web/cashier" method="post">
            <input type="hidden" name="merchant_id" value="<?php echo $_POST['merchant_id']; ?>" class="dhpay-radio" />
            <input type="hidden" name="invoice_id" value="<?php  echo  $_POST['invoice_id'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="order_no" value="<?php  echo  $_POST['order_no'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="currency" value="<?php  echo  $_POST['currency'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="amount" value="<?php  echo  $_POST['amount'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="buyer_email" value="<?php echo $_POST['buyer_email']; ?>" class="dhpay-radio" />
            <input type="hidden" name="return_url" value="<?php  echo  $_POST['return_url'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="remark" value="<?php  echo  $_POST['remark'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="shipping_country" value="<?php  echo $_POST['shipping_country']; ?>" class="dhpay-radio" />
            <input type="hidden" name="first_name" value="<?php  echo  $_POST['first_name'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="last_name" value="<?php  echo  $_POST['last_name'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="product_name" value="<?php  echo $_POST['product_name']; ?>" class="dhpay-radio" />
            <input type="hidden" name="product_price" value="<?php  echo $_POST['product_price']; ?>" class="dhpay-radio" />
            <input type="hidden" name="product_quantity" value="<?php  echo $_POST['product_quantity']; ?>" class="dhpay-radio" />
            <input type="hidden" name="address_line" value="<?php echo $_POST['address_line']; ?>" class="dhpay-radio" />
            <input type="hidden" name="city" value="<?php  echo  $_POST['city'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="country" value="<?php  echo  $_POST['country'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="state" value="<?php  echo  $_POST['state'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="zipcode" value="<?php  echo  $_POST['zipcode'];  ?>" class="dhpay-radio" />
            <input type="hidden" name="hash" value="<?php echo $hash; ?>" class="dhpay-radio" />
            <ul class="pay-list">
                <li>
                    <input type="radio" name="payname" value="dhpay" class="dhpay-radio" checked="checked" /> <span class="pay-type">             <div class="pay-logo"></div>             <span class="visa"></span> <span class="master"></span> <span class="ae"></span> </span>
                </li>
                <li>
                    <input type="radio" name="payname" value="paypal" disabled class="dhpay-radio" /> <span class="paypal"></span> </li>
                <li>
                    <input type="radio" name="payname" value="letian" disabled class="dhpay-radio" /> <span class="letian"></span> </li>
            </ul>
            <input type="button" value="返回上一步" onclick="javascript:history.go(-1);" />
            <input type="submit" value="下一步:  去付款" /> </form>
    </div>
</body>

</html>


```

#### callback 返回信息解析
```php
<?php
/**
* dhpay php demo *
* callback method
*
* author: lijie <lijierd@dhgate.com> * date: 2014.09.04
*/
/**
* 回调方法会接受如下参数 *
* mechant_id: 商户号,对应商户提交的商户号
* invoice_id: 交易号,对应商户提交的交易号
* order_no: 订单号,对应商户提交的订单号
* currency: 交易币种
* amount: 交易金额
* status: 交易状态(00 处理中, 01 成功, 02 失败)
* failure_reason: 如果交易状态为“失败”,则会有相应的失败原因
* trans_date: 交易日期
* trans_time: 交易时间
* hash: 返回参数的签名 */
echo <<<EOF
<div style="padding-left:305px">
<p style="width:500px;font-size:18px;margin:20px
0 20px 0;padding-bottom:10px;border-bottom:1px solid gray;">DHPay Demo -- PHP 版本</p>
<p>DHPay 支付状态</p> EOF;
switch ($_GET['status']) { case '00':
echo '处理中';
break; case '01':
echo '成功付款';
break; case '02':
echo '付款失败, 失败原因为: ' . $_GET['failure_reason'];
break; default:
break; }
echo '</div>';
```


