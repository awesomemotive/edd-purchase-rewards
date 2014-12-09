<?php
/**
 * Sharing Class
 */

class EDD_Purchase_Rewards_Sharing {
	
	public function __construct() {

		// add scripts to the footer	
		add_action( 'wp_footer', array( $this, 'sharing_scripts' ) );

		// share product + apply discount using ajax
		add_action( 'wp_ajax_sharing_thanks', array( $this, 'sharing_thanks' ) );
		add_action( 'wp_ajax_nopriv_sharing_thanks', array( $this, 'sharing_thanks' ) );
	}

	/**
	 * Determine which social networks are enabled
	 * @param  string  $network network name
	 * @return boolean          true if network exists, false otherwise
	 */
	public function is_enabled( $network = '' ) {
		global $edd_options;

		$networks = array(
			'twitter',
			'facebook',
			'googleplus',
			'linkedin'
		);

		$networks = apply_filters( 'edd_purchase_rewards_sharing_networks', $networks );

		if ( $network ) {
			return in_array( $network, $networks );
		}
		elseif ( $networks ) {
			return true;
		}

	}

	/**
	 * Sharing icons
	 */
	public function sharing_icons() {
		global $post;

		$twitter_default_text = edd_get_option( 'edd_purchase_rewards_sharing_twitter_message' );

		// URL to share
		$share_url = apply_filters( 'edd_purchase_rewards_share_url', get_home_url( '', '', 'http' ) );

		ob_start();

			/**
			 * Show correct sharing title and message if the customer can be rewarded.
			 */
			if ( edd_purchase_rewards()->functions->can_reward() ) {
				// if the customer has already shared, show them the sharing thanks title and message
				if ( edd_purchase_rewards()->functions->has_shared() ) {
					$share_title 	= $this->share_success_title();
					$share_message 	= $this->share_success_message();
				}
				elseif ( edd_get_option( 'edd_purchase_rewards_force_share' ) ) {
					// default sharing title and message
					$share_title 	= edd_purchase_rewards()->functions->filter_template_tags( edd_get_option( 'edd_purchase_rewards_sharing_title' ) );
					$share_message 	= edd_purchase_rewards()->functions->filter_template_tags( edd_get_option( 'edd_purchase_rewards_sharing_message' ) );
				}
				else {
					$share_title 	= edd_get_option( 'edd_purchase_rewards_sharing_default_title' );
					$share_message 	= edd_get_option( 'edd_purchase_rewards_sharing_default_message' );
				}
			}
			// standard social sharing
			else {
				$share_title 	= edd_get_option( 'edd_purchase_rewards_sharing_default_title' );
				$share_message 	= edd_get_option( 'edd_purchase_rewards_sharing_default_message' );
			}

			$share_class = edd_purchase_rewards()->functions->has_shared() ? ' shared' : '';
		?>

		<div class="edd-pr">
			<div class="edd-pr-message<?php echo $share_class; ?>">
				<?php if ( $share_title ) : ?>
					<h2 class="share-title"><?php echo esc_attr( $share_title ); ?></h2>
				<?php endif; ?>
				
				<?php if ( $share_message ) : ?>
					<div class="share-message">
					<?php echo wpautop( $share_message, false ); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $this->is_enabled( 'twitter' ) ) : 
				$twitter_username = edd_get_option( 'edd_purchase_rewards_sharing_twitter_username' );
				$twitter_count_box = 'vertical';
				$twitter_button_size = 'medium';
			?>
			<div class="share twitter">
				<a href="https://twitter.com/share" data-lang="en" class="twitter-share-button" data-count="<?php echo $twitter_count_box; ?>" data-size="<?php echo $twitter_button_size; ?>" data-counturl="<?php echo $share_url; ?>" data-url="<?php echo $share_url; ?>" data-text="<?php echo $twitter_default_text; ?>" data-via="<?php echo $twitter_username; ?>" data-related="<?php echo $twitter_username; ?>">
					Share
				</a>
			</div>
			<?php endif; ?>

			<?php if ( $this->is_enabled( 'facebook' ) ) : 
				$data_share = 'false';
				$facebook_button_layout = 'box_count';
			?>
			
			<div class="share facebook">
				<div class="fb-like" data-href="<?php echo $share_url; ?>" data-send="true" data-action="like" data-layout="<?php echo $facebook_button_layout; ?>" data-share="<?php echo $data_share; ?>" data-width="" data-show-faces="false"></div>
			</div>
			<?php endif; ?>

			<?php if ( $this->is_enabled( 'googleplus' ) ) : 
				$googleplus_button_size = 'tall';
				$google_button_annotation = 'bubble';
				$google_button_recommendations = 'false';
			?>
			<div class="share googleplus">
				<div class="g-plusone" data-recommendations="<?php echo $google_button_recommendations; ?>" data-annotation="<?php echo $google_button_annotation;?>" data-callback="plusOned" data-size="<?php echo $googleplus_button_size; ?>" data-href="<?php echo $share_url; ?>"></div>
			</div>
			<?php endif; ?>

			<?php if ( $this->is_enabled( 'linkedin' ) ) :  
				$linkedin_counter = 'top';
				$locale = apply_filters( 'edd_purchase_rewards_locale_linkedin', 'en_US' );
			?>

			<div class="share linkedin">
			<?php if( is_ssl() ) : // load https version of linkedin ?>
				<script src="https://platform.linkedin.com/in.js" type="text/javascript">lang: <?php echo $locale; ?></script>
			<?php else : ?>
				<script src="http://platform.linkedin.com/in.js" type="text/javascript">lang: <?php echo $locale; ?></script>
			<?php endif; ?>
			
			<script type="IN/Share" data-counter="<?php echo $linkedin_counter; ?>" data-onSuccess="share" data-url="<?php echo $share_url; ?>"></script>
			</div>
			<?php endif; ?>

		</div>

		<?php
			$html = ob_get_clean();
			return apply_filters( 'edd_purchase_rewards_sharing_icons', $html );	
	}


	/**
	 * Social sharing scripts
	 *
	 * @since 1.0
	*/
	public function sharing_scripts() {
		
		if ( ! edd_is_success_page() ) {
			return;
		}

		if ( ! ( edd_get_option( 'edd_purchase_rewards_force_share' ) || edd_get_option( 'edd_purchase_rewards_enable_sharing' ) ) ) {
			return;
		}

		?>
		<script type="text/javascript">

		<?php 
		/**
		 * Twitter
		*/
		
		if ( $this->is_enabled( 'twitter' ) ) : 
		?>
	  	window.twttr = (function (d,s,id) {
		  var t, js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
		  js.src="https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
		  return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
		}(document, "script", "twitter-wjs"));

		twttr.ready(function (twttr) {
		    twttr.events.bind('tweet', function (event) {
		        jQuery.event.trigger({
		            type: "purchaseShared",
		            url: event.target.baseURI
		        });
		    });
		});
		<?php endif; ?>

		<?php 
		/**
		 * Google +
		*/
		if ( $this->is_enabled( 'googleplus' ) ) : 

		$locale = apply_filters( 'edd_purchase_rewards_locale_google', 'en-US' );
		
		?>
		window.___gcfg = {
		  lang: '<?php echo $locale; ?>',
		  parsetags: 'onload'
		};

		(function() {
		    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		    po.src = 'https://apis.google.com/js/plusone.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();

		function plusOned(obj) {
			console.log(obj);
			jQuery.event.trigger({
			    type: "purchaseShared",
			    url: obj.href
			});
		}
		<?php endif; ?>

		<?php 
		/**
		 * LinkedIn
		*/
		if ( $this->is_enabled( 'linkedin' ) ) : 
		?>
		function share(url) {
			console.log(url);
		 	jQuery.event.trigger({
	            type: "purchaseShared",
	            url: url
	        });
		}
		<?php endif; ?>


		<?php
		/**
		 * Facebook
		*/
		if ( $this->is_enabled( 'facebook' ) ) :

		$locale = apply_filters( 'edd_purchase_rewards_locale_facebook', 'en_US' );
		?>

		(function(d, s, id) {
		     var js, fjs = d.getElementsByTagName(s)[0];
		     if (d.getElementById(id)) {return;}
		     js = d.createElement(s); js.id = id;
		     js.src = "//connect.facebook.net/<?php echo $locale; ?>/all.js";
		     fjs.parentNode.insertBefore(js, fjs);
		 }(document, 'script', 'facebook-jssdk'));

		window.fbAsyncInit = function() {
		    // init the FB JS SDK
		    FB.init({
		      status	: true,
		      cookie	: true,                               
		      xfbml		: true                              
		    });

		    FB.Event.subscribe('edge.create', function(href, widget) {
		        jQuery.event.trigger({
		            type: "purchaseShared",
		            url: href
		        });     
		    });
		};
		<?php endif; ?>

		<?php if ( edd_purchase_rewards()->functions->can_reward() && edd_get_option( 'edd_purchase_rewards_force_share' ) ) : ?>

		jQuery(document).ready(function ($) {

			jQuery(document).on( 'purchaseShared', function(e) {

			    	var postData = {
			            action: 'sharing_thanks',
			        };

			    	$.ajax({
		            type: "POST",
		            data: postData,
		            dataType: "json",
		            url: edd_scripts.ajaxurl,
		            success: function ( share_response ) {
		            	// console.log( share_response );

		                if( share_response ) {
		                	
		                    if ( share_response.msg == 'valid' ) {
		                       console.log('successfully shared');
		                       console.log( share_response );

		                       jQuery('.share-title').html( share_response.success_title );
		                       jQuery('.share-message').html( share_response.success_message );

		                       // add CSS class so the box can be styled
		                       jQuery('.edd-pr-message').addClass('shared');
		                    } 
		                    else {
		                        console.log( share_response );
		                    }
		                } 
		                else {
		                    console.log( share_response );
		                }
		            }
		        }).fail(function (data) {
		            console.log( data );
		        });

			});
		});

		<?php endif; ?>

		</script>
		<?php
	}

	
	/**
	 * Sharing ajax function
	*/
	public function sharing_thanks() {
	
		// See if the customer has shared already. This is so the customer cannot share again and keep getting discounts
		$has_shared = edd_purchase_rewards()->functions->has_shared();

		// setup our return array
		$return = array();

		// User has not shared before
		if ( ! $has_shared ) {
			// create discount code if enabled
			edd_purchase_rewards()->discounts->create_discount();

			// send email if enabled
			if ( edd_purchase_rewards()->functions->can_email() ) {
				edd_purchase_rewards()->emails->send();
			}

			// return some data back to the website
			$return['msg'] 				= 'valid';
			$return['success_title'] 	= $this->share_success_title();
			$return['success_message'] 	= $this->share_success_message();

			// mark purchase as shared
			update_post_meta( edd_purchase_rewards()->functions->get_payment_id(), 'edd_pr_shared', true );

			do_action( 'edd_purchase_rewards_after_share');

		} else {
			$return['msg'] = 'Already Shared';
		}
		
		echo json_encode( $return );

		edd_die();
	}


	/**
	 * Success Title when download has been shared
	 *
	 * @since 1.0
	*/
	public function share_success_title() {

		$title = edd_get_option( 'edd_purchase_rewards_sharing_thanks_title' );

		return apply_filters( 'edd_purchase_rewards_sharing_thanks_title', $title );
	}

	/**
	 * Success Message when download has been shared
	 * Displays discount code within message
	 *
	 * @since 1.0
	*/
	public function share_success_message() {
		
		// return if no purchase session 
		if ( ! edd_purchase_rewards()->functions->get_payment_id() )
			return;

		$message = edd_purchase_rewards()->functions->filter_template_tags( edd_get_option( 'edd_purchase_rewards_sharing_thanks_message' ) );
		$message = wpautop( $message, false );

		// display the discount code
		$discount = '<h3>' . edd_purchase_rewards()->discounts->get_discount() . '</h3>';

		$message = $message . $discount; 

		return apply_filters( 'edd_purchase_rewards_sharing_thanks_message', $message );
	}

}