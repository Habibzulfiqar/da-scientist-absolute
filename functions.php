<?php
/**
 * Da Scientist - Absolute Core
 * functions.php — Theme setup, asset loading, WooCommerce filters, COD logic, RTO tracking.
 *
 * @package DaScientistAbsolute
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// 1. THEME SUPPORT
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'after_setup_theme', function () {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
} );

// ─────────────────────────────────────────────────────────────────────────────
// 2. VITE ASSET ENQUEUE — Dev/Prod Toggle
// Set VITE_DEV_MODE to false before pushing to Hostinger.
// ─────────────────────────────────────────────────────────────────────────────

define( 'VITE_DEV_MODE', true ); // Toggle to false for production build

// ─────────────────────────────────────────────────────────────────────────────
// 2a. VITE REACT REFRESH PREAMBLE — Required for `@vitejs/plugin-react` during dev
// Without this, the React HMR client crashes with "can't detect preamble" exception.
// ─────────────────────────────────────────────────────────────────────────────
add_action( 'wp_head', function () {
    if ( defined( 'VITE_DEV_MODE' ) && VITE_DEV_MODE ) {
        $vite_host = ( isset( $_SERVER['HTTP_HOST'] ) && strpos( $_SERVER['HTTP_HOST'], 'localhost' ) !== false ) ? 'localhost' : '127.0.0.1';
        ?>
        <script type="module">
            import RefreshRuntime from 'http://<?php echo esc_js( $vite_host ); ?>:5173/@react-refresh'
            RefreshRuntime.injectIntoGlobalHook(window)
            window.$RefreshReg$ = () => {}
            window.$RefreshSig$ = () => () => {}
            window.__vite_plugin_react_preamble_installed__ = true
        </script>
        <?php
    }
}, 1 );

add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'dascentist-style', get_stylesheet_uri(), array(), defined( 'VITE_DEV_MODE' ) && VITE_DEV_MODE ? time() : '1.0.0' );

    if ( defined( 'VITE_DEV_MODE' ) && VITE_DEV_MODE ) {
        // Dynamically match the request host (localhost or 127.0.0.1) to avoid CORS issues
        $vite_host = ( isset( $_SERVER['HTTP_HOST'] ) && strpos( $_SERVER['HTTP_HOST'], 'localhost' ) !== false ) ? 'localhost' : '127.0.0.1';
        
        // Load from Vite HMR dev server (run: npm run dev)
        wp_enqueue_script( 'vite-client', "http://{$vite_host}:5173/@vite/client", array(), null, true );
        wp_enqueue_script( 'dascentist-app', "http://{$vite_host}:5173/src/app.jsx", array( 'vite-client' ), null, true );
    } else {
        // Load compiled production bundle (CSS & JS)
        wp_enqueue_style( 'dascentist-app-compiled', get_template_directory_uri() . '/assets/dist/app.css', array(), '1.0.0' );
        wp_enqueue_script( 'dascentist-app', get_template_directory_uri() . '/assets/dist/app.js', array(), '1.0.0', true );
    }

    wp_localize_script( 'dascentist-app', 'daScientistGlobals', array(
        'store_api_url' => esc_url_raw( rest_url( 'wc/store/v1/' ) ),
        'nonce'         => wp_create_nonce( 'wc_store_api' ),
        'checkout_url'  => wc_get_checkout_url(),
        'ajax_url'      => admin_url( 'admin-ajax.php' ),
    ) );
} );

// ─────────────────────────────────────────────────────────────────────────────
// 2b. VITE MODULE TYPE — Required for ES Module scripts (Vite dev + prod)
// WordPress does not add type="module" by default. Without this, Vite's
// HMR client and JSX bundles will silently fail to execute in the browser.
// ─────────────────────────────────────────────────────────────────────────────

add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
    $vite_handles = array( 'vite-client', 'dascentist-app' );
    if ( in_array( $handle, $vite_handles, true ) ) {
        // Replace standard <script src> with <script type="module" crossorigin src>
        $tag = '<script type="module" crossorigin src="' . esc_url( $src ) . '"></script>' . "\n";
    }
    return $tag;
}, 10, 3 );

// ─────────────────────────────────────────────────────────────────────────────
// 3. NONCE RENEWAL ENDPOINT
// Handles expired 24hr WP REST nonces — React intercepts 403 and calls this.
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'rest_api_init', function () {
    register_rest_route( 'dascentist/v1', '/nonce', array(
        'methods'             => 'GET',
        'callback'            => function () {
            return new WP_REST_Response( array(
                'nonce' => wp_create_nonce( 'wc_store_api' ),
            ), 200 );
        },
        'permission_callback' => '__return_true',
    ) );
} );

// ─────────────────────────────────────────────────────────────────────────────
// 4. COD EXCLUSIVITY — Force Cash on Delivery as the only payment gateway
// ─────────────────────────────────────────────────────────────────────────────

add_filter( 'woocommerce_available_payment_gateways', function ( $gateways ) {
    foreach ( $gateways as $id => $gateway ) {
        if ( $id !== 'cod' ) {
            unset( $gateways[ $id ] );
        }
    }
    return $gateways;
} );

// ─────────────────────────────────────────────────────────────────────────────
// 5. FRAUD HOLDING — Set new orders to "Pending Confirmation" instead of Processing
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'woocommerce_checkout_order_processed', function ( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;
    $order->update_status( 'pending', __( 'Awaiting team confirmation before courier booking.', 'dascentist-absolute' ) );
}, 10, 1 );

// ─────────────────────────────────────────────────────────────────────────────
// 6. WHATSAPP / SMS NOTIFICATION — Fire on new order via local Pakistani gateway
// Replace API_KEY and sender values from your .env / wp-config.php constants.
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'woocommerce_checkout_order_processed', function ( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    $phone   = $order->get_billing_phone();
    $name    = $order->get_billing_first_name();
    $message = rawurlencode(
        "Salaam $name! Your Da Scientist order #$order_id has been received. Our team will confirm it with you shortly. Thank you for choosing Da Scientist."
    );

    // Replace with your AlphaSMS / LifetimeSMS gateway endpoint and API key
    $sms_api_key = defined( 'DASCENTIST_SMS_API_KEY' ) ? DASCENTIST_SMS_API_KEY : '';
    $sms_sender  = defined( 'DASCENTIST_SMS_SENDER' ) ? DASCENTIST_SMS_SENDER : 'DaScentist';

    if ( ! empty( $sms_api_key ) && ! empty( $phone ) ) {
        wp_remote_post( 'https://api.alphasms.com.pk/send', array(
            'body' => array(
                'key'     => $sms_api_key,
                'sender'  => $sms_sender,
                'number'  => $phone,
                'message' => $message,
            ),
        ) );
    }
}, 20, 1 );

// ─────────────────────────────────────────────────────────────────────────────
// 7. RTO TRACKING — Flag customers with 2+ return-to-shipper records
// Stores RTO count as user/order meta. Flags repeat offenders for manual review.
// ─────────────────────────────────────────────────────────────────────────────

// Increment RTO counter when order is marked "Cancelled" (courier returned)
add_action( 'woocommerce_order_status_cancelled', function ( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    $phone    = $order->get_billing_phone();
    $rto_key  = 'dascentist_rto_count_' . preg_replace( '/\D/', '', $phone );
    $rto_count = (int) get_option( $rto_key, 0 ) + 1;
    update_option( $rto_key, $rto_count );

    // Flag the order with RTO meta for admin visibility
    $order->update_meta_data( '_dascentist_rto_flagged', $rto_count >= 2 ? 'yes' : 'no' );
    $order->save();
}, 10, 1 );

// Check RTO status when a new order is placed — hold flagged customers for manual review
add_action( 'woocommerce_checkout_order_processed', function ( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    $phone     = $order->get_billing_phone();
    $rto_key   = 'dascentist_rto_count_' . preg_replace( '/\D/', '', $phone );
    $rto_count = (int) get_option( $rto_key, 0 );

    if ( $rto_count >= 2 ) {
        $order->update_meta_data( '_dascentist_rto_flagged', 'yes' );
        $order->add_order_note(
            sprintf(
                __( '⚠️ RTO Alert: This phone number has %d previous return-to-shipper records. Hold for manual review before booking courier.', 'dascentist-absolute' ),
                $rto_count
            )
        );
        $order->save();
    }
}, 30, 1 );

// ─────────────────────────────────────────────────────────────────────────────
// 8. CHECKOUT CUSTOMIZATION: Lahore-Based Timelines & Dual Phone Fields
// ─────────────────────────────────────────────────────────────────────────────

// Add Alternate Phone and refine Email/Phone fields
add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
    // 1. Completely unset redundant fields to match Scents N Stories minimal shopify checkout
    unset( $fields['billing']['billing_company'] );
    unset( $fields['billing']['billing_state'] );
    unset( $fields['billing']['billing_postcode'] );

    // 2. First Name & Last Name (inline row)
    $fields['billing']['billing_first_name']['class'] = array( 'form-row-first' );
    $fields['billing']['billing_first_name']['priority'] = 10;
    
    $fields['billing']['billing_last_name']['class'] = array( 'form-row-last' );
    $fields['billing']['billing_last_name']['priority'] = 20;

    // 3. Country / Region (full row)
    $fields['billing']['billing_country']['class'] = array( 'form-row-wide' );
    $fields['billing']['billing_country']['priority'] = 30;

    // 4. Street Address (full row)
    $fields['billing']['billing_address_1']['class'] = array( 'form-row-wide' );
    $fields['billing']['billing_address_1']['priority'] = 40;

    // 5. Apartment, suite, unit (full row, optional)
    $fields['billing']['billing_address_2']['placeholder'] = _x( 'Area, landmark (optional)', 'placeholder', 'dascentist-absolute' );
    $fields['billing']['billing_address_2']['class'] = array( 'form-row-wide' );
    $fields['billing']['billing_address_2']['priority'] = 50;

    // 6. City & Alternate Phone (inline row)
    $fields['billing']['billing_city']['class'] = array( 'form-row-first' );
    $fields['billing']['billing_city']['priority'] = 60;

    $fields['billing']['billing_alternate_phone'] = array(
        'label'        => __( 'Alternate Phone No.', 'dascentist-absolute' ),
        'placeholder'  => _x( 'e.g. 03XX-XXXXXXX (optional)', 'placeholder', 'dascentist-absolute' ),
        'required'     => false,
        'class'        => array( 'form-row-last' ),
        'clear'        => true,
        'priority'     => 70,
    );

    // 7. Primary Phone (full row, required)
    $fields['billing']['billing_phone']['placeholder'] = _x( '03XX-XXXXXXX', 'placeholder', 'dascentist-absolute' );
    $fields['billing']['billing_phone']['class'] = array( 'form-row-wide' );
    $fields['billing']['billing_phone']['priority'] = 80;

    // 8. Email Address (full row, optional)
    $fields['billing']['billing_email']['required'] = false;
    $fields['billing']['billing_email']['placeholder'] = _x( 'Email address (optional)', 'placeholder', 'dascentist-absolute' );
    $fields['billing']['billing_email']['class'] = array( 'form-row-wide' );
    $fields['billing']['billing_email']['priority'] = 90;

    return $fields;
} );

// Disable WooCommerce Order Comments (Additional Info Block)
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

// Force default checkout country to Pakistan (PK)
add_filter( 'default_checkout_billing_country', function() {
    return 'PK';
} );


// Save Alternate Phone field to WooCommerce order metadata
add_action( 'woocommerce_checkout_update_order_meta', function ( $order_id ) {
    if ( ! empty( $_POST['billing_alternate_phone'] ) ) {
        update_post_meta( $order_id, '_billing_alternate_phone', sanitize_text_field( $_POST['billing_alternate_phone'] ) );
    }
} );

// Expose Alternate Phone in WooCommerce Admin Edit Order screen
add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
    $alt_phone = get_post_meta( $order->get_id(), '_billing_alternate_phone', true );
    if ( $alt_phone ) {
        echo '<p><strong>' . __( 'Alternate Phone', 'dascentist-absolute' ) . ':</strong> ' . esc_html( $alt_phone ) . '</p>';
    }
} );

// Display Lahore shipping timeline banner directly below the checkout table shipping row
add_action( 'woocommerce_review_order_after_shipping', function () {
    ?>
    <tr class="shipping-timeline-notice-row">
        <td colspan="2">
            <div class="checkout-shipping-timeline">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="1" y="3" width="15" height="13" rx="2"/>
                    <polygon points="16 8 20 8 23 11 23 16 16 16"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                <span><strong>Lahore:</strong> Next day delivery (limited areas) | <strong>Other Cities:</strong> 2–4 working days.</span>
            </div>
        </td>
    </tr>
    <?php
} );

// ── RELOCATE COUPON FORM TO RIGHT COLUMN ─────────────────────────────────────

// Inject coupon placeholder directly inside form.checkout
add_action( 'woocommerce_checkout_before_order_review', function() {
    echo '<div id="dascentist-checkout-coupon-holder"></div>';
}, 5 );

// Append script to move coupon form to placeholder on load
add_action( 'wp_footer', function() {
    if ( is_checkout() && ! is_wc_endpoint_url() ) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var couponForm = document.querySelector('form.checkout_coupon');
            var holder = document.getElementById('dascentist-checkout-coupon-holder');
            if (couponForm && holder) {
                holder.appendChild(couponForm);
            }
        });
        </script>
        <?php
    }
} );

// Inject a styled luxury trust content module directly below the Place Order CTA
add_action( 'woocommerce_review_order_after_submit', function() {
    ?>
    <div class="checkout-trust-content">
        <h4 class="trust-title">Why Da Scientist Absolute?</h4>
        <ul class="trust-list">
            <li>
                <span class="trust-icon">&#x2767;</span>
                <div class="trust-text">
                    <strong>Authenticity Guaranteed</strong>
                    <p>Compounded directly in small batches at our Lahore laboratory. 100% pure raw ingredients.</p>
                </div>
            </li>
            <li>
                <span class="trust-icon">&#x2609;</span>
                <div class="trust-text">
                    <strong>Tamper-Proof Seal</strong>
                    <p>Every bottle is hand-packed, batch-serialized, and shipped in our secure luxury signatures box.</p>
                </div>
            </li>
            <li>
                <span class="trust-icon">&#x2698;</span>
                <div class="trust-text">
                    <strong>Nationwide Delivery</strong>
                    <p>Cash on Delivery via Trax and TCS. Standard delivery takes 2–4 working days.</p>
                </div>
            </li>
        </ul>
    </div>
    <?php
} );



