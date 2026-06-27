import React, { useEffect, useState, useCallback } from 'react';
import { useCart } from '../context/CartContext';
import './PerfumeGrid.css';

/**
 * PerfumeGrid — Shop Archive
 *
 * URL Parameter → Filter State Bridge:
 *   ?family=oud      → filters by WC product tag "oud"
 *   ?family=floral   → filters by WC product tag "floral"
 *   ?family=citrus   → filters by WC product tag "citrus"
 *   ?family=woody    → filters by WC product tag "woody"
 *   ?collection=signature → filters by WC category "signature"
 *   ?collection=private   → filters by WC category "private"
 *   ?collection=gift      → filters by WC category "gift"
 *   ?collection=limited   → filters by WC category "limited"
 *
 * Features visible "Add to Cart" button on every card with visual feedback:
 *   - Normal: "Add to Cart" (charcoal outline)
 *   - Process: "Adding..." (disabled)
 *   - Success: "Added ✓" (displays for 2 seconds, fills amber)
 */

// Names to always suppress from the storefront
const EXCLUDED_NAMES = [ 'content marketing' ];

function hasRealImage( product ) {
    const src = product.images?.[0]?.src ?? '';
    return src.length > 0 && ! src.includes( 'woocommerce-placeholder' );
}

function cleanProducts( list ) {
    return list.filter( p =>
        hasRealImage( p ) &&
        ! EXCLUDED_NAMES.includes( p.name?.toLowerCase().trim() )
    );
}

/**
 * Parse ?family= and ?collection= from the current URL.
 * Returns { categories: string[], tags: string[] }
 */
function parseUrlFilters() {
    const params     = new URLSearchParams( window.location.search );
    const family     = params.get( 'family' );      // → tag slug
    const collection = params.get( 'collection' );  // → category slug

    return {
        tags:       family     ? [ family ]     : [],
        categories: collection ? [ collection ] : [],
    };
}

/**
 * Reflect the active filters back onto the PHP sidebar checkboxes
 * and show the "Clear All" button if anything is active.
 */
function syncSidebarCheckboxes( { tags, categories } ) {
    tags.forEach( slug => {
        const cb = document.querySelector(
            `.sidebar-filter-input[data-filter-type="tag"][data-filter-slug="${ slug }"]`
        );
        if ( cb ) cb.checked = true;
    } );

    categories.forEach( slug => {
        const cb = document.querySelector(
            `.sidebar-filter-input[data-filter-type="category"][data-filter-slug="${ slug }"]`
        );
        if ( cb ) cb.checked = true;
    } );

    const hasActive = tags.length + categories.length > 0;
    const clearBtn  = document.getElementById( 'sidebar-clear-all' );
    if ( clearBtn ) clearBtn.style.display = hasActive ? 'block' : 'none';
}

export default function PerfumeGrid() {
    const [ allProducts, setAllProducts ] = useState( [] );
    const [ filtered,    setFiltered    ] = useState( [] );
    const [ loading,     setLoading     ] = useState( true );
    const [ filtering,   setFiltering   ] = useState( false );
    const [ activeLabel, setActiveLabel ] = useState( null ); // human-readable filter badge

    // Track add-to-cart state per product ID: 'adding' | 'added' | null
    const [ addingStates, setAddingStates ] = useState( {} );

    const { addToCart } = useCart();
    const apiBase = daScientistGlobals.store_api_url;

    // ── Helpers ─────────────────────────────────────────────────────────────
    const buildLabel = ( { tags, categories } ) => {
        const parts = [ ...tags, ...categories ];
        if ( ! parts.length ) return null;
        return parts.map( s => s.charAt( 0 ).toUpperCase() + s.slice( 1 ) ).join( ', ' );
    };

    const fetchFiltered = useCallback( async ( { tags, categories } ) => {
        if ( tags.length === 0 && categories.length === 0 ) return null;
        const params = new URLSearchParams();
        params.set( 'per_page', '48' );
        params.set( '_fields', 'id,name,permalink,images,price_html,categories,tags' );
        if ( categories.length ) params.set( 'category', categories.join( ',' ) );
        if ( tags.length )       params.set( 'tag',      tags.join( ',' ) );
        const res  = await fetch( `${ apiBase }products?${ params }` );
        const data = await res.json();
        return cleanProducts( Array.isArray( data ) ? data : [] );
    }, [ apiBase ] );

    // ── Initial fetch + URL param bootstrap ─────────────────────────────────
    useEffect( () => {
        const urlFilters = parseUrlFilters();
        const hasUrlFilter = urlFilters.tags.length + urlFilters.categories.length > 0;

        // Always fetch the full catalog first so "clear" works without a refetch
        fetch( `${ apiBase }products?per_page=48&_fields=id,name,permalink,images,price_html,categories,tags` )
            .then( r => r.json() )
            .then( async data => {
                const all = cleanProducts( Array.isArray( data ) ? data : [] );
                setAllProducts( all );

                if ( hasUrlFilter ) {
                    // URL had params — fetch filtered set and activate sidebar
                    setFiltering( true );
                    try {
                        const result = await fetchFiltered( urlFilters );
                        setFiltered( result ?? all );
                        setActiveLabel( buildLabel( urlFilters ) );
                    } catch {
                        setFiltered( all );
                    } finally {
                        setFiltering( false );
                    }

                    // After DOM is painted, check the sidebar boxes
                    requestAnimationFrame( () => syncSidebarCheckboxes( urlFilters ) );
                } else {
                    setFiltered( all );
                }

                setLoading( false );
            } )
            .catch( () => setLoading( false ) );
    }, [ apiBase, fetchFiltered ] );

    // ── Sidebar checkbox event listener ─────────────────────────────────────
    const handleFilterChange = useCallback( async ( e ) => {
        const { categories = [], tags = [] } = e.detail || {};

        if ( categories.length === 0 && tags.length === 0 ) {
            setFiltered( allProducts );
            setActiveLabel( null );
            return;
        }

        setFiltering( true );
        try {
            const result = await fetchFiltered( { tags, categories } );
            setFiltered( result ?? allProducts );
            setActiveLabel( buildLabel( { tags, categories } ) );
        } catch ( err ) {
            console.error( 'Filter fetch failed:', err );
        } finally {
            setFiltering( false );
        }
    }, [ allProducts, fetchFiltered ] );

    useEffect( () => {
        window.addEventListener( 'dascentist-filter-change', handleFilterChange );
        return () => window.removeEventListener( 'dascentist-filter-change', handleFilterChange );
    }, [ handleFilterChange ] );

    // ── Quick Add Action ────────────────────────────────────────────────────
    const handleQuickAdd = async ( e, productId ) => {
        e.preventDefault();
        e.stopPropagation();

        setAddingStates( prev => ( { ...prev, [productId]: 'adding' } ) );
        await addToCart( productId );
        setAddingStates( prev => ( { ...prev, [productId]: 'added' } ) );

        // Reset checkmark after 2 seconds
        setTimeout( () => {
            setAddingStates( prev => ( { ...prev, [productId]: null } ) );
        }, 2000 );
    };

    // ── Skeleton ─────────────────────────────────────────────────────────────
    if ( loading ) {
        return (
            <div className="grid-loading" aria-label="Loading products">
                { Array.from( { length: 8 } ).map( ( _, i ) => (
                    <div key={ i } className="skeleton-card">
                        <div className="skeleton-image" />
                        <div className="skeleton-text" />
                        <div className="skeleton-text skeleton-text--short" />
                    </div>
                ) ) }
            </div>
        );
    }

    // ── Render ────────────────────────────────────────────────────────────────
    return (
        <section className="perfume-grid-section" aria-label="Product Catalog">

            {/* Results / active filter bar */}
            <div className="grid-results-bar">
                { activeLabel && (
                    <span className="grid-active-filter">
                        { activeLabel }
                        <button
                            className="grid-filter-clear-x"
                            aria-label="Clear filter"
                            onClick={ () => {
                                setFiltered( allProducts );
                                setActiveLabel( null );
                                document.querySelectorAll( '.sidebar-filter-input:checked' )
                                    .forEach( el => { el.checked = false; } );
                                const cb = document.getElementById( 'sidebar-clear-all' );
                                if ( cb ) cb.style.display = 'none';
                                // Clean URL without reload
                                window.history.replaceState( {}, '', window.location.pathname );
                            } }
                        >✕</button>
                    </span>
                ) }
                <span className="grid-results-count">
                    { filtering ? (
                        <span className="filtering-indicator">Filtering…</span>
                    ) : (
                        <>{ filtered.length } { filtered.length === 1 ? 'fragrance' : 'fragrances' }</>
                    ) }
                </span>
            </div>

            {/* Grid */}
            { filtered.length > 0 ? (
                <div className={ `perfume-grid ${ filtering ? 'is-filtering' : '' }` }>
                    { filtered.map( product => {
                        const addingStatus = addingStates[product.id];
                        return (
                            <a
                                key={ product.id }
                                href={ product.permalink }
                                className="product-card"
                                aria-label={ product.name }
                            >
                                <div className="card-image-wrap">
                                    <img
                                        src={ product.images[0].src }
                                        alt={ product.images[0].alt || product.name }
                                        loading="lazy"
                                    />
                                </div>
                                <div className="card-body">
                                    <p className="card-category">
                                        { product.categories?.[0]?.name ?? 'Fragrance' }
                                    </p>
                                    <h3 className="card-title">{ product.name }</h3>
                                    <p
                                        className="card-price"
                                        dangerouslySetInnerHTML={ { __html: product.price_html } }
                                    />
                                    
                                    {/* Minimalist CTA Outline Button */}
                                    <button
                                        className={ `card-add-to-cart-btn ${ addingStatus === 'added' ? 'is-added' : '' }` }
                                        disabled={ addingStatus === 'adding' }
                                        onClick={ (e) => handleQuickAdd( e, product.id ) }
                                    >
                                        { addingStatus === 'adding' ? (
                                            'Adding…'
                                        ) : addingStatus === 'added' ? (
                                            'Added ✓'
                                        ) : (
                                            'Add to Cart'
                                        ) }
                                    </button>
                                </div>
                            </a>
                        );
                    } ) }
                </div>
            ) : (
                <div className="grid-empty">
                    <p>No fragrances match your selection.</p>
                    <button
                        className="grid-empty-reset"
                        onClick={ () => {
                            setFiltered( allProducts );
                            setActiveLabel( null );
                            document.querySelectorAll( '.sidebar-filter-input:checked' )
                                .forEach( el => { el.checked = false; } );
                            const clearBtn = document.getElementById( 'sidebar-clear-all' );
                            if ( clearBtn ) clearBtn.style.display = 'none';
                            window.history.replaceState( {}, '', window.location.pathname );
                        } }
                    >
                        Clear Filters
                    </button>
                </div>
            ) }

        </section>
    );
}
