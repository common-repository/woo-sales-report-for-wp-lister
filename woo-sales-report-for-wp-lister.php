<?php
/**
 * @wordpress-plugin
 * Plugin Name: Sales Report for WooCommerce & WP-Lister
 * Plugin URI:  https://www.zorem.com/shop/woocommerce-sales-report-for-wp-lister/
 * Description: This plugin simply adds a report tab to display sales report by WP-Lister WooCommerce Reports. The plugin adds an additional report tab which display sales report by WP-Lister. You’ll find this report available in WooCommerce reports section.
 * Version: 1.4.1
 * Author:      zorem
 * Author URI:  http://www.zorem.com/
 * License:     GPL-2.0+
 * License URI: http://www.zorem.com/
 * Text Domain: woo-sales-report-for-wp-lister
 * WC tested up to: 3.9.0
**/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}


/**
 * # Zorem WP Lister Report Main Plugin Class
 *
 * ## Plugin Overview
 *
 * This plugin adds a new section in the WooCommerce Reports -> Orders area called 'Sales By Channel'.
 *
 */
class Zorem_Channel_Lister_Report {

	/** plugin version number */
	public static $version = '1.2';

	/** @var string the plugin file */
	public static $plugin_file = __FILE__;

	/** @var string the plugin file */
	public static $plugin_dir;


	/**
	 * Initializes the plugin
	 *
	 * @since 1.0
	 */
	public static function init() {

		global $wpdb;

		self::$plugin_dir = dirname( __FILE__ );			

		// Add the reports layout to the WooCommerce -> Reports admin section
		add_filter( 'woocommerce_admin_reports',  __CLASS__ . '::initialize_source_admin_report', 12, 1 );

		// Add the path to the report class so WooCommerce can parse it
		add_filter( 'wc_admin_reports_path',  __CLASS__ . '::initialize_source_admin_reports_path', 12, 3 );

		// Load translation files
		add_action( 'plugins_loaded', __CLASS__ . '::load_plugin_textdomain' );
		
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_styles' ,4);
		
		register_activation_hook( __FILE__, __CLASS__ . '::woo_sales_country_report_install');

	}


	/**
	 * Add our location report to the WooCommerce order reports array.
	 *
	 * @param array Array of All Report types & their labels
	 * @return array Array of All Report types & their labels, including the 'Sales by location' report.
	 * @since 1.0
	 */
	public static function initialize_source_admin_report( $report ) {

		$report['orders']['reports']['sales_by_channel'] = array(
			'title'       => __( 'Sales By Channel', 'woo-sales-report-for-wp-lister' ),
			'description' => '',
			'hide_title'  => true,
			'callback'    => array( 'WC_Admin_Reports', 'get_report' ),
			);

		return $report;

	}


	/**
	 * If we hit one of our reports in the WC get_report function, change the path to our dir.
	 *
	 * @param array Array of Report types & their labels
	 * @return array Array of Report types & their labels, including the Subscription product type.
	 * @since 1.0
	 */
	public static function initialize_source_admin_reports_path( $report_path, $name, $class ) {		
		if ( 'WC_Report_sales_by_channel' == $class ) {
			$report_path = self::$plugin_dir . '/classes/class-wc-report-' . $name . '.php';
		}

		return $report_path;

	}


	/**
	 * Load our language settings for internationalization
	 *
	 * @since 1.0
	 */
	public static function load_plugin_textdomain() {

		load_plugin_textdomain( 'woo-sales-report-for-wp-lister', false, basename( self::$plugin_dir ) . '/lang' );

	}
	
	/**
	 * Load admin styles.
	 */
	public function admin_styles() {		
		wp_enqueue_style( 'source_report_style', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css' );
	}	
} // end \WC_Source_Report class


Zorem_Channel_Lister_Report::init();