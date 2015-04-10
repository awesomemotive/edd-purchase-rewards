<?php
/**
 * Functions
 */

class EDD_Purchase_Rewards_Functions {

	/**
	 * Determines if the customer is able to be rewarded
	 *
	 * @return  boolean true if can reward, false otherwise
	 */
	public function can_reward( $payment_id = false ) {
		// get purchase amount
		if ( ! edd_get_purchase_session() ) {
			if ( $payment_id ) {
				// No purchase session available, try to determine the
				// purchase amount by the given payment ID parameter.
				$purchase_amount = edd_get_payment_amount( $payment_id );
			} else {
				// return if no purchase session and no payment ID. Falls back to standard sharing mode
				return false;
			}
		} else {
			$purchase_amount = edd_get_payment_amount( $this->get_payment_id() );
		}

		// enable for free purchases
		$enable_free_purchases 	= edd_get_option( 'edd_purchase_rewards_enable_free_purchases', false );

		// minimum purchase amount
		$minimum_purchase_amount = edd_get_option( 'edd_purchase_rewards_minimum_purchase_amount' );

		// one of two discount options must be enabled
		if ( edd_get_option( 'edd_purchase_rewards_discount_code' ) || edd_get_option( 'edd_purchase_rewards_generate_discount' ) ) {
			// free purchase rewards enabled and purchase total is 0.00
			if ( $enable_free_purchases && '0' == $purchase_amount ) {
				return true;
			}
			// if the minimum purchase amount is less than or equal to the actual purchase amount we let the customer share
			elseif ( ! empty( $minimum_purchase_amount ) && ( $minimum_purchase_amount <= $purchase_amount ) ) {
				return true;
			}
			// no minimum amount
			elseif ( empty( $minimum_purchase_amount ) ) {
				return true;
			}
		}

		// return false by default
		return false;
	}

	

	/**
	 * Has the user already shared?
	 * @return boolean true if user has shared, false otherwise
	 */
	public function has_shared() {
		$has_shared = get_post_meta( edd_purchase_rewards()->functions->get_payment_id(), 'edd_pr_shared', true );
		
		if ( $has_shared )
			return (bool) true;

		return (bool) false;
	}

	/**
	 * Determines if we can send the email to the customer
	 */
	public function can_email() {

		// get value of the email option from the settings
		$can_email = edd_get_option( 'edd_purchase_rewards_enable_email', false );

		if ( $can_email  ) {
			return true;
		}

		// return false by default
		return false;
	}

	
	/**
	 * Get payment ID from purchase session
	 * @return int payment ID
	 */
	public function get_payment_id() {
		// get payment key from query string if it exists
		$payment_key = isset( $_GET['payment_key'] ) ? $_GET['payment_key'] : '';	

		// get purchase session
		$purchase_session = edd_get_purchase_session();
		
		// get the key
		$purchase_key = ! empty( $payment_key ) ? $payment_key : $purchase_session['purchase_key'];
		
		// get the payment ID from the purchase key
		$payment_id = edd_get_purchase_id_by_key( $purchase_key );

		if ( $payment_id ) {
			return $payment_id;
		}

		return null;
	}


	/**
	 * Displays the discount amount
	 * @param  string $text The discount to be formatted
	 * @return string       the formatted discount amount
	 */
	public function filter_template_tags( $text = '', $discount_code = null, $payment_id = '' ) {
							
		$code               = edd_get_option( 'edd_purchase_rewards_discount_code' );

		$generate_discount 	= edd_get_option( 'edd_purchase_rewards_generate_discount' );
		$type               = edd_get_option( 'edd_purchase_rewards_discount_type' );
		
		// this needs to pull the information from the database if discount code has been generated
		$amount             = edd_get_option( 'edd_purchase_rewards_discount_amount' );

		// payment ID
		$payment_id = ! empty( $payment_id ) ? $payment_id : $this->get_payment_id();
		$user_info  = edd_get_payment_meta_user_info( $payment_id );

		// saved discount
		$saved_discount_code = get_post_meta( $payment_id, '_edd_purchase_rewards_discount', true );
		
		// discount code is already saved
		if ( $saved_discount_code ) {
			$discount = edd_format_discount_rate( edd_get_discount_type( $saved_discount_code ), edd_get_discount_amount( $saved_discount_code ) );
		}
		// discount code has not been saved yet, get from options
		else {
			// selected discount or generated discount
			if ( $generate_discount ) {
				
				switch ( $type ) {
					case 'percentage':
						$discount = $amount . '%';
						break;
					
					case 'flat':
						$discount = edd_currency_filter( edd_format_amount( $amount ) );
						break;
				}

			} 
			// selected discount
			else {
				$discount = edd_format_discount_rate( edd_get_discount_type( $code ), edd_get_discount_amount( $code ) );
			}
		}
		
		// Retrieve the customer name
		if ( isset( $user_info['id'] ) && $user_info['id'] > 0 ) {
			$user_data     = get_userdata( $user_info['id'] );
			$customer_name = $user_data->display_name;
		} elseif ( isset( $user_info['first_name'] ) ) {
			$customer_name = $user_info['first_name'];
		} else {
			$customer_name = $user_info['email'];
		}

		$text = str_replace( '{name}', 				$customer_name, 		$text );
		$text = str_replace( '{discount_code}', 	$discount_code, 		$text );
		$text = str_replace( '{discount_amount}', 	'<strong>' . $discount. '</strong>', $text );
		$text = str_replace( '{site_name}',  		get_bloginfo( 'name' ), $text );
		$text = str_replace( '{site_url}',  		get_site_url(), 		$text );

		return $text;
	}

	/**
	 * Get customer info
	 * We can use this to retrieve the customer's first name and email
	 * 
	 * @param string $info the information we want to retrieve
	 * @since  1.0
	 */
	public function get_customer( $info = '' ) {
		// get purchase session
		// this is a helper class for accessing EDD_Session EDD()->session->get( 'edd_purchase' );
		$purchase_session = edd_get_purchase_session();

		// get purchase ID from key purchase key
		$payment_id = edd_get_purchase_id_by_key( $purchase_session['purchase_key'] );

		// get the user info from purchase session, based on the payment ID
		$user_info = edd_get_payment_meta_user_info( $payment_id );

		// if parameter was passed into function and the array key exists
		//if ( $info && array_key_exists( $info, $user_info ) )
		if ( $info )
			return $user_info[ $info ];

		// return null by default
		return null;
	}

}