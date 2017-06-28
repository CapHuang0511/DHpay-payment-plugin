<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-dhpay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-dhpay" class="form-horizontal">

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="entry_dhpay_title"><?php echo $entry_dhpay_title; ?></label>
            <div class="col-sm-10">
              <input type="text" name="dhpay_title" value="<?php echo $dhpay_title; ?>" placeholder="<?php echo $entry_dhpay_title; ?>" id="entry_dhpay_title" class="form-control"/>
              <?php if ($error_dhpay_title) { ?>
              <div class="text-danger"><?php echo $error_dhpay_title; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="entry_dhpay_merchant_id"><?php echo $entry_dhpay_merchant_id; ?></label>
            <div class="col-sm-10">
              <input type="text" name="dhpay_merchant_id" value="<?php echo $dhpay_merchant_id; ?>" placeholder="<?php echo $entry_dhpay_merchant_id; ?>" id="entry_dhpay_merchant_id" class="form-control"/>
                <?php if ($error_dhpay_merchant_id) { ?>
                <div class="text-danger"><?php echo $error_dhpay_merchant_id; ?></div>
                <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="entry_dhpay_private_key"><?php echo $entry_dhpay_private_key; ?></label>
            <div class="col-sm-10">
              <input type="text" name="dhpay_private_key" value="<?php echo $dhpay_private_key; ?>" placeholder="<?php echo $entry_dhpay_private_key; ?>" id="entry_dhpay_private_key" class="form-control"/>
              <?php if ($error_dhpay_private_key) { ?>
              <div class="text-danger"><?php echo $error_dhpay_private_key; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-dhpay_mode"><?php echo $entry_dhpay_mode; ?></label>
            <div class="col-sm-10">
              <select name="dhpay_mode" id="input-dhpay_mode" class="form-control">
                <?php if ($dhpay_mode == 'Test') { ?>
                <option value="Test" selected="selected"><?php echo $text_test; ?></option>
                <option value="Live"><?php echo $text_live; ?></option>
                <?php } else { ?>
                <option value="Test"><?php echo $text_test; ?></option>
                <option value="Live" selected="selected"><?php echo $text_live; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-dhpay_payment_method"><?php echo $entry_dhpay_payment_method; ?></label>
            <div class="col-sm-10">
              <select name="dhpay_payment_method" id="input-dhpay_payment_method" class="form-control">
                <?php if ($dhpay_payment_method == 'iframe') { ?>
                <option value="iframe" selected="selected"><?php echo $text_iframe ?></option>
                <option value="redirect"><?php echo $text_redirect ?></option>
                <?php } else { ?>
                <option value="iframe"><?php echo $text_iframe ?></option>
                <option value="redirect" selected="selected"><?php echo $text_redirect ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-dhpay_order_status_id"><?php echo $entry_dhpay_order_status_id; ?></label>
            <div class="col-sm-10">
              <select name="dhpay_order_status_id" id="input-dhpay_order_status_id" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $dhpay_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-dhpay_order_status_processing_id"><?php echo $entry_dhpay_order_status_processing_id; ?></label>
            <div class="col-sm-10">
              <select name="dhpay_order_status_processing_id" id="input-dhpay_order_status_processing_id" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $dhpay_order_status_processing_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-dhpay_order_status_fail_id"><?php echo $entry_dhpay_order_status_fail_id; ?></label>
            <div class="col-sm-10">
              <select name="dhpay_order_status_fail_id" id="input-dhpay_order_status_fail_id" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $dhpay_order_status_fail_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-dhpay_sort_order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="dhpay_sort_order" value="<?php echo $dhpay_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-dhpay_sort_order" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-dhpay_status"><?php echo $entry_dhpay_status; ?></label>
            <div class="col-sm-10">
              <select name="dhpay_status" id="input-dhpay_status" class="form-control">
                <?php if ($dhpay_status == 'Active') { ?>
                <option value="Active" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="Disable"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="Active"><?php echo $text_enabled; ?></option>
                <option value="Disable" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_dhpay_style_layout; ?></label>
            <div class="col-sm-10">
                <select name="dhpay_style_layout" id="input-dhpay_style_layout" class="form-control">
                    <?php if (empty($dhpay_style_layout) || $dhpay_style_layout == $text_vertical) { ?>
                    <option value="<?php echo $text_vertical;?>" selected="selected"><?php echo $text_vertical; ?></option>
                    <option value="<?php echo $text_horizontal;?>"><?php echo $text_horizontal; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $text_vertical;?>"><?php echo $text_vertical; ?></option>
                    <option value="<?php echo $text_horizontal;?>" selected="selected"><?php echo $text_horizontal; ?></option>
                    <?php } ?>
                </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_dhpay_style_body; ?></label>
            <div class="col-sm-10">
                <input type="text" name="dhpay_style_body" value="<?php echo $dhpay_style_body ?>" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_dhpay_style_title; ?></label>
            <div class="col-sm-10">
                <input type="text" name="dhpay_style_title" value="<?php echo $dhpay_style_title ?>" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_dhpay_style_button; ?></label>
            <div class="col-sm-10">
                <input type="text" name="dhpay_style_button" value="<?php echo $dhpay_style_button ?>" class="form-control" />
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>