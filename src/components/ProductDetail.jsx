import React, { useEffect, useState } from 'react';
import { useCart } from '../context/CartContext';
import './ProductDetail.css';

/**
 * ProductDetail — Single Product Page
 * Renders the luxury product detail with asymmetric vertical image stack,
 * size variation swatches, sticky control panel, and editorial accordions.
 * Mounted onto #single-perfume-root in single-product.php.
 */
export default function ProductDetail( { productId } ) {
    const [ product, setProduct ]           = useState( null );
    const [ activeVariation, setVariation ] = useState( null );
    const [ quantity, setQuantity ]         = useState( 1 );
    const [ openAccordions, setOpenAccordions ] = useState( {
        scent: false,
        ingredients: false,
        shipping: false
    } );
    const { addToCart, isLoading, cart }          = useCart();

    useEffect( () => {
        if ( ! productId ) return;
        fetch( `${ daScientistGlobals.store_api_url }products/${ productId }` )
            .then( r => r.json() )
            .then( data => {
                setProduct( data );
                if ( data.variations?.length ) setVariation( data.variations[0] );
            } )
            .catch( err => console.error( 'Failed to fetch product:', err ) );
    }, [ productId ] );

    if ( ! product ) {
        return <div className="product-loading">Loading molecular database…</div>;
    }

    const images      = product.images ?? [];
    const variations  = product.variations ?? [];
    const hasVariations = variations.length > 0;

    const handleAddToCart = () => {
        addToCart( product.id, quantity, activeVariation?.id ?? null );
    };

    const toggleAccordion = ( key ) => {
        setOpenAccordions( prev => ( {
            ...prev,
            [ key ]: ! prev[ key ]
        } ) );
    };

    return (
        <section className="product-detail">

            {/* Asymmetric Vertical Image Stack */}
            <div className="product-gallery-stack">
                { images.length === 0 ? (
                    <div className="gallery-image-wrap">
                        <div className="image-placeholder" />
                    </div>
                ) : (
                    images.map( ( img, i ) => (
                        <div key={ img.id || i } className="gallery-image-wrap">
                            <img
                                src={ img.src }
                                alt={ img.alt || `${ product.name } ${ i + 1 }` }
                                loading={ i === 0 ? 'eager' : 'lazy' }
                            />
                        </div>
                    ) )
                ) }
            </div>

            {/* Sticky Control Panel */}
            <div className="product-summary-sticky">
                <div className="product-summary-header">
                    <p className="product-category">
                        { product.categories?.[0]?.name ?? 'Fragrance' }
                    </p>
                    <h1 className="product-title">{ product.name }</h1>
                    <div className="product-price"
                        dangerouslySetInnerHTML={ { __html: product.price_html } }
                    />
                </div>

                {/* Size / Variation Swatches */}
                { hasVariations && (
                    <div className="variation-swatches">
                        <p className="swatch-label">Size</p>
                        <div className="swatch-group">
                            { variations.map( v => (
                                <button
                                    key={ v.id }
                                    className={ `swatch-btn ${ activeVariation?.id === v.id ? 'active' : '' }` }
                                    onClick={ () => setVariation( v ) }
                                >
                                    { v.attributes?.[0]?.value ?? v.id }
                                </button>
                            ) ) }
                        </div>
                    </div>
                ) }

                {/* Quantity and CTA */}
                <div className="action-row">
                    <div className="qty-row">
                        <button className="qty-btn" onClick={ () => setQuantity( q => Math.max( 1, q - 1 ) ) }>−</button>
                        <span className="qty-value">{ quantity }</span>
                        <button className="qty-btn" onClick={ () => setQuantity( q => q + 1 ) }>+</button>
                    </div>

                    {(() => {
                        const isInCart = cart?.items?.some( item => {
                            if ( activeVariation ) {
                                return item.id === activeVariation.id;
                            }
                            return item.id === product.id;
                        } );

                        return (
                            <button
                                className={`add-to-cart-cta ${isInCart ? 'in-cart' : ''}`}
                                disabled={ isLoading }
                                onClick={ () => isInCart ? window.dispatchEvent( new CustomEvent( 'dascentist-toggle-cart', { detail: { open: true } } ) ) : handleAddToCart() }
                            >
                                { isLoading ? 'Adding…' : ( isInCart ? 'Already in Cart' : 'Add to Cart' ) }
                            </button>
                        );
                    })()}
                </div>

                {/* Main Product Description */}
                { product.description && (
                    <div
                        className="product-description"
                        dangerouslySetInnerHTML={ { __html: product.description } }
                    />
                ) }

                {/* Editorial Accordions */}
                <div className="product-accordions">
                    {/* Scent Profile */}
                    <div className={ `accordion-item ${ openAccordions.scent ? 'open' : '' }` }>
                        <button className="accordion-trigger" onClick={ () => toggleAccordion( 'scent' ) }>
                            <span>Scent Profile</span>
                            <span className="accordion-icon">{ openAccordions.scent ? '−' : '+' }</span>
                        </button>
                        <div className="accordion-content">
                            <div className="accordion-content-inner">
                                <p>A molecular exploration of dry woods, rich spice, and warm musk. Designed to linger close to the skin, responding uniquely to your body chemistry to create a deeply personal olfactory signature.</p>
                            </div>
                        </div>
                    </div>

                    {/* Ingredients */}
                    <div className={ `accordion-item ${ openAccordions.ingredients ? 'open' : '' }` }>
                        <button className="accordion-trigger" onClick={ () => toggleAccordion( 'ingredients' ) }>
                            <span>Ingredients</span>
                            <span className="accordion-icon">{ openAccordions.ingredients ? '−' : '+' }</span>
                        </button>
                        <div className="accordion-content">
                            <div className="accordion-content-inner">
                                <p className="ingredients-list">alcohol denat., parfum (fragrance), aqua (water), benzyl salicylate, limonene, linalool, citral, geraniol, farnesol, eugenol.</p>
                            </div>
                        </div>
                    </div>

                    {/* Shipping & Returns */}
                    <div className={ `accordion-item ${ openAccordions.shipping ? 'open' : '' }` }>
                        <button className="accordion-trigger" onClick={ () => toggleAccordion( 'shipping' ) }>
                            <span>Shipping & Returns</span>
                            <span className="accordion-icon">{ openAccordions.shipping ? '−' : '+' }</span>
                        </button>
                        <div className="accordion-content">
                            <div className="accordion-content-inner">
                                <p>Complimentary standard shipping across Pakistan. All orders are processed securely via premium Cash on Delivery (COD). Delivery times average 2-4 business days.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    );
}
