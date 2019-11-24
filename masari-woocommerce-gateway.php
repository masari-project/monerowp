<?php
/*
Plugin Name: Masari Woocommerce Gateway
Plugin URI: https://github.com/masari-project/masariwp
Description: Extends WooCommerce by adding a Masari Gateway
Version: 3.0.1
Tested up to: 4.9.8
Author: mosu-forge, SerHack
Author URI: https://monerointegrations.com/
*/
// This code isn't for Dark Net Markets, please report them to Authority!

defined( 'ABSPATH' ) || exit;

// Constants, you can edit these if you fork this repo
define('MASARI_GATEWAY_MAINNET_EXPLORER_URL', 'https://msrchain.net/');
define('MASARI_GATEWAY_TESTNET_EXPLORER_URL', '');
define('MASARI_GATEWAY_ADDRESS_PREFIX', 0x1C);
define('MASARI_GATEWAY_ADDRESS_PREFIX_INTEGRATED', 0x1D);
define('MASARI_GATEWAY_ATOMIC_UNITS', 12);
define('MASARI_GATEWAY_ATOMIC_UNIT_THRESHOLD', 10); // Amount under in atomic units payment is valid
define('MASARI_GATEWAY_DIFFICULTY_TARGET', 60);

// Do not edit these constants
define('MASARI_GATEWAY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MASARI_GATEWAY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MASARI_GATEWAY_ATOMIC_UNITS_POW', pow(10, MASARI_GATEWAY_ATOMIC_UNITS));
define('MASARI_GATEWAY_ATOMIC_UNITS_SPRINTF', '%.'.MASARI_GATEWAY_ATOMIC_UNITS.'f');

// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action('plugins_loaded', 'masari_init', 1);
function masari_init() {

    // If the class doesn't exist (== WooCommerce isn't installed), return NULL
    if (!class_exists('WC_Payment_Gateway')) return;

    // If we made it this far, then include our Gateway Class
    require_once('include/class-masari-gateway.php');

    // Create a new instance of the gateway so we have static variables set up
    new Masari_Gateway($add_action=false);

    // Include our Admin interface class
    require_once('include/admin/class-masari-admin-interface.php');

    add_filter('woocommerce_payment_gateways', 'masari_gateway');
    function masari_gateway($methods) {
        $methods[] = 'Masari_Gateway';
        return $methods;
    }

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'masari_payment');
    function masari_payment($links) {
        $plugin_links = array(
            '<a href="'.admin_url('admin.php?page=masari_gateway_settings').'">'.__('Settings', 'masari_gateway').'</a>'
        );
        return array_merge($plugin_links, $links);
    }

    add_filter('cron_schedules', 'masari_cron_add_one_minute');
    function masari_cron_add_one_minute($schedules) {
        $schedules['one_minute'] = array(
            'interval' => 60,
            'display' => __('Once every minute', 'masari_gateway')
        );
        return $schedules;
    }

    add_action('wp', 'masari_activate_cron');
    function masari_activate_cron() {
        if(!wp_next_scheduled('masari_update_event')) {
            wp_schedule_event(time(), 'one_minute', 'masari_update_event');
        }
    }

    add_action('masari_update_event', 'masari_update_event');
    function masari_update_event() {
        Masari_Gateway::do_update_event();
    }

    add_action('woocommerce_thankyou_'.Masari_Gateway::get_id(), 'masari_order_confirm_page');
    add_action('woocommerce_order_details_after_order_table', 'masari_order_page');
    add_action('woocommerce_email_after_order_table', 'masari_order_email');

    function masari_order_confirm_page($order_id) {
        Masari_Gateway::customer_order_page($order_id);
    }
    function masari_order_page($order) {
        if(!is_wc_endpoint_url('order-received'))
            Masari_Gateway::customer_order_page($order);
    }
    function masari_order_email($order) {
        Masari_Gateway::customer_order_email($order);
    }

    add_action('wc_ajax_masari_gateway_payment_details', 'masari_get_payment_details_ajax');
    function masari_get_payment_details_ajax() {
        Masari_Gateway::get_payment_details_ajax();
    }

    add_filter('woocommerce_currencies', 'masari_add_currency');
    function masari_add_currency($currencies) {
        $currencies['Masari'] = __('Masari', 'masari_gateway');
        return $currencies;
    }

    add_filter('woocommerce_currency_symbol', 'masari_add_currency_symbol', 10, 2);
    function masari_add_currency_symbol($currency_symbol, $currency) {
        switch ($currency) {
        case 'Masari':
            $currency_symbol = 'MSR';
            break;
        }
        return $currency_symbol;
    }

    if(Masari_Gateway::use_masari_price()) {

        // This filter will replace all prices with amount in Masari (live rates)
        add_filter('wc_price', 'masari_live_price_format', 10, 3);
        function masari_live_price_format($price_html, $price_float, $args) {
            if(!isset($args['currency']) || !$args['currency']) {
                global $woocommerce;
                $currency = strtoupper(get_woocommerce_currency());
            } else {
                $currency = strtoupper($args['currency']);
            }
            return Masari_Gateway::convert_wc_price($price_float, $currency);
        }

        // These filters will replace the live rate with the exchange rate locked in for the order
        // We must be careful to hit all the hooks for price displays associated with an order,
        // else the exchange rate can change dynamically (which it should for an order)
        add_filter('woocommerce_order_formatted_line_subtotal', 'masari_order_item_price_format', 10, 3);
        function masari_order_item_price_format($price_html, $item, $order) {
            return Masari_Gateway::convert_wc_price_order($price_html, $order);
        }

        add_filter('woocommerce_get_formatted_order_total', 'masari_order_total_price_format', 10, 2);
        function masari_order_total_price_format($price_html, $order) {
            return Masari_Gateway::convert_wc_price_order($price_html, $order);
        }

        add_filter('woocommerce_get_order_item_totals', 'masari_order_totals_price_format', 10, 3);
        function masari_order_totals_price_format($total_rows, $order, $tax_display) {
            foreach($total_rows as &$row) {
                $price_html = $row['value'];
                $row['value'] = Masari_Gateway::convert_wc_price_order($price_html, $order);
            }
            return $total_rows;
        }

    }

    add_action('wp_enqueue_scripts', 'masari_enqueue_scripts');
    function masari_enqueue_scripts() {
        if(Masari_Gateway::use_masari_price())
            wp_dequeue_script('wc-cart-fragments');
        if(Masari_Gateway::use_qr_code())
            wp_enqueue_script('masari-qr-code', MASARI_GATEWAY_PLUGIN_URL.'assets/js/qrcode.min.js');

        wp_enqueue_script('masari-clipboard-js', MASARI_GATEWAY_PLUGIN_URL.'assets/js/clipboard.min.js');
        wp_enqueue_script('masari-gateway', MASARI_GATEWAY_PLUGIN_URL.'assets/js/masari-gateway-order-page.js');
        wp_enqueue_style('masari-gateway', MASARI_GATEWAY_PLUGIN_URL.'assets/css/masari-gateway-order-page.css');
    }

    // [masari-price currency="USD"]
    // currency: BTC, GBP, etc
    // if no none, then default store currency
    function masari_price_func( $atts ) {
        global  $woocommerce;
        $a = shortcode_atts( array(
            'currency' => get_woocommerce_currency()
        ), $atts );

        $currency = strtoupper($a['currency']);
        $rate = Masari_Gateway::get_live_rate($currency);
        if($currency == 'BTC')
            $rate_formatted = sprintf('%.8f', $rate / 1e8);
        else
            $rate_formatted = sprintf('%.5f', $rate / 1e8);

        return "<span class=\"masari-price\">1 MSR = $rate_formatted $currency</span>";
    }
    add_shortcode('masari-price', 'masari_price_func');


    // [masari-accepted-here]
    function masari_accepted_func($atts) {
        $height = $atts['height'];
        $width = $atts['width'];
        return '<img src="'.MASARI_GATEWAY_PLUGIN_URL.'assets/images/masari-accepted-here.png" height="'.$height.'" width="'.$width.'"/>';
    }
    add_shortcode('masari-accepted-here', 'masari_accepted_func');

}

register_deactivation_hook(__FILE__, 'masari_deactivate');
function masari_deactivate() {
    $timestamp = wp_next_scheduled('masari_update_event');
    wp_unschedule_event($timestamp, 'masari_update_event');
}

register_activation_hook(__FILE__, 'masari_install');
function masari_install() {
    global $wpdb;
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . "masari_gateway_quotes";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               order_id BIGINT(20) UNSIGNED NOT NULL,
               payment_id VARCHAR(95) DEFAULT '' NOT NULL,
               currency VARCHAR(6) DEFAULT '' NOT NULL,
               rate BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               amount BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               paid TINYINT NOT NULL DEFAULT 0,
               confirmed TINYINT NOT NULL DEFAULT 0,
               pending TINYINT NOT NULL DEFAULT 1,
               created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (order_id)
               ) $charset_collate;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "masari_gateway_quotes_txids";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
               payment_id VARCHAR(95) DEFAULT '' NOT NULL,
               txid VARCHAR(64) DEFAULT '' NOT NULL,
               amount BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               height MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
               PRIMARY KEY (id),
               UNIQUE KEY (payment_id, txid, amount)
               ) $charset_collate;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "masari_gateway_live_rates";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               currency VARCHAR(6) DEFAULT '' NOT NULL,
               rate BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (currency)
               ) $charset_collate;";
        dbDelta($sql);
    }
}
