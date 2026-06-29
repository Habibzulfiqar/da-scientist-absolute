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

// ── PUBLIC SECURE ORDER TRACKING AJAX ENDPOINT ─────────────────────────────────

add_action( 'wp_ajax_dascentist_track_order', 'dascentist_ajax_track_order' );
add_action( 'wp_ajax_nopriv_dascentist_track_order', 'dascentist_ajax_track_order' );

function dascentist_ajax_track_order() {
    // Basic sanitization
    $order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( trim( $_POST['order_id'] ) ) : '';
    $contact  = isset( $_POST['contact'] )  ? sanitize_text_field( trim( $_POST['contact'] ) )  : '';

    if ( empty( $order_id ) || empty( $contact ) ) {
        wp_send_json_error( array( 'message' => 'Please enter both your Order ID and contact details.' ) );
    }

    // Attempt to load order
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        wp_send_json_error( array( 'message' => 'Order not found. Please verify the ID.' ) );
    }

    // Verify contact matches billing email OR billing phone
    $billing_email = strtolower( trim( $order->get_billing_email() ) );
    $billing_phone = preg_replace( '/[^0-9]/', '', $order->get_billing_phone() ); // Strip symbols
    $input_contact = strtolower( trim( $contact ) );
    $input_phone   = preg_replace( '/[^0-9]/', '', $contact );

    $email_matches = ( ! empty( $billing_email ) && $input_contact === $billing_email );
    $phone_matches = ( ! empty( $billing_phone ) && ! empty( $input_phone ) && strpos( $billing_phone, $input_phone ) !== false || strpos( $input_phone, $billing_phone ) !== false );

    if ( ! $email_matches && ! $phone_matches ) {
        wp_send_json_error( array( 'message' => 'The contact details entered do not match this Order ID.' ) );
    }

    // Format safe response (No customer addresses or full names exposed)
    $status  = $order->get_status();
    $created = $order->get_date_created()->date_i18n( 'F j, Y, g:i a' );
    
    // Retrieve items list
    $items = array();
    foreach ( $order->get_items() as $item_id => $item ) {
        $items[] = $item->get_name() . ' x ' . $item->get_quantity();
    }

    // Map order status to human friendly timelines
    $friendly_status = 'Pending';
    $step = 1;
    if ( in_array( $status, array( 'pending', 'on-hold', 'failed' ) ) ) {
        $friendly_status = 'Order Received & Awaiting Validation';
        $step = 1;
    } elseif ( in_array( $status, array( 'processing' ) ) ) {
        $friendly_status = 'Compounding & Preparing Shipment';
        $step = 2;
    } elseif ( in_array( $status, array( 'completed' ) ) ) {
        $friendly_status = 'Dispatched via Courier';
        $step = 3;
    } elseif ( in_array( $status, array( 'cancelled', 'refunded' ) ) ) {
        $friendly_status = 'Cancelled / Returned';
        $step = 4;
    }

    wp_send_json_success( array(
        'order_id'   => $order->get_id(),
        'date'       => $created,
        'status'     => $friendly_status,
        'step'       => $step,
        'items'      => $items,
        'total'      => html_entity_decode( strip_tags( $order->get_formatted_order_total() ) ),
    ) );
}

// ── CUSTOM CONTACT FORM SUBMISSION AJAX HANDLER ───────────────────────────────

add_action( 'wp_ajax_dascentist_submit_contact', 'dascentist_ajax_submit_contact' );
add_action( 'wp_ajax_nopriv_dascentist_submit_contact', 'dascentist_ajax_submit_contact' );

function dascentist_ajax_submit_contact() {
    $name     = isset( $_POST['name'] )     ? sanitize_text_field( trim( $_POST['name'] ) )     : '';
    $contact  = isset( $_POST['contact'] )  ? sanitize_text_field( trim( $_POST['contact'] ) )  : '';
    $interest = isset( $_POST['interest'] ) ? sanitize_text_field( trim( $_POST['interest'] ) ) : '';
    $message  = isset( $_POST['message'] )  ? sanitize_textarea_field( trim( $_POST['message'] ) ) : '';

    if ( empty( $name ) || empty( $contact ) || empty( $message ) ) {
        wp_send_json_error( array( 'message' => 'Please fill in all required fields.' ) );
    }

    // In production, we send an email to concierge@dascentist.com
    $to      = get_option( 'admin_email' );
    $subject = "[Da Scientist Correspondence] - {$interest} from {$name}";
    $body    = "Correspondence received:\n\n"
             . "Name: {$name}\n"
             . "Contact Info: {$contact}\n"
             . "Topic: {$interest}\n\n"
             . "Message:\n{$message}\n\n"
             . "Sent via Da Scientist Client Relations Portal.";
    
    $headers = array( 'Content-Type: text/plain; charset=UTF-8' );

    // Fire email (fails silently on localhost if no SMTP setup, returns true on server)
    @wp_mail( $to, $subject, $body, $headers );

    wp_send_json_success( array( 'message' => 'Correspondence successfully dispatched.' ) );
}

// ── PROGRAMMATIC LUXURY PAGES INITIALIZATION ─────────────────────────────────

add_action( 'init', function() {
    // Only run this execution block once to optimize server database loads
    if ( get_option( 'dascentist_pages_setup_complete_v3' ) ) {
        return;
    }

    $essential_pages = array(
        'faq' => array(
            'title'   => 'FAQ',
            'content' => '<!-- React Accodion Mount Root -->' . "\n" . '<div id="faq-accordion-root"></div>',
        ),
        'track-order' => array(
            'title'   => 'Track Your Order',
            'content' => '<!-- React Tracking Form Mount Root -->' . "\n" . '<div id="order-track-root"></div>',
        ),
        'contact' => array(
            'title'   => 'Contact',
            'content' => '<!-- React Contact Form Mount Root -->' . "\n" . '<div id="contact-root"></div>',
        ),
        'shipping-policy' => array(
            'title'   => 'Shipping Policy',
            'content' => '
<div class="luxury-policy-page">
    <h2>Shipping &amp; Delivery Policy</h2>
    <p>At Da Scientist Absolute, each bottle is compounded and dispatched from our central laboratory in Lahore, Punjab.</p>
    
    <table class="luxury-policy-table">
        <thead>
            <tr>
                <th>Destination</th>
                <th>Delivery Timeline</th>
                <th>Shipping Charges</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Lahore</strong></td>
                <td>Next Working Day (for orders processed before 2PM)</td>
                <td>Free above ₨5,000 / ₨250 Flat Fee</td>
            </tr>
            <tr>
                <td><strong>National (Major Cities)</strong></td>
                <td>2 – 4 Working Days</td>
                <td>Free above ₨5,000 / ₨250 Flat Fee</td>
            </tr>
            <tr>
                <td><strong>National (Remote Areas)</strong></td>
                <td>3 – 5 Working Days</td>
                <td>Free above ₨5,000 / ₨250 Flat Fee</td>
            </tr>
        </tbody>
    </table>
    
    <p>Orders are shipped using secure, batch-serialized, tamper-proof signature packaging via Trax, Leopards, or TCS to guarantee scent integrity from laboratory to address.</p>
</div>',
        ),
        'returns-exchange' => array(
            'title'   => 'Returns & Exchange',
            'content' => '
<div class="luxury-policy-page">
    <h2>Returns &amp; Exchange Policy</h2>
    <p>We take pride in our formulations. If you are not completely satisfied with your selection, we offer a <strong>7-day hassle-free exchange</strong> from the date of delivery.</p>
    
    <h3>Guidelines for Exchange</h3>
    <ul>
        <li>The fragrance bottle must be returned in its original Signature Box.</li>
        <li>The external serialized seal and sticker must remain untampered and unbroken.</li>
        <li>Exchanges can be made for any other scent of equal value, or applied as credit towards premium formulations.</li>
    </ul>
    
    <p>To request an exchange, contact Client Relations via WhatsApp. We will coordinate a reverse-pickup courier directly to your address.</p>
</div>',
        ),
        'privacy-policy' => array(
            'title'   => 'Privacy Policy',
            'content' => '
<div class="luxury-policy-page">
    <h2>Privacy Policy</h2>
    <p>Your privacy is of utmost importance to the house of Da Scientist Absolute. We collect only transaction-essential billing information (Name, Delivery Address, Contact Number) to fulfill shipping and courier routing.</p>
    <p>We do not store financial payment card details, as all transactions are conducted securely via Cash on Delivery or verified bank transfer. Your contact details are never shared with third-party networks outside our booked shipping couriers.</p>
</div>',
        ),
        'terms' => array(
            'title'   => 'Terms of Service',
            'content' => '
<div class="luxury-policy-page">
    <h2>Terms of Service</h2>
    <p>By browsing this storefront or placing an order, you agree to comply with the terms of Da Scientist Absolute. All products are intended for private personal use only.</p>
    <p>We reserve the right to decline orders that fail standard validation checks (invalid contact numbers or addresses). Prices and scent descriptions are subject to change without prior notice.</p>
</div>',
        ),
        'our-philosophy' => array(
            'title'   => 'Our Philosophy',
            'content' => '
<div class="luxury-policy-page">
    <div class="luxury-quote-block">
        <p>"At Da Scientist Absolute, perfumery is not a commercial exercise—it is a private compounding craft. We treat scent as a personal confession, an invisible layer of character that speaks before you do."</p>
    </div>
    
    <div class="luxury-section-card">
        <span class="luxury-card-num">01</span>
        <h3>Compounding in Small Batches</h3>
        <p>Every single formulation is aged, blended, and bottled by hand inside our Lahore laboratory. We refuse industrial mass-production pipelines, compounding only in limited volumes to protect the integrity of active botanicals.</p>
    </div>

    <div class="luxury-section-card">
        <span class="luxury-card-num">02</span>
        <h3>The Extrait Standard</h3>
        <p>While standard fragrances limit oil concentration to 15% (Eau de Parfum), our formulations start at a minimum of 30% to 40% pure oil concentration. This premium standard ensures exceptional projection, longevity, and depth on the skin.</p>
    </div>
</div>',
        ),
        'the-ingredients' => array(
            'title'   => 'The Ingredients',
            'content' => '
<div class="luxury-policy-page">
    <p>We build our formulations using premium raw ingredients selected for structural depth, olfactory character, and skin compatibility.</p>
    
    <div class="luxury-ingredients-grid">
        <div class="ingredient-card">
            <span class="ingredient-num">I. ASSAM OUD</span>
            <span class="ingredient-name">Assam Oud</span>
            <p>Earthy, warm, complex wood resins distilled directly from choice botanicals.</p>
        </div>
        <div class="ingredient-card">
            <span class="ingredient-num">II. TURKISH ROSE</span>
            <span class="ingredient-name">Turkish Rose</span>
            <p>Rich, velvet floral absolute harvested during the first morning light.</p>
        </div>
        <div class="ingredient-card">
            <span class="ingredient-num">III. MYSORE SANDALWOOD</span>
            <span class="ingredient-name">Santal Blanc</span>
            <p>Smooth, creamy Mysore sandalwood that provides a warm, lingering base.</p>
        </div>
        <div class="ingredient-card">
            <span class="ingredient-num">IV. MOLECULAR AMBER</span>
            <span class="ingredient-name">Amber Absolute</span>
            <p>Clean, warm compounds that adapt to skin chemistry, projecting a private signature scent.</p>
        </div>
    </div>
</div>',
        ),
        'sustainability' => array(
            'title'   => 'Sustainability',
            'content' => '
<div class="luxury-policy-page">
    <div class="luxury-quote-block">
        <p>"True luxury respects the earth. Our commitment to sustainability governs how we harvest, bottle, and distribute our compounds."</p>
    </div>

    <div class="luxury-section-card">
        <span class="luxury-card-num">01</span>
        <h3>Botanical Sourcing</h3>
        <p>We work closely with family-owned farms and organic collectors to source raw materials, ensuring that harvests are conducted ethically without depleting local bio-systems.</p>
    </div>

    <div class="luxury-section-card">
        <span class="luxury-card-num">02</span>
        <h3>Recyclable signature Materials</h3>
        <p>Our bottles are constructed from heavy, reusable cosmetic glass. All outer shipping boxes are hand-crafted using 100% biodegradable FSC-certified paper, assembled without plastics or non-recyclable coatings.</p>
    </div>
</div>',
        ),
        'art-of-layering' => array(
            'title'   => 'The Art of Layering',
            'content' => '
<div class="luxury-policy-page">
    <div class="luxury-quote-block">
        <p>Scent layering is the practice of combining two or more fragrances to craft a custom signature that belongs exclusively to you.</p>
    </div>

    <div class="luxury-section-card">
        <h3>How to Layer</h3>
        <p>Always apply the heaviest fragrance first. Start by spraying rich, woody, or ambery notes (like Oud or Sandalwood) as the foundation, allowing them to settle on the skin. Follow with a lighter citrus or fresh floral scent on top.</p>
        <p>This allows the base notes to anchor the top notes, extending projection and adding multidimensional depth.</p>
    </div>
</div>',
        ),
        'understanding-oud' => array(
            'title'   => 'Understanding Oud',
            'content' => '
<div class="luxury-policy-page">
    <div class="luxury-quote-block">
        <p>Oud—often referred to as liquid gold—is one of the most expensive and sought-after raw materials in perfumery.</p>
    </div>

    <div class="luxury-section-card">
        <h3>The Origin</h3>
        <p>Oud is distilled from the resinous heartwood of Aquilaria trees. When the wood undergoes a specific biological infection, it produces a dark, dense resin to protect itself. This resin-saturated wood is then aged and steam-distilled to yield the precious oil.</p>
        <p>Its character is warm, smoky, complex, and intensely wood-centered, providing an unmatched foundation for luxury extraits.</p>
    </div>
</div>',
        ),
        'fragrance-memory' => array(
            'title'   => 'Fragrance and Memory',
            'content' => '
<div class="luxury-policy-page">
    <div class="luxury-quote-block">
        <p>The sense of smell is the only human sense that bypasses standard cognitive processing, connecting directly to the brain\'s emotional center.</p>
    </div>

    <div class="luxury-section-card">
        <h3>The Olfactory Bridge</h3>
        <p>When you inhale a scent, the olfactory receptors send signals straight to the amygdala and hippocampus—the areas responsible for emotional recall and memory storage.</p>
        <p>This biological pathway explains why a single trace of sandalwood or fresh citrus can instantly transport you back to a specific moment, person, or time in your life.</p>
    </div>
</div>',
        ),
        'how-to-choose' => array(
            'title'   => 'How to Choose',
            'content' => '
<div class="luxury-policy-page">
    <div class="luxury-quote-block">
        <p>Choosing a fragrance online can be challenging. We recommend selecting a scent based on your desired mood and environment.</p>
    </div>

    <div class="luxury-ingredients-grid">
        <div class="ingredient-card">
            <span class="ingredient-num">ARCHETYPE A</span>
            <span class="ingredient-name">Citrus &amp; Fresh</span>
            <p>Energetic, clean, and modern. Best suited for daytime wear and warmer climates.</p>
        </div>
        <div class="ingredient-card">
            <span class="ingredient-num">ARCHETYPE B</span>
            <span class="ingredient-name">Floral &amp; Rose</span>
            <p>Romantic, complex, and classical. Perfect for evening events and personal signature wear.</p>
        </div>
        <div class="ingredient-card">
            <span class="ingredient-num">ARCHETYPE C</span>
            <span class="ingredient-name">Woody &amp; Oud</span>
            <p>Rich, warm, smoky, and mysterious. Best for cooler seasons or formal occasions.</p>
        </div>
    </div>
</div>',
        )
    );

    // Only run this execution block once to optimize server database loads
    if ( get_option( 'dascentist_pages_setup_complete_v8' ) ) {
        return;
    }

    // 1. Separate Static Pages from Blog Articles
    $static_pages = array(
        'faq'              => $essential_pages['faq'],
        'track-order'      => $essential_pages['track-order'],
        'contact'          => $essential_pages['contact'],
        'shipping-policy'  => $essential_pages['shipping-policy'],
        'returns-exchange' => $essential_pages['returns-exchange'],
        'privacy-policy'   => $essential_pages['privacy-policy'],
        'terms'            => $essential_pages['terms'],
        'our-philosophy'   => $essential_pages['our-philosophy'],
        'the-ingredients'  => $essential_pages['the-ingredients'],
        'sustainability'   => $essential_pages['sustainability']
    );

    $blog_posts = array(
        'art-of-layering'   => $essential_pages['art-of-layering'],
        'understanding-oud' => $essential_pages['understanding-oud'],
        'fragrance-memory'  => $essential_pages['fragrance-memory'],
        'how-to-choose'     => $essential_pages['how-to-choose']
    );

    // 2. Programmatically create the "The Journal" posts index page shell
    $blog_page = get_page_by_path( 'blog' );
    if ( ! $blog_page ) {
        $blog_id = wp_insert_post( array(
            'post_type'   => 'page',
            'post_title'  => 'The Journal',
            'post_status' => 'publish',
            'post_name'   => 'blog'
        ) );
        if ( $blog_id && ! is_wp_error( $blog_id ) ) {
            update_option( 'page_for_posts', $blog_id );
        }
    } else {
        update_option( 'page_for_posts', $blog_page->ID );
    }

    // 3. Create or Overwrite Static Pages (Force updates layout revisions)
    foreach ( $static_pages as $slug => $page_data ) {
        $page_check = get_page_by_path( $slug );
        $new_page = array(
            'post_type'    => 'page',
            'post_title'   => $page_data['title'],
            'post_content' => $page_data['content'],
            'post_status'  => 'publish',
            'post_name'    => $slug
        );
        if ( ! $page_check ) {
            wp_insert_post( $new_page );
        } else {
            $new_page['ID'] = $page_check->ID;
            wp_update_post( $new_page );
        }
    }

    // 4. Create or Overwrite Blog Articles
    foreach ( $blog_posts as $slug => $post_data ) {
        $post_check = get_page_by_path( $slug, OBJECT, 'post' );
        $new_post = array(
            'post_type'    => 'post',
            'post_title'   => $post_data['title'],
            'post_content' => $post_data['content'],
            'post_status'  => 'publish',
            'post_name'    => $slug
        );
        if ( ! $post_check ) {
            $post_id = wp_insert_post( $new_post );
            if ( $post_id && ! is_wp_error( $post_id ) ) {
                $journal_cat = get_term_by( 'name', 'The Journal', 'category' );
                if ( ! $journal_cat ) {
                    $new_cat = wp_insert_term( 'The Journal', 'category' );
                    if ( ! is_wp_error( $new_cat ) ) {
                        wp_set_post_categories( $post_id, array( $new_cat['term_id'] ) );
                    }
                } else {
                    wp_set_post_categories( $post_id, array( $journal_cat->term_id ) );
                }
            }
        } else {
            $new_post['ID'] = $post_check->ID;
            wp_update_post( $new_post );
        }
    }

    // 5. Clean Flush URL rewrite rules
    flush_rewrite_rules( true );

    // Lock setup flag in options table
    update_option( 'dascentist_pages_setup_complete_v8', true );
} );
