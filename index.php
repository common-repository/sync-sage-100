<?php if ( ! defined( 'ABSPATH' ) ) exit; 
/*
	Plugin Name: Sync Sage 100
	Plugin URI: https://profiles.wordpress.org/fahadmahmood/sync-sage-100
	Description: A user friendly plugin to synchronize Sage 100 data into WordPress with API endpoints and manual import.
	Version: 1.0.1
	Author: Fahad Mahmood
	Author URI: http://androidbubble.com/blog/
	Text Domain: sync-sage-100
	Domain Path: /languages
	License: GPL2	
	
	This WordPress plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version. This WordPress plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License	along with this WordPress plugin. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/


	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}else{
		 clearstatcache();
	}
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$wpss_all_plugins = get_plugins();
	$wpss_active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
	
	//if ( array_key_exists('woocommerce/woocommerce.php', $wpss_all_plugins) && in_array('woocommerce/woocommerce.php', $wpss_active_plugins) ) {
		
		
	
	
	global $wpss_data, $wpss_pro, $wpss_activated, $yith_pre_order, $wpss_premium_link, $wpss_url, $wpss_options;
	
	$wpss_premium_link = '';//https://shop.androidbubbles.com/product/sync-sage-100';//https://shop.androidbubble.com/products/wordpress-plugin?variant=36439508779163';//
	
	$yith_pre_order = (in_array( 'yith-pre-order-for-woocommerce/init.php',  $wpss_active_plugins) || in_array( 'yith-woocommerce-pre-order.premium/init.php',  $wpss_active_plugins));
	
	$wpss_activated = true;
	
	$wpss_url = plugin_dir_url( __FILE__ );
	$wpss_data = get_plugin_data(__FILE__);

	$wpss_options = get_option('wpss_options', array());
	
	
	define( 'WPSS_PLUGIN_DIR', dirname( __FILE__ ) );
	
	$wpss_pro_file = WPSS_PLUGIN_DIR . '/pro/wpss-pro.php';
	$wpss_pro =  file_exists($wpss_pro_file);
	require_once WPSS_PLUGIN_DIR . '/inc/functions.php';
	
	if($wpss_pro)
	include_once($wpss_pro_file);		
	
	if(is_admin()){
		add_action( 'admin_menu', 'wpss_admin_menu' );	
		
		if(function_exists('wpss_plugin_links')){
			$plugin = plugin_basename(__FILE__); 
			add_filter("plugin_action_links_$plugin", 'wpss_plugin_links' );	
		}			
		
		
	}
	
		
	//}