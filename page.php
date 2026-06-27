<?php
/**
 * Page Template — Da Scientist Absolute
 * Wraps standard pages (Checkout, Cart, About) inside the theme's responsive container.
 *
 * @package DaScientistAbsolute
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>

<main id="primary" class="site-main page-template-main">
    <div class="container page-container">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header page-header-stack">
                    <h1 class="entry-title page-title"><?php the_title(); ?></h1>
                </header>
                <div class="entry-content page-entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
