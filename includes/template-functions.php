<?php
/**
 * Template functions
 */

/**
 * Prepend the text to the success page
 */
function edd_purchase_rewards_display_reward( $content ) {
	global $post;

	// if shortcode is used on page, don't add it to payment receipt table
	if ( has_shortcode( $post->post_content, 'edd_purchase_rewards' ) )
		return $content;

	// make sure we only add it to the success page
	if ( $post && edd_is_success_page() && is_main_query() && ! post_password_required() && in_the_loop() ) {
		// get the greeting
		$new_content = edd_purchase_rewards_reward_customer();
		// prepend it to the content and return
		return $new_content . $content;
	}

	return $content;
}
add_filter( 'the_content', 'edd_purchase_rewards_display_reward' );


/**
 * Shows either discount code or loads the sharing functionality
 *
 * @since  1.0
 * @return void
 */
function edd_purchase_rewards_reward_customer() {
	global $post;

	// get payment ID
	$payment_id = edd_purchase_rewards()->functions->get_payment_id();

	// get current purchase session
	$purchase_session = edd_get_purchase_session();

	// only the last purchase reward will show on the purchase confirmation screen
	if ( ! ( $purchase_session['purchase_key'] == edd_get_payment_key( $payment_id ) ) ) {
		return;
	}

	// force the customer to share before receiving reward
	$force_share = edd_get_option( 'edd_purchase_rewards_force_share' );

	// customer must share to at least 1 social network to be rewarded
	if ( $force_share && edd_purchase_rewards()->functions->can_reward() ) {

		// if shortcode, return it
		if ( has_shortcode( $post->post_content, 'edd_purchase_rewards' ) ) {
			return edd_purchase_rewards()->sharing->sharing_icons();
		}
		// else, echo it
		else {
			echo edd_purchase_rewards()->sharing->sharing_icons();
		}

	}
	// customer is rewarded without having to share
	else {

		// if shortcode, return it
		if ( has_shortcode( $post->post_content, 'edd_purchase_rewards' ) ) {
			return edd_purchase_rewards_show_reward();
		}
		// else, echo it
		else {
			echo edd_purchase_rewards_show_reward();
		}

	}

}

/**
 * The default text and discount code
 *
 * @since  1.0
 */
function edd_purchase_rewards_show_reward() {
	global $post;

	ob_start();
	?>
	
	<?php if ( edd_purchase_rewards()->functions->can_reward() ) : // reward can be given ?>

		<?php
			// get discount code. Either generated or normal
			$discount_code  = edd_purchase_rewards()->discounts->get_discount();

			$reward_title   = edd_purchase_rewards()->functions->filter_template_tags( edd_get_option( 'edd_purchase_rewards_reward_title' ) );
			$reward_message = edd_purchase_rewards()->functions->filter_template_tags( edd_get_option( 'edd_purchase_rewards_reward_message' ) );
		?>
	
		<?php if ( $discount_code ) : ?>
			<div class="edd-pr">
				<h2><?php echo esc_attr( $reward_title ); ?></h2>
				<p><?php echo $reward_message; ?></p>
				
				<?php if ( apply_filters( 'edd_purchase_rewards_show_discount_code', true ) ) : ?>
				<h3><?php echo $discount_code; ?></h3>
				<?php endif; ?>

			</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php
		// sharing enabled
		$sharing_enabled = edd_get_option( 'edd_purchase_rewards_enable_sharing' );

		// show the sharing icons so a customer can share their purchase
		if ( $sharing_enabled ) {
			echo edd_purchase_rewards()->sharing->sharing_icons();
		}
	?>

<?php
	$html = ob_get_clean();
	return apply_filters( 'edd_purchase_rewards_show_reward', $html, $discount_code, $reward_title, $reward_message, $sharing_enabled );
}

/**
 * Actions that run when a purchase is marked as complete
 *
 * @since  1.0
 * @return void
 */
function edd_purchase_rewards_complete_purchase( $payment_id ) {

	$user_info 		= edd_get_payment_meta_user_info( $payment_id );
	$email 			= $user_info['email'];

	// check if force sharing is enabled
	$force_share 	= edd_get_option( 'edd_purchase_rewards_force_share' );
	
	// check to see if we can send emails
	$can_email 		= edd_purchase_rewards()->functions->can_email();

	// create discount code (if enabled) if customer can be rewarded and customer isn't forced to share purchase
	if ( edd_purchase_rewards()->functions->can_reward( $payment_id ) && ! edd_get_option( 'edd_purchase_rewards_force_share' ) ) {

		edd_purchase_rewards()->discounts->create_discount( $email, $payment_id );

		// send email to customer
		if ( $can_email ) {
			edd_purchase_rewards()->emails->send( $email, $payment_id );
		}

	}

}
add_action( 'edd_complete_purchase', 'edd_purchase_rewards_complete_purchase' );