<?php
/**
 * Homepage Template — Da Scientist Absolute
 * Full luxury storefront landing page.
 *
 * @package DaScientistAbsolute
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

// Fetch 3 featured products for the collection strip
$featured_products = wc_get_products( array(
    'limit'   => 3,
    'status'  => 'publish',
    'orderby' => 'date',
    'order'   => 'DESC',
) );
?>

<!-- ═══════════════════════════════════════════════════════════════
     HERO — Full viewport dark cinematic section
     ═══════════════════════════════════════════════════════════════ -->
<section class="home-hero" aria-label="Hero">
    <div class="hero-noise"></div>
    <div class="hero-content">
        <p class="hero-eyebrow">Est. 2024 &mdash; Karachi, Pakistan</p>
        <h1 class="hero-wordmark">
            <span class="hero-wordmark-main">DA SCIENTIST</span>
            <span class="hero-wordmark-sub">ABSOLUTE</span>
        </h1>
        <p class="hero-manifesto">
            Every bottle is a private confession.<br>
            Worn by those who refuse to be ordinary.
        </p>
        <div class="hero-cta-group">
            <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="hero-cta-primary">
                Discover the Collection
            </a>
            <a href="#scent-philosophy" class="hero-cta-secondary">
                Our Olfactory Language ↓
            </a>
        </div>
    </div>
    <div class="hero-scroll-hint" aria-hidden="true">
        <span class="scroll-line"></span>
        <span class="scroll-label">Scroll</span>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════
     FEATURED COLLECTION STRIP
     ═══════════════════════════════════════════════════════════════ -->
<section class="home-featured" aria-label="Featured Fragrances">
    <div class="container">
        <div class="section-label-row">
            <span class="section-label">The Collection</span>
            <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="section-link">View All →</a>
        </div>
        <h2 class="section-title">New Arrivals</h2>
    </div>

    <div class="featured-strip">
        <?php if ( $featured_products ) : ?>
            <?php foreach ( $featured_products as $product ) : 
                $img_id  = $product->get_image_id();
                $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '';
            ?>
            <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="featured-card">
                <div class="featured-card-image">
                    <?php if ( $img_url ) : ?>
                        <img src="<?php echo esc_url( $img_url ); ?>"
                             alt="<?php echo esc_attr( $product->get_name() ); ?>"
                             loading="lazy" />
                    <?php else : ?>
                        <div class="featured-card-placeholder"></div>
                    <?php endif; ?>
                    <div class="featured-card-overlay">
                        <span class="featured-card-cta">Add to Cart</span>
                    </div>
                </div>
                <div class="featured-card-body">
                    <h3 class="featured-card-name"><?php echo esc_html( $product->get_name() ); ?></h3>
                    <p class="featured-card-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════
     BRAND STORY — 2-column: text + editorial image
     ═══════════════════════════════════════════════════════════════ -->
<section class="home-story container" aria-label="Brand Story">
    <div class="story-text">
        <span class="section-label">The House</span>
        <h2 class="story-heading">Scent Is the<br>Most Honest Language</h2>
        <p class="story-body">
            Da Scientist was born from a single obsession: the belief that fragrance should provoke feeling, not just impression. We source raw materials from the oldest oud markets in the Middle East, the rose fields of Isparta, and the bergamot groves of Calabria.
        </p>
        <p class="story-body">
            Every formula is handcrafted in small batches, bottled in Karachi. No shortcuts. No synthetic shortcuts. Only precision and restraint — the hallmarks of true luxury.
        </p>
        <a href="#" class="story-cta">Read Our Story →</a>
    </div>
    <div class="story-image">
        <?php
        // Use the first demo product's image as editorial hero
        $story_products = wc_get_products( array( 'limit' => 1, 'status' => 'publish' ) );
        if ( $story_products && $story_products[0]->get_image_id() ) {
            echo wp_get_attachment_image( $story_products[0]->get_image_id(), 'large', false, array( 'class' => 'story-img', 'loading' => 'lazy' ) );
        }
        ?>
        <div class="story-image-label" aria-hidden="true">
            <span>Crafted in Small Batches</span>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════
     SCENT PHILOSOPHY — 3 pillars
     ═══════════════════════════════════════════════════════════════ -->
<section class="home-philosophy" id="scent-philosophy" aria-label="Scent Philosophy">
    <div class="container">
        <div class="section-label-row center">
            <span class="section-label">Olfactory Language</span>
        </div>
        <h2 class="section-title center">Three Pillars of the House</h2>
        <div class="philosophy-grid">

            <div class="philosophy-pillar">
                <div class="pillar-glyph" aria-hidden="true">&#x2767;</div>
                <h3 class="pillar-name">Oud &amp; Resinous</h3>
                <p class="pillar-desc">
                    The ancient heartwood of the agarwood tree. Smoky, medicinal, and deeply intimate — the backbone of all great oriental perfumery.
                </p>
                <a href="<?php echo esc_url( home_url( '/shop/?product_tag=oud' ) ); ?>" class="pillar-link">
                    Shop Oud →
                </a>
            </div>

            <div class="philosophy-pillar philosophy-pillar--center">
                <div class="pillar-glyph" aria-hidden="true">&#x2698;</div>
                <h3 class="pillar-name">Floral &amp; Rose</h3>
                <p class="pillar-desc">
                    From Bulgarian absolute to Turkish rose otto — each petal a different frequency. Luminous, sensual, and without compromise.
                </p>
                <a href="<?php echo esc_url( home_url( '/shop/?product_tag=floral' ) ); ?>" class="pillar-link">
                    Shop Floral →
                </a>
            </div>

            <div class="philosophy-pillar">
                <div class="pillar-glyph" aria-hidden="true">&#x2609;</div>
                <h3 class="pillar-name">Citrus &amp; Fresh</h3>
                <p class="pillar-desc">
                    Cold-pressed bergamot from Calabria and Japanese yuzu — volatile, electric, and gone before you know it. Wear it twice a day.
                </p>
                <a href="<?php echo esc_url( home_url( '/shop/?product_tag=citrus' ) ); ?>" class="pillar-link">
                    Shop Citrus →
                </a>
            </div>

        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════
     FOOTER CTA BAND
     ═══════════════════════════════════════════════════════════════ -->
<section class="home-cta-band" aria-label="Call to Action">
    <div class="container home-cta-inner">
        <h2 class="cta-band-heading">Find Your Signature Scent</h2>
        <p class="cta-band-sub">Cash on delivery. Free shipping above ₨5,000. Ships nationwide.</p>
        <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="cta-band-btn">
            Shop the Full Collection
        </a>
    </div>
</section>

<?php get_footer(); ?>
