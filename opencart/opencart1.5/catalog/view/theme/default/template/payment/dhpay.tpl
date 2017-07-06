<?php if (DHPAY_PAYMENT_METHOD == 'iframe') { ?>
    <table class="table table-bordered" width="95%">
        <tr>
            <td height="350"><?php echo Dhpay_Front_Core::request($dhpay_params);?></td>
        </tr>
    </table>
    <script type="text/javascript">dhpay_iframe_submit();</script>
<?php } else { ?>
  <div class="buttons">
    <div class="right">
      <?php
        $dhpay_button = '<input type="submit" class="btn btn-primary" data-loading-text="Loading..." id="button-payment-dhpay" value="' . $button_confirm . '"/>';
        Dhpay_Front_Core::button($dhpay_button);
        echo Dhpay_Front_Core::request($dhpay_params);
      ?>
    </div>
  </div>
<?php } ?>