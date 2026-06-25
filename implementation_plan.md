# Implementation Plan: High-Performance Hybrid WordPress/React Architecture (Da Scientist - Plan B)

Set up a high-converting, asset-optimized hybrid e-commerce store for the luxury fragrance brand **Da Scientist** (`dascentist.com`). This plan utilizes a monolithic WooCommerce core (for stability, SEO, and native Pakistani courier support) augmented with a Vite-bundled JavaScript layer (React or Alpine.js) for an app-like browsing experience. The architecture is hardened for a Hostinger Business Plan to survive severe ad-traffic spikes, with lightweight trust controls suited to a new local brand.

---

## User Review Required

> [!IMPORTANT]
> **Vite Deployment Workflow:**
> Front-end development assets will reside inside `/src` within the custom theme directory (`/wp-content/themes/da-scientist-absolute/`) and compile down to a single minified bundle `/assets/dist/app.js` using Vite.

> [!IMPORTANT]
> **Hostinger Cache Configuration:**
> To survive sharp traffic surges from Facebook and Instagram ads, **LiteSpeed Cache (LSCache)** must be configured at the server level with Page and Object Caching active. This serves static HTML to ad referrals instantly, saving server RAM exclusively for dynamic cart REST API calls.

---

## Proposed Changes

### Step 0: Theme Scaffolding & Asset Bundling
Set up the custom scratch theme architecture and configure the client-side compiler.

#### [NEW] `wp-content/themes/da-scientist-absolute/`
The root directory of your custom, coded-from-scratch theme.

1. **Initialize Theme Core**: Create clean base files (`style.css`, `index.php`, `functions.php`, `header.php`, `footer.php`).
   * Inside `style.css`, initialize the custom theme header block exactly as follows:
     ```css
     /*
     Theme Name: Da Scientist - Absolute Core
     Theme URI: https://dascentist.com
     Author: Habib Zulfiqar
     Description: A custom, scratch-coded hybrid WordPress theme utilizing a React/Vite interactive layer and native WooCommerce Store API loops for a luxury fragrance storefront.
     Version: 1.0.0
     Text Domain: dascentist-absolute
     */
     ```
2. **Configure Vite Bundler**: Set up `package.json` and `vite.config.js` inside the theme folder to compile code down to a single distribution asset.
3. **Enqueue & Localize Script**: Code the loading block inside `functions.php` to hook the compiled build while passing global routing variables to JavaScript:
   ```php
   wp_enqueue_script('dascentist-app', get_template_directory_uri() . '/assets/dist/app.js', array(), '1.0', true);
   wp_localize_script('dascentist-app', 'daScientistGlobals', array(
       'store_api_url' => esc_url_raw(rest_url('wc/store/v1/')),
       'nonce'         => wp_create_nonce('wp_rest'),
       'checkout_url'  => wc_get_checkout_url()
   ));
   ```
4. **Session Nonce Expiration Handling**: Add a small JavaScript helper that refetches a fresh nonce via a lightweight authenticated endpoint if a Store API call ever returns `403 Forbidden`, ensuring that returning users (e.g. from an abandoned cart the next day) do not experience broken buttons.

---

### Step 1: REST API Data Layer & Component Mounting
Mount dynamic frontend rendering blocks onto empty container DOM targets inside your custom WordPress templates.

#### [MODIFY] `archive-product.php` & `single-product.php`
1. **Drop React Targets with no-JS Fallbacks**: Replace legacy loops with target containers like `<div id="perfume-grid-root"></div>` and `<div id="luxury-cart-drawer"></div>`. Keep a minimal server-rendered product title, price, and image inside each container as a no-JS fallback so crawlers and slow connections see core content before hydration.
2. **Consume WooCommerce Store API**: Program client components to query the native `/wp-json/wc/store/v1/products` endpoints for instantaneous client-side product filtering, sorting, and real-time variation switches.
3. **AJAX Cart Operations**: Wire addition buttons directly to the WooCommerce Cart Store API endpoint (`/cart/add-item`) so the cart drawer updates instantly without page reloads.

---

### Step 2: Native Checkout Escape Hatch & Tracking Lock-In
Bypass the headless checkout trap by routing transactions back through the native monolithic engine, ensuring flawless script and plugin behavior.

#### [MODIFY] `page-checkout.php`
1. **The Handshake Redirect**: When a customer clicks "Proceed to Checkout" inside your dynamic cart drawer, execute an immediate redirect:
   ```javascript
   window.location.href = daScientistGlobals.checkout_url;
   ```
2. **Meta Ads Tracking Layer**: By utilizing the standard WooCommerce checkout page loop, the official **Facebook for WooCommerce** plugin hooks seamlessly into the DOM. `InitiateCheckout` and `Purchase` events fire natively with 100% data deduplication via server-side Meta Conversions API (CAPI).
3. **Pakistani Courier Automation**: Because the transactional loop remains inside core WooCommerce, standard shipping plugins for local carriers (**Trax, Leopards, Rider, or TCS**) work natively. City dropdowns, cash-handling fees, and bookings map correctly to their courier portals without custom integrations.

---

### Step 3: COD Trust Controls & Abandoned Cart Recovery
Protect delivery logistics from high return-to-shipper (RTO) rates common in the local market without creating checkout friction.

#### [NEW] Custom COD Flow and WhatsApp/SMS Notifications
1. **COD Exclusive Gate**: Limit the primary storefront checkout gateway exclusively to Cash on Delivery.
2. **Pending Confirmation Loop**: On order placement, set order status to `"Pending Confirmation"` rather than auto-advancing to `"Processing"`. Trigger a WhatsApp Business API or SMS *notification* (not a verification code) — a friendly *"We're confirming your order, our team will reach out shortly"* message — so the customer feels reassured.
3. **Manual Validation Dashboard**: Build a simple admin dashboard view listing all `"Pending Confirmation"` orders so the team can do a quick manual call/WhatsApp confirmation before booking the courier.
4. **RTO Prevention Tracking**: Track repeat RTO (return-to-shipper) customers by phone number. After 2 RTOs, flag the customer record so future orders from that number are queued for manual review.
5. **Abandoned Cart Recovery**: Install an abandoned cart recovery plugin (e.g. CartFlows or FunnelKit) tied to WooCommerce's native cart session, sending a follow-up email or WhatsApp message after ~1 hour of inactivity.

---

### Step 4: Design Brief & Performance Tuning (Luxury UX)
Apply the visual guidelines directly into your compiled styles, ensuring fast performance on mobile screens where 90%+ of ad traffic arrives.

* **Typography**: Clean serif display headlines paired with an ultra-clean grotesque sans-serif body scale.
* **Palette**: Charcoal (`#1C1A17`), Ivory (`#F5F1EA`), and an elegant amber or botanical accent.
* **Micro-interactions**: Implement CSS-driven AJAX add-to-cart feedback animations, smooth slide-out cart drawers, and subtle fade effects on image variations.
* **SEO Integrations**: Confirm RankMath or Yoast is installed and active, generating Product/Offer schema and an XML sitemap automatically. Add a lightweight "Notes & Stories" editorial template.
* **Performance Targets**: Minify all theme assets, combine external scripts, optimize images with WebP formats, and force LiteSpeed page compilation to hit a mobile Lighthouse LCP score of **under 1.5 seconds**.

---

### Step 5: Staging & Production Hardening Checklist
* Clone the live site to a staging subdomain before any future core/plugin update.
* Move the COD/RTO-tracking logic into a small custom plugin rather than `functions.php` to ensure it survives theme updates.
* Install a security baseline: a WAF/firewall plugin (e.g. Wordfence), disable XML-RPC if unused, and enforce strong admin passwords + 2FA on the WP admin login.
* Set up automated daily backups via Hostinger dashboard.
* Confirm `wp-config.php` file editing is disabled in the dashboard (`DISALLOW_FILE_EDIT`).

---

## Verification Plan

### Automated / Integration Tests
* Run production compilation commands via Vite and verify that `app.js` builds correctly into `/assets/dist/`.
* Inspect network logs to ensure that your JavaScript frontend fetches and posts payload blocks safely to `/wp-json/wc/store/v1/` without producing HTTP 401 or 403 authorization errors.
* Confirm that LiteSpeed Object Caching reduces database query execution times under simulated concurrent traffic.

### Manual Verification
* Access the store layout from a mobile device, add product variants to the cart drawer, and verify that the cart updates immediately without a page refresh.
* Proceed to checkout via COD, check that local courier city modules parse correctly, and confirm the Meta Pixel Helper extension registers tracking events accurately.
* Verify that custom text confirmations arrive on a local mobile carrier in real-time, moving the draft order status into the main fulfillment queue automatically.
