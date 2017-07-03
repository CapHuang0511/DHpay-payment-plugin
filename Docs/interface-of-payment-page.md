## 请求地址

* 生产环境：[https://www.dhpay.com/merchant/web/cashier](https://www.dhpay.com/merchant/web/cashier)
* 沙箱环境：[https://www.dhpay.com/merchant/web/cashier?env=dhpaysandbox](https://www.dhpay.com/merchant/web/cashier?env=dhpaysandbox)

## web 表单

以下 form 表单由商户端实现

```html
<form name="form" method="post" action="https://www.dhpay.com/merchant/web/cashier">
<input type="hidden" name="merchant_id" value="499800">
<!-- 商户编号 -->
<input type="hidden" name="invoice_id" value="14066905163268948">
<!-- 交易号, 商户传入，必须唯一 -->
<input type="hidden" name="order_no" value="564652357887">
<!-- 订单编号，商户传入 -->
<input type="hidden" name="currency" value="USD">
<!-- 币种，由商户传入 -->
<input type="hidden" name="amount" value="458.52">
<!-- 金额，由商户传入 -->
<input type="hidden" name="buyer_email" value="XXX@126.com">
<!-- 买家邮件地址，接入 ae 必须传以作风控 -->
<input type="hidden" name="shipping_country" value="US">
<!—买家货运国家，二维代码 -->
<input type="hidden" name="first_name" value="Steve">
<!—billing 账单 名 -->
<input type="hidden" name="last_ name" value="martin">
<!—billing 账单 姓 -->
<input type="hidden" name="country" value="US">
<!—billing 账单 国家 --><input type="hidden" name="state" value="alabama">
<!—billing 账单 州省 -->
<input type="hidden" name="city" value="newyork">
<!—billing 账单 城市 -->
<input type="hidden" name="address_line" value="Bboolltem st.02">
<!—billing 账单 详细地址 -->
<input type="hidden" name="zipcode" value="100081">
<!—billing 账单 邮政编码 -->
<input type="hidden" name="product_name" value="book">
<!—billing 账单 商品名 -->
<input type="hidden" name="product_price" value="1.00">
<!—billing 账单 商品价格 -->
<input type="hidden" name="product_quantity" value="3">
<!—billing 账单 商品数量 -->
 <input type="hidden" name="shipping_first_name" value="Steve">
 <!—shipping 名 -->
<input type="hidden" name="shipping_last_name" value="martin">
<!—shipping 姓 -->
<input type="hidden" name="country" value="US">
<!—shipping 国家 -->
<input type="hidden" name="shipping_state" value="alabama">
<!—shipping 州省 -->
<input type="hidden" name="shipping_city" value="newyork">
<!—shipping 城市 -->
<input type="hidden" name="shipping_address_line" value="Bboolltem st.02">
<!—shipping 详细地址 -->
<input type="hidden" name="shipping_zipcode" value="100081">
<!—shipping 邮政编码 -->
<input type="hidden" name="shipping_email" value="test@126.com">
<!—shipping email -->
<input type="hidden" name="shipping_phone" value="112300081">
<!—shipping phone -->
<input type="hidden" name="return_url" value="http://merchant.com/callback">
<!-- 支付动作完成后返回到该 url，支付结果以 GET 方式发送 -->
<input type="hidden" name="notify_url" value="http://merchant.com/notify">
<!-- 异步通知地址，如果有回调失败的情况发生，会将支付结果发送到该地址，注意不要设置登陆限制，支付结果以 GET 方式发送 -->
<input type="hidden" name="remark" value="订单相关信息">
<!—备注项 -->
<input type="hidden" name="hash" value="1630dc083d70a1e8af60f49c143a7b95">
<!-- 对传入参数进行 hash 签名，签名算法见附录 -->
<img src="../path/../dhpay 支 付 .gif" width="150" height="30"onclick="document.form.submit(); "/>
</form>
```

## 表单提交参数列表

|           参数            |  类型  |  长度  | 是否必须 |                    示例                    |                    描述                    |
| :---------------------: | :--: | :--: | :--: | :--------------------------------------: | :--------------------------------------: |
|      merchant\_id       |  字符  | 6-15 |  是   |                  595300                  |              商户号，由DHpay 颁发               |
|       invoice\_id       |  字符  |  30  |  是   |            14066905163268948             |    交易号，由商户生成，本次交易的唯一标示以数字0-9，大小写字母组成     |
|        order\_no        |  字符  |  50  |  是   |                1462197131                |                   订单号                    |
|        currency         |  字符  |  3   |  是   |                   USD                    |              商户号，由DHpay 颁发               |
|         amount          |  数字  | 20,2 |  是   |                  20.85                   |            交易金额,必 须包含 2 位 小数             |
|      buyer\_email       |  字符  |  30  |  否   |               xxx@126.com                |           买家邮件地 址,接入 AE 卡必须传输            |
|    shipping\_country    |  字符  |  2   |  否   |                    US                    |        订单货运国 家二维代码, 风控要素,见附录国家代码         |
|       first\_name       |  字符  |  50  |  否   |                  Steve                   |                Billing 名                 |
|       last\_name        |  字符  |  50  |  否   |                  martin                  |                Billing 姓                 |
|         country         |  字符  |  2   |  否   |                    US                    |           Billing 国家, 见附录国家 代码           |
|          state          |  字符  |  50  |  否   |                 alabama                  |                Billing 州省                |
|          city           |  字符  |  50  |  否   |                 newyork                  |                Billing 城市                |
|      address\_line      |  字符  | 500  |  否   |             Bboolltem st.02              |              Billing 详细地 址               |
|         zipcode         |  字符  | 4-10 |  ?   |                 1005523                  |              Billing 邮政编 码               |
|      product\_name      |  字符  | 500  |  是   |                   Book                   | **商品名称 该字段需要 上报外管局 结汇时需要, 请按真实产 品名称传递**  |
|     product\_price      |  字符  | 20,2 |  是   |                  10.01                   |            商品单价,必 须包含 2 位 小数             |
|    product\_quantity    |  数字  |  10  |  是   |                    2                     |                   商品数量                   |
|  shipping\_first\_name  |  字符  |  50  |  否   |                  Steve                   |                shipping 名                |
|  shipping\_last\_name   |  字符  |  50  |  否   |                  martin                  |                shipping 姓                |
| shipping\_address\_line |  字符  | 500  |  否   |             Bboolltem st.02              |              shipping 详细 地址              |
|     shipping\_city      |  字符  |  50  |  否   |                 newyork                  |               shipping 城市                |
|     shipping\_state     |  字符  |  50  |  否   |                 alabama                  |               shipping 州省                |
|    shipping\_zipcode    |  字符  | 4-10 |  ?   |                 1005523                  |              shipping 邮政 编码              |
|     shipping\_email     |  字符  |  30  |  否   |               xxx@126.com                |                  邮箱联系人                   |
|     shipping\_phone     |  字符  | 2-50 |  否   |               02056874321                |                   电话信息                   |
|       return\_url       |  字符  | 500  |  否   | [http://www.merchant.com/payback](http://www.merchant.com/payback) |         支付完成返 回地址,如果 不填则以商 户设置为准         |
|         remark          |  字符  | 1000 |  否   |                    备注                    |                    备注                    |
|       notify\_url       |  字符  | 256  |  否   | [http://www.merchant.com/notify](http://www.merchant.com/notify) |                  异步通知地址                  |
|        buyer\_id        |  字符  |  40  |  否   |                 2455611                  |               用户在商户平台的唯一标识               |
|    auto\_bind\_token    |  数字  |  1   |  否   |                    1                     |        是否绑定token（0：不绑定，1：绑定。默认为1）        |
|          hash           |  字符  | 150  |  是   |              3135sdf63s51wf              | 参数签名,详 细签名算法 请见附录“商 户提交支付 请求参数中 hash 字段的 签名方法” |

## Web 回调返回参数

下列参数将会以 get 形式附加在商户提交的 url 或者商户设置的 url 上。

|       参数        |  类型  |  长度  |                    示例                    |                    描述                    |
| :-------------: | :--: | :--: | :--------------------------------------: | :--------------------------------------: |
|  merchant\_id   |  字符  |  30  |                  595300                  |              商户号，由DHpay 颁发               |
|   invoice\_id   |  字符  |  50  |            14066905163268948             |              交易号,对应商户提交的交易号              |
|    order\_no    |  字符  |  50  |                1462197131                |              订单号,对应商户提交的订单号              |
|    currency     |  字符  |  3   |                   USD                    |                   交易币种                   |
|     amount      |  数值  | 20,2 |                  100.01                  |                   交易金额                   |
|     ref\_no     |  字符  |  50  |          20140818161430\_152634          |                DHpay 参考号                 |
|     status      |  字符  |  2   |                    01                    |         交易状态； 00 处理中 01 成功 02 失败         |
| failure\_reason |  字符  | 100  |                                          |    如果交易状态为“失败”，则会有相应的失败原因,    见失败原因字典    |
|   trans\_date   |  字符  |  8   |                 20140722                 |                   交易日期                   |
|   trans\_time   |  字符  |  6   |                  182201                  |                   交易时间                   |
|   description   |  字符  |      | Declined  due  to  the billing information does not match bank on file! |        Avs 校验结果为地址与邮编都不匹配而导致交易失败         |
|      hash       |  字符  | 150  |                                          | 返回参数的签名，详细签名算法请见附录“商户接收支付响应参数中hash字段的签名方法” |

### 商户通知

#### 通知接收的握手协议

商户接收到交易成功或失败的通知,验证签名之后应该返回给 DHpay 一个  
“success”,如果商户没有返回”success”,DHpay 系统会认为商户服务器没有接收到 通知,会重新发送通知直到达到发送通知上限 10 次,只要 DHpay 系统收到商户端返回 的”success”字样,则认为商户接收通知成功,不会再继续发送。

#### 示例

[http://www.merchant.com/payback?merchant\_id=xxx&invoice\_id=2121212121&](http://www.merchant.com/payback?merchant_id=xxx&invoice_id=2121212121&)

