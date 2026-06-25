<?php
/**
 * Da Scientist - Absolute functions and definitions
 *
 * @package DaScientistAbsolute
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Enable support for WooCommerce
add_action( 'after_setup_theme', function() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
});

// Enqueue scripts and localize core WooCommerce REST data variables
add_action( 'wp_enqueue_scripts', function() {
    // If local dev environment, load Vite dev client or standard build
    // For production/transfer, enqueues the compiled distribution bundle
    $asset_path = get_template_directory_uri() . '/assets/dist/app.js';
    
    wp_enqueue_style( 'dascentist-style', get_stylesheet_uri(), array(), '1.0.0' );
    wp_enqueue_script( 'dascentist-app', $asset_path, array(), '1.0.0', true );

    wp_localize_script( 'dascentist-app', 'daScientistGlobals', array(
        'store_api_url' => esc_url_raw( rest_url( 'wc/store/v1/' ) ),
        'nonce'         => wp_create_nonce( 'wp_rest' ),
        'checkout_url'  => wc_get_checkout_url(),
        'ajax_url'      => admin_url( 'admin-ajax.php' )
    ));
});

// Lightweight REST Endpoint to refresh expired Rest Nonces (lasts ~24 hours)
add_action( 'rest_api_init', function () {
    register_rest_route( 'dascentist/v1', '/nonce', array(
        'methods'             => 'GET',
        'callback'            => function() {
            return new WP_REST_Response( array(
                'nonce' => wp_create_nonce( 'wp_rest' )
            ), 200 );
        },
        'permission_callback' => '__return_true'
    ));
});
