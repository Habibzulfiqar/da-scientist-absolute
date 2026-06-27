import React, { useEffect, useState, useCallback } from 'react';
import './PerfumeGrid.css';

/**
 * PerfumeGrid — Shop Archive
 * Clean editorial card grid — no cart buttons.
 * Each card is a full-link to the single product page.
 * Filters out products without a real featured image.
 * Responds to 'dascentist-filter-change' events from the PHP sidebar.
 */

// Slugs / names to always exclude from the storefront grid
const EXCLUDED_NAMES = [
    'content marketing',
];

// A product is "displayable" if it has at least one image with a real src
function hasRealImage( product ) {
    const src = product.images?.[0]?.src ?? '';
    // WooCommerce placeholder URLs contain "woocommerce-placeholder"
    return src.length > 0 && ! src.includes( 'woocommerce-placeholder' );
}

function cleanProducts( list ) {
    return list.filter( p => {
        if ( ! hasRealImage( p ) ) return false;
        if ( EXCLUDED_NAMES.includes( p.name?.toLowerCase().trim() ) ) return false;
        return true;
    } );
}

export default function PerfumeGrid() {
    const [ products,  setProducts  ] = useState( [] );
    const [ filtered,  setFiltered  ] = useState( [] );
    const [ loading,   setLoading   ] = useState( true );
    const [ filtering, setFiltering ] = useState( false );

    const apiBase = daScientistGlobals.store_api_url;

    // ── Initial fetch ──────────────────────────────────────────────────────
    useEffect( () => {
        fetch( `${ apiBase }products?per_page=48&_fields=id,name,permalink,images,price_html,categories,tags` )
            .then( r => r.json() )
            .then( data => {
                const clean = cleanProducts( Array.isArray( data ) ? data : [] );
                setProducts( clean );
                setFiltered( clean );
                setLoading( false );
            } )
            .catch( () => setLoading( false ) );
    }, [ apiBase ] );

    // ── Real-time sidebar filter ───────────────────────────────────────────
    const handleFilterChange = useCallback( async ( e ) => {
        const { categories = [], tags = [] } = e.detail || {};

        if ( categories.length === 0 && tags.length === 0 ) {
            setFiltered( products );
            return;
        }

        setFiltering( true );
        try {
            const params = new URLSearchParams();
            params.set( 'per_page', '48' );
            params.set( '_fields', 'id,name,permalink,images,price_html,categories,tags' );
            if ( categories.length ) params.set( 'category', categories.join( ',' ) );
            if ( tags.length )       params.set( 'tag',      tags.join( ',' ) );

            const res  = await fetch( `${ apiBase }products?${ params.toString() }` );
            const data = await res.json();
            setFiltered( cleanProducts( Array.isArray( data ) ? data : [] ) );
        } catch ( err ) {
            console.error( 'Filter fetch failed:', err );
        } finally {
            setFiltering( false );
        }
    }, [ apiBase, products ] );

    useEffect( () => {
        window.addEventListener( 'dascentist-filter-change', handleFilterChange );
        return () => window.removeEventListener( 'dascentist-filter-change', handleFilterChange );
    }, [ handleFilterChange ] );

    // ── Skeleton ───────────────────────────────────────────────────────────
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

    return (
        <section className="perfume-grid-section" aria-label="Product Catalog">

            {/* Results bar */}
            <div className="grid-results-bar">
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
                    { filtered.map( product => (
                        <a
                            key={ product.id }
                            href={ product.permalink }
                            className="product-card"
                            aria-label={ product.name }
                        >
                            {/* Image */}
                            <div className="card-image-wrap">
                                <img
                                    src={ product.images[0].src }
                                    alt={ product.images[0].alt || product.name }
                                    loading="lazy"
                                />
                            </div>

                            {/* Text — category · title · price only */}
                            <div className="card-body">
                                <p className="card-category">
                                    { product.categories?.[0]?.name ?? 'Fragrance' }
                                </p>
                                <h3 className="card-title">{ product.name }</h3>
                                <p
                                    className="card-price"
                                    dangerouslySetInnerHTML={ { __html: product.price_html } }
                                />
                            </div>
                        </a>
                    ) ) }
                </div>
            ) : (
                <div className="grid-empty">
                    <p>No fragrances match your selection.</p>
                    <button
                        className="grid-empty-reset"
                        onClick={ () => {
                            setFiltered( products );
                            document.querySelectorAll( '.sidebar-filter-input:checked' )
                                .forEach( el => { el.checked = false; } );
                            const clearBtn = document.getElementById( 'sidebar-clear-all' );
                            if ( clearBtn ) clearBtn.style.display = 'none';
                        } }
                    >
                        Clear Filters
                    </button>
                </div>
            ) }

        </section>
    );
}
