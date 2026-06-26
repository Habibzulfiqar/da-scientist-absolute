import React from 'react';
import ReactDOM from 'react-dom/client';
import PerfumeGrid from './components/PerfumeGrid';
import ProductDetail from './components/ProductDetail';
import MiniCartDrawer from './components/MiniCartDrawer';
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
        console.log('🔍 app.jsx: cartRoot found:', cartRoot);
        if ( ! cartRoot ) {
            console.log('📦 app.jsx: Creating #luxury-cart-drawer container...');
            cartRoot = document.createElement( 'div' );
            cartRoot.id = 'luxury-cart-drawer';
            document.body.appendChild( cartRoot );
        }
        console.log('📦 app.jsx: Mounting MiniCartDrawer...');
        ReactDOM.createRoot( cartRoot ).render(
            <React.StrictMode>
                <CartProvider>
                    <MiniCartDrawer />
                </CartProvider>
            </React.StrictMode>
        );
        console.log('✅ app.jsx: MiniCartDrawer mounted.');
    } catch (err) {
        console.error('❌ app.jsx: Error rendering MiniCartDrawer:', err);
    }
};

// Safe execution for ES Modules (which may load after DOMContentLoaded has already fired)
console.log('⏰ app.jsx: Scheduling init(). readyState:', document.readyState);
if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', init );
} else {
    init();
}
