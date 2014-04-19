<?php

/**
 *  Load the frontend scripts and styles
 *  
 *  @since 1.0
 *  @return void
 */
function edd_purchase_rewards_styles() {

	global $post;

	if ( ! is_object( $post ) ) {
		return;
	}

	?>
	<style>
		.edd-pr {
			margin-bottom: 2rem;
		}

		.edd-pr .share {
			display: inline-block;
			vertical-align: top;
			padding: 0 0.5rem;
		}

		/* Change the styling on the message when the customer has shared */
		.edd-pr-message.shared {
		
		}
	</style>
	<?php

}
add_action( 'wp_head', 'edd_purchase_rewards_styles' );
