<?php
/**
 * Archive Template: Product Shop
 * Renders the luxury shop catalog with desktop sidebar + React grid.
 *
 * @package DaScientistAbsolute
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

// Fetch all product categories for the sidebar
$product_categories = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'exclude'    => get_option( 'default_product_cat' ),
    'orderby'    => 'name',
) );

// Fetch all product tags for sidebar
$product_tags = get_terms( array(
    'taxonomy'   => 'product_tag',
    'hide_empty' => true,
    'orderby'    => 'count',
    'order'      => 'DESC',
    'number'     => 12,
) );
?>

<div id="shop-wrapper">

    <!-- Shop Title Bar -->
    <header class="shop-header">
        <div class="container shop-header-inner">
            <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
            <p class="shop-subtitle">
                <?php
                // Dynamic clean query matching React grid logic (currently 6 displayable products)
                $args = array(
                    'post_type'      => 'product',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                );
                $p_query = get_posts( $args );
                $display_count = 0;
                foreach ( $p_query as $pid ) {
                    $prod = wc_get_product( $pid );
                    if ( $prod ) {
                        $name = strtolower( trim( $prod->get_name() ) );
                        $has_image = (bool) $prod->get_image_id();
                        if ( $name !== 'content marketing' && $has_image ) {
                            $display_count++;
                        }
                    }
                }
                echo esc_html( $display_count );
                ?> fragrances
            </p>
        </div>
    </header>

    <!-- Shop Body: Sidebar + Grid -->
    <div class="container shop-body">

        <!-- ── Left Sidebar (Desktop only) ── -->
        <aside class="shop-sidebar" aria-label="Product Filters">

            <!-- Mobile Filter Toggle (visible < 1024px) -->
            <button class="mobile-filter-toggle" aria-expanded="false" aria-controls="sidebar-filter-body">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="20" y2="12"/><line x1="12" y1="18" x2="20" y2="18"/>
                </svg>
                Filters
                <span class="filter-toggle-plus">+</span>
            </button>

            <div id="sidebar-filter-body" class="sidebar-filter-body">

                <!-- ─ Filter Group: Category ─ -->
                <?php if ( $product_categories && ! is_wp_error( $product_categories ) ) : ?>
                <div class="sidebar-group" data-group="category">
                    <button class="sidebar-group-header" aria-expanded="true">
                        <span>Category</span>
                        <svg class="sidebar-chevron" width="10" height="6" viewBox="0 0 10 6" fill="none">
                            <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.2"/>
                        </svg>
                    </button>
                    <ul class="sidebar-group-body">
                        <?php foreach ( $product_categories as $cat ) : ?>
                        <li class="sidebar-checkbox-row">
                            <label class="custom-checkbox-label">
                                <input
                                    type="checkbox"
                                    class="sidebar-filter-input"
                                    data-filter-type="category"
                                    data-filter-slug="<?php echo esc_attr( $cat->slug ); ?>"
                                    id="cat-<?php echo esc_attr( $cat->slug ); ?>"
                                />
                                <span class="custom-checkbox"></span>
                                <span class="checkbox-label-text"><?php echo esc_html( $cat->name ); ?></span>
                                <span class="checkbox-count"><?php echo esc_html( $cat->count ); ?></span>
                            </label>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- ─ Filter Group: Fragrance Family ─ -->
                <?php if ( $product_tags && ! is_wp_error( $product_tags ) ) : ?>
                <div class="sidebar-group" data-group="tag">
                    <button class="sidebar-group-header" aria-expanded="true">
                        <span>Fragrance Family</span>
                        <svg class="sidebar-chevron" width="10" height="6" viewBox="0 0 10 6" fill="none">
                            <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.2"/>
                        </svg>
                    </button>
                    <ul class="sidebar-group-body">
                        <?php foreach ( $product_tags as $tag ) : ?>
                        <li class="sidebar-checkbox-row">
                            <label class="custom-checkbox-label">
                                <input
                                    type="checkbox"
                                    class="sidebar-filter-input"
                                    data-filter-type="tag"
                                    data-filter-slug="<?php echo esc_attr( $tag->slug ); ?>"
                                    id="tag-<?php echo esc_attr( $tag->slug ); ?>"
                                />
                                <span class="custom-checkbox"></span>
                                <span class="checkbox-label-text"><?php echo esc_html( $tag->name ); ?></span>
                                <span class="checkbox-count"><?php echo esc_html( $tag->count ); ?></span>
                            </label>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- ─ Filter Group: Notes ─ -->
                <div class="sidebar-group" data-group="notes">
                    <button class="sidebar-group-header" aria-expanded="false">
                        <span>Concentration</span>
                        <svg class="sidebar-chevron" width="10" height="6" viewBox="0 0 10 6" fill="none">
                            <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.2"/>
                        </svg>
                    </button>
                    <ul class="sidebar-group-body" style="max-height:0;">
                        <?php
                        $concentrations = array(
                            'eau-de-parfum'  => 'Eau de Parfum',
                            'extrait'        => 'Extrait de Parfum',
                            'eau-de-toilette'=> 'Eau de Toilette',
                        );
                        foreach ( $concentrations as $slug => $label ) : ?>
                        <li class="sidebar-checkbox-row">
                            <label class="custom-checkbox-label">
                                <input
                                    type="checkbox"
                                    class="sidebar-filter-input"
                                    data-filter-type="tag"
                                    data-filter-slug="<?php echo esc_attr( $slug ); ?>"
                                    id="conc-<?php echo esc_attr( $slug ); ?>"
                                />
                                <span class="custom-checkbox"></span>
                                <span class="checkbox-label-text"><?php echo esc_html( $label ); ?></span>
                            </label>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- ─ Clear Filters ─ -->
                <button class="sidebar-clear-btn" id="sidebar-clear-all" style="display:none;">
                    Clear All Filters ✕
                </button>

            </div>
        </aside>

        <!-- ── Right: React Product Grid ── -->
        <main class="shop-grid-col" id="main-content">
            <div id="perfume-grid-root">
                <!-- Server-rendered SEO fallback — React replaces this on hydration -->
                <div class="no-js-fallback-grid">
                    <?php
                    if ( woocommerce_product_loop() ) {
                        while ( have_posts() ) {
                            the_post();
                            global $product;
                            ?>
                            <div class="fallback-card" data-id="<?php echo esc_attr( $product->get_id() ); ?>">
                                <div class="fallback-thumbnail">
                                    <?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
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
        </main>

    </div><!-- .shop-body -->
</div><!-- #shop-wrapper -->

<script>
// ── Sidebar filter accordion ───────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    // Group accordion toggle
    document.querySelectorAll('.sidebar-group-header').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group = btn.closest('.sidebar-group');
            var body  = group.querySelector('.sidebar-group-body');
            var isOpen = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            body.style.maxHeight = isOpen ? '0' : body.scrollHeight + 'px';
            group.classList.toggle('group-open', !isOpen);
        });
        // Open by default if aria-expanded="true"
        if (btn.getAttribute('aria-expanded') === 'true') {
            var body = btn.closest('.sidebar-group').querySelector('.sidebar-group-body');
            body.style.maxHeight = 'none';
            btn.closest('.sidebar-group').classList.add('group-open');
        }
    });

    // Mobile filter toggle
    var mobileFilterBtn  = document.querySelector('.mobile-filter-toggle');
    var sidebarFilterBody = document.getElementById('sidebar-filter-body');
    if (mobileFilterBtn && sidebarFilterBody) {
        mobileFilterBtn.addEventListener('click', function () {
            var isOpen = mobileFilterBtn.getAttribute('aria-expanded') === 'true';
            mobileFilterBtn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            sidebarFilterBody.classList.toggle('mobile-open', !isOpen);
            mobileFilterBtn.querySelector('.filter-toggle-plus').textContent = isOpen ? '+' : '−';
        });
    }

    // Checkbox filter dispatch — React listens for this
    var clearBtn = document.getElementById('sidebar-clear-all');
    function getActiveFilters() {
        var cats = [], tags = [];
        document.querySelectorAll('.sidebar-filter-input:checked').forEach(function (el) {
            if (el.dataset.filterType === 'category') cats.push(el.dataset.filterSlug);
            if (el.dataset.filterType === 'tag')      tags.push(el.dataset.filterSlug);
        });
        return { categories: cats, tags: tags };
    }

    document.querySelectorAll('.sidebar-filter-input').forEach(function (input) {
        input.addEventListener('change', function () {
            var filters = getActiveFilters();
            var hasActive = filters.categories.length + filters.tags.length > 0;
            if (clearBtn) clearBtn.style.display = hasActive ? 'block' : 'none';
            window.dispatchEvent(new CustomEvent('dascentist-filter-change', { detail: filters }));
        });
    });

    // Clear all filters
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            document.querySelectorAll('.sidebar-filter-input:checked').forEach(function (el) {
                el.checked = false;
            });
            clearBtn.style.display = 'none';
            window.dispatchEvent(new CustomEvent('dascentist-filter-change', { detail: { categories: [], tags: [] } }));
        });
    }
});
</script>

<?php get_footer(); ?>
