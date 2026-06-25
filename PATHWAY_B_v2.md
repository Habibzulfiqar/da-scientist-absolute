# `PATHWAY_B.md` (v2 — WordPress/WooCommerce Hybrid, Hardened)

## 🌌 The Vision: Da Scientist

A high-converting, asset-optimized hybrid e-commerce store for the luxury fragrance brand **Da Scientist** (`dascentist.com`). Monolithic WooCommerce core (for stability, SEO, and native Pakistani courier support) + a Vite-bundled JS layer (React/Alpine) for an app-like browsing feel. Built to survive heavy Meta/Instagram ad traffic on a Hostinger Business plan, with lightweight — not friction-heavy — COD trust controls suited to a new local brand.

---

## 🛠️ The Tech Stack Architecture

* **Core engine:** WordPress + WooCommerce (server-rendered, SEO-native).
* **Interactivity layer:** Vite-bundled React or Alpine.js, mounted onto specific DOM targets — not a full headless rewrite.
* **Checkout:** Native WooCommerce checkout (no headless checkout trap).
* **Hosting:** Hostinger Business Plan + **LiteSpeed Cache** (page + object caching) for ad-traffic spikes.
* **Shipping:** Native Trax / Leopards / Rider / TCS plugins.
* **Tracking:** Facebook for WooCommerce plugin (CAPI + Pixel, deduplicated) + GA4.
* **Payments:** Cash on Delivery only, with lightweight trust/fraud controls (see Step 3 below — no OTP).

---

## ⏱️ Phase 1: Master Timeline

| Step | Task | Target Time | Who Executes? | Status |
| --- | --- | --- | --- | --- |
| **Step 0** | Theme Scaffolding & Vite Bundling | 45 Mins | **AI Agent** | 🛑 Waiting |
| **Step 1** | REST Data Layer & Component Mounting | 1 Hour | **AI Agent** | 🛑 Waiting |
| **Step 2** | Native Checkout + Tracking Lock-In | 45 Mins | **AI Agent** | 🛑 Waiting |
| **Step 3** | COD Trust Controls (no OTP) + Abandoned Cart Recovery | 45 Mins | **AI Agent** | 🛑 Waiting |
| **Step 4** | Design Brief + Performance Tuning | 1 Hour | **AI Agent + You** | 🛑 Waiting |
| **Step 5** | Staging → Production Hardening Checklist | 30 Mins | **You** | 🛑 Waiting |
| **🚀 LIVE** | **Launch Instagram/Meta Ads** | **Total: ~5 Hours** | **You** | 🛑 Waiting |

---

## 🚦 Phase 2: Exact Implementation Prompts

### Step 0: Theme Scaffolding & Asset Bundling

```text
PROMPT FOR THE AI AGENT:
Create a custom WordPress theme from scratch for the luxury fragrance brand "Da Scientist" at
wp-content/themes/da-scientist-hybrid/.

1. Create base files: style.css, index.php, functions.php, header.php, footer.php.
2. Set up package.json and vite.config.js inside the theme folder, compiling to a single
   distribution asset at /assets/dist/app.js.
3. Enqueue and localize the script in functions.php:

wp_enqueue_script('dascentist-app', get_template_directory_uri() . '/assets/dist/app.js', array(), '1.0', true);
wp_localize_script('dascentist-app', 'daScientistGlobals', array(
    'store_api_url' => esc_url_raw(rest_url('wc/store/v1/')),
    'nonce'         => wp_create_nonce('wp_rest'),
    'checkout_url'  => wc_get_checkout_url()
));

4. Note: the wp_rest nonce expires roughly every 24 hours. Add a small JS helper that refetches
   a fresh nonce via a lightweight authenticated endpoint if a Store API call ever returns 403,
   so long-lived sessions (e.g. an abandoned cart reopened the next day) don't silently break.
```

---

### Step 1: REST API Data Layer & Component Mounting

```text
PROMPT FOR THE AI AGENT:
Mount dynamic frontend components onto archive-product.php and single-product.php.

1. Replace legacy product loops with target containers, e.g. <div id="perfume-grid-root"></div>
   and <div id="luxury-cart-drawer"></div> — but keep a minimal server-rendered product title,
   price, and image inside each container as a no-JS fallback, so crawlers and slow connections
   always see core content even before the JS bundle hydrates.
2. Query /wp-json/wc/store/v1/products for client-side filtering, sorting, and variation switching.
3. Wire add-to-cart buttons to the Store API's /cart/add-item endpoint so the cart drawer updates
   instantly without a page reload.
```

---

### Step 2: Native Checkout & Tracking Lock-In

```text
PROMPT FOR THE AI AGENT:
Route checkout back through native WooCommerce — do not build a custom headless checkout.

1. On "Proceed to Checkout" in the cart drawer, redirect:
   window.location.href = daScientistGlobals.checkout_url;
2. Install and configure the official "Facebook for WooCommerce" plugin so InitiateCheckout and
   Purchase events fire natively with server-side CAPI deduplication. Also wire GA4 via its
   official WooCommerce integration or a server-side GTM container — confirm which before building.
3. Install and configure native shipping plugins for Trax, Leopards, Rider, and TCS so city
   dropdowns, COD handling fees, and courier bookings work through core WooCommerce without
   custom code.
```

---

### Step 3: Lightweight COD Trust Controls + Abandoned Cart Recovery

*(Replaces OTP verification — too much friction for a new local brand still building trust, and an added cost per order with no proven fraud reduction at this stage.)*

```text
PROMPT FOR THE AI AGENT:
Configure Cash on Delivery as the sole payment method, with lightweight trust controls suited to
a new D2C brand — no OTP gate.

1. Limit the storefront checkout gateway to Cash on Delivery only.
2. On order placement, set status to "Pending Confirmation" rather than auto-advancing to
   "Processing". Trigger a WhatsApp Business API or SMS *notification* (not a verification code) —
   a friendly "We're confirming your order, our team will reach out shortly" message — so the
   customer feels reassured without doing any extra work.
3. Build a simple admin dashboard view (or use a plugin like WooCommerce Order Status Manager)
   listing all "Pending Confirmation" orders so the team can do a quick manual call/WhatsApp
   confirmation before pushing to courier booking. This is lower-cost and lower-friction than OTP
   while still catching fake/joke orders.
4. Track repeat RTO (return-to-shipper) customers by phone number. After 2 RTOs, flag the customer
   record so future orders from that number are queued for manual review before courier booking —
   this is the actual lever that reduces COD fraud cost over time, more than upfront OTP friction.
5. Install an abandoned cart recovery plugin (e.g. CartFlows or FunnelKit) tied to WooCommerce's
   native cart session, sending a follow-up email or WhatsApp message after ~1 hour of inactivity.
   This recovers ad spend that would otherwise be wasted on drop-offs.
```

---

### Step 4: Design Brief & Performance Tuning (Luxury UX)

Lock this brief before generating any theme styles — specificity here is what separates a luxury feel from a generic WooCommerce theme:

* **Typography:** serif display headlines (e.g. a refined editorial serif) + clean grotesque sans body, generous letter-spacing on headings, large type scale.
* **Palette:** Charcoal `#1C1A17`, Ivory `#F5F1EA`, amber or botanical-green accent — named, not "premium."
* **Imagery treatment:** full-bleed hero photography on homepage and category headers; zoom-on-hover for bottle macro shots on PDPs; no stock-photo placeholders.
* **Motion language:** 400–600ms cross-fades, subtle hover lifts (translateY 2–4px, no bounce), slow ease-out curves — never default CSS linear transitions.
* **Spacing:** deliberately under-filled viewport, generous section padding — luxury sites breathe, they don't fill every pixel.
* **Mobile-first:** since ad traffic is 90%+ mobile, build and review mobile layouts before desktop.

```text
PROMPT FOR THE AI AGENT:
Apply the design brief above to the compiled theme styles.

1. Implement the typography, palette, spacing, and motion specs exactly as listed — no default
   Tailwind/Bootstrap spacing or system fonts.
2. Add CSS-driven AJAX add-to-cart feedback animations and a smooth slide-out cart drawer using the
   specified easing and timing.
3. Optimize all images to WebP/AVIF, minify and combine theme assets, and confirm LiteSpeed page
   compilation is active.
4. Target a mobile Lighthouse LCP score under 1.5 seconds — run Lighthouse and report the result.
5. Confirm RankMath or Yoast is installed and active, generating Product/Offer schema and an XML
   sitemap automatically. Add a lightweight "Notes & Stories" content section/template for
   fragrance-family editorial content to support organic search.
```

---

### Step 5: Staging & Production Hardening Checklist (You — before going live)

* Clone the live site to a staging subdomain (Hostinger's staging tool or a plugin like WP Staging) before any future core/plugin update — test there first, especially once ads are live.
* Move the COD/RTO-tracking logic into a small custom plugin, not `functions.php` — it survives theme changes and updates.
* Install a security baseline: a WAF/firewall plugin (e.g. Wordfence or Hostinger's built-in), disable XML-RPC if unused, enforce strong admin passwords + 2FA on the WP admin login itself (this is a good place for verification friction — your admin account, not your customers).
* Set up automated daily backups (Hostinger has this built in — confirm it's enabled, don't assume).
* Confirm `wp-config.php` file editing is disabled in the dashboard and `DISALLOW_FILE_EDIT` is set.

---

## 💰 Operational Running Cost Caps

* **Hosting:** Hostinger Business Plan (flat monthly fee — already budgeted).
* **SMS/WhatsApp notifications:** minimal — confirmation-only messages, not verification codes, so volume and cost stay low.
* **Abandoned cart recovery plugin:** free tier available on most options (CartFlows/FunnelKit) at this order volume.
* **No added per-order verification cost** — the manual-call + RTO-tracking approach replaces what would have been an OTP line item.

---

## Where to Start

1. Confirm Hostinger Business plan is active and LiteSpeed Cache is enabled at server level.
2. Paste the **Step 0 prompt block** into your AI agent's chat window.
3. Lock your **Step 4 design brief** in writing before you reach that step — it's the difference between a generic WooCommerce theme and an actual luxury storefront.
4. Don't skip **Step 5** once ads start running — a broken plugin update mid-campaign is the most common way ad-funded WP stores lose money for no design or marketing reason at all.
