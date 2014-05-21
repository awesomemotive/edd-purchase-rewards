<?php

class EDD_Purchase_Rewards_Shortcodes {

	public function __construct() {
		add_shortcode( 'edd_purchase_rewards', array( $this, 'edd_purchase_rewards' ) );
	}

	public function edd_purchase_rewards( $atts, $content = null ) {
		global $post;
		
		static $done = false;

		// shortcode_atts( 
		// 	array( 
		// 		'amount' => '', 
		// 		'description' => '', 
		// 		'reference' => '', 
		// 		'context' => ''
		// 	), 
		// 	$atts, 
		// 	'edd_purchase_rewards'
		// );

		// $defaults = array(
		// 	'amount'      => '',
		// 	'description' => '',
		// 	'context'     => '',
		// 	'reference'   => '',
		// 	'status'      => ''
		// );

	    // Nothing to do
	    if ( $done )
	        return;

	    $success_page = edd_get_option( 'success_page' ) ? is_page( edd_get_option( 'success_page' ) ) : false;

	    // shortcode is only run once, on the success page
	    if ( $success_page )
	        $done = true;

		//$args = wp_parse_args( $atts, $defaults );

		return edd_purchase_rewards_reward_customer();

	}

}