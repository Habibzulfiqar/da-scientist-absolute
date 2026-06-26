import React from 'react';
import { useCart } from '../context/CartContext';
import './CartDrawer.css';

/**
 * CartDrawer — Premium Sliding Side-Cart Layer
 * Matches UI specifications for Byredo-style slide and overlay blur.
 */
export default function CartDrawer() {
    const {
        cart,
        isOpen,
        setIsOpen,
        removeItem,
        updateQuantity,
        proceedToCheckout,
        isLoading
    } = useCart();

    const items    = cart?.items ?? [];
    const subtotal = cart?.totals?.total_price ?? '0';
    const currency = cart?.totals?.currency_symbol ?? '₨';

    const formatPrice = ( raw ) => {
        const numeric = parseInt( raw, 10 );
        const minorUnit = cart?.totals?.currency_minor_unit ?? 2;
        const divider = Math.pow( 10, minorUnit );
        return `${ currency } ${ ( numeric / divider ).toLocaleString() }`;
    };

    return (
        <>
            {/* Backdrop Dim and Blur Overlay */}
            <div
                className={ `cart-overlay ${ isOpen ? 'visible' : '' }` }
                onClick={ () => setIsOpen( false ) }
            />

            {/* Sliding Drawer */}
            <aside className={ `cart-drawer ${ isOpen ? 'open' : '' }` }>

                {/* Fixed Drawer Header */}
                <div className="drawer-header">
                    <h2 className="drawer-title">Your Cart</h2>
                    <button 
                        className="drawer-close" 
                        onClick={ () => setIsOpen( false ) }
                        aria-label="Close cart"
                    >
                        ✕
                    </button>
                </div>

                {/* Scrollable Drawer Body */}
                <div className="drawer-body">
                    { items.length === 0 ? (
                        <div className="drawer-empty">
                            <p className="empty-message">Your cart is empty</p>
                            <button 
                                className="continue-shopping"
                                onClick={ () => setIsOpen( false ) }
                            >
                                Continue Shopping
                            </button>
                        </div>
                    ) : (
                        <ul className="cart-items">
                            { items.map( item => (
                                <li key={ item.key } className="cart-item">
                                    {/* Product Thumbnail */}
                                    <div className="item-image">
                                        { item.images?.[0] ? (
                                            <img src={ item.images[0].src } alt={ item.name } />
                                        ) : (
                                            <div className="image-placeholder" />
                                        ) }
                                    </div>

                                    {/* Item Details */}
                                    <div className="item-details">
                                        <h3 className="item-name">{ item.name }</h3>
                                        
                                        {/* Variation Attributes (Size, etc.) */}
                                        { item.variation && item.variation.length > 0 && (
                                            <p className="item-variation">
                                                { item.variation.map( v => v.value ).join(' / ') }
                                            </p>
                                        ) }

                                        {/* Quantity Selector and Remove Link */}
                                        <div className="item-actions">
                                            <div className="item-qty-adjuster">
                                                <button
                                                    className="qty-adjust-btn"
                                                    disabled={ isLoading || item.quantity <= 1 }
                                                    onClick={ () => updateQuantity( item.key, item.quantity - 1 ) }
                                                >
                                                    −
                                                </button>
                                                <span className="qty-value">{ item.quantity }</span>
                                                <button
                                                    className="qty-adjust-btn"
                                                    disabled={ isLoading }
                                                    onClick={ () => updateQuantity( item.key, item.quantity + 1 ) }
                                                >
                                                    +
                                                </button>
                                            </div>
                                            <button
                                                className="item-remove-link"
                                                onClick={ () => removeItem( item.key ) }
                                                disabled={ isLoading }
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    {/* Line Item Total Price */}
                                    <div className="item-price-wrap">
                                        <span className="item-line-price">
                                            { formatPrice( item.totals?.line_total ?? '0' ) }
                                        </span>
                                    </div>
                                </li>
                            ) ) }
                        </ul>
                    ) }
                </div>

                {/* Fixed Action Footer */}
                { items.length > 0 && (
                    <div className="drawer-footer">
                        <div className="subtotal-row">
                            <span className="subtotal-label">Subtotal</span>
                            <span className="subtotal-amount">{ formatPrice( subtotal ) }</span>
                        </div>
                        
                        <button
                            className="checkout-btn"
                            disabled={ isLoading }
                            onClick={ proceedToCheckout }
                        >
                            { isLoading ? 'Processing…' : 'Proceed to Checkout' }
                        </button>
                        
                        <p className="cod-badge-note">
                            Free Shipping in Pakistan · Cash on Delivery
                        </p>
                    </div>
                ) }
            </aside>
        </>
    );
}
