# 接入方式
商户的收银台页面嵌入 iframe，iframe 的 src 为 dhpay 的请求地址，并传入必要参数

# 请求地址
https://www.dhpay.com/merchant/web/cashier/iframe/before 

# 参数列表

|参数| 类型| 长度| 必需| 示例| 描述|
| :-: |  :-: |  :-: |  :-: |  :-: |  :-: | 
|参数| 类型| 长度| 必需| 示例| 描述|
|mechant_id |字符| 6-15| 是| 595300 |商户号，由 DHpay 颁发 |
|invoice_id| 字符 |30 |是 |14066905163268948 |交易号，由商户生成，本次交易的唯一标示 以数字 0-9，大小写字母组成 |
|order_no |字符| 50| 是 |1462197131 |订单号 |
|currency| 字符 |3 |是 |USD |币种 |
|amount |数字| 20,2 |是| 20.85 |交易金额，必须包含 2 位小数 |
|local_currency| 字符 |3 |否 |GBP |币种，保值交易需要填写 |
|local_amount |数字| 20,2 |否| 30.15 |本地交易金额，必须包含 2 位小数，保值交易需要填写 |
|fix_rate |数字| 20,6 |否| 1.562300 |本地交易金额保值汇率，必须包含 6 位小数。保值交易需要填写 |
|buyer_email |字符| 30| 否| XXX@126.com |买家邮件地址，接入 AE 卡必须传输 |
|shipping_country |字符 |2| 否 |US | 订单货运国家二维代码，风控要素，见附录国家代码 |
|first_name |字符 |50 |否| Steve  | Billing 名 |
|last_name |字符| 50 |否 |martin |Billing 姓 |
|country |字符| 2 |否| US |Billing 国家，见附录国家代码 |
|state| 字符 |50 |否 |alabama| Billing 州省 |
|city |字符| 50 |否 |newyork| Billing 城市 |
|address_line |字符 |500| 否| Bboolltem st.02 |Billing 详细地址 |
|zipcode |字符| 4-10 | | 1005523 |Billing 邮政编码 |
|product_name| 字符 |500 |是 |Book  | 商品名称 该字段需要上报外管局结汇时需要，请按真实产品名称传递 |
|product_price |数字 |20,2| 是| 10.01 |商品单价，必须包含 2 位小数 |
|product_quantity| 数字| 10| 是| 2 |商品数量 |
|shipping_first_name|字符| 50| 否| Steve |  shipping 名 |
|shipping_last_name |字符| 50| 否| martin |shipping 姓|
|shipping_address_line |字符 |500| 否 |Bboolltem st.02 |shipping 详细地址 |
|shipping_city |字符| 50| 否| newyork |shipping 城市 |
|shipping_state |字符| 50 |否 |alabama |shipping 州省 |
|shipping_zipcode |字符| 4-10 | | 1005523 |shipping 邮政编码 |
|shipping_email |字符 |30| 否 |XXX@126.com |邮箱联系人 |
|shipping_phone |字符 | 2-50 |   否| 02056874321 |电话信息 |
|return_url |字符| 500 |否 |http://www.merchant.com/payback |支付完成返回地址，如果不填则以商户设置为准 |
|remark |字符 |1000 |否 || 备注 |
|hash |字符| 150| 是 || 参数签名，详细签名算法请见附录“商户提交支付请求参数中 hash 字段的签名方法”|

# 示例代码
```html
<iframe src="https://www.dhpay.com/merchant/web/cashier/iframe/before?merchant_ id=1000000&invoice_id=1000000&order_no=1000000&currency=USD&amount=16 .99&buyer_email=buyer@dhpay.com&return_url=http://www.dhpay.com/callback&r emark=remark&shipping_country=US&first_name=firstname&last_name=lastname &product_name=book&product_price=16.99&product_quantity=1&address_line=shi pping_address&city=city&country=US&state=AL&zipcode=10551&hash=1000000 &payname=dhpay" width="100%" height="500px" style="border:none;">
</iframe>
```

# 示例图
![](/assets/QQ20161215-1@2x.png)

# 商户通知

 - 通知返回参数

| 参数 | 类型 | 长度 | 示例 | 描述 |
| :--: | :--: | :--: | :--: | :--: |
|merchant_id| 字符| 30| 595300 |商户号，对应商户提交的商户号 |
|invoice_id| 字符| 50 |14066905163268948 |交易号，对应商户提交的交易号 |
|order_no| 字符| 50| 1462197131| 订单号，对应商户提交的订单号 |
|currency |字符 |3 |USD| 交易币种 |
|amount| 数值 |20,2 |100.01 |交易金额，带两位小数 |
|ref_no| 字符 |50 |20140818161430_152634| DHpay 参考号 |
|is_3d| 字符 |1 |1 | 3d 授权标示 0 不做 3d 1 做 3d |
|status |字符 |2 |01 |交易状态 00 处理中 01 成功 02 失败 |
|failure_reason| 字符 |100 | ？ |如果交易状态为“失败”，则会有相应的失败原因,见失败原因字典 |
|trans_date |字符| 8 |20140722 |交易日期 |
|trans_time| 字符| 6| 182201 |交易时间 |
|hash |字符 |150 | ？ | 返回参数的签名|


 - 通知接收的握手协议

商户接收到交易成功或失败的通知，验证签名之后应该返回给 DHpay 一个   “success”，如果商户没有返回”success”，DHpay 系统会认为商户服务器没有接收到通知，会重新发送通知直到达到发送通知上限 10 次，只要 DHpay 系统受到商户端返回的”success”字样，则认为商户接收通知成功，不会再继续发送。


# 回调页面
回调页面加上如下 js 代码,使整个页面转向支付结果页
```html
<script type="text/javascript">
//依据 DHpay 传回来的结果,构造支付结果页,
parent.window.location.href="paymentresult.php";// 
</script>
```

