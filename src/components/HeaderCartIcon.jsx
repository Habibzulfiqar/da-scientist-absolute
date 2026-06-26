import React from 'react';
import { useCart } from '../context/CartContext';
import './HeaderCartIcon.css';

/**
 * HeaderCartIcon — Luxury Cart Indicator
 * Mounts in header.php at #header-cart-icon-root.
 * Synchronizes with CartContext state in real-time.
 */
export default function HeaderCartIcon() {
    const { itemCount } = useCart();

    const toggleCart = () => {
        window.dispatchEvent( new CustomEvent( 'dascentist-toggle-cart', { detail: { open: true } } ) );
    };

    return (
        <button 
            className="header-cart-btn" 
            onClick={ toggleCart }
            aria-label={`Open cart: ${itemCount} items`}
        >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                <line x1="3" y1="6" x2="21" y2="6" />
                <path d="M16 10a4 4 0 01-8 0" />
            </svg>
            { itemCount > 0 && (
                <span className="header-cart-badge">{ itemCount }</span>
            ) }
        </button>
    );
}
