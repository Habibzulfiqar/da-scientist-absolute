<?php
/**
 * The Template for displaying product archives (Shop).
 *
 * @package DaScientistAbsolute
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header();
?>

<div id="shop-wrapper" class="container">
    <header class="shop-header">
        <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
    </header>

    <!-- React/Vite mount target with server-rendered fallbacks for SEO -->
    <div id="perfume-grid-root">
        <div class="no-js-fallback-grid">
            <?php
            if ( woocommerce_product_loop() ) {
                while ( have_posts() ) {
                    the_post();
                    global $product;
                    ?>
                    <div class="fallback-card" data-id="<?php echo esc_attr( $product->get_id() ); ?>">
                        <div class="fallback-thumbnail">
                            <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                        </div>
                        <h3><?php the_title(); ?></h3>
                        <p class="price"><?php echo $product->get_price_html(); ?></p>
                    </div>
                    <?php
                }
            } else {
                echo '<p>' . esc_html__( 'No products found.', 'dascentist-absolute' ) . '</p>';
            }
            ?>
        </div>
    </div>
</div>

<?php
get_footer();
