# Da Scientist — Storefront Pages Reference & Progress Tracker

This document tracks the progress and specifications of all pages on the **Da Scientist / Da Scientist Absolute** hybrid WordPress-React platform.

We utilize a **Hybrid React Architecture**: 
* WordPress PHP manages routing, URL generation, page shells, and core SEO metadata.
* React mounts on specific DOM hooks (like `#perfume-grid-root` or `#contact-root`) to provide instant, high-performance, single-page interactions without reloading the page.

---

## 📊 Pages Status & Progress Matrix

| Page Name | Slug (Path) | Front-end Mounting Method | React Component | Progress |
| :--- | :--- | :--- | :--- | :--- |
| **Home Page** | `/` | PHP Template (`front-page.php`) | Home Carousels (Static CSS) | `[x]` Complete |
| **Shop Archive** | `/shop` | React Mounting (`#perfume-grid-root`) | `PerfumeGrid.jsx` | `[x]` Complete |
| **Product Detail** | `/product/*` | React Mounting (`#single-perfume-root`) | `ProductDetail.jsx` | `[x]` Complete |
| **Shopping Cart** | `/cart` | Fallback Cart Page + Mini-Cart Drawer | `CartDrawer.jsx` | `[x]` Complete |
| **Checkout Flow** | `/checkout` | Styled Form Overlay (Custom CSS) | Static Checkout Form | `[x]` Complete |
| **My Account** | `/my-account` | PHP Page (`page.php`) | Dashboard Profile | `[x]` Complete |
| **About/Philosophy**| `/about` | PHP Page Template | Static Editorial Content | `[x]` Complete |
| **Contact Us** | `/contact` | React Mounting (`#contact-root`) | `ContactForm.jsx` | `[x]` Complete |
| **The Journal (Blog)**| `/blog` | PHP Post Archive | Static Post Feed | `[x]` Complete |
| **Track Your Order** | `/track-order` | React Mounting (`#order-track-root`) | `OrderTracking.jsx` | `[x]` Complete |
| **Returns & Exchange**| `/returns-exchange` | PHP Page Template | Static Policy Text | `[x]` Complete |
| **Shipping Policy** | `/shipping-policy` | PHP Page Template | Static Policy Text | `[x]` Complete |
| **FAQs** | `/faq` | React Mounting (`#faq-accordion-root`) | `FAQAccordion.jsx` | `[x]` Complete |
| **Privacy Policy** | `/privacy-policy` | PHP Page Template | Static Legal Text | `[x]` Complete |
| **Terms of Service** | `/terms` | PHP Page Template | Static Legal Text | `[x]` Complete |

*Status Key: `[x]` Complete | `[/]` In Progress | `[ ]` Pending*

---

## ⚡ Automated Page Generation (100% Automated Out-of-the-Box)
To make your theme 100% operational instantly without manual database configurations, we have written a **Programmatic Pages Bootstrap** inside `functions.php`.

* On theme activation/initial load, the theme checks your WordPress database.
* If any of the essential pages are missing (Contact, FAQs, Order Tracking, Shipping Policy, Returns & Exchange, Privacy Policy, Terms), the script **automatically inserts them** with pre-written, pixel-perfect layout content and proper React mounting elements.
* It locks the database flag (`dascentist_pages_setup_complete_v3`) once run to ensure database requests stay extremely fast.

---

## 🛠️ Implemented React Page Components

We have built, verified, and compiled the following React widgets:

### 1. Connect with the House (`ContactForm.jsx` & `ContactForm.css`)
* **Mounting Root**: `<div id="contact-root"></div>`
* **AJAX Endpoint**: `dascentist_submit_contact`
* **Features**:
  * Side-by-side editorial layout on desktop. Left panel outlines Client Relations WhatsApp details, direct email, and Laboratory Dispatch info.
  * Right panel renders the secure submission form: Name, Phone/Email, Interest Topic (Compounding, Bulk, Support), and Message.
  * Includes validation and an elegant success fade screen.

### 2. Live Order Tracking (`OrderTracking.jsx` & `OrderTracking.css`)
* **Mounting Root**: `<div id="order-track-root"></div>`
* **AJAX Endpoint**: `dascentist_track_order`
* **Features**: Form inputs verifying Order ID against billing info to return live dispatch milestones.

### 3. FAQ Accordion (`FAQAccordion.jsx` & `FAQAccordion.css`)
* **Mounting Root**: `<div id="faq-accordion-root"></div>`
* **Features**: Smooth sliding accordion rows covering concentration levels (30%-40% oil), timelines, packaging, and exchanges.
