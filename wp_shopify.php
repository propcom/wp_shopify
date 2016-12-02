<?php

	/**
	 * The plugin bootstrap file
	 *
	 * This file is read by WordPress to generate the plugin information in the plugin
	 * admin area. This file also includes all of the dependencies used by the plugin,
	 * registers the activation and deactivation functions, and defines a function
	 * that starts the plugin.
	 *
	 * @link              https://github.com/propcom/wp_shopify
	 * @since             1.0.0
	 * @package           Wordpress Shopify
	 *
	 * @wordpress-plugin
	 * Plugin Name:       WordPress Shopify Plugin
	 * Plugin URI:        https://github.com/propcom/wp_shopify
	 * Description:       Connects wordpress with shopify
	 * Version:           1.0.0
	 * Author:            Josh Grierson
	 * Author URI:        https://github.com/propcom
	 * License:           GPL-2.0+
	 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
	 * Text Domain:       wp_shopify
	 * Domain Path:       /languages
	 */

	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	function activate_Wordpress_Shopify() {

		require_once plugin_dir_path( __FILE__ ) . 'includes/wp_shopify-activator.php';
		Wordpress_Shopify_Activator::activate();

	}

	function deactivate_Wordpress_Shopify() {

		require_once plugin_dir_path( __FILE__ ) . 'includes/wp_shopify-deactivator.php';
		Wordpress_Shopify_Deactivator::deactivate();

	}

	register_activation_hook( __FILE__, 'activate_Wordpress_Shopify' );
	register_deactivation_hook( __FILE__, 'deactivate_Wordpress_Shopify' );

	require plugin_dir_path( __FILE__ ) . 'includes/wp_shopify.php';

	function run_Wordpress_Shopify() {

		$plugin = new Wordpress_Shopify();
		$plugin->run();

	}

	run_Wordpress_Shopify();
