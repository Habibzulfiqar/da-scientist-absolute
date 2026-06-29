<?php
/**
 * The Journal (Blog Index) - Premium Editorial Lookbook
 * Designed to look like a high-end luxury editorial print magazine.
 *
 * @package DaScientistAbsolute
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>

<div id="journal-wrapper" class="editorial-journal-wrapper">

    <!-- Print-Style Page Header -->
    <header class="editorial-journal-header">
        <div class="header-noise-overlay"></div>
        <div class="container header-inner">
            <span class="editorial-eyebrow">The Chronicles of Scent</span>
            <h1 class="editorial-main-title">The Journal</h1>
            <p class="editorial-tagline">Essays on olfactory memory, raw ingredients, and the philosophy of compound layering.</p>
            <div class="editorial-header-divider"></div>
        </div>
    </header>

    <div class="container editorial-journal-body">
        <?php if ( have_posts() ) : ?>
            
            <div class="editorial-journal-grid">
                <?php
                $post_counter = 0;
                while ( have_posts() ) :
                    the_post();
                    $post_counter++;
                    $formatted_index = str_pad( $post_counter, 2, '0', STR_PAD_LEFT );
                    
                    // First post is featured Lookbook banner
                    if ( $post_counter === 1 ) :
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class( 'journal-featured-hero' ); ?>>
                            <a href="<?php the_permalink(); ?>" class="featured-hero-link">
                                <div class="featured-hero-image-wrap">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'full' ); ?>
                                    <?php else : ?>
                                        <div class="editorial-placeholder-bg">
                                            <span>✻</span>
                                        </div>
                                    <?php endif; ?>
                                    <span class="featured-tag">Featured Essay</span>
                                </div>
                                
                                <div class="featured-hero-content">
                                    <span class="card-index"><?php echo esc_html( $formatted_index ); ?></span>
                                    <span class="card-category">
                                        <?php
                                        $cats = get_the_category();
                                        echo ! empty( $cats ) ? esc_html( $cats[0]->name ) : 'Chronicles';
                                        ?>
                                    </span>
                                    <h2 class="card-title"><?php the_title(); ?></h2>
                                    <p class="card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28, '...' ) ); ?></p>
                                    <div class="card-action">
                                        <span class="card-date"><?php echo get_the_date( 'M d, Y' ); ?></span>
                                        <span class="card-read-btn">Explore Essay &rarr;</span>
                                    </div>
                                </div>
                            </a>
                        </article>

                    <?php else : 
                        // Alternating layouts for regular posts
                        $is_even = ( $post_counter % 2 === 0 );
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class( $is_even ? 'journal-editorial-card journal-card--even' : 'journal-editorial-card journal-card--odd' ); ?>>
                            <a href="<?php the_permalink(); ?>" class="editorial-card-link">
                                <div class="editorial-card-image-wrap">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'large' ); ?>
                                    <?php else : ?>
                                        <div class="editorial-placeholder-bg">
                                            <span>✻</span>
                                        </div>
                                    <?php endif; ?>
                                    <span class="card-number-badge"><?php echo esc_html( $formatted_index ); ?></span>
                                </div>

                                <div class="editorial-card-content">
                                    <span class="card-category">
                                        <?php
                                        $cats = get_the_category();
                                        echo ! empty( $cats ) ? esc_html( $cats[0]->name ) : 'Editorial';
                                        ?>
                                    </span>
                                    
                                    <h3 class="card-title"><?php the_title(); ?></h3>
                                    <p class="card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '...' ) ); ?></p>
                                    
                                    <div class="card-footer">
                                        <span class="card-date"><?php echo get_the_date( 'F Y' ); ?></span>
                                        <span class="card-arrow">&rarr;</span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>

            <!-- Print style Pagination -->
            <div class="editorial-pagination-wrap">
                <div class="pagination-line"></div>
                <div class="editorial-pagination">
                    <?php
                    echo paginate_links( array(
                        'prev_text' => '&larr; Prev',
                        'next_text' => 'Next &rarr;',
                    ) );
                    ?>
                </div>
            </div>

        <?php else : ?>
            <div class="editorial-empty-notice">
                <span class="notice-icon">✻</span>
                <p>The library vaults are currently sealed. No essays found.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Editorial newsletter subscribe block -->
    <section class="journal-newsletter-section">
        <div class="newsletter-noise"></div>
        <div class="container newsletter-inner">
            <span class="newsletter-eyebrow">Absolute Correspondence</span>
            <h2 class="newsletter-title">Subscribe to the Chronicles</h2>
            <p class="newsletter-description">Receive private notices, olfactory essays, and batch launch compounding alerts direct from our Lahore laboratory.</p>
            <form class="newsletter-form-minimal" onsubmit="event.preventDefault(); alert('Subscribed to private updates.');">
                <input type="email" placeholder="Your Email Address" required />
                <button type="submit">Join the Club</button>
            </form>
        </div>
    </section>

</div>

<?php
get_footer();
