<?php
/**
 * Functions
 */

class EDD_Purchase_Rewards_Functions {

	/**
	 * Determines if the customer is able to be rewarded
	 */
	public function can_reward() {

		$free_purchases_enabled = edd_get_option( 'edd_purchase_rewards_enable_free_purchases', false );

		// get purchase session
		$purchase_session 	= edd_get_purchase_session();
		// get purchase ID from key purchase key
		$payment_id			= edd_get_purchase_id_by_key( $purchase_session['purchase_key'] );
		// get purchase amount
		$purchase_amount 	= edd_get_payment_amount( $payment_id );
		
		$minimum_purchase_amount = edd_get_option( 'edd_purchase_rewards_minimum_purchase_amount' );

		// free purchase rewards enabled and purchase total is 0.00
		if ( $free_purchases_enabled && '0' == $purchase_amount ) {
			return true;
		}
		// if the minimum purchase amount is less than or equal to the actual purchase amount we let the customer share
		elseif ( $minimum_purchase_amount && ( $minimum_purchase_amount <= $purchase_amount ) ) {
			return true;
		}

		// return false by default
		return false;
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
	 * Retrieve the discount code from the options
	 *
	 * @since  1.0
	 */
	public function get_discount() {
		// get our discount from the database
		$discount_code = edd_get_option( 'edd_purchase_rewards_discount_code', null );

		return edd_get_discount_code( $discount_code );

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
		if ( $info && array_key_exists( $info, $user_info ) )
			return $user_info[ $info ];

		// return null by default
		return null;
	}

}	