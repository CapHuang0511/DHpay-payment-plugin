1、编辑config/xml/tab_modules_list.xml
在<tab class_name="AdminPayment" display_type="default_list">下
追加<module name="dhpay" position="42"/>

2、ipn地址：http://域名/modules/dhpay/payment_ipn.php