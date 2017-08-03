app、skin两个目录上传到magento发布目录下
如果网站开启编译、需要关闭编译状态，然后再上传目录
编译状态是否开启，请检查
system->tools->Compilation
Compiler status应该是Disabled

DHpay的商户号需要绑定集成网站

http://www.xxx.com/index.php/dhpay/payment/notify

后台系统->配置->payment methods:
91Magento Dhpay Payment

Enabled		是否启用
Title		标题
MerchantID	商户id,从dhpay支付接口处获取
Key		密钥,从dhpay支付接口处获取
Gateway		网关提交接口,填写https://www.dhpay.com/merchant/web/cashier
NotifyUrl	和DHpay的商户号需要绑定集成网站地址相同http://www.xxx.com/index.php/dhpay/payment/return
New order status	新订单状态 选择pending
Order status when payment success by dhpay:订单支付成功状态 选择processing
Describe	描述出现在checkout onepage中的payment method下的描述,支持html
Redirect Message 支付跳转时的描述信息,支持html You will be redirected to the Dhpay website in a few seconds. 

