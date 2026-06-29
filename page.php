<?php
/**
 * Page Template — Da Scientist Absolute
 * Wraps standard pages inside a luxury, high-end editorial layout.
 *
 * @package DaScientistAbsolute
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>
    <div class="luxury-page-wrapper">
        
        <!-- Full-bleed Luxury Hero -->
        <header class="luxury-page-hero">
            <div class="hero-noise"></div>
            <div class="hero-glow"></div>
            <div class="container hero-content-inner">
                <span class="hero-eyebrow">Da Scientist Absolute</span>
                <h1 class="hero-title"><?php the_title(); ?></h1>
                <div class="hero-divider-wrap">
                    <span class="line-left"></span>
                    <span class="crest-icon">✻</span>
                    <span class="line-right"></span>
                </div>
            </div>
        </header>

        <!-- Main Content Area with Asymmetric Grid -->
        <div class="luxury-page-body container">
            <div class="luxury-page-grid">
                
                <!-- Left Column: Sticky Brand Crest & Navigation -->
                <aside class="luxury-page-sidebar">
                    <div class="sticky-sidebar-content">
                        <div class="sidebar-crest">
                            <span class="crest-symbol">DA</span>
                            <span class="crest-subtitle">SCIENTIST</span>
                        </div>
                        <div class="sidebar-tagline">
                            Private Compounding Laboratory <br>
                            Lahore, Pakistan
                        </div>
                        <div class="sidebar-decor-line"></div>
                        <p class="sidebar-quote">
                            "Scent is a personal confession, an invisible layer of character that projects before you speak."
                        </p>
                    </div>
                </aside>

                <!-- Right Column: Primary Content -->
                <main class="luxury-page-main-content">
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'luxury-page-article' ); ?>>
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </article>
                </main>

            </div>
        </div>

    </div>
<?php endwhile; ?>

<?php
get_footer();

