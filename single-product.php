<?php
/**
 * The Template for displaying all single products.
 *
 * @package DaScientistAbsolute
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header();
?>

<div id="product-wrapper" class="container">
    <!-- React/Vite mount target with server-rendered fallback details for single page SEO -->
    <div id="single-perfume-root">
        <?php
        while ( have_posts() ) :
            the_post();
            global $product;
            ?>
            <div class="fallback-product-detail" data-id="<?php echo esc_attr( $product->get_id() ); ?>">
                <div class="fallback-gallery">
                    <?php echo $product->get_image('woocommerce_single'); ?>
                </div>
                <div class="fallback-summary">
                    <h1><?php the_title(); ?></h1>
                    <p class="price"><?php echo $product->get_price_html(); ?></p>
                    <div class="description">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
            <?php
        endwhile;
        ?>
    </div>
</div>

<?php
get_footer();
