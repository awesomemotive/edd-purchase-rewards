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
	 */
	public function send( $email = '', $subject = '', $message = '' ) {

		// retrieve customer's email from the get_customer() function
		$email 		= edd_purchase_rewards()->functions->get_customer( 'email' );

		// retrieve customer's first_name from the get_customer() function
		$first_name = edd_purchase_rewards()->functions->get_customer( 'first_name' );

		// email subject
		$subject 	= __( 'Your Discount Code', 'edd-purchase-rewards' );

		// email message
		$discount_code	= edd_purchase_rewards()->functions->get_discount();
		
		// email message
		$message 	= $this->email_body( $first_name, $discount_code );

		$headers   	= array();
		$headers[] 	= 'From: ' . stripslashes_deep( html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT, 'UTF-8' ) ) . '<' . get_option( 'admin_email' ) . '>';
		$headers   	= apply_filters( 'edd_purchase_rewards_email_headers', $headers );

		wp_mail( $email, $subject, $message, $headers );
	}

	/**
	 * The body of the email
	 * @return string contents of the email body
	 * @since  1.0
	 */
	public function email_body( $first_name = '', $discount_code = '' ) {
		$default_email_body = __( "Dear", "edd-purchase-rewards" ) . ' ' . $first_name . ",\n\n";
		$default_email_body .= __( "Below is a discount code to use on your next purchase:", "edd-purchase-rewards" ) . "\n\n";
		$default_email_body .= $discount_code . "\n\n";

		$default_email_body = apply_filters( 'edd_purchase_rewards_email_body', $default_email_body, $first_name, $discount_code );
		
		return $default_email_body;
	}

}