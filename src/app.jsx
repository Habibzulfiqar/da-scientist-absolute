import React from 'react';
import ReactDOM from 'react-dom/client';
import PerfumeGrid from './components/PerfumeGrid';
import ProductDetail from './components/ProductDetail';
import CartDrawer from './components/CartDrawer';
import HeaderCartIcon from './components/HeaderCartIcon';
import OrderTracking from './components/OrderTracking';
import FAQAccordion from './components/FAQAccordion';
import ContactForm from './components/ContactForm';
import { CartProvider } from './context/CartContext';

console.log('🚀 app.jsx loaded from Vite!');

/**
 * Unified React Mounting Core
 * Scans the DOM for target container nodes and conditionally mounts
 * independent React trees — leaving server-rendered fallbacks intact for SEO.
 */
const init = () => {
    console.log('🔄 app.jsx: init() executing. DOM readyState:', document.readyState);

    // 1. Shop Archive Grid — /shop/
    const gridRoot = document.getElementById( 'perfume-grid-root' );
    console.log('🔍 app.jsx: gridRoot found:', gridRoot);
    if ( gridRoot ) {
        try {
            console.log('📦 app.jsx: Mounting PerfumeGrid...');
            ReactDOM.createRoot( gridRoot ).render(
                <React.StrictMode>
                    <CartProvider>
                        <PerfumeGrid />
                    </CartProvider>
                </React.StrictMode>
            );
            console.log('✅ app.jsx: PerfumeGrid mounted.');
        } catch (err) {
            console.error('❌ app.jsx: Error rendering PerfumeGrid:', err);
        }
    }

    // 2. Single Product Detail — /product/slug/
    const productRoot = document.getElementById( 'single-perfume-root' );
    console.log('🔍 app.jsx: productRoot found:', productRoot);
    if ( productRoot ) {
        try {
            const productId = parseInt( productRoot.dataset.id, 10 );
            console.log('📦 app.jsx: Mounting ProductDetail for ID:', productId);
            ReactDOM.createRoot( productRoot ).render(
                <React.StrictMode>
                    <CartProvider>
                        <ProductDetail productId={ productId } />
                    </CartProvider>
                </React.StrictMode>
            );
            console.log('✅ app.jsx: ProductDetail mounted.');
        } catch (err) {
            console.error('❌ app.jsx: Error rendering ProductDetail:', err);
        }
    }

    // 3. Global Mini-Cart Drawer — always present in header/footer
    try {
        let cartRoot = document.getElementById( 'luxury-cart-drawer' );
        if ( ! cartRoot ) {
            console.log('📦 app.jsx: Creating #luxury-cart-drawer container...');
            cartRoot = document.createElement( 'div' );
            cartRoot.id = 'luxury-cart-drawer';
            document.body.appendChild( cartRoot );
        }
        console.log('📦 app.jsx: Mounting CartDrawer...');
        ReactDOM.createRoot( cartRoot ).render(
            <React.StrictMode>
                <CartProvider>
                    <CartDrawer />
                </CartProvider>
            </React.StrictMode>
        );
        console.log('✅ app.jsx: CartDrawer mounted.');
    } catch (err) {
        console.error('❌ app.jsx: Error rendering CartDrawer:', err);
    }

    // 4. Header Dynamic Cart Icon
    try {
        const headerCartRoot = document.getElementById( 'header-cart-icon-root' );
        console.log('🔍 app.jsx: headerCartRoot found:', headerCartRoot);
        if ( headerCartRoot ) {
            ReactDOM.createRoot( headerCartRoot ).render(
                <React.StrictMode>
                    <CartProvider>
                        <HeaderCartIcon />
                    </CartProvider>
                </React.StrictMode>
            );
            console.log('✅ app.jsx: HeaderCartIcon mounted.');
        }
    } catch (err) {
        console.error('❌ app.jsx: Error rendering HeaderCartIcon:', err);
    }

    // 5. Secure Order Tracking — mounted on /track-order page
    const trackRoot = document.getElementById( 'order-track-root' );
    console.log('🔍 app.jsx: trackRoot found:', trackRoot);
    if ( trackRoot ) {
        try {
            console.log('📦 app.jsx: Mounting OrderTracking...');
            ReactDOM.createRoot( trackRoot ).render(
                <React.StrictMode>
                    <OrderTracking />
                </React.StrictMode>
            );
            console.log('✅ app.jsx: OrderTracking mounted.');
        } catch (err) {
            console.error('❌ app.jsx: Error rendering OrderTracking:', err);
        }
    }

    // 6. FAQ Accordion — mounted on /faq page
    const faqRoot = document.getElementById( 'faq-accordion-root' );
    console.log('🔍 app.jsx: faqRoot found:', faqRoot);
    if ( faqRoot ) {
        try {
            console.log('📦 app.jsx: Mounting FAQAccordion...');
            ReactDOM.createRoot( faqRoot ).render(
                <React.StrictMode>
                    <FAQAccordion />
                </React.StrictMode>
            );
            console.log('✅ app.jsx: FAQAccordion mounted.');
        } catch (err) {
            console.error('❌ app.jsx: Error rendering FAQAccordion:', err);
        }
    }

    // 7. Contact Form — mounted on /contact page
    const contactRoot = document.getElementById( 'contact-root' );
    console.log('🔍 app.jsx: contactRoot found:', contactRoot);
    if ( contactRoot ) {
        try {
            console.log('📦 app.jsx: Mounting ContactForm...');
            ReactDOM.createRoot( contactRoot ).render(
                <React.StrictMode>
                    <ContactForm />
                </React.StrictMode>
            );
            console.log('✅ app.jsx: ContactForm mounted.');
        } catch (err) {
            console.error('❌ app.jsx: Error rendering ContactForm:', err);
        }
    }
};

// Safe execution for ES Modules (which may load after DOMContentLoaded has already fired)
console.log('⏰ app.jsx: Scheduling init(). readyState:', document.readyState);
if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', init );
} else {
    init();
}
