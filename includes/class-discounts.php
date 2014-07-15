<?php
/**
 * EDD Purchase Rewards - Discounts class
 *
 */
class EDD_Purchase_Rewards_Discounts {

	/**
	 * Retrieve the discount code from the options
	 * This will either be one that all customers will receive, or a unique one-time use discount
	 *
	 * @uses  create_discount()
	 * @since  1.0
	 */
	public function get_discount( $payment_id = '' ) {
		// get our discount from the database
		$discount_code 		= edd_get_option( 'edd_purchase_rewards_discount_code', null );

		// discount will be generated
		$generate_discount 	= edd_get_option( 'edd_purchase_rewards_generate_discount', null );	
		
		// get payment ID
		$payment_id 		= ! empty( $payment_id ) ? $payment_id : edd_purchase_rewards()->functions->get_payment_id();

		// get discount from purchase
		$saved_code 		= get_post_meta( $payment_id, '_edd_purchase_rewards_discount', true );

		
		// code has already been stored
		if ( $saved_code ) {
			$code 			= edd_get_discount_code( $saved_code );
		}
		// code has not been stored yet
		else {
			// retrieve pre-selected discount
			if ( $discount_code && ! $generate_discount ) {
				$code = edd_get_discount_code( $discount_code );
			}
		}

		// return our code
		if ( isset( $code ) ) {
			return $code;
		}

		return false;
	}

	/**
	 * Create a discount
	 * @todo  make filterable, trim input
	 */
	public function create_discount( $email = '', $payment_id = '' ) {

		// get purchase session
		$purchase_session = edd_get_purchase_session();
		// get the key
		$purchase_key = $purchase_session['purchase_key'];
		// get the payment ID from the purchase key
		$payment_id = ! empty( $payment_id ) ? $payment_id : edd_get_purchase_id_by_key( $purchase_key );

		// generate discount is not enabled
		if ( ! edd_get_option( 'edd_purchase_rewards_generate_discount' ) ) {
			// record the currently saved discount code
			edd_insert_payment_note( $payment_id, sprintf( __( 'Discount code given: %s', 'edd-purchase-rewards' ), edd_get_discount_code( edd_get_option( 'edd_purchase_rewards_discount_code' ) ) ) );
			
			// store into payment meta
			update_post_meta( $payment_id, '_edd_purchase_rewards_discount', edd_get_option( 'edd_purchase_rewards_discount_code' ) );
			
			// then return 
			return;
		}
		
		$type 	= edd_get_option( 'edd_purchase_rewards_discount_type' );
		$amount = edd_get_option( 'edd_purchase_rewards_discount_amount' );
		
		$email = ! empty ( $email ) ? $email : edd_purchase_rewards()->functions->get_customer( 'email' );

		$details = apply_filters( 'edd_purchase_rewards_create_discount', array(
			'name' 			=> $email, // customer's email address
			'code' 			=> $this->create_discount_code(),
			'status' 		=> 'active',
			'use_once' 		=> true,
			'max'			=> '1',
			'amount'		=> $amount,
			'type'			=> $type
		));

		// create discount
		$code = edd_store_discount( $details );

		

		// insert payment note
		edd_insert_payment_note( $payment_id, sprintf( __( 'Discount code given: %s', 'edd-purchase-rewards' ),  edd_get_discount_code( $code ) ) );

		// store into payment meta
		update_post_meta( $payment_id, '_edd_purchase_rewards_discount', $code );
	}

	/**
	 * Create a new discount code
	 */
	public function create_discount_code() {
		do {
			$salt = md5( time() . mt_rand() );
			$code = substr( $salt, 0, 15 );
		}

		while ( $this->discount_code_exists( $code ) );

		return $code;
	}

	/**
	 * Check if the code already exists
	 */
	public function discount_code_exists( $code ) {
		global $wpdb;
		$wpdb->get_results( $wpdb->prepare( "SELECT meta_key FROM $wpdb->postmeta where meta_key='_edd_discount_code' and meta_value=%s", $code ) );
		
		if ( ( $wpdb->num_rows ) > 0 ) 
			return true;
		
		return false;
	}

	/**
	 * Whether or not the discount code can be used
	 * @param  int  $code_id discount code
	 * @return boolean
	 */
	public function discount_code_used( $code_id = null ) {
		$max_uses = get_post_meta( $code_id, '_edd_discount_max_uses', true );
		$uses     = get_post_meta( $code_id, '_edd_discount_uses', true );

		// uses matches the max uses, therefore discount code cannot be used anymore
		if ( $uses == $max_uses ) {
			return (boolean) true;
		}

		return (boolean) false;
	}

	/**
	 * Get the HTML for showing the discounts
	 * @param  array $discount_codes The array of discount codes
	 * @return string
	 */
	public function get_discount_code_html( $discount_codes ) {
		ob_start();
	
		echo apply_filters( 'edd_purchase_rewards_show_discounts_heading', sprintf( __( '%sAvailable discount codes%s', 'edd-purchase-rewards' ), '<h4>', '</h4>' ) );
	?>
		<ul class="edd-pr-discounts">
			<?php foreach ( $discount_codes as $code ) : ?>
				<li><?php echo $code; ?></li>
			<?php endforeach; ?>
		</ul>
	<?php
		$html = ob_get_clean();
		return apply_filters( 'edd_purchase_rewards_show_discounts_html', $html, $discount_codes );	
	}
}