<?php

/**
 * DHPAY Woocommerce payment module
 *
 * @license   see LICENSE.md
 * @source    https://github.com/dhpay/WooCommerce
 * @copyright Copyright (c) 2015 DHPAY B.V.
 *
 * Plugin Name: DHPAY Payment Module
 * Plugin URI: http://www.dhpay.com/webshop-modules/online-payments-for-wordpress-woocommerce
 * Description: DHPAY Payment Module for WooCommerce
 * Author: DHPAY
 * Author URI: http://www.dhpay.com
 * Version: 2.3.6
 * License: http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE
 */

add_action('plugins_loaded', 'DHPAY_Init');

require(plugin_dir_path( __FILE__ ) . '/api/api/dhpay_api_webservice.php');
require(plugin_dir_path( __FILE__ ) . '/classes/helper.php');
require(plugin_dir_path( __FILE__ ) .'/classes/dhpay_shortcodes.php' );
require(plugin_dir_path( __FILE__ ) .'/classes/dhpay_request.php' );

function DHPAY_Init()
{
    /**
     * Load the Payment Gateway class if WooCommerce did not load it
     */
    if(!class_exists('WC_Payment_Gateway')) return;

    /**
     * Class DHPAY
     */
    class DHPAY extends WC_Payment_Gateway
    {
        /**
         * Constructor
         */
        public function __construct()
        {
            // Enables shortcut link to DHPAY settings on the plugin overview page
            add_filter('plugin_action_links', 'IC_add_action_plugin', 10, 5);

            // Load DHPAY translations
            load_plugin_textdomain('dhpay', false, dirname(plugin_basename(__FILE__)) . '/languages/');

            // Set core gateway settings
            $this->method_title = 'DHPAY';
            $this->id = 'DHPAY';
            $this->title = 'DHPAY';

            $this->version = DHPAY_Helper::getVersion();

            // Create admin configuration form
            $this->initForm();

            // Initialise gateway settings
            $this->init_settings();
            $this->title = $this->settings['dhpay_title'] . '<img height="30" width="120" alt="logo" src="https://www.dhpay.com/merchantaccount/zh_CN/v2/image/download/pay-3.jpg">';

            // Core gateway is for configuration use only and should never be enabled
            $this->enabled  = isset( $this->settings['enabled'] ) && $this->settings['enabled'] == 'yes' ? 'yes' : 'no';

            // Add postback URL to configuration form
            $this->settings['postbackurl'] = add_query_arg('wc-api', 'dhpay_result', home_url('/'));

            // Payment listener/API hook
            add_action('woocommerce_api_dhpay_result', array($this, 'result'));

            // Since we use a class wrapper, our class is called  twice. To prevent double execution we do a check if the gateway is already registered.
            $loaded_gateways = apply_filters('woocommerce_payment_gateways', array());

            if (in_array($this->id, $loaded_gateways))
            {
                return;
            }

            // Add DHPAY as WooCommerce gateway
            add_filter('woocommerce_payment_gateways', array($this, 'addGateway'));

            // Check if on admin page
            if (is_admin())
            {
                // Run install if false - not using install hook to make sure people who upgrade get the correct tables installed (Upgrade function was added later)
                if (!get_option('DHPAY_Installed', false))
                {
                    $this->install();
                }

                add_action('woocommerce_update_options_payment_gateways_DHPAY', array($this, 'process_admin_options'));

                // Ajax callback for getPaymentMethods
                add_action('wp_ajax_ic_getpaymentmethods', array($this, 'getPaymentMethods'));

                // Add scripts
                add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
            }
        }

        public function enqueueScripts()
        {
            // Add files only on DHPAY's configuration page
            if (DHPAY_helper::isDhpayPage($this->id))
            {
                wp_enqueue_script('dhpay', plugin_dir_url( __FILE__ ) . 'assets/js/dhpay.js', array('jquery'), $this->version);
                wp_enqueue_style('dhpay', plugin_dir_url( __FILE__ ) . 'assets/css/dhpay.css', array(), $this->version);

                wp_localize_script('dhpay', 'objectL10n', array(
                    'loading' => __('Loading payment method data...', 'dhpay'),
                    'refresh' => __('Refresh payment methods', 'dhpay')
                ));
            }
        }

        public function ipn()
        {
            global $wpdb;

            $dhpay = Dhpay_Project_Helper::getInstance()->postback();
            $dhpay->setMerchantID(intval($this->settings['merchantid']))->setSecretCode($this->settings['secretcode']);

            if ($dhpay->validate())
            {
                $data = $dhpay->GetPostback();
                $order_id = $dhpay->getOrderID();
                $order = new WC_Order($order_id);

                switch ($dhpay->getStatus())
                {
                    case Dhpay_StatusCode::ERROR:
                        $order->add_order_note($dhpay->getStatus());
                        break;
                    case Dhpay_StatusCode::OPEN:
                        break;
                    case Dhpay_StatusCode::AUTHORIZED:
                        $order->payment_complete();
                        break;
                    case Dhpay_StatusCode::SUCCESS:
                        $order->payment_complete();
                        break;
                    case Dhpay_StatusCode::REFUND:
                        $order->update_status('refunded');
                        $order->add_order_note($dhpay->getStatus());
                        break;
                    case Dhpay_StatusCode::CHARGEBACK:
                        $order->update_status('cancelled');
                        $order->add_order_note($dhpay->getStatus());
                        break;
                }
            }

        }
        public function result()
        {
            if ($_GET['return_type'] == 2){
                $this->ipn();
                echo 'success';exit;
            }

            global $wpdb;

            $dhpay = Dhpay_Project_Helper::getInstance()->result();

            $dhpay->setMerchantID($this->settings['merchantid']);
            $dhpay->setSecretCode($this->settings['secretcode']);

            if ($dhpay->validate())
            {

                $order_id = $dhpay->getOrderID();
                $order = new WC_Order($order_id);

                switch ($dhpay->getStatus())
                {
                    case Dhpay_StatusCode::ERROR:
                        $order->add_order_note('User cancelled order.');
                        // wp_safe_redirect($order->get_cancel_order_url());
                        break;
                    case Dhpay_StatusCode::OPEN:
                        wp_safe_redirect($order->get_checkout_order_received_url());exit;
                        break;
                    case Dhpay_StatusCode::SUCCESS:
                        WC()->cart->empty_cart();
                        $this->ipn();
                        wp_safe_redirect($order->get_checkout_order_received_url());exit;
                        break;
                }
            }
            wp_redirect(get_page_link($this->get_option('payment_failed_page')));exit;
            // exit(__('Postback URL installed successfully.', 'dhpay'));
        }

        public function generate_select_page_html( $key, $data ) {
            $field    = $this->get_field_key( $key );
            $defaults = array(
                'title'             => '',
                'disabled'          => false,
                'class'             => '',
                'css'               => '',
                'placeholder'       => '',
                'type'              => 'text',
                'desc_tip'          => false,
                'description'       => '',
                'custom_attributes' => array(),
                'options'           => array()
            );

            $args = wp_parse_args( $data, $defaults );
            $args['name'] = $args['id'] = esc_attr($field);

            ob_start();
            ?><tr valign="top" class="single_select_page">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title']); ?></label>
                <?php echo $this->get_tooltip_html( $data ); ?>
            </th>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title']); ?></span></legend>
                    <?php str_replace(' id=', " data-placeholder='" . esc_attr__( 'Select a page&hellip;', 'woocommerce' ) .  "' style='" . $args['css'] . "' class='" . $args['class'] . "' id=", wp_dropdown_pages( $args ) ); ?> <?php echo $this->get_description_html( $args ); ?>
                </fieldset>
            </td>
            </tr><?php
            return ob_get_clean();
        }

        public function admin_options()
        {
            global $wpdb;

            if (class_exists('SoapClient') === false)
            {
                // die(DHPAY_Helper::generateAjaxError(__('This plugin requires SOAP to make payments. Please contact your hosting provider to support SOAP.', 'dhpay')));
            }

            ob_start();
            //WC_Admin_Settings::output_fields( $this->form_fields );
            $this->generate_settings_html();
            $settings = ob_get_contents();
            ob_end_clean();

            $paymentMethods = array();

            $variables = array(
                '{image}' => plugins_url('', __FILE__) . '/assets/images/dhpay-header.png',
                '{version}' => $this->version,
                '{manual}' => __('View the manual', 'dhpay'),
                '{website}' => __('Visit the DHPAY website', 'dhpay'),
                '{configuration}' => __('Configuration', 'dhpay'),
                '{payment_methods}' => __('Payment methods', 'dhpay'),
                '{information}' => __('Information', 'dhpay'),
                // Ricardo (7-5-2015): This is a little hack-ish, but who you gonne call?
                '{settings}' => '<h3 class="wc-settings-sub-title">' . __('Merchant configuration', 'dhpay') . '</h3>' . '<p>' . __('Your Merchant ID and Secretcode are available in the DHPAY Merchant Portal. See the Manual for more information.', 'dhpay') . '</p>' . $settings,
                '{missing_methods)' => __('Are there payments methods missing from this payment table? Enable or activate them in the DHPAY Merchant Portal at', 'dhpay'),
                '{refreshButtonValue}' => __('Refresh Payment methods', 'dhpay'),
                '{error}' => '',
                '{list}' => '',
                '{upgrade_notice}' => '',
                '{IC_version}' => __('Module version', 'dhpay'),
                '{WC_version_label}' => __('WC Version', 'woocommerce'),
                '{WC_version}' => WC()->version,
                '{WP_version_label}' => __('WP Version', 'woocommerce'),
                '{WP_version}' => get_bloginfo('version') . ' (' . get_bloginfo('language') . ')',
                '{IC_API}' => __('using DHPAY API', 'dhpay') . ' ' . Dhpay_Project_Helper::getInstance()->getReleaseVersion(),
                '{IC_Support}' => __('Please include this information when you create a support ticket, this way we can help you better', 'dhpay')
            );

            DHPAY_Helper::generateListItems($paymentMethods, $variables);

            if (DHPAY_Helper::isUpgradeNoticeAvailable())
            {
                DHPAY_Helper::generateUpgradeNotice($variables);
            }

            $template = file_get_contents(plugin_dir_path(__FILE__) . 'templates/admin.php');

            foreach ($variables as $key => $value) {
                $template = str_replace($key, $value, $template);
            }

            echo $template;
        }

        public function addGateway($methods)
        {
            $methods[] = 'DHPAY';

            return $methods;
        }

        private function initForm()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'id'=>'enabled',
                    'title' => __('Active', 'dhpay'),
                    'type' => 'checkbox',
                    'label' => ' ',
                    'description' => __('Enable or disable the payment method during the checkout process', 'dhpay'),
                    'desc_tip' => true
                ),
                'postbackurl' => array(
                    'id'=>'postbackurl',
                    'title' => __('Postback URL', 'dhpay'),
                    'type' => 'text',
                    'description' => __('Copy and paste this URL to the Success, Error and Postback section of your DHPAY merchant account page.', 'dhpay'),
                    'desc_tip' => true
                ),
                'merchantid' => array(
                    'id'=>'merchantid',
                    'title' => __('Merchant ID', 'dhpay'),
                    'type' => 'text',
                    'description' => __('Copy the Merchant ID from your DHPAY account.', 'dhpay'),
                    'desc_tip' => true
                ),
                'dhpay_title' => array(
                    'id'=>'dhpay_title',
                    'title' => __('Dhpay Title', 'dhpay'),
                    'type' => 'text',
                    'description' => __('Payment description.', 'dhpay'),
                    'desc_tip' => true
                ),
                'secretcode' => array(
                    'id'=>'secretcode',
                    'title' => __('Secretcode', 'dhpay'),
                    'type' => 'text',
                    'description' => __('Copy the Secret Code from your DHPAY account.', 'dhpay'),
                    'desc_tip' => true
                ),
                'test_mode' => array(
                    'id'=>'test_mode',
                    'title' => __('Test Mode', 'dhpay'),
                    'type' => 'checkbox',
                    'description' => __('test mode.', 'dhpay'),
                    'desc_tip' => true
                ),
                'checkout_method' => array(
                    'id'=>'checkout_method',
                    'title' => __('Checkout Method', 'dhpay'),
                    'type' => 'select',
                    'options'=> array('Redirect'=>'Redirect', 'Iframe'=>'Iframe'),
                    'description' => __('Checkout Method', 'dhpay'),
                    'desc_tip' => true
                ),
                'checkout_page' => array(
                    'id'       => 'checkout_page',
                    'title'    => __( 'Checkout Page', 'dhpay' ),
                    'desc'     => 'Checkout Page',
                    'default'  => '',
                    'class'    => 'wc-enhanced-select-nostd',
                    'css'      => 'min-width:300px;',
                    'type'     => 'select_page',
                    'desc_tip' => true,
                    'selected' => absint( self::get_option( 'checkout_page' ) )
                ),
                'payment_failed_page' => array(
                    'id'       => 'payment_failed_page',
                    'title'    => __( 'Payment Failed Page', 'dhpay' ),
                    'desc'     => 'Payment Failed Page',
                    'default'  => '',
                    'class'    => 'wc-enhanced-select-nostd',
                    'css'      => 'min-width:300px;',
                    'type'     => 'select_page',
                    'desc_tip' => true,
                    'selected' => absint( self::get_option( 'payment_failed_page' ) )
                ),
                'steptwo' => array(
                    'title' => __('Optional configuration', 'dhpay'),
                    'type' => 'title'
                ),
                'style_layout' => array(
                    'id'=>'style_layout',
                    'title' => __('Layout Style', 'dhpay'),
                    'type' => 'select',
                    'options'=> array('vertical'=>'Vertical', 'horizontal'=>'Horizontal'),
                    'description' => __('layout style', 'dhpay'),
                    'desc_tip' => true
                ),
                'style_body' => array(
                    'id'=>'style_body',
                    'title' => __('Body Style', 'dhpay'),
                    'type' => 'text',
                    'description' => __('body style', 'dhpay'),
                    'desc_tip' => true
                ),
                'style_title' => array(
                    'id'=>'style_title',
                    'title' => __('Title Style', 'dhpay'),
                    'type' => 'text',
                    'description' => __('body title', 'dhpay'),
                    'desc_tip' => true
                ),
                'style_button' => array(
                    'id'=>'style_button',
                    'title' => __('Button Style', 'dhpay'),
                    'type' => 'text',
                    'description' => __('button style', 'dhpay'),
                    'desc_tip' => true
                ),
            );
        }

        private function install()
        {
            global $wpdb;

            // Add custom status (To prevent user cancel - or re-pay on standard status pending)
            wp_insert_term(__('Awaiting Payment', 'dhpay'), 'shop_order_status');

            update_option('DHPAY_Installed', true);
        }

        public function process_payment($order_id){
            $url = get_page_link($this->get_option('checkout_page'));
            if (!strrpos($url, '?')){
                $url .= '?';
            }

            if (substr($url, -1, 1) != '&' and substr($url, -1, 1) != '?'){
                $url .= '&';
            }
            $url .= 'order_id=' . $order_id;
            return array(
                'result'   => 'success',
                'redirect' => $url
            );
        }

        public function init_hooks(){

            add_action( 'init', array( 'Dhpay_Shortcodes', 'init' ));
        }
    }

    function IC_add_action_plugin($actions, $plugin_file)
    {
        static $plugin;

        if (!isset($plugin))
        {
            $plugin = plugin_basename(__FILE__);
        }

        if ($plugin == $plugin_file)
        {
            $actions = array_merge(array('settings' => '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=dhpay') . '">' . __('Settings', 'General') . '</a>'), $actions);
        }

        return $actions;
    }


    $dhpay = new DHPAY();
    $dhpay->init_hooks();

}
