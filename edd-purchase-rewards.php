<?php
/*
Plugin Name: Easy Digital Downloads - Purchase Rewards
Plugin URI: http://sumobi.com/shop/edd-purchase-rewards/
Description: Increase sales and build customer loyalty by rewarding customers
Version: 1.1
Author: Andrew Munro, Sumobi
Author URI: http://sumobi.com/
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Purchase_Rewards' ) ) {

	final class EDD_Purchase_Rewards {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of EDD Purchase Rewards exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * Plugin Version
		 */
		private $version = '1.1';

		/**
		 * Plugin Title
		 */
		public $title = 'EDD Purchase Rewards';

		/**
		 * Class Properties
		 */
		public $emails;
		public $sharing;
		public $settings;
		public $shortcodes;
		public $functions;
		public $discounts;

		/**
		 * Main Instance
		 *
		 * Ensures that only one instance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 *
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_Purchase_Rewards ) ) {
				self::$instance = new EDD_Purchase_Rewards;
				self::$instance->setup_constants();
				self::$instance->hooks();
				self::$instance->includes();

				// Setup objects
				self::$instance->emails 	= new EDD_Purchase_Rewards_Email;
				self::$instance->sharing 	= new EDD_Purchase_Rewards_Sharing;
				self::$instance->settings 	= new EDD_Purchase_Rewards_Settings;
				self::$instance->shortcodes = new EDD_Purchase_Rewards_Shortcodes;
				self::$instance->functions 	= new EDD_Purchase_Rewards_Functions;
				self::$instance->discounts 	= new EDD_Purchase_Rewards_Discounts;
			}

			return self::$instance;
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version
			if ( ! defined( 'EDD_PURCHASE_REWARDS_VERSION' ) ) {
				define( 'EDD_PURCHASE_REWARDS_VERSION', $this->version );
			}

			// Plugin Folder Path
			if ( ! defined( 'EDD_PURCHASE_REWARDS_PLUGIN_DIR' ) ) {
				define( 'EDD_PURCHASE_REWARDS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'EDD_PURCHASE_REWARDS_PLUGIN_URL' ) ) {
				define( 'EDD_PURCHASE_REWARDS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'EDD_PURCHASE_REWARDS_PLUGIN_FILE' ) ) {
				define( 'EDD_PURCHASE_REWARDS_PLUGIN_FILE', __FILE__ );
			}
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {

			// plugin meta
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), 10, 2 );
			
			// text domain
			add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );

			do_action( 'edd_purchase_rewards_setup_actions' );
		}

		/**
		 * Includes
		 *
		 * @since 1.0
		 * @access private
		 * @return void
		 */
		private function includes() {
			require_once EDD_PURCHASE_REWARDS_PLUGIN_DIR . 'includes/template-functions.php';
			require_once EDD_PURCHASE_REWARDS_PLUGIN_DIR . 'includes/class-email.php';
			require_once EDD_PURCHASE_REWARDS_PLUGIN_DIR . 'includes/class-sharing.php';
			require_once EDD_PURCHASE_REWARDS_PLUGIN_DIR . 'includes/class-settings.php';
			require_once EDD_PURCHASE_REWARDS_PLUGIN_DIR . 'includes/class-shortcodes.php';
			require_once EDD_PURCHASE_REWARDS_PLUGIN_DIR . 'includes/class-functions.php';
			require_once EDD_PURCHASE_REWARDS_PLUGIN_DIR . 'includes/class-discounts.php';
			require_once EDD_PURCHASE_REWARDS_PLUGIN_DIR . 'includes/scripts.php';
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( EDD_PURCHASE_REWARDS_PLUGIN_DIR ) ) . '/languages/';
			$lang_dir = apply_filters( 'edd_purchase_rewards_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'edd-purchase-rewards' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'edd-purchase-rewards', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-purchase-rewards/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/edd-purchase-rewards folder
				load_textdomain( 'edd-purchase-rewards', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/edd-purchase-rewards/languages/ folder
				load_textdomain( 'edd-purchase-rewards', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-purchase-rewards', false, $lang_dir );
			}
		}

		/**
		 * Plugin settings link
		 *
		 * @since 1.0
		*/
		public function settings_link( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions' ) . '">' . __( 'Settings', 'edd-purchase-rewards' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Modify plugin metalinks
		 *
		 * @access      public
		 * @since       1.0.0
		 * @param       array $links The current links array
		 * @param       string $file A specific plugin table entry
		 * @return      array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
		    if ( $file == plugin_basename( __FILE__ ) ) {
		        $plugins_link = array(
		            '<a title="'. __( 'View more plugins for Easy Digital Downloads by Sumobi', 'edd-purchase-rewards' ) .'" href="https://easydigitaldownloads.com/blog/author/andrewmunro/?ref=166" target="_blank">' . __( 'Author\'s EDD plugins', 'edd-purchase-rewards' ) . '</a>'
		        );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;
		}

	}

	/**
	 * Loads a single instance
	 *
	 * This follows the PHP singleton design pattern.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * @example <?php $edd_purchase_rewards = edd_purchase_rewards(); ?>
	 *
	 * @since 1.0
	 *
	 * @see EDD_Purchase_Rewards::get_instance()
	 *
	 * @return object Returns an instance of the EDD_Purchase_Rewards class
	 */
	function edd_purchase_rewards() {

	    if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {

	        if ( ! class_exists( 'EDD_Extension_Activation' ) ) {
	            require_once 'includes/class-activation.php';
	        }

	        $activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
	        $activation = $activation->run();
	        
	    } else {
	        return EDD_Purchase_Rewards::get_instance();
	    }
	}
	add_action( 'plugins_loaded', 'edd_purchase_rewards', apply_filters( 'edd_purchase_rewards_action_priority', 10 ) );

}