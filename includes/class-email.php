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
		$email = ! empty( $email ) ? $email : edd_purchase_rewards()->functions->get_customer( 'email' );

		// email subject
		$subject = apply_filters( 'edd_purchase_rewards_email_subject', __( 'Your Discount Code', 'edd-purchase-rewards' ) );

		// get discount code
		$discount_code	= edd_purchase_rewards()->discounts->get_discount( $payment_id );
		
		// email message
		$message = ! empty( $edd_options['edd_purchase_rewards_email'] ) ? $edd_options['edd_purchase_rewards_email'] : __( "Hello {name},\n\nHere is your discount code that will entitle you {discount_amount} off your next purchase:\n\n{discount_code}\n\nEnjoy!\n\n{site_name}\n{site_url}", "edd-purchase-rewards" );
		// filter message
		$message = edd_purchase_rewards()->functions->filter_template_tags( $message, $discount_code, $payment_id );

		//$from_name  = get_bloginfo( 'name' );
		$from_name = isset( $edd_options['from_name'] ) ? $edd_options['from_name'] : get_bloginfo('name');

		//$from_email = get_bloginfo( 'admin_email' );
		$from_email = isset( $edd_options['from_email'] ) ? $edd_options['from_email'] : get_option('admin_email');

		// only send email if discount code exists
		if ( $discount_code ) {
			
			// use new EDD 2.1 Email class
			if ( class_exists( 'EDD_Emails' ) ) {


				EDD()->emails->__set( 'heading', apply_filters( 'edd_purchase_rewards_email_heading', __( 'Your Discount Code', 'edd-purchase-rewards' ) ) );

				// send an email for each custom email
				EDD()->emails->send( $email, $subject, $message );

			} else {
				
				// use old EDD emails

				$headers   	= array();
				$headers[] 	= 'From: ' . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
				$headers[]  = "Reply-To: ". $from_email . "\r\n";
				$headers   	= apply_filters( 'edd_purchase_rewards_email_headers', $headers );

				wp_mail( $email, $subject, $message, $headers );

			}
		}


	}

}