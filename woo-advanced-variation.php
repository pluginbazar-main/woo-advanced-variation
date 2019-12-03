<?php
/*
	Plugin Name: Variation Swatches for WooCommerce - Lite
	Plugin URI: https://wordpress.org/plugins/woo-advanced-variation/
	Description: Advanced multifunctional product variation swatches for WooCommerce with Colors, Images and Buttons.
	Version: 1.0.0
	Author: Pluginbazar
	Author URI: https://www.pluginbazar.com/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

if ( ! class_exists( 'VariationSwatchesWoo' ) ) {
	/**
	 * Class VariationSwatchesWoo
	 */
	class VariationSwatchesWoo {


		/**
		 * WooCommerceAdvancedVariation constructor
		 */
		function __construct() {

			$this->define_constants();
			$this->define_classes();

			$this->load_functions();
			$this->load_scripts();

			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		}


		/**
		 * Loading TextDomain
		 */
		function load_textdomain() {

			load_plugin_textdomain( VSWOO_TEXTDOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Loading Functions
		 */
		function load_functions() {

			require_once( plugin_dir_path( __FILE__ ) . 'includes/functions.php' );
			require_once( plugin_dir_path( __FILE__ ) . 'includes/functions-settings.php' );
			require_once( plugin_dir_path( __FILE__ ) . 'includes/functions-ajax.php' );
		}


		/**
		 * Define Classes
		 */
		function define_classes() {

			require_once( plugin_dir_path( __FILE__ ) . 'includes/classes/class-pb-settings.php' );
			require_once( plugin_dir_path( __FILE__ ) . 'includes/classes/class-hook.php' );
		}


		/**
		 * Defining Constant
		 */
		function define_constants() {

			define( 'VSWOO_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
			define( 'VSWOO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			define( 'VSWOO_PLUGIN_FILE', plugin_basename( __FILE__ ) );
			define( 'VSWOO_TEXTDOMAIN', 'woo-advanced-variation' );
			define( 'VSWOO_TYPE', 'free' );
		}


		/**
		 * Loading Scripts frontend and backend
		 */
		function load_scripts() {

			add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}


		/**
		 * Front Scripts Loading
		 */
		function front_scripts() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_style( 'dashicons' );

			wp_enqueue_script( 'vswoo-front', plugins_url( 'assets/front/js/scripts.js', __FILE__ ), array( 'jquery' ) );
			wp_localize_script( 'vswoo-front', 'vswoo_ajax', array( 'vswoo_ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			wp_enqueue_style( 'vswoo-front', VSWOO_PLUGIN_URL . 'assets/front/css/style.css' );
			wp_enqueue_style( 'vswoo-tool-tip', VSWOO_PLUGIN_URL . 'assets/tool-tip.css' );
		}


		/**
		 * Admin Scripts Loading
		 */
		function admin_scripts() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_style( 'dashicons' );

			wp_enqueue_style( 'vswoo-admin', VSWOO_PLUGIN_URL . 'assets/admin/css/style.css' );
			wp_enqueue_style( 'vswoo-tool-tip', VSWOO_PLUGIN_URL . 'assets/tool-tip.css' );
		}
	}

	new VariationSwatchesWoo();
}