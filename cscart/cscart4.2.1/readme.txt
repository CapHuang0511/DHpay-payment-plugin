1、将文件覆盖到cscart目录下
2、进入cascara后台，点击右上角【Add-ons】->【Manage add-ons】
   选择【Dhpay payments】点击install，Active安装成功后，点击【settings】配置

3、在后台点击右上角【Administration】->【Payment methods】
   点击右边加号，添加dhpay支付方式。
   name             : dhpay
   processor        : 选择Dhpay
   template         : dhpay.tpl
   Payment category : 选择Credit cart
   icon Url	     : https://www.dhpay.com/merchantaccount/zh_CN/v2/image/download/pay-btn-1.png
   保存后，安装完成。

4、dhpay配置的ion地址： http://域名/index.php?dispatch=payment_notification.dhpay_ipn&payment=dhpay
   

使用时的注意事项：
 选择redirect方式时，一定要在payment methods--dhpay中将iframe mode设置为disable；
 选择iframe方式时，一定要在payment methods--dhpay将iframe mode设置为enable 。