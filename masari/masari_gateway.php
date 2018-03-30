<?php
/*
Plugin Name: Masari - WooCommerce Gateway
Plugin URI: https://getmasari.org
Description: Extends WooCommerce by Adding the Masari Gateway
Version: 2.0
Author: Masari Project
Author URI: https://monerointegrations.com
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action('plugins_loaded', 'masari_init', 0);


function masari_gateway($methods)
{
	$methods[] = 'Masari_Gateway';
	return $methods;
}

function masari_add_my_currency($currencies)
{
	$currencies['MSR'] = __('Masari', 'woocommerce');
	return $currencies;
}

function masari_add_my_currency_symbol($currency_symbol, $currency)
{
	switch ($currency) {
		case 'MSR':
			$currency_symbol = 'MSR';
			break;
	}
	return $currency_symbol;
}


function masari_scripts(){
	wp_enqueue_style('materials_icons', 'https://fonts.googleapis.com/icon?family=Material+Icons');
	wp_enqueue_style('montserrat_font', 'http://fonts.googleapis.com/css?family=Lato:400,700');
	wp_enqueue_style('masari_gateway-style',  plugin_dir_url( __FILE__ ).'assets/style.css');
}

function masari_init()
{
    /* If the class doesn't exist (== WooCommerce isn't installed), return NULL */
    if (!class_exists('WC_Payment_Gateway')) return;


    /* If we made it this far, then include our Gateway Class */
    include_once('include/masari_payments.php');
    require_once('library.php');
	
	add_filter('woocommerce_currencies', 'masari_add_my_currency');
	add_filter('woocommerce_currency_symbol', 'masari_add_my_currency_symbol', 10, 2);
	
	// Lets add it too WooCommerce
	add_filter('woocommerce_payment_gateways', 'masari_gateway');
	
	//registering css/js
	add_action( 'wp_enqueue_scripts', 'masari_scripts' );
}

/*
 * Add custom link
 * The url will be http://yourworpress/wp-admin/admin.php?=wc-settings&tab=checkout
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'masari_payment');
function masari_payment($links)
{
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout') . '">' . __('Settings', 'masari_payment') . '</a>',
    );

    return array_merge($plugin_links, $links);
}

add_action('admin_menu', 'monero_create_menu');
function monero_create_menu()
{
    add_menu_page(
        __('Masari', 'textdomain'),
        'Masari',
        'manage_options',
        'admin.php?page=wc-settings&tab=checkout&section=masari_gateway',
        '',
        plugins_url('masari/assets/masari_icon_small.png'),
        56 // Position on menu, woocommerce has 55.5, products has 55.6

    );
}


