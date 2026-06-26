import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';

const CartContext = createContext( null );

// ─────────────────────────────────────────────────────────────────────────────
// Nonce-Aware Fetch Wrapper
// Intercepts 401/403 responses, renews the WP REST nonce automatically.
// ─────────────────────────────────────────────────────────────────────────────
async function fetchWithNonce( url, options = {} ) {
    const makeRequest = ( nonce ) => fetch( url, {
        ...options,
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Nonce': nonce,
            'X-WP-Nonce': nonce,
            ...( options.headers || {} ),
        },
    } );

    let res = await makeRequest( daScientistGlobals.nonce );

    if ( res.status === 401 || res.status === 403 ) {
        try {
            const restBase = daScientistGlobals.store_api_url.split( '/wc/' )[0] + '/';
            const refreshRes = await fetch( `${ restBase }dascentist/v1/nonce`, {
                credentials: 'same-origin'
            } );
            const { nonce: freshNonce } = await refreshRes.json();
            if ( freshNonce ) {
                daScientistGlobals.nonce = freshNonce;
                res = await makeRequest( freshNonce );
            }
        } catch ( err ) {
            console.error( 'Nonce refresh failed:', err );
        }
    }

    return res;
}

// ─────────────────────────────────────────────────────────────────────────────
// Shared Cart Fetch — always hits the server, never stale
// Any CartProvider calling this gets the true current session cart.
// ─────────────────────────────────────────────────────────────────────────────
async function fetchCartFromServer( apiBase ) {
    const res  = await fetchWithNonce( `${ apiBase }cart` );
    const data = await res.json();
    return data;
}

// ─────────────────────────────────────────────────────────────────────────────
// Cart Provider
// ─────────────────────────────────────────────────────────────────────────────
export function CartProvider( { children } ) {
    const [ cart, setCart ]           = useState( null );
    const [ isOpen, setIsOpen ]       = useState( false );
    const [ isLoading, setIsLoading ] = useState( false );

    const apiBase = daScientistGlobals.store_api_url;

    // ─── Fetch cart from server ───────────────────────────────────────────────
    const fetchCart = useCallback( async () => {
        try {
            const data = await fetchCartFromServer( apiBase );
            setCart( data );
        } catch ( err ) {
            console.error( 'Cart fetch failed:', err );
        }
    }, [ apiBase ] );

    // Fetch cart once on mount so each root stays current
    useEffect( () => { fetchCart(); }, [ fetchCart ] );

    // ─── Cross-root Drawer Toggle ─────────────────────────────────────────────
    // When drawer opens, always refetch the cart so any root that opens it
    // gets the latest session state (fixes the isolated CartProvider bug).
    useEffect( () => {
        const handleToggle = async ( e ) => {
            const shouldOpen = e.detail?.open;

            if ( typeof shouldOpen !== 'undefined' ) {
                if ( shouldOpen ) {
                    // Refetch cart so drawer always shows fresh state
                    await fetchCart();
                }
                setIsOpen( shouldOpen );
            } else {
                setIsOpen( prev => ! prev );
            }
        };

        window.addEventListener( 'dascentist-toggle-cart', handleToggle );
        return () => window.removeEventListener( 'dascentist-toggle-cart', handleToggle );
    }, [ fetchCart ] );

    // ─── Add Item ─────────────────────────────────────────────────────────────
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

            if ( res.ok ) {
                setCart( data );
                // Broadcast to ALL roots: refresh cart + open drawer
                window.dispatchEvent( new CustomEvent( 'dascentist-cart-updated', { detail: data } ) );
                window.dispatchEvent( new CustomEvent( 'dascentist-toggle-cart', { detail: { open: true } } ) );
            } else if ( res.status === 400 ) {
                // Already in cart (sold individually) — open drawer anyway
                window.dispatchEvent( new CustomEvent( 'dascentist-toggle-cart', { detail: { open: true } } ) );
            } else {
                console.error( 'Add to cart failed:', data );
            }
        } catch ( err ) {
            console.error( 'Add to cart error:', err );
        } finally {
            setIsLoading( false );
        }
    }, [ apiBase ] );

    // ─── Remove Item ──────────────────────────────────────────────────────────
    const removeItem = useCallback( async ( itemKey ) => {
        try {
            const res  = await fetchWithNonce( `${ apiBase }cart/remove-item`, {
                method: 'POST',
                body: JSON.stringify( { key: itemKey } ),
            } );
            const data = await res.json();
            if ( res.ok ) {
                setCart( data );
                window.dispatchEvent( new CustomEvent( 'dascentist-cart-updated', { detail: data } ) );
            }
        } catch ( err ) {
            console.error( 'Remove item error:', err );
        }
    }, [ apiBase ] );

    // ─── Update Quantity ──────────────────────────────────────────────────────
    const updateQuantity = useCallback( async ( itemKey, newQuantity ) => {
        setIsLoading( true );
        try {
            const res  = await fetchWithNonce( `${ apiBase }cart/update-item`, {
                method: 'POST',
                body: JSON.stringify( { key: itemKey, quantity: newQuantity } ),
            } );
            const data = await res.json();
            if ( res.ok ) {
                setCart( data );
                window.dispatchEvent( new CustomEvent( 'dascentist-cart-updated', { detail: data } ) );
            }
        } catch ( err ) {
            console.error( 'Update quantity error:', err );
        } finally {
            setIsLoading( false );
        }
    }, [ apiBase ] );

    // ─── Listen for cart updates from OTHER roots ─────────────────────────────
    // When another root (e.g. PerfumeGrid) successfully adds/removes an item,
    // it broadcasts 'dascentist-cart-updated'. All other roots update themselves.
    useEffect( () => {
        const handleUpdate = ( e ) => {
            if ( e.detail ) setCart( e.detail );
        };
        window.addEventListener( 'dascentist-cart-updated', handleUpdate );
        return () => window.removeEventListener( 'dascentist-cart-updated', handleUpdate );
    }, [] );

    // ─── Checkout ─────────────────────────────────────────────────────────────
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
            updateQuantity,
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
