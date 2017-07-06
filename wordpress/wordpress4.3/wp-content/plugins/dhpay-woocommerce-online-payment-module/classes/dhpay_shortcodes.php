<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Shortcodes class.
 *
 * @class 		WC_Shortcodes
 * @version		2.1.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class Dhpay_Shortcodes {
    static $options = array();
	/**
	 * Init shortcodes
	 */
	public static function init() {
        self::$options = get_option('woocommerce_DHPAY_settings');
		$shortcodes = array(
			'dhpay_checkout' => __CLASS__ . '::checkout',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}

	}

	/**
	 * Shortcode Wrapper
	 *
	 * @param string[] $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'dhpay',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		return ob_get_clean();
	}

	/**
	 * Checkout page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function checkout( $atts ) {
        $request = new Dhpay_Request();
        $params = $request->get_dhpay_args();
        $target = '_self';
        $method = 'POST';
        $html = array();
        if (self::$options['checkout_method'] == 'Iframe'){
            $method = 'GET' ;
            $target = 'dhpay_payment_iframe';
            $html[] = '<iframe frameborder="0" width="95%" height="450" scrolling="no" name="dhpay_payment_iframe"></iframe>';
        }
        $html[] = '<form action="' . $request->get_request_url() . '" method="' . $method . '" id="dhpay_payment_form" target="' . $target . '">';
        foreach ($params as $name => $val) {
            $html[] = '<input type="hidden" name="' . $name . '" value="' . $val . '" />';
        }
        
        $html[] = '</form>';
        $html[] = '<script type="text/javascript">function dhpay_iframe_submit(){document.getElementById("dhpay_payment_form").submit()}dhpay_iframe_submit();</script>';
        echo join('', $html);
	}
}
