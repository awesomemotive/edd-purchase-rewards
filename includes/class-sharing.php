<?php
/**
 * Sharing Class
 */

class EDD_Purchase_Rewards_Sharing {
	
	public function __construct() {

		// add the sharing icons after the content
	//	add_action( 'edd_payment_receipt_after_table', array( $this, 'sharing_icons' ) );

		// wipe share variable on edd_complete_purchase

		// add CSS styling	
		

		// add scripts to the footer	
		add_action( 'wp_footer', array( $this, 'sharing_scripts' ) );

		// share product + apply discount using ajax
		add_action( 'wp_ajax_sharing_thanks', array( $this, 'sharing_thanks' ) );
		add_action( 'wp_ajax_nopriv_sharing_thanks', array( $this, 'sharing_thanks' ) );
	}






	/**
	 * Sharing icons
	 */
	public function sharing_icons() {
		global $post;

		// // get purchase session
		// $purchase_session 	= edd_get_purchase_session();
		// // get purchase ID from key purchase key
		// $payment_id			= edd_get_purchase_id_by_key( $purchase_session['purchase_key'] );
		// // get purchase amount
		// $purchase_amount 	= edd_get_payment_amount( $payment_id );

		// var_dump( $purchase_amount );

	//	global $edd_options;
		

		//$free_purchases_enabled = edd_get_option( 'edd_purchase_rewards_enable_free_purchases' );

		//var_dump( $free_purchases_enabled );


	//	var_dump( $this->can_reward() );

		$twitter_default_text = 'Custom text for Twitter goes here';

		// URL to share
		$share_url = get_home_url();

		//echo $share_url;
		ob_start();

			// customer is allowed to be rewarded
			if ( edd_purchase_rewards()->functions->can_reward() ) {
				// if the customer has already shared, show them the sharing thanks title and message
				if ( $this->has_shared() ) {
					$share_title 	= $this->share_success_title();
					$share_message 	= $this->share_success_message();
				}
				else {
					// default sharing title and message
					$share_title 	= edd_get_option( 'edd_purchase_rewards_sharing_title' );
					$share_message 	= edd_get_option( 'edd_purchase_rewards_sharing_message' );
				}	
			}
			// standard social sharing
			else {
				$share_title 	= edd_get_option( 'edd_purchase_rewards_sharing_default_title' );
				$share_message 	= edd_get_option( 'edd_purchase_rewards_sharing_default_message' );
			}

			$share_class = $this->has_shared() ? ' shared' : '';
		?>

		

		<div class="edd-pr">
			<div class="edd-pr-message<?php echo $share_class; ?>">
				<?php if ( $share_title ) : ?>
					<h2 class="share-title"><?php echo esc_attr( $share_title ); ?></h2>
				<?php endif; ?>
				
				<?php if ( $share_message ) : ?>
				<p class="share-message"><?php echo esc_attr( $share_message ); ?></p>
				<?php endif; ?>
			</div>

			<?php 
				$twitter_username = '';
				$twitter_count_box = 'vertical';
				$twitter_button_size = 'medium';
			?>
			<div class="share twitter">
				<a href="https://twitter.com/share" data-lang="en" class="twitter-share-button" data-count="<?php echo $twitter_count_box; ?>" data-size="<?php echo $twitter_button_size; ?>" data-counturl="<?php echo $share_url; ?>" data-url="<?php echo $share_url; ?>" data-text="<?php echo $twitter_default_text; ?>" data-via="<?php echo $twitter_username; ?>" data-related="<?php echo $twitter_username; ?>">
					Share
				</a>
			</div>

			<?php
				$data_share = 'false';
				$facebook_button_layout = 'box_count';
			?>
			
			<div class="share facebook">
				<div class="fb-like" data-href="<?php echo $share_url; ?>" data-send="true" data-action="like" data-layout="<?php echo $facebook_button_layout; ?>" data-share="<?php echo $data_share; ?>" data-width="" data-show-faces="false"></div>
			</div>

			<?php 
				$googleplus_button_size = 'tall';
				$google_button_annotation = 'bubble';
				$google_button_recommendations = 'false';
			?>
			<div class="share googleplus">
				<div class="g-plusone" data-recommendations="<?php echo $google_button_recommendations; ?>" data-annotation="<?php echo $google_button_annotation;?>" data-callback="plusOned" data-size="<?php echo $googleplus_button_size; ?>" data-href="<?php echo $share_url; ?>"></div>
			</div>

			<?php 
				$linkedin_counter = 'top';
			?>
			<div class="share linkedin">
			<script src="http://platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script>
			<script type="IN/Share" data-counter="<?php echo $linkedin_counter; ?>" data-onSuccess="share" data-url="<?php echo $share_url; ?>"></script>
			</div>

		</div>

		<?php
			$html = ob_get_clean();

			return $html;
			
	}


	/**
	 * Social sharing scripts
	 *
	 * @since 1.0
	*/
	public function sharing_scripts() {

		?>
		<script type="text/javascript">

		<?php 
		/**
		 * Twitter
		*/
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
		            type: "productShared",
		            url: event.target.baseURI
		        });
		    });
		});


		<?php 
		/**
		 * Google +
		*/
		?>
		window.___gcfg = {
		  lang: 'en-US',
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
			    type: "productShared",
			    url: obj.href
			});
		}

		<?php 
		/**
		 * LinkedIn
		*/
		?>
		function share(url) {
			console.log(url);
		 	jQuery.event.trigger({
	            type: "productShared",
	            url: url
	        });
		}


		<?php
		/**
		 * Facebook
		*/
		?>

		(function(d, s, id) {
		     var js, fjs = d.getElementsByTagName(s)[0];
		     if (d.getElementById(id)) {return;}
		     js = d.createElement(s); js.id = id;
		     js.src = "//connect.facebook.net/en_US/all.js";
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
		            type: "productShared",
		            url: href
		        });     
		    });
		};

		<?php if ( edd_purchase_rewards()->functions->can_reward() ) : ?>

		jQuery(document).ready(function ($) {

			jQuery(document).on( 'productShared', function(e) {

			//	if( e.url == window.location.href ) {

			    	var postData = {
			            action: 'sharing_thanks',
			            nonce: edd_scripts.ajax_nonce
			        };

			    	$.ajax({
		            type: "POST",
		            data: postData,
		            dataType: "json",
		            url: edd_scripts.ajaxurl,
		            success: function ( share_response ) {
		            	// console.log('hello');
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
		                        console.log('failed to share');
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

			//	}
			});
		});

		<?php endif; ?>

		</script>
		<?php
	}

	

	/**
	 * Sharing thanks message
	*/
	public function sharing_thanks() {

		// check nonce
		check_ajax_referer( 'edd_ajax_nonce', 'nonce' );

		$can_email =  edd_purchase_rewards()->functions->can_email();
		
		// set a variable into the session, so the customer cannot share again and keep getting discounts
		// It's ok if they purchase again and get another discount, more money for you!
		$has_shared = EDD()->session->get( 'shared' );

		// setup our return array
		$return = array();

		// User has not shared, the send email to them with the discount code
		if ( ! $this->has_shared() ) {
			
			// send email if enabled
			if ( $can_email ) {
				edd_purchase_rewards()->emails->send();
			}

			// return some data back to the website
			$return['msg'] 				= 'valid';
			$return['success_title'] 	= $this->share_success_title();
			$return['success_message'] 	= $this->share_success_message();

		} else {
			$return['msg'] = 'Already Shared';
		}
		
		// store boolean into session so the customer can't keep receiving discounts
		$has_shared = EDD()->session->set( 'shared', true );

		echo json_encode( $return );

		edd_die();
	}

	/**
	 * Has the user already shared?
	 * @return boolean true if user has shared, false otherwise
	 */
	public function has_shared() {
		$has_shared = EDD()->session->get( 'shared' );

		if ( $has_shared )
			return true;

		return false;
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
		
		$message = edd_get_option( 'edd_purchase_rewards_sharing_thanks_message' );
		
		// display the discount code
		$message .= edd_purchase_rewards()->functions->get_discount();

		return apply_filters( 'edd_purchase_rewards_sharing_thanks_message', $message );
	}

}