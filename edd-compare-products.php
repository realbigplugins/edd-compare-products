<?php
/*
Plugin Name: Easy Digital Downloads - Compare Products
Plugin URI: https://easydigitaldownloads.com/extensions/compare-products
Description: Allows helpful product comparison tables to be easily generated.
Version: 1.1.1
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
License: GPL2
Text Domain: edd-compare-products
Domain Path: languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'EDD_Compare_Products' ) ) {
	/**
	 * Main EDD_Compare_Products class
	 *
	 * @since       0.1
	 */
	class EDD_Compare_Products {
		/**
		 * @var         EDD_Compare_Products $instance The one true EDD_Compare_Products
		 * @since       0.1
		 */
		private static $instance;
		
		/**
         * @var         EDD_Compare_Products $plugin_data Holds Plugin Header Info
         * @since       1.1.2
         */
		public $plugin_data;

		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       0.1
		 * @return      object self::$instance The one true EDD_Compare_Products
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new EDD_Compare_Products();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				self::$instance->hooks();
			}

			return self::$instance;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       0.1
		 * @return      void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
            if ( ! function_exists( 'get_plugin_data' ) ) {
                require_once ABSPATH . '/wp-admin/includes/plugin.php';
            }
            
            // Only call this once, accessible always
            $this->plugin_data = get_plugin_data( __FILE__ );
            
            if ( ! defined( 'EDD_Compare_Products_ID' ) ) {
                // Plugin Text Domain
                define( 'EDD_Compare_Products_ID', $this->plugin_data['TextDomain'] );
            }

            if ( ! defined( 'EDD_Compare_Products_VER' ) ) {
                // Plugin version
                define( 'EDD_Compare_Products_VER', $this->plugin_data['Version'] );
            }
			
			if ( ! defined( 'EDD_Compare_Products_DIR' ) ) {
				// Plugin path
				define( 'EDD_Compare_Products_DIR', plugin_dir_path( __FILE__ ) );
			}
			
			if ( ! defined( 'EDD_Compare_Products_URL' ) ) {
				// Plugin URL
				define( 'EDD_Compare_Products_URL', plugin_dir_url( __FILE__ ) );	
			}
			
		}

		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       0.1
		 * @return      void
		 */
		private function includes() {
			// Include scripts
			require_once EDD_Compare_Products_DIR . 'includes/scripts.php';
			require_once EDD_Compare_Products_DIR . 'includes/functions.php';
			require_once EDD_Compare_Products_DIR . 'includes/shortcodes.php';
			require_once EDD_Compare_Products_DIR . 'includes/settings.php';
		}

		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       0.1
		 * @return      void
		 */
		private function hooks() {
            // Register settings section
            add_filter( 'edd_settings_sections_extensions', 'edd_compare_products_settings_section', 1 );
			// Register settings
			add_filter( 'edd_settings_extensions', 'edd_compare_products_settings', 1 );
			// Sanitize meta fields settings
			add_filter( 'edd_settings_extensions_sanitize', 'edd_compare_settings_sanitize_meta_fields' );
            
			// Add compare button to lists of downloads within Archives
			add_filter( 'edd_purchase_download_form', 'edd_compare_products_add_compare_button_archive', 10, 2 );
            
            // Add compare button to lists of downloads within the [downloads] shortcode
            add_action( 'edd_download_after', 'edd_compare_products_add_compare_button_downloads_shortcode' );
            
			// Add URL container in footer
			add_action( 'wp_footer', 'EDD_Compare_Products_URL' );
			// Handle licensing
			if ( class_exists( 'EDD_License' ) ) {
				$license = new EDD_License( __FILE__, 'Compare Products', EDD_Compare_Products_VER, 'Kyle Maurer' );
			}
		}

		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       0.1
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for language directory
			$lang_dir = EDD_Compare_Products_DIR . '/languages/';
			$lang_dir = apply_filters( 'edd_compare_products_languages_directory', $lang_dir );
			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), EDD_Compare_Products_ID );
			$mofile = sprintf( '%1$s-%2$s.mo', EDD_Compare_Products_ID, $locale );
			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-compare-products/' . $mofile;
			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/edd-compare-products/ folder
				load_textdomain( EDD_Compare_Products_ID, $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/edd-compare-products/languages/ folder
				load_textdomain( EDD_Compare_Products_ID, $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( EDD_Compare_Products_ID, false, $lang_dir );
			}
		}


	}
} // End if class_exists check
/**
 * The main function responsible for returning the one true EDD_Compare_Products
 * instance to functions everywhere
 *
 * @since       0.1
 * @return      \EDD_Compare_Products The one true EDD_Compare_Products
 */
function EDD_Compare_Products_load() {
	if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		if ( ! class_exists( 'EDD_Extension_Activation' ) ) {
			require_once 'includes/class.extension-activation.php';
		}
		$activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();

		return EDD_Compare_Products::instance();
	} else {
		return EDD_Compare_Products::instance();
	}
}

add_action( 'plugins_loaded', 'EDD_Compare_Products_load' );