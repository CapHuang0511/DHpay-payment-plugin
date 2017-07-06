<?php
require_once DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/dhpay/dhpay_front_core.php';
class dhpay
{
    var $code, $title, $description, $enabled, $sort_order, $order_id;
    var $order_pending_status = 1;
    var $order_status = DEFAULT_ORDERS_STATUS_ID;

    function dhpay()
    {
        global $order;
        $this->code = "dhpay";
        $this->title = MODULE_PAYMENT_DHPAY_TEXT_ADMIN_TITLE;
        $this->description = '';
        $this->sort_order = DHPAY_SORT_ORDER;
        $this->enabled = Dhpay_Front_Core::show($order->info['currency'], $_SESSION['language']);
        if (DHPAY_PAYMENT_METHOD == 'Iframe'){
            $this->form_action_url = HTTP_SERVER . '/dhpay_page.php?main_page=dhpay';
        }else{
            $this->form_action_url = Dhpay_Front_Core::getFormUrl();
        }


        if ((int)DHPAY_ORDER_STATUS_ID > 0) {
            $this->order_status = DHPAY_ORDER_STATUS_ID;
        }

    }

    /**
     * 管理后台验证插件是否被安装
     * @return int
     */
    function check()
    {
        global $db;
        if (!isset($this->_check)) {
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DHPAY_STATUS'");
            $this->_check = $check_query->RecordCount();
        }
        return $this->_check;
    }

    /**
     * 前台支付验证是否可以显示支付方式
     * @return array|bool
     */
    function selection()
    {
        if (! Dhpay_Front_Core::show($_SESSION['currency'], $_SESSION['language'])) {
            return false;
        }
        return array(
            'id' => $this->code,
            'module' => MODULE_PAYMENT_DHPAY_TEXT_CATALOG_LOGO,
            'icon' => MODULE_PAYMENT_DHPAY_TEXT_CATALOG_LOGO,
            'info' => MODULE_PAYMENT_DHPAY_BOX_DESCRIPTION
        );
    }

    function process_button()
    {
        global $db, $order, $currencies;
        require_once(DIR_WS_CLASSES . 'order.php');
        //force zen cart to load existing order without creating dumplicate order
        if (isset($_SESSION['order_id']) && ($_SESSION['cart']->cartID == $_SESSION['old_cart_id']) && ($_SESSION['old_cur'] == $_SESSION['currency'])) {
            $order_id = $_SESSION['order_id'];
        } else {
            if (isset($_SESSION['order_id'])) {
                $order_id = $_SESSION['order_id'];
                $db->Execute('delete from ' . TABLE_ORDERS . ' where orders_id = "' . (int)$order_id . '"');
                $db->Execute('delete from ' . TABLE_ORDERS_TOTAL . ' where orders_id = "' . (int)$order_id . '"');
                $db->Execute('delete from ' . TABLE_ORDERS_STATUS_HISTORY . ' where orders_id = "' . (int)$order_id . '"');
                $db->Execute('delete from ' . TABLE_ORDERS_PRODUCTS . ' where orders_id = "' . (int)$order_id . '"');
                $db->Execute('delete from ' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . ' where orders_id = "' . (int)$order_id . '"');
                $db->Execute('delete from ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' where orders_id = "' . (int)$order_id . '"');
            }
            $order = new order();
            $order->info['order_status'] = $this->order_status;//init status,pending
            require_once(DIR_WS_CLASSES . 'order_total.php');
            $order_total_modules = new order_total();
            $order_totals = $order_total_modules->process();
            $order_id = $order->create($order_totals);
            $order->create_add_products($order_id, 2);
            $_SESSION['order_id'] = $order_id;
            $_SESSION['old_cart_id'] = $_SESSION['cart']->cartID;//if customer add or remove item,update qty
            $_SESSION['old_cur'] = $_SESSION['currency']; //if the customer swich the currency
        } // generate order block end, to be compatible with previous version,we do dump data to DHPAY talbe(but not manditory)

        date_default_timezone_set(date_default_timezone_get());

        $datestr = date('YmdHis');
        $currency = $_SESSION['currency'];

        //due to zen cart bug,we should not do currency exchange with zen cart build in currency class,keep in mind
        $amount = zen_round($order->info['total'] * $currencies->currencies[$currency]['value'], $currencies->currencies[$currency]['decimal_places']);
        $strServerUrl = zen_href_link('dhpay.php', 'dh_rt=real_time', 'SSL', false, false, true);

        $billfirstname = trim($order->billing['firstname']);
        $billlastname = trim($order->billing['lastname']);
        $billaddress = trim($order->billing['street_address']) . "\n" . $order->billing['suburb'];
        $billcountry = trim($order->billing['country']['iso_code_2']);
        $billstate = trim($order->billing['state']);
        if ($billcountry == 'US' || $billcountry == 'CA') {
            $billstate = $this->getTwoStateISOByFullname($billstate);
        }

        $billcity = trim($order->billing['city']);
        $billemail = trim($order->customer['email_address']);
        $billphone = trim($order->customer['telephone']);
        $billpost = trim($order->billing['postcode']);
        $deliverycountry = trim($order->delivery['country']['iso_code_2']);

        $product_name = $product_quantity = $product_price = '';
        for ($i = 0; $i < sizeof($order->products) && $i <= 50; $i++) {
            $pname = $order->products[$i]["name"];
            if ($pname == '') {
                $pname = 'Order ' . $order_id;
                break;
            }
            $price_unit = zen_round($order->products[$i]['price'] * $currencies->currencies[$currency]['value'], $currencies->currencies[$currency]['decimal_places']);
            if ($pname == "" || $pname == null) $pname = 'product' . $i;
            $product_name = $pname;
            $product_quantity = $order->products[$i]["qty"];
            $product_price = $price_unit;
            break;
        }
        $remark1 = $order_id;

        $deliveryfirstname = trim($order->delivery['firstname']);
        $deliverylastname = trim($order->delivery['lastname']);

        $deliveryaddress = trim($order->delivery['street_address']). "\n" . $order->delivery['suburb'];
        $deliverycountry = trim($order->delivery['country']['iso_code_2']);
        $deliverystate = trim($order->delivery['state']);

        if ($deliverycountry == 'US' || $deliverycountry == 'CA') {
            $deliverystate = $this->getTwoStateISOByFullname($deliverystate);
        }

        $deliverycity = trim($order->delivery['city']);
        $deliveryemail = trim($order->customer['email_address']);
        $deliveryphone = trim($order->customer['telephone']);
        $deliverypost = trim($order->delivery['postcode']);


        $data = array(
            'product_name'=>$product_name,
            'product_quantity'=>$product_quantity,
            'product_price'=>$price_unit,
            'invoice_id'=>$order_id,
            'order_no'=>$order_id,
            'currency'=>$currency,
            'amount'=>$amount,
            'return_url'=>$strServerUrl,
            'remark'=>'',
            'first_name'=>$billfirstname,
            'last_name'=>$billlastname,
            'address_line'=>$billaddress,
            'country'=>$billcountry,
            'state'=>$billstate,
            'city'=>$billcity,
            'buyer_email'=>$billemail,
            'zipcode'=>$billpost,
            'shipping_country'=>$deliverycountry,
            'shipping_first_name'=>$deliveryfirstname,
            'shipping_last_name'=>$deliverylastname,
            'shipping_state'=>$deliverystate,
            'shipping_phone'=>$deliveryphone,
            'shipping_city'=>$deliverycity,
            'shipping_address_line'=>$deliveryaddress,
            'shipping_zipcode'=>$deliverypost,
            'shipping_email'=>$deliveryemail
        );
        $data = Dhpay_Front_Core::data($data);


        foreach($data as $name=>$val){
            $process_button_string[] = '<input type="hidden" name="' . $name . '" value="' . $val . '" />';
        }
        return join('', $process_button_string);
    }

    function before_process()
    {
        //deprecated as we produce customer order before customer leave our store
        die();//george fix,no need method,should be clear
    }

    function after_process()
    {
        global $insert_id, $db;
        $sql_data_array = array(
            'orders_id' => (int)$insert_id,
            'orders_status_id' => $this->order_status,
            'date_added' => 'now()',
            'comments' => 'DHPAY OrderID:' . $this->order_id
        );

        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
        $_SESSION['order_created'] = '';
        return true;
    }

    function output_error()
    {
        return false;
    }

    function javascript_validation()
    {
        return false;
    }

    function pre_confirmation_check()
    {
        return false;
    }

    function confirmation()
    {
        return true;
    }

    function getTwoStateISOByFullname($fullname)
    {
        $stateArray = array(
            'Alabama' => 'AL', 'Alaska' => 'AK', 'American Samoa' => 'AS', 'Arizona' => 'AZ',
            'Arkansas' => 'AR', 'Armed Forces Africa' => 'AF', 'Armed Forces Americas' => 'AA', 'Armed Forces Canada' => 'AC',
            'Armed Forces Europe' => 'AE', 'Armed Forces Middle East' => 'AM', 'Armed Forces Pacific' => 'AP', 'California' => 'CA',
            'Colorado' => 'CO', 'Connecticut' => 'CT', 'Delaware' => 'DE', 'District of Columbia' => 'DC',
            'Federated States Of Micronesia' => 'FM', 'Florida' => 'FL', 'Georgia' => 'GA', 'Guam' => 'GU',
            'Hawaii' => 'HI', 'Idaho' => 'ID', 'Illinois' => 'IL', 'Indiana' => 'IN',
            'Iowa' => 'IA', 'Kansas' => 'KS', 'Kentucky' => 'KY', 'Louisiana' => 'LA',
            'Maine' => 'ME', 'Marshall Islands' => 'MH', 'Maryland' => 'MD', 'Massachusetts' => 'MA',
            'Michigan' => 'MI', 'Minnesota' => 'MN', 'Mississippi' => 'MS', 'Missouri' => 'MO',
            'Montana' => 'MT', 'Nebraska' => 'NE', 'Nevada' => 'NV', 'New Hampshire' => 'NH',
            'New Jersey' => 'NJ', 'New Mexico' => 'NM', 'New York' => 'NY', 'North Carolina' => 'NC',
            'North Dakota' => 'ND', 'Northern Mariana Islands' => 'MP', 'Ohio' => 'OH', 'Oklahoma' => 'OK',
            'Oregon' => 'OR', 'Palau' => 'PW', 'Pennsylvania' => 'PA', 'Puerto Rico' => 'PR',
            'Rhode Island' => 'RI', 'South Carolina' => 'SC', 'South Dakota' => 'SD', 'Tennessee' => 'TN',
            'Texas' => 'TX', 'Utah' => 'UT', 'Vermont' => 'VT', 'Virgin Islands' => 'VI',
            'Virginia' => 'VA', 'Washington' => 'WA', 'West Virginia' => 'WV', 'Wisconsin' => 'WI',
            'Wyoming' => 'WY',
            'Alberta' => 'AB', 'British Columbia' => 'BC', 'Manitoba' => 'MB', 'New Brunswick' => 'NB',
            'Newfoundland and Labrador' => 'NL', 'Northwest Territories' => 'NT', 'Nova Scotia' => 'NS', 'Nunavut' => 'NU',
            'Ontario' => 'ON', 'Prince Edward Island' => 'PE', 'Quebec' => 'QC', 'Saskatchewan' => 'SK',
            'Yukon Territory' => 'YT');

        return $stateArray[ucwords(strtolower($fullname))];

    }

    function install()
    {
        $this->remove();
        global $db, $language, $module_type;
        if (!defined('MODULE_PAYMENT_DHPAY_TEXT_CONFIG_1_1')) {
            $lang_file = DIR_FS_CATALOG_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $this->code . '.php';
            if (file_exists($lang_file)) {
                include($lang_file);
            } else { //load default lang file
                include(DIR_FS_CATALOG_LANGUAGES . 'english' . '/modules/' . $module_type . '/' . $this->code . '.php');
            }
        }
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_1_1 . "', 'DHPAY_STATUS', 'Active', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_1_2 . "', '9', '1', 'zen_cfg_select_option(array(\'Active\', \'Disable\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_2_1 . "', 'DHPAY_MERCHANT_ID', '', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_2_2 . "', '9', '2', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_3_1 . "', 'DHPAY_PRIVATE_KEY', '', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_3_2 . "', '9', '3', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_5_1 . "', 'DHPAY_ORDER_STATUS_ID', '1', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_5_2 . "', '9', '5', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_6_1 . "', 'DHPAY_ORDER_STATUS_PROCESSING_ID', '2', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_6_2 . "', '9', '6', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_7_1 . "', 'DHPAY_ORDER_STATUS_FAIL_ID', '2', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_7_2 . "', '9', '7', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_8_1 . "', 'DHPAY_SORT_ORDER', '0', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_8_2 . "', '9', '8', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_9_1 . "', 'DHPAY_MODE', 'Live', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_9_2 . "', '9', '9', 'zen_cfg_select_option(array(\'Live\', \'Test\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_13_1 . "', 'DHPAY_PAYMENT_METHOD', 'Redirect', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_13_2 . "', '9', '11', 'zen_cfg_select_option(array(\'Redirect\', \'Iframe\'), ', now())");

        /*'MODULE_PAYMENT_DHPAY_STYLE_BODY',
            'MODULE_PAYMENT_DHPAY_STYLE_TITLE',
            'MODULE_PAYMENT_DHPAY_STYLE_BUTTON',
            'MODULE_PAYMENT_DHPAY_STYLE_LAYOUT'*/
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_14_1 . "', 'DHPAY_STYLE_LAYOUT', '', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_14_2 . "', '9', '12', 'zen_cfg_select_option(array(\'Vertical\', \'Horizontal\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_15_1 . "', 'DHPAY_STYLE_BODY', '', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_15_2 . "', '9', '13', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_16_1 . "', 'DHPAY_STYLE_TITLE', '', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_16_2 . "', '9', '14', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_17_1 . "', 'DHPAY_STYLE_BUTTON', '', '" . MODULE_PAYMENT_DHPAY_TEXT_CONFIG_17_2 . "', '9', '15', now())");
    }

    function keys()
    {
        return array(
            'DHPAY_STATUS',
            'DHPAY_MERCHANT_ID',
            'DHPAY_PRIVATE_KEY',
            'DHPAY_MODE',
            'DHPAY_PAYMENT_METHOD',
            'DHPAY_STATUS',
            'DHPAY_ORDER_STATUS_ID',
            'DHPAY_ORDER_STATUS_FAIL_ID',
            'DHPAY_ORDER_STATUS_PROCESSING_ID',
            'DHPAY_STYLE_LAYOUT',
            'DHPAY_STYLE_BODY',
            'DHPAY_STYLE_TITLE',
            'DHPAY_STYLE_BUTTON',
            'DHPAY_SORT_ORDER',
        );
    }

    function remove()
    {
        global $db;
        $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE  'DHPAY_%'");
        $db->Execute("DROP TABLE IF EXISTS DHPAY");
        $db->Execute("DROP TABLE IF EXISTS DHPAY_SESSION");
    }
//end of class
}

?>