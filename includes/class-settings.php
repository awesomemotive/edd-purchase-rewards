<?php
/**
 * Settings
 *
 * @since 1.0
*/

class EDD_Purchase_Rewards_Settings {

	public function __construct() {
		// add settings
		add_filter( 'edd_settings_extensions', array( $this, 'settings' ) );

		// sanitize settings
		add_filter( 'edd_settings_extensions_sanitize', array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Settings
	 */
	public function settings( $settings ) {

		// make sure we only show active discounts
		$discounts = edd_get_discounts( array( 'post_status' => 'active', 'posts_per_page' => -1 ) );

		if ( $discounts ) {
			$discount_options = array( 0 => __( 'Select discount', 'edd-purchase-rewards' ) );

			foreach ( $discounts as $discount ) {
				$discount_options[ $discount->ID ] = $discount->post_title;
			}
		}
		// no discounts
		else {
			$discount_options = array( 0 => __( 'No discounts found', 'edd-purchase-rewards' ) );
		}

	  	$new_settings = array(
			array(
				'id' 		=> 'edd_purchase_rewards_header',
				'name' 		=> '<strong>' . edd_purchase_rewards()->title . '</strong>',
				'type' 		=> 'header'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_minimum_purchase_amount',
				'name' 		=> __( 'Minimum Purchase Amount', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Set the minimum purchase amount a customer must reach before a purchase reward is given. Leave blank for no minimum amount.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'std'		=> '',
				'size' 		=> 'small',
			),
			array(
				'id' 		=> 'edd_purchase_rewards_discount_code',
				'name' 		=> __( 'Discount Code', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Select the discount code that the customer will receive.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'select',
				'options' 	=> $discount_options
			),
			array(
				'id' 		=> 'edd_purchase_rewards_generate_discount',
				'name' 		=> __( 'Create Discount Code', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Create a unique, one-time use discount code for the customer. This will override the discount code field above.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'checkbox'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_discount_type',
				'name' 		=> __( 'Discount Type', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Select the discount type for the discount code that will be created.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'select',
				'options' 	=> array(
					'percentage' 	=>  __( 'Percentage', 'edd-purchase-rewards' ),
					'flat' 			=>  __( 'Flat', 'edd-purchase-rewards' ),
				),
				'std'		=> 'percentage'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_discount_amount',
				'name' 		=> __( 'Discount Amount', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Enter the discount amount for the discount code that will be created.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'std'		=> '10',
				'size' 		=> 'small',
			),
			array(
				'id' 		=> 'edd_purchase_rewards_enable_sharing',
				'name' 		=> __( 'Enable Purchase Sharing', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'This will display the sharing icons, regardless of whether the customer must share their purchase for a reward.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'checkbox'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_force_share',
				'name' 		=> __( 'Force Share For Discount Reward', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Customer must share their purchase to at least 1 social network before a reward is given. If Enable Purchase Sharing is not enabled above, it will only show the sharing icons when needed.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'checkbox',
				'std'		=> 1
			),
			// array(
			// 	'id' 		=> 'edd_purchase_rewards_enable_free_purchases',
			// 	'name' 		=> __( 'Enable Discount Reward For Free Purchases', 'edd-purchase-rewards' ),
			// 	'desc' 		=> '<p class="description">' . __( 'Allow discount reward for completely free purchases.', 'edd-purchase-rewards' ) . '</p>',
			// 	'type' 		=> 'checkbox'
			// ),
			array(
				'id' 		=> 'edd_purchase_rewards_enable_email',
				'name' 		=> __( 'Email Discount Code To Customer', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Email the discount code to the customer. The discount code is also displayed on the purchase confirmation page', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'checkbox'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_email',
				'name' 		=> __( 'Reward Email', 'edd-purchase-rewards' ),
				'type' 		=> 'rich_editor',
				'desc' 		=> '<p class="description">' . __( 'Enter the email message you\'d like your customers to receive when they receive their discount code. Use template tags below to customize the email.', 'edd-purchase-rewards' ) . '<br/>' .
							'{name} - ' . __( 'The customer\'s name', 'edd-purchase-rewards' ) . '<br/>' .
							'{discount_code} - ' . __( 'The discount code the customer is receiving', 'edd-purchase-rewards' ) . '<br/>' .
							'{site_name} - ' . __( 'Your site name', 'edd-purchase-rewards' ) . '<br/>' .
							'{site_url} - ' . __( 'Your site\'s URL', 'edd-purchase-rewards' ) . '</p>',
				'std' 		=> __( "Hello {name},\n\nHere is your discount code that you can use on your next purchase:\n\n{discount_code}\n\nEnjoy!\n\n{site_name}", "edd-purchase-rewards" )
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_twitter_message',
				'name' 		=> __( 'Twitter Message', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Custom Twitter Message that will be shown in the twitter popup. The site URL is already appended to the tweet', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'std'		=>  sprintf( __( 'I\'ve just purchased from %s', 'edd-purchase-rewards' ), get_bloginfo( 'name' ) ),
				'size' 		=> 'large',
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_twitter_username',
				'name' 		=> __( 'Twitter Username', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Enter the Twitter username you want the Follow button to use. Leave blank to disable.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'std' 		=> ''
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_default_title',
				'name' 		=> __( 'Default Sharing Title', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Title shown when sharing is enabled and no discount is set, or when purchase does not meet reward requirements.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'std'		=> 'Share your purchase!',
				'size' 		=> 'large',
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_default_message',
				'name' 		=> __( 'Default Sharing Message', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Message shown when sharing is enabled and no discount is set, or when purchase does not meet reward requirements.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'size' 		=> 'large',
				'std'		=> 'Share your purchase with friends'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_reward_title',
				'name' 		=> __( 'Reward Title', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Title shown when purchase meets reward requirements.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'std'		=> __( 'You\'ve Been Rewarded!', 'edd-purchase-rewards' ),
				'size' 		=> 'large',
			),
			array(
				'id' 		=> 'edd_purchase_rewards_reward_message',
				'name' 		=> __( 'Reward Message', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Message shown when purchase meets reward requirements.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'size' 		=> 'large',
				'std'		=> __( "We'd like to offer you a {discount_amount} discount which you can use on your next purchase:", 'edd-purchase-rewards' ),
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_title',
				'name' 		=> __( 'Reward Sharing Title', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Title shown when sharing is enabled and purchase meets reward requirements', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'size' 		=> 'large',
				'std'		=> __( 'Share for a discount on your next purchase!', 'edd-purchase-rewards' ),
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_message',
				'name' 		=> __( 'Reward Sharing Message', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Message shown when sharing is enabled and purchase meets reward requirements', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'size' 		=> 'large',
				'std'		=> __( "We'd like to offer you a {discount_amount} discount which you can use on your next purchase, just for sharing!", 'edd-purchase-rewards' ),
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_thanks_title',
				'name' 		=> __( 'Reward Sharing Thanks Title', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Title shown when purchase has met reward requirements and the customer has shared their purchase', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'size' 		=> 'large',
				'std'		=> __( 'Thanks for sharing!', 'edd-purchase-rewards' ),
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_thanks_message',
				'name' 		=> __( 'Reward Sharing Thanks Message', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Message shown when purchase has met reward requirements and the customer has shared their purchase', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'size' 		=> 'large',
				'std'		=> __( "Here's your {discount_amount} discount code", 'edd-purchase-rewards' ),
			),
			

		);

		// merge with old settings
		return array_merge( $settings, $new_settings );
	}

	/**
	 * Sanitize settings
	 *
	 * @since 1.0
	*/
	function sanitize_settings( $input ) {

		// only allow number, eg 10 or 10.00
		if ( is_numeric( $input['edd_purchase_rewards_discount_amount'] ) )
			$input['edd_purchase_rewards_discount_amount'] = $input['edd_purchase_rewards_discount_amount'];
		else
			$input['edd_purchase_rewards_discount_amount'] = edd_get_option( 'edd_purchase_rewards_discount_amount' );

		// minimum purchase amount
		if ( is_numeric( $input['edd_purchase_rewards_minimum_purchase_amount'] ) || empty( $input['edd_purchase_rewards_minimum_purchase_amount'] ) )
			$input['edd_purchase_rewards_minimum_purchase_amount'] = $input['edd_purchase_rewards_minimum_purchase_amount'];
		else
			$input['edd_purchase_rewards_minimum_purchase_amount'] = edd_get_option( 'edd_purchase_rewards_minimum_purchase_amount' );

	
		return $input;
	}

}