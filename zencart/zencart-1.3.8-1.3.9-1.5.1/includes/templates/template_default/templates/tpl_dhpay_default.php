<div id="aboutUs" style="height: 300px;">
    <h1 id="aboutUsHeading"><?php echo HEADING_TITLE; ?></h1>
    <div id="aboutUsMainContent" style="height: 250px;">

       <?php
       require_once DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/dhpay/dhpay_front_core.php';
       echo Dhpay_Front_Core::iframe($_POST, '95%', '120%');
       ?>
    </div>
    <script type="text/javascript">dhpay_iframe_submit();</script>
</div>
