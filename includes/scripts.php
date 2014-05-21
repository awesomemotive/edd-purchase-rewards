<?php

/**
 *  Load the frontend scripts and styles
 *  
 *  @since 1.0
 *  @return void
 */
function edd_purchase_rewards_styles() {
	global $post;

	if ( ! is_object( $post ) )
		return;
	?>
	<style>
		.edd-pr {
			margin-bottom: 4rem;
		}

		.edd-pr .share {
			display: inline-block;
			vertical-align: top;
			margin-right: 1rem;
		}

		.edd-pr-message.shared {
			margin-bottom: 2rem;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'edd_purchase_rewards_styles' );
