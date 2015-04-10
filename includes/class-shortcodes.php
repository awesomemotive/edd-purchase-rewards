<?php

class EDD_Purchase_Rewards_Shortcodes {

	public function __construct() {
		add_shortcode( 'edd_purchase_rewards', array( $this, 'purchase_rewards_shortcode' ) );
		add_shortcode( 'edd_purchase_rewards_discounts', array( $this, 'show_discounts_shortcode' ) );
	}

	public function purchase_rewards_shortcode( $atts, $content = null ) {
		global $post;
		
		static $done = false;

	    // Nothing to do
	    if ( $done ) {
	        return;
	    }

	    // shortcode is only run once, on the success page
	    if ( edd_is_success_page() ) {
	        $done = true;
	    }

		return edd_purchase_rewards_reward_customer();

	}

	/**
	 * Show the customer the discount codes that they have available to use
	 */
	public function show_discounts_shortcode( $atts, $content = null ) {
		if ( ! is_user_logged_in() )
			return;

		// get current user's purchases
		$purchases      = edd_get_users_purchases( '', -1 );
		$purchase_ids   = array();
		$discount_codes = array();

		if ( $purchases ) {
			$purchase_ids = wp_list_pluck( $purchases, 'ID' );
		}

		if ( $purchase_ids ) {
			foreach ( $purchase_ids as $id ) {
				$discount_code = get_post_meta( $id, '_edd_purchase_rewards_discount', true );

				if ( $discount_code && ! edd_purchase_rewards()->discounts->discount_code_used( $discount_code ) ) {
					$discount_codes[] = edd_get_discount_code( $discount_code );
				}
			}
		}
		
		$purchases = edd_purchase_rewards()->discounts->get_discount_code_html( $discount_codes );

		return $purchases;
	}	

}