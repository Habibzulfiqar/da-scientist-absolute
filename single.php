<?php
/**
 * Single Journal Post - Premium Editorial Reader Template
 * Designed to look like a high-end luxury print magazine article.
 *
 * @package DaScientistAbsolute
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>

<main id="primary" class="site-main editorial-article-wrapper">
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'editorial-article-core' ); ?>>
            
            <!-- Cover Hero Banner (Full Screen Height Look) -->
            <header class="editorial-article-cover">
                <div class="cover-image-container">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'full' ); ?>
                    <?php else : ?>
                        <div class="cover-placeholder-gradient"></div>
                    <?php endif; ?>
                    <div class="cover-vignette-overlay"></div>
                    <div class="cover-noise-overlay"></div>
                </div>

                <div class="container cover-content-inner">
                    <span class="cover-eyebrow">
                        <?php
                        $cats = get_the_category();
                        echo ! empty( $cats ) ? esc_html( $cats[0]->name ) : 'Chronicles';
                        ?>
                    </span>
                    <h1 class="cover-article-title"><?php the_title(); ?></h1>
                    
                    <div class="cover-meta-strip">
                        <span class="meta-date"><?php echo get_the_date( 'F d, Y' ); ?></span>
                        <span class="meta-dot">✻</span>
                        <span class="meta-author">By <?php the_author(); ?></span>
                    </div>
                </div>
            </header>

            <!-- Article Body with Asymmetric Reader Layout -->
            <div class="container article-reader-container">
                <div class="article-reader-grid">
                    
                    <!-- Left Sidebar: Sticky Meta & Scent Profile -->
                    <aside class="article-reader-sidebar">
                        <div class="sticky-sidebar-inner">
                            <div class="sidebar-block">
                                <span class="sidebar-block-label">ESTIMATED READ</span>
                                <span class="sidebar-block-val">
                                    <?php
                                    $word_count = str_word_count( strip_tags( get_post_field( 'post_content', get_the_ID() ) ) );
                                    $reading_time = ceil( $word_count / 200 );
                                    echo esc_html( $reading_time ) . ' Minute' . ( $reading_time > 1 ? 's' : '' );
                                    ?>
                                </span>
                            </div>

                            <div class="sidebar-block">
                                <span class="sidebar-block-label">CATALOG CODE</span>
                                <span class="sidebar-block-val">DSA-B<?php the_ID(); ?></span>
                            </div>

                            <div class="sidebar-decor-cross">✻</div>

                            <div class="sidebar-block">
                                <span class="sidebar-block-label">LAB DISPATCH</span>
                                <span class="sidebar-block-val">Lahore central Laboratory</span>
                            </div>
                        </div>
                    </aside>

                    <!-- Right Column: The Narrative with Drop-caps and spacing -->
                    <div class="article-reader-content">
                        <div class="editorial-body-narrative">
                            <?php the_content(); ?>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Bottom Navigation Section -->
            <footer class="editorial-article-footer">
                <div class="container footer-inner">
                    <div class="footer-navigation-row">
                        <div class="nav-direction nav-prev-article">
                            <?php
                            $prev_post = get_previous_post();
                            if ( $prev_post ) :
                                ?>
                                <span class="nav-eyebrow">&larr; Previous Essay</span>
                                <a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" class="nav-title">
                                    <?php echo esc_html( $prev_post->post_title ); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="nav-hairline-separator"></div>

                        <div class="nav-direction nav-next-article">
                            <?php
                            $next_post = get_next_post();
                            if ( $next_post ) :
                                ?>
                                <span class="nav-eyebrow">Next Essay &rarr;</span>
                                <a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="nav-title">
                                    <?php echo esc_html( $next_post->post_title ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="footer-back-to-journal">
                        <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="back-link">Return to the Journal</a>
                    </div>
                </div>
            </footer>

        </article>
        <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
