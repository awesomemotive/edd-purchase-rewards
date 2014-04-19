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
	}

	/**
	 * Settings
	 */
	public function settings( $settings ) {

		// make sure we only show active discounts
		$discounts = edd_get_discounts( array( 'post_status' => 'active') );

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
				'id' 		=> 'edd_purchase_rewards_discount_code',
				'name' 		=> __( 'Discount Code', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Select the discount that the customer should receive via email after sharing. Leave as default to trigger simple sharing.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'select',
				'options' 	=> $discount_options
			),
			array(
				'id' 		=> 'edd_purchase_rewards_enable_free_purchases',
				'name' 		=> __( 'Enable Discount Reward For Free Purchases', 'edd-purchase-rewards' ),
				'desc' 		=> __( 'Allow discount reward for completely free purchases. The discount reward will still be shown if a customer\'s purchase contains a mix of paid and free downloads.', 'edd-purchase-rewards' ),
				'type' 		=> 'checkbox'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_enable_email',
				'name' 		=> __( 'Email Discount Code To Customer', 'edd-purchase-rewards' ),
				'desc' 		=> __( 'Send the discount code to the customer via email', 'edd-purchase-rewards' ),
				'type' 		=> 'checkbox'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_force_share',
				'name' 		=> __( 'Share For Reward', 'edd-purchase-rewards' ),
				'desc' 		=> __( 'Customer must share their purchase to at least 1 social network before a reward is given. If customer does not meet the reward requirements, the sharing icons are still displayed so they can share there purchase.', 'edd-purchase-rewards' ),
				'type' 		=> 'checkbox'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_minimum_purchase_amount',
				'name' 		=> __( 'Minimum Purchase Amount', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Set the minimum purchase amount a customer must reach before a purchase reward is given', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'std'		=> ''
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_default_title',
				'name' 		=> __( 'Default Sharing Title', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Title shown when sharing is enabled and no discount is set, or when purchase does not meet reward requirements.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'std'		=> 'Share your purchase!'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_default_message',
				'name' 		=> __( 'Default Sharing Message', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Message shown when sharing is enabled and no discount is set, or when purchase does not meet reward requirements.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'textarea',
				'std'		=> 'Share your purchase with your friends and family'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_reward_title',
				'name' 		=> __( 'Reward Title', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Title shown when purchase meets reward requirements.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'std'		=> 'You\'ve Been Rewarded!'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_reward_message',
				'name' 		=> __( 'Reward Message', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Message shown when purchase meets reward requirements.', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'textarea',
				'std'		=> 'As a token of our appreciation, we\'d like to offer you a discount which you can use on your next order:'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_title',
				'name' 		=> __( 'Reward Sharing Title', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Title shown when sharing is enabled and purchase meets reward requirements', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'std'		=> 'Share for a discount on your next order!'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_message',
				'name' 		=> __( 'Reward Sharing Message', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Message shown when sharing is enabled and purchase meets reward requirements', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'textarea',
				'std'		=> 'As a token of our appreciation, we\'d like to offer you a discount which you can use on your next order'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_thanks_title',
				'name' 		=> __( 'Reward Sharing Thanks Title', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Title shown when purchase has met reward requirements and the customer has shared their purchase', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'text',
				'std'		=> 'Thanks for sharing!'
			),
			array(
				'id' 		=> 'edd_purchase_rewards_sharing_thanks_message',
				'name' 		=> __( 'Reward Sharing Thanks Message', 'edd-purchase-rewards' ),
				'desc' 		=> '<p class="description">' . __( 'Message shown when purchase has met reward requirements and the customer has shared their purchase', 'edd-purchase-rewards' ) . '</p>',
				'type' 		=> 'textarea',
				'std'		=> sprintf ( __( 'Here\'s your discount code: %s', 'edd-purchase-rewards' ), '' )
			),

		);

		// merge with old settings
		return array_merge( $settings, $new_settings );
	}

}