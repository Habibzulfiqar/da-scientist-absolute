import React, { useEffect, useState } from 'react';
import { useCart } from '../context/CartContext';
import './ProductDetail.css';

/**
 * ProductDetail — Single Product Page
 * Renders the luxury product detail with image carousel,
 * size variation swatches, and add-to-cart.
 * Mounted onto #single-perfume-root in single-product.php.
 */
export default function ProductDetail( { productId } ) {
    const [ product, setProduct ]         = useState( null );
    const [ activeImage, setActiveImage ] = useState( 0 );
    const [ activeVariation, setVariation ] = useState( null );
    const [ quantity, setQuantity ]       = useState( 1 );
    const [ zoomed, setZoomed ]           = useState( false );
    const { addToCart, isLoading }        = useCart();

    useEffect( () => {
        if ( ! productId ) return;
        fetch( `${ daScientistGlobals.store_api_url }products/${ productId }` )
            .then( r => r.json() )
            .then( data => {
                setProduct( data );
                if ( data.variations?.length ) setVariation( data.variations[0] );
            } );
    }, [ productId ] );

    if ( ! product ) {
        return <div className="product-loading">Loading…</div>;
    }

    const images      = product.images ?? [];
    const variations  = product.variations ?? [];
    const hasVariations = variations.length > 0;

    const handleAddToCart = () => {
        addToCart( product.id, quantity, activeVariation?.id ?? null );
    };

    return (
        <section className="product-detail">

            {/* Gallery */}
            <div className="product-gallery">
                <div
                    className={ `gallery-main ${ zoomed ? 'zoomed' : '' }` }
                    onClick={ () => setZoomed( ! zoomed ) }
                    title={ zoomed ? 'Click to zoom out' : 'Click to zoom in' }
                >
                    { images[ activeImage ] && (
                        <img
                            src={ images[ activeImage ].src }
                            alt={ images[ activeImage ].alt || product.name }
                        />
                    ) }
                </div>
                { images.length > 1 && (
                    <div className="gallery-thumbnails">
                        { images.map( ( img, i ) => (
                            <button
                                key={ img.id }
                                className={ `thumb-btn ${ activeImage === i ? 'active' : '' }` }
                                onClick={ () => setActiveImage( i ) }
                            >
                                <img src={ img.src } alt={ img.alt } />
                            </button>
                        ) ) }
                    </div>
                ) }
            </div>

            {/* Summary */}
            <div className="product-summary">
                <p className="product-category">
                    { product.categories?.[0]?.name ?? 'Fragrance' }
                </p>
                <h1 className="product-title">{ product.name }</h1>
                <div className="product-price"
                    dangerouslySetInnerHTML={ { __html: product.price_html } }
                />

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

                {/* Quantity */}
                <div className="qty-row">
                    <button className="qty-btn" onClick={ () => setQuantity( q => Math.max( 1, q - 1 ) ) }>−</button>
                    <span className="qty-value">{ quantity }</span>
                    <button className="qty-btn" onClick={ () => setQuantity( q => q + 1 ) }>+</button>
                </div>

                <button
                    className="add-to-cart-cta"
                    disabled={ isLoading }
                    onClick={ handleAddToCart }
                >
                    { isLoading ? 'Adding…' : 'Add to Cart' }
                </button>

                {/* Description */}
                { product.description && (
                    <div
                        className="product-description"
                        dangerouslySetInnerHTML={ { __html: product.description } }
                    />
                ) }
            </div>

        </section>
    );
}
