<?php
/**
 * EDD Purchase Rewards Email Class
 *
 */
class EDD_Purchase_Rewards_Email {

	/**
	 * Send email on successful purchase
	 *
	 * @since  1.0
	 * @todo  add filter for subject
	 */
	public function send( $email = '', $payment_id = '', $subject = '', $message = '' ) {
		global $edd_options;

		// customer email
		$email 		= ! empty( $email ) ? $email : edd_purchase_rewards()->functions->get_customer( 'email' );

		// email subject
		$subject 	= __( 'Your Discount Code', 'edd-purchase-rewards' );

		// get discount code
		$discount_code	= edd_purchase_rewards()->discounts->get_discount( $payment_id );
		
		// email message
		$message    = ! empty( $edd_options['edd_purchase_rewards_email'] ) ? $edd_options['edd_purchase_rewards_email'] : __( "Hello {name},\n\nHere is your discount code that will entitle you {discount_amount} off your next purchase:\n\n{discount_code}\n\nEnjoy!\n\n{site_name}\n{site_url}", "edd-purchase-rewards" );
		// filter message
		$message    = edd_purchase_rewards()->functions->filter_template_tags( $message, $discount_code, $payment_id );

		$from_name  = get_bloginfo( 'name' );
		$from_email = get_bloginfo( 'admin_email' );

		$headers   	= array();
		$headers[] 	= 'From: ' . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
		$headers[]  = "Reply-To: ". $from_email . "\r\n";
		$headers   	= apply_filters( 'edd_purchase_rewards_email_headers', $headers );
		
		// only send email if discount code exists
		if ( $discount_code ) {
			wp_mail( $email, $subject, $message, $headers );
		}
	}

}