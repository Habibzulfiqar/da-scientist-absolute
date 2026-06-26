import React, { useEffect, useState, useCallback } from 'react';
import { useCart } from '../context/CartContext';
import './PerfumeGrid.css';

/**
 * PerfumeGrid — Shop Archive
 * Renders the luxury product catalog.
 * Responds to 'dascentist-filter-change' events from the PHP sidebar.
 * Mounted onto #perfume-grid-root in archive-product.php.
 */
export default function PerfumeGrid() {
    const [ products,    setProducts ]   = useState( [] );
    const [ filtered,    setFiltered ]   = useState( [] );
    const [ loading,     setLoading ]    = useState( true );
    const [ filtering,   setFiltering ]  = useState( false );
    const { addToCart, isLoading, cart } = useCart();

    const apiBase = daScientistGlobals.store_api_url;

    // ── Initial product fetch ──────────────────────────────────────────────
    useEffect( () => {
        fetch( `${ apiBase }products?per_page=24&_fields=id,name,permalink,images,price_html,categories,tags` )
            .then( r => r.json() )
            .then( data => {
                const list = Array.isArray( data ) ? data : [];
                setProducts( list );
                setFiltered( list );
                setLoading( false );
            } )
            .catch( () => setLoading( false ) );
    }, [ apiBase ] );

    // ── Real-time sidebar filter listener ─────────────────────────────────
    const handleFilterChange = useCallback( async ( e ) => {
        const { categories = [], tags = [] } = e.detail || {};

        // No active filters — show everything without a new fetch
        if ( categories.length === 0 && tags.length === 0 ) {
            setFiltered( products );
            return;
        }

        setFiltering( true );
        try {
            const params = new URLSearchParams();
            params.set( 'per_page', '24' );
            params.set( '_fields', 'id,name,permalink,images,price_html,categories,tags' );
            if ( categories.length ) params.set( 'category', categories.join( ',' ) );
            if ( tags.length      ) params.set( 'tag',      tags.join( ',' ) );

            const res  = await fetch( `${ apiBase }products?${ params.toString() }` );
            const data = await res.json();
            setFiltered( Array.isArray( data ) ? data : [] );
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

    // ── Loading skeleton ───────────────────────────────────────────────────
    if ( loading ) {
        return (
            <div className="grid-loading" aria-label="Loading products">
                { Array.from( { length: 6 } ).map( ( _, i ) => (
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

            {/* Product Grid */}
            { filtered.length > 0 ? (
                <div className={ `perfume-grid ${ filtering ? 'is-filtering' : '' }` }>
                    { filtered.map( product => {
                        const isInCart = cart?.items?.some( item => item.id === product.id );
                        return (
                            <article key={ product.id } className="product-card">
                                <a href={ product.permalink } className="card-image-link" tabIndex="-1" aria-hidden="true">
                                    <div className="card-image-wrap">
                                        { product.images?.[0] ? (
                                            <img
                                                src={ product.images[0].src }
                                                alt={ product.images[0].alt || product.name }
                                                loading="lazy"
                                            />
                                        ) : (
                                            <div className="card-image-placeholder" />
                                        ) }
                                    </div>
                                </a>

                                <div className="card-body">
                                    <p className="card-category">
                                        { product.categories?.[0]?.name ?? 'Fragrance' }
                                    </p>
                                    <h3 className="card-title">
                                        <a href={ product.permalink }>{ product.name }</a>
                                    </h3>
                                    <p
                                        className="card-price"
                                        dangerouslySetInnerHTML={ { __html: product.price_html } }
                                    />
                                    <button
                                        className={ `card-add-btn ${ isInCart ? 'in-cart' : '' }` }
                                        disabled={ isLoading }
                                        aria-label={ isInCart ? `Open cart — ${ product.name } is in your cart` : `Add ${ product.name } to cart` }
                                        onClick={ () => isInCart
                                            ? window.dispatchEvent( new CustomEvent( 'dascentist-toggle-cart', { detail: { open: true } } ) )
                                            : addToCart( product.id, 1 )
                                        }
                                    >
                                        { isLoading ? '…' : ( isInCart ? 'In Cart — View →' : 'Add to Cart' ) }
                                    </button>
                                </div>
                            </article>
                        );
                    } ) }
                </div>
            ) : (
                <div className="grid-empty">
                    <p>No fragrances match your selection.</p>
                    <button
                        className="grid-empty-reset"
                        onClick={ () => {
                            setFiltered( products );
                            // Clear checkboxes in sidebar
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
