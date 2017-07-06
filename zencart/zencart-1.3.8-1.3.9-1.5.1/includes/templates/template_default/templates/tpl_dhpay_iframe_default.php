<div id="aboutUs" style="height: 300px;">
    <h1 id="aboutUsHeading"><?php echo HEADING_TITLE; ?></h1>
    <div id="aboutUsMainContent" style="height: 250px;">

       <?php
       require(DIR_WS_CLASSES . 'payment.php');
       $payment = new payment('dhpay');
       $params = array('product_name', 'product_quantity', 'product_price',
                        'merchant_id', 'invoice_id', 'order_no', 'currency', 'amount',
                        'return_url', 'remark', 'first_name', 'last_name', 'address_line',
                        'country', 'state', 'city', 'buyer_email', 'zipcode', 'shipping_country', 'hash',
                        'body_style', 'title_style', 'language', 'layout', 'button_style',
                        'shipping_first_name', 'shipping_last_name', 'shipping_state', 'shipping_phone',
                        'shipping_city', 'shipping_address_line', 'shipping_zipcode', 'shipping_email');
       $queryArr = array();
       foreach($params as $p){
           $queryArr[] = $p . '=' . urlencode($_POST[$p]);
       }

       //var_dump('&currency');
       $querystr = join('&', $queryArr);

       //echo $querystr;exit;
       ?>
        <iframe frameborder="0" width="95%" height="95%" name="iframePay" scrolling="no" src="<?php echo $payment->get_checkout_confirm_form_replacement(), $querystr; ?>"></iframe>
    </div>
    <div><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>
</div>
