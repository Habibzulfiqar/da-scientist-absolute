import React, { useEffect, useState } from 'react';
import { useCart } from '../context/CartContext';
import './PerfumeGrid.css';

/**
 * PerfumeGrid — Shop Archive
 * Renders the luxury product catalog with client-side filtering.
 * Mounted onto #perfume-grid-root in archive-product.php.
 */
export default function PerfumeGrid() {
    const [ products, setProducts ]   = useState( [] );
    const [ filtered, setFiltered ]   = useState( [] );
    const [ loading, setLoading ]     = useState( true );
    const [ activeFilter, setFilter ] = useState( 'all' );
    const { addToCart, isLoading }    = useCart();

    useEffect( () => {
        fetch( `${ daScientistGlobals.store_api_url }products?per_page=24` )
            .then( r => r.json() )
            .then( data => {
                setProducts( data );
                setFiltered( data );
                setLoading( false );
            } )
            .catch( () => setLoading( false ) );
    }, [] );

    // Client-side filtering — no page reloads
    const applyFilter = ( tag ) => {
        setFilter( tag );
        if ( tag === 'all' ) {
            setFiltered( products );
        } else {
            setFiltered( products.filter( p =>
                p.tags?.some( t => t.slug === tag )
            ) );
        }
    };

    if ( loading ) {
        return (
            <div className="grid-loading">
                <span className="loading-dot" /><span className="loading-dot" /><span className="loading-dot" />
            </div>
        );
    }

    return (
        <section className="perfume-grid-section">

            {/* Filter Bar */}
            <div className="filter-bar">
                { [ 'all', 'eau-de-parfum', 'oud', 'floral', 'citrus' ].map( tag => (
                    <button
                        key={ tag }
                        className={ `filter-btn ${ activeFilter === tag ? 'active' : '' }` }
                        onClick={ () => applyFilter( tag ) }
                    >
                        { tag === 'all' ? 'All Scents' : tag.replace( '-', ' ' ).replace( /\b\w/g, l => l.toUpperCase() ) }
                    </button>
                ) ) }
            </div>

            {/* Product Grid */}
            <div className="perfume-grid">
                { filtered.map( product => (
                    <article key={ product.id } className="product-card">
                        <a href={ product.permalink } className="card-image-link">
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
                            <p className="card-price"
                                dangerouslySetInnerHTML={ { __html: product.price_html } }
                            />
                            <button
                                className="card-add-btn"
                                disabled={ isLoading }
                                onClick={ () => addToCart( product.id, 1 ) }
                            >
                                { isLoading ? '…' : 'Add to Cart' }
                            </button>
                        </div>
                    </article>
                ) ) }
            </div>

        </section>
    );
}
