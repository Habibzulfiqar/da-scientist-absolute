import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';

const CartContext = createContext( null );

// ─────────────────────────────────────────────────────────────────────────────
// Nonce-Aware Fetch Wrapper
// Intercepts 403 Forbidden responses, renews the WP REST nonce,
// caches the new token, and automatically replays the original request.
// ─────────────────────────────────────────────────────────────────────────────
async function fetchWithNonce( url, options = {} ) {
    const makeRequest = ( nonce ) => fetch( url, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': nonce,
            ...( options.headers || {} ),
        },
    } );

    let res = await makeRequest( daScientistGlobals.nonce );

    if ( res.status === 403 ) {
        // Nonce expired — request a fresh token
        const refreshRes = await fetch( '/wp-json/dascentist/v1/nonce' );
        const { nonce: freshNonce } = await refreshRes.json();

        if ( freshNonce ) {
            daScientistGlobals.nonce = freshNonce; // cache for all future calls
            res = await makeRequest( freshNonce );   // replay original request
        }
    }

    return res;
}

// ─────────────────────────────────────────────────────────────────────────────
// Cart Provider
// ─────────────────────────────────────────────────────────────────────────────
export function CartProvider( { children } ) {
    const [ cart, setCart ]             = useState( null );
    const [ isOpen, setIsOpen ]         = useState( false );
    const [ isLoading, setIsLoading ]   = useState( false );

    const apiBase = daScientistGlobals.store_api_url;

    // Fetch cart on mount to sync open sessions (abandoned cart recovery)
    const fetchCart = useCallback( async () => {
        try {
            const res  = await fetchWithNonce( `${ apiBase }cart` );
            const data = await res.json();
            setCart( data );
        } catch ( err ) {
            console.error( 'Cart sync failed:', err );
        }
    }, [ apiBase ] );

    useEffect( () => {
        fetchCart();
    }, [ fetchCart ] );

    // Add item to WooCommerce cart via Store API
    const addToCart = useCallback( async ( productId, quantity = 1, variationId = null ) => {
        setIsLoading( true );
        try {
            const body = { id: productId, quantity };
            if ( variationId ) body.variation_id = variationId;

            const res  = await fetchWithNonce( `${ apiBase }cart/add-item`, {
                method: 'POST',
                body: JSON.stringify( body ),
            } );
            const data = await res.json();
            setCart( data );
            setIsOpen( true ); // Open cart drawer after adding
        } catch ( err ) {
            console.error( 'Add to cart failed:', err );
        } finally {
            setIsLoading( false );
        }
    }, [ apiBase ] );

    // Remove item from cart
    const removeItem = useCallback( async ( itemKey ) => {
        try {
            const res  = await fetchWithNonce( `${ apiBase }cart/remove-item`, {
                method: 'POST',
                body: JSON.stringify( { key: itemKey } ),
            } );
            const data = await res.json();
            setCart( data );
        } catch ( err ) {
            console.error( 'Remove item failed:', err );
        }
    }, [ apiBase ] );

    // Proceed to WooCommerce native checkout — the golden handshake
    const proceedToCheckout = () => {
        window.location.href = daScientistGlobals.checkout_url;
    };

    const itemCount = cart?.items_count ?? 0;

    return (
        <CartContext.Provider value={ {
            cart,
            isOpen,
            isLoading,
            itemCount,
            setIsOpen,
            addToCart,
            removeItem,
            proceedToCheckout,
            fetchCart,
        } }>
            { children }
        </CartContext.Provider>
    );
}

export function useCart() {
    const ctx = useContext( CartContext );
    if ( ! ctx ) throw new Error( 'useCart must be used inside CartProvider' );
    return ctx;
}
