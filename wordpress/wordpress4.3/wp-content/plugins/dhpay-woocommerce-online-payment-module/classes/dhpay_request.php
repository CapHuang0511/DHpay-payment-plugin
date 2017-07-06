<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Generates requests to send to Dhpay
 */
class Dhpay_Request {

	/**
	 * Stores line items to send to Dhpay
	 * @var array
	 */
	protected $line_items = array();

	/**
	 * Endpoint for requests from Dhpay
	 * @var string
	 */
	protected $notify_url;

    protected $order;

	/**
	 * Constructor
	 * @param WC_Gateway_Dhpay $gateway
	 */
	public function __construct( ) {
		$this->options    = get_option('woocommerce_DHPAY_settings');
		$this->notify_url = $this->options['postbackurl'];
        $this->order = wc_get_order($_GET['order_id']);

	}

	/**
	 * Get the Dhpay request URL for an order
	 * @return string
	 */
	public function get_request_url() {
        $mode = $this->options['test_mode'];
        $sandbox = ($mode== 'yes') ? true : false;
        $checout_method = $this->options['checkout_method'];
        $url = 'https://www.dhpay.com/merchant/web/cashier';

        if ($checout_method == 'Iframe'){
            $url = 'https://www.dhpay.com/merchant/web/cashier/iframe/before';
        }

        if ($sandbox && $checout_method != 'Iframe'){
            $url .= '?env=dhpaysandbox';
        }
        return $url;
	}

	/**
	 * Get Dhpay Args for passing to PP
	 *
	 * @param WC_Order $order
	 * @return array
	 */
	public function get_dhpay_args( ) {

        if (! $this->order)return array();
        $order = $this->order;

		$order_product = $order->get_items();
        $product = array_shift($order_product);
        $params = array(
            'merchant_id' => $this->options['merchantid'],
            'invoice_id' => $order->get_order_number(),
            'order_no' => $order->id,//
            'currency' => $order->order_currency,
            'amount' => sprintf('%.2f', $order->order_total),
            'buyer_email' => $order->billing_email,
            'shipping_country' => $order->shipping_country,
            'first_name' => $order->billing_first_name,
            'last_name' => $order->billing_last_name,
            'country' => $order->billing_country,
            'state' => $order->billing_state,
            'city' => $order->billing_city,
            'address_line' => $order->billing_address_1 . "\n" . $order->billing_address_2,
            'zipcode' => $order->billing_postcode,
            'product_name' => $product['name'],
            'product_price' => sprintf("%.2f", $product['line_subtotal']*1.0/$product['qty']),
            'product_quantity' => $product['qty'],
            'return_url' => $this->notify_url,
            'remark' => '',
            'hash' => '',

            'shipping_first_name'=> $order->shipping_first_name,
            'shipping_last_name'=>$order->shipping_last_name,
            'shipping_state'=>$order->shipping_state,
            'shipping_city'=>$order->shipping_city,
            'shipping_address_line'=>$order->shipping_address_1 . "\n" . $order->shipping_address_2,
            'shipping_zipcode'=>$order->shipping_postcode,
            'shipping_email'=>'',
            'shipping_phone'=>$order->billing_phone,
            'layout'=>$this->options['style_layout'],
            'title_style'=>$this->options['style_title'],
            'button_style'=>$this->options['style_button'],
            'body_style'=>$this->options['style_body']);

        $params['hash'] = $this->request_hash($params, $this->options['secretcode' ]);
        $mode = $this->options['test_mode'];
        $sandbox = ($mode== 'yes') ? true : false;
        if ($sandbox){
            $params['env'] = 'dhpaysandbox';
        }
        return apply_filters( 'woocommerce_dhpay_args', $params);
	}

	/**
	 * Get order item names as a string
	 * @param  WC_Order $order
	 * @return string
	 */
	protected function get_order_item_names( $order ) {
		$item_names = array();

		foreach ( $order->get_items() as $item ) {
			$item_names[] = $item['name'] . ' x ' . $item['qty'];
		}

		return implode( ', ', $item_names );
	}

	/**
	 * Get order item names as a string
	 * @param  WC_Order $order
	 * @param  array $item
	 * @return string
	 */
	protected function get_order_item_name( $order, $item ) {
		$item_name = $item['name'];
		$item_meta = new WC_Order_Item_Meta( $item );

		if ( $meta = $item_meta->display( true, true ) ) {
			$item_name .= ' ( ' . $meta . ' )';
		}

		return $item_name;
	}

	/**
	 * Return all line items
	 */
	protected function get_line_items() {
		return $this->line_items;
	}

	/**
	 * Remove all line items
	 */
	protected function delete_line_items() {
		$this->line_items = array();
	}

	/**
	 * Get line items to send to dhpay
	 *
	 * @param  WC_Order $order
	 * @return bool
	 */
	protected function prepare_line_items( $order ) {
		$this->delete_line_items();
		$calculated_total = 0;

		// Products
		foreach ( $order->get_items( array( 'line_item', 'fee' ) ) as $item ) {
			if ( 'fee' === $item['type'] ) {
				$item_line_total  = $this->number_format( $item['line_total'], $order );
				$line_item        = $this->add_line_item( $item['name'], 1, $item_line_total );
				$calculated_total += $item_line_total;
			} else {
				$product          = $order->get_product_from_item( $item );
				$item_line_total  = $this->number_format( $order->get_item_subtotal( $item, false ), $order );
				$line_item        = $this->add_line_item( $this->get_order_item_name( $order, $item ), $item['qty'], $item_line_total, $product->get_sku() );
				$calculated_total += $item_line_total * $item['qty'];
			}

			if ( ! $line_item ) {
				return false;
			}
		}

		// Shipping Cost item - dhpay only allows shipping per item, we want to send shipping for the order
		if ( $order->get_total_shipping() > 0 && ! $this->add_line_item( sprintf( __( 'Shipping via %s', 'woocommerce' ), $order->get_shipping_method() ), 1, $this->round( $order->get_total_shipping(), $order ) ) ) {
			return false;
		}

		// Check for mismatched totals
		if ( $this->number_format( $calculated_total + $order->get_total_tax() + $this->round( $order->get_total_shipping(), $order ) - $this->round( $order->get_total_discount(), $order ), $order ) != $this->number_format( $order->get_total(), $order ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Add Dhpay Line Item
	 * @param string  $item_name
	 * @param integer $quantity
	 * @param integer $amount
	 * @param string  $item_number
	 * @return bool successfully added or not
	 */
	protected function add_line_item( $item_name, $quantity = 1, $amount = 0, $item_number = '' ) {
		$index = ( sizeof( $this->line_items ) / 4 ) + 1;

		if ( $amount < 0 || $index > 9 ) {
			return false;
		}

		$this->line_items[ 'item_name_' . $index ]   = html_entity_decode( wc_trim_string( $item_name ? $item_name : __( 'Item', 'woocommerce' ), 127 ), ENT_NOQUOTES, 'UTF-8' );
		$this->line_items[ 'quantity_' . $index ]    = $quantity;
		$this->line_items[ 'amount_' . $index ]      = $amount;
		$this->line_items[ 'item_number_' . $index ] = $item_number;

		return true;
	}

	/**
	 * Get the state to send to dhpay
	 * @param  string $cc
	 * @param  string $state
	 * @return string
	 */
	protected function get_dhpay_state( $cc, $state ) {
		if ( 'US' === $cc ) {
			return $state;
		}

		$states = WC()->countries->get_states( $cc );

		if ( isset( $states[ $state ] ) ) {
			return $states[ $state ];
		}

		return $state;
	}

	/**
	 * Check if currency has decimals
	 *
	 * @param  string $currency
	 *
	 * @return bool
	 */
	protected function currency_has_decimals( $currency ) {
		if ( in_array( $currency, array( 'HUF', 'JPY', 'TWD' ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Round prices
	 *
	 * @param  float|int $price
	 * @param  WC_Order $order
	 *
	 * @return float|int
	 */
	protected function round( $price, $order ) {
		$precision = 2;

		if ( ! $this->currency_has_decimals( $order->get_order_currency() ) ) {
			$precision = 0;
		}

		return round( $price, $precision );
	}

	/**
	 * Format prices
	 *
	 * @param  float|int $price
	 * @param  WC_Order $order
	 *
	 * @return float|int
	 */
	protected function number_format( $price, $order ) {
		$decimals = 2;

		if ( ! $this->currency_has_decimals( $order->get_order_currency() ) ) {
			$decimals = 0;
		}

		return number_format( $price, $decimals, '.', '' );
	}

    protected function request_hash($data, $private_key)
    {
        // 签名的表单字段名
        $hash_src = '';
        $hash_key = array('amount', 'currency', 'invoice_id', 'merchant_id');
        // 按 key 名进行顺序排序
        sort($hash_key);
        foreach ($hash_key as $key) {
            $hash_src .= $data[$key];
        }
        // 密钥放最前面
        $hash_src = $private_key . $hash_src;
        // sha256 算法
        $hash = hash('sha256', $hash_src);

        return $hash;
    }

}
