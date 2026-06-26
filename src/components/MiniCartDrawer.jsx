import React from 'react';
import { useCart } from '../context/CartContext';
import './MiniCartDrawer.css';

/**
 * MiniCartDrawer — Global Sliding Cart Panel
 * Always present in the DOM (appended to body).
 * Opens automatically after adding an item; closes via overlay or X button.
 * Checkout redirects to native WooCommerce — the golden handshake.
 */
export default function MiniCartDrawer() {
    const {
        cart,
        isOpen,
        setIsOpen,
        removeItem,
        proceedToCheckout,
        itemCount,
    } = useCart();

    const items    = cart?.items ?? [];
    const subtotal = cart?.totals?.total_price ?? '0';
    const currency = cart?.totals?.currency_symbol ?? 'Rs.';

    const formatPrice = ( raw ) =>
        `${ currency } ${ ( parseInt( raw, 10 ) / 100 ).toLocaleString() }`;

    return (
        <>
            {/* Floating Cart Button */}
            <button
                className={ `cart-trigger ${ itemCount > 0 ? 'has-items' : '' }` }
                onClick={ () => setIsOpen( true ) }
                aria-label="Open cart"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                    <line x1="3" y1="6" x2="21" y2="6" />
                    <path d="M16 10a4 4 0 01-8 0" />
                </svg>
                { itemCount > 0 && <span className="cart-badge">{ itemCount }</span> }
            </button>

            {/* Overlay */}
            <div
                className={ `cart-overlay ${ isOpen ? 'visible' : '' }` }
                onClick={ () => setIsOpen( false ) }
            />

            {/* Sliding Drawer */}
            <aside className={ `cart-drawer ${ isOpen ? 'open' : '' }` }>

                <div className="drawer-header">
                    <h2 className="drawer-title">Your Cart</h2>
                    <button className="drawer-close" onClick={ () => setIsOpen( false ) }>✕</button>
                </div>

                <div className="drawer-body">
                    { items.length === 0 ? (
                        <div className="drawer-empty">
                            <p>Your cart is empty.</p>
                            <a href="/shop" className="browse-link">Browse Scents →</a>
                        </div>
                    ) : (
                        <ul className="cart-items">
                            { items.map( item => (
                                <li key={ item.key } className="cart-item">
                                    <div className="item-image">
                                        { item.images?.[0] && (
                                            <img src={ item.images[0].src } alt={ item.name } />
                                        ) }
                                    </div>
                                    <div className="item-details">
                                        <p className="item-name">{ item.name }</p>
                                        <p className="item-meta">Qty: { item.quantity }</p>
                                        <p className="item-price">
                                            { formatPrice( item.totals?.line_total ?? '0' ) }
                                        </p>
                                    </div>
                                    <button
                                        className="item-remove"
                                        onClick={ () => removeItem( item.key ) }
                                        aria-label="Remove item"
                                    >
                                        ✕
                                    </button>
                                </li>
                            ) ) }
                        </ul>
                    ) }
                </div>

                { items.length > 0 && (
                    <div className="drawer-footer">
                        <div className="subtotal-row">
                            <span>Subtotal</span>
                            <span>{ formatPrice( subtotal ) }</span>
                        </div>
                        {/* Golden Handshake — redirect to native WooCommerce checkout */}
                        <button
                            className="checkout-btn"
                            onClick={ proceedToCheckout }
                        >
                            Proceed to Checkout
                        </button>
                        <p className="cod-note">Cash on Delivery · Secure Checkout</p>
                    </div>
                ) }
            </aside>
        </>
    );
}
