<?php
/**
 * Template functions
 */

/**
 * Outputs the rewards after the payment table
 * 
 * @since  1.0
 * @return void
 */
function edd_purchase_rewards_load_rewards() {
	global $post;

	// if shortcode is used on page, don't add it to payment receipt table
	if ( has_shortcode( $post->post_content, 'edd_purchase_rewards' ) )
		return;

	// no shortcode, let's load it
	add_action( 'edd_payment_receipt_after_table', 'edd_purchase_rewards_reward_customer' );
}
//add_action( 'template_redirect', 'edd_purchase_rewards_load_rewards' );


/**
 * Prepend the text to the success page
 */
function edd_purchase_rewards_display_reward( $content ) {
	global $post;

	// if shortcode is used on page, don't add it to payment receipt table
	if ( has_shortcode( $post->post_content, 'edd_purchase_rewards' ) )
		return;

	$success_page = edd_get_option( 'success_page' ) ? is_page( edd_get_option( 'success_page' ) ) : false;

	// make sure we only add it to the success page
	if ( $post && $success_page && is_main_query() && ! post_password_required() ) {
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

	// force the customer to share before receiving reward
	$force_share 	= edd_get_option( 'edd_purchase_rewards_force_share' );

	// customer must share to receive reward
	if ( $force_share ) {

		// if shortcode, return it
		if ( has_shortcode( $post->post_content, 'edd_purchase_rewards' ) ) {
			return edd_purchase_rewards()->sharing->sharing_icons();
		}
		// else, echo it
		else {
			echo edd_purchase_rewards()->sharing->sharing_icons();
		}

	}
	// show the reward
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

	// check to see if we can send emails
	$can_email =  edd_purchase_rewards()->functions->can_email();

	// check to see if the email has already been sent
	$email_sent = EDD()->session->get( 'edd_pr_email_sent' );

	// get discount code
	$discount_code = edd_purchase_rewards()->functions->get_discount();

	// send email if it hasn't already been sent and send email is enabled
	if ( ! $email_sent && $can_email ) {
		edd_purchase_rewards()->emails->send();

		// mark as being sent
		EDD()->session->set( 'edd_pr_email_sent', true );
	}

	$reward_title 	= edd_get_option( 'edd_purchase_rewards_reward_title', __( 'You\'ve Been Rewarded!', 'edd-purchase-rewards' ) );
	$reward_message = edd_get_option( 'edd_purchase_rewards_reward_message', __( 'As a token of our appreciation, we\'d like to offer you a discount, which you can use on your next order:', 'edd-purchase-rewards' ) );

	ob_start();
	?>

	<div class="edd-pr">
		<h2><?php echo esc_attr( $reward_title ); ?></h2>
		<p><?php echo esc_attr( $reward_message ); ?></p>
		<h3><?php echo $discount_code; ?></h3>
	</div>

<?php
	$html = ob_get_clean();
	return $html;
}

/**
 * Clear session variables that we have set when the purchase is complete
 * @return [type] [description]
 */
function edd_purchase_rewards_reset_session() {
	// clear email
	EDD()->session->set( 'edd_pr_email_sent', null );
	// clear sharing
	EDD()->session->set( 'shared', null );
}
add_action( 'edd_complete_purchase', 'edd_purchase_rewards_reset_session' );