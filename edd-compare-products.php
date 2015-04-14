<?php
/*
Plugin Name: Easy Digital Downloads - Compare Products
Plugin URI: http://realbigplugins.com
Description: A brief description of the Plugin.
Version: 0.1
Author: Kyle Maurer
Author URI: http://kyleblog.net
License: GPL2
Text Domain: edd-compare-products
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
			// Plugin version
			define( 'EDD_COMPARE_PRODUCTS_VER', '0.1' );
			// Plugin path
			define( 'EDD_COMPARE_PRODUCTS_DIR', plugin_dir_path( __FILE__ ) );
			// Plugin URL
			define( 'EDD_COMPARE_PRODUCTS_URL', plugin_dir_url( __FILE__ ) );
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
			require_once EDD_COMPARE_PRODUCTS_DIR . 'includes/scripts.php';
			require_once EDD_COMPARE_PRODUCTS_DIR . 'includes/functions.php';
			require_once EDD_COMPARE_PRODUCTS_DIR . 'includes/shortcodes.php';
			require_once EDD_COMPARE_PRODUCTS_DIR . 'includes/widgets.php';
			require_once EDD_COMPARE_PRODUCTS_DIR . 'includes/settings.php';
		}

		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       0.1
		 * @return      void
		 */
		private function hooks() {
			// Register settings
			add_filter( 'edd_settings_extensions', 'edd_compare_products_settings', 1 );
			// Sanitize meta fields settings
			add_filter( 'edd_settings_extensions_sanitize', 'edd_compare_settings_sanitize_meta_fields' );
			// Handle licensing
			if ( class_exists( 'EDD_License' ) ) {
				$license = new EDD_License( __FILE__, 'EDD Compare Products', EDD_COMPARE_PRODUCTS_VER, 'Kyle Maurer' );
			}
			add_action( 'admin_notices', function() {
				//global $edd_options;
				// var_dump();
			});
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
			$lang_dir = EDD_COMPARE_PRODUCTS_DIR . '/languages/';
			$lang_dir = apply_filters( 'edd_compare_products_languages_directory', $lang_dir );
			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'edd-compare-products' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'edd-compare-products', $locale );
			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-compare-products/' . $mofile;
			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/edd-compare-products/ folder
				load_textdomain( 'edd-compare-products', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/edd-compare-products/languages/ folder
				load_textdomain( 'edd-compare-products', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-compare-products', false, $lang_dir );
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
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where your extension is activated but EDD is not
 *              present.
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
/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       0.1
 * @return      void
 */
function edd_compare_products_activation() {
	/* Activation functions here */
}

register_activation_hook( __FILE__, 'edd_compare_products_activation' );