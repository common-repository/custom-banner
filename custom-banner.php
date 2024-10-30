<?php

/*
Plugin Name: Custom Banner
Plugin URI: 
Description: The ultimate plugin for creating a customizable, responsive, and swipeable banner.
Version: 1.0.0
Author: Nucliex
Author URI:
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once plugin_dir_path(__FILE__) . 'includes/admin/settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/display.php';

// Enqueue Front-End Scripts
add_action( 'wp_enqueue_scripts', 'custom_banner_enqueue_scripts' );
function custom_banner_enqueue_scripts() {
	if ( !custom_banner_is_enabled() ) {
		return;
	}

	// CSS
	wp_enqueue_style( 'custom_banner', plugin_dir_url(__FILE__) . 'assets/css/cb.min.css', array(), '1.0.0' );

	// JS (if more than 1 message)
	if ( count( get_option('custom_banner_banner_text') ) > 1 ) {

        // Enqueue Swiper
        wp_enqueue_style( 'swiper', plugin_dir_url( __FILE__ ) . 'lib/swiper@11.0.7/swiper-bundle.min.css', array(), '11.0.7', 'all' );
        wp_enqueue_script( 'swiper', plugin_dir_url( __FILE__ ) . 'lib/swiper@11.0.7/swiper-bundle.min.js', array(), '11.0.7', true );

        // Enqueue Banner JS
        wp_enqueue_script( 'custom-banner', plugin_dir_url(__FILE__) . 'assets/js/cb.js', array( 'swiper', 'jquery' ), '1.0.0', true );
        wp_localize_script('custom-banner', 'bannerVars', array(
            'bannerAutoplay' => esc_html(get_option('custom_banner_autoplay', 'on')),
            'bannerDelay' => intval(get_option('custom_banner_delay', '3')),
        ));
	}
}

// Enqueue Admin Scripts
function custom_banner_admin_enqueue_scripts() {
    // CSS
    wp_enqueue_style( 'custom-banner-admin', plugin_dir_url(__FILE__) . 'assets/css/cb-settings.min.css', array(), '1.0.0' );
    wp_enqueue_style( 'custom-banner', plugin_dir_url(__FILE__) . 'assets/css/cb.min.css', array(), '1.0.0' );

    // JS
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'custom-banner-admin', plugin_dir_url(__FILE__) . 'assets/js/cb-settings.min.js', array( 'wp-color-picker', 'jquery' ), '1.0.0', true );
    wp_localize_script('custom-banner-admin', 'customBannerAjax', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('custom_banner_ajax_nonce'),
    ));

    // Enqueue Swiper
    wp_enqueue_style( 'swiper', plugin_dir_url( __FILE__ ) . 'lib/swiper@11.0.7/swiper-bundle.min.css', array(), '11.0.7', 'all' );
    wp_enqueue_script( 'swiper', plugin_dir_url( __FILE__ ) . 'lib/swiper@11.0.7/swiper-bundle.min.js', array(), '11.0.7', true );
}

// Default settings
register_activation_hook(__FILE__, 'custom_banner_activate');
function custom_banner_activate() {
    add_option('custom_banner_enabled', 'on');
    add_option('custom_banner_text_color', '#000000');
    add_option('custom_banner_background_color', '#ffffff');
    add_option('custom_banner_width', 'medium');
    add_option('custom_banner_arrows', 'square');
    add_option('custom_banner_autoplay', 'on');
    add_option('custom_banner_delay', '3');
}

