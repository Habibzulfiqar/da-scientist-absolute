<?php
/**
 * Da Scientist — Header
 * Dynamic Editor's Pick: pulls newest published product with a real image.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// ── Editor's Pick — latest product with a featured image ─────────────────
$editors_pick = null;
$ep_args = array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => 6,  // sample from newest 6 to find one with image
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => array(
        array(
            'key'     => '_thumbnail_id',
            'compare' => 'EXISTS',
        ),
    ),
);
$ep_query = new WP_Query( $ep_args );
if ( $ep_query->have_posts() ) {
    while ( $ep_query->have_posts() ) {
        $ep_query->the_post();
        $ep_product = wc_get_product( get_the_ID() );
        if ( $ep_product && $ep_product->get_image_id() ) {
            $editors_pick = $ep_product;
            break;
        }
    }
    wp_reset_postdata();
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="header-container">

            <!-- Mobile Menu Toggle -->
            <button class="mobile-nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
                <span class="line line-1"></span>
                <span class="line line-2"></span>
            </button>

            <!-- Brand Logo -->
            <div class="site-branding">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo-link">
                    DA SCIENTIST
                </a>
                <p class="logo-tagline">ABSOLUTE</p>
            </div>

            <!-- Desktop Navigation with Mega Menu -->
            <nav class="desktop-navigation" aria-label="Primary Navigation">
                <ul class="nav-menu">

                    <!-- MEGA MENU: Fragrances (unified catalog hub) -->
                    <li class="has-mega-menu">
                        <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="mega-trigger" aria-haspopup="true" aria-expanded="false">
                            Fragrances
                        </a>
                        <div class="mega-panel" role="region" aria-label="Fragrances Menu">
                            <div class="mega-inner">

                                <!-- Col 1: Discover -->
                                <div class="mega-col">
                                    <h4 class="mega-col-heading">Discover</h4>
                                    <ul class="mega-links">
                                        <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">View All Fragrances</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?orderby=date' ) ); ?>">New Arrivals</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?orderby=popularity' ) ); ?>">Best Sellers</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?collection=gift' ) ); ?>">Gift Sets</a></li>
                                    </ul>
                                </div>

                                <div class="mega-col-divider"></div>

                                <!-- Col 2: By Collection -->
                                <div class="mega-col">
                                    <h4 class="mega-col-heading">By Collection</h4>
                                    <ul class="mega-links">
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?collection=private' ) ); ?>">Private Blends</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?collection=signature' ) ); ?>">Signature Series</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?collection=limited' ) ); ?>">Limited Editions</a></li>
                                    </ul>
                                </div>

                                <div class="mega-col-divider"></div>

                                <!-- Col 3: By Scent Family -->
                                <div class="mega-col">
                                    <h4 class="mega-col-heading">By Scent Family</h4>
                                    <ul class="mega-links">
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?family=oud' ) ); ?>">Oud and Resinous</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?family=floral' ) ); ?>">Floral and Rose</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?family=citrus' ) ); ?>">Citrus and Fresh</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?family=woody' ) ); ?>">Woody and Musky</a></li>
                                    </ul>
                                </div>

                                <div class="mega-col-divider"></div>

                                <!-- Col 4: Editor's Pick — dynamic query -->
                                <div class="mega-col mega-col-feature">
                                    <div class="mega-feature-label">Editor's Pick</div>
                                    <?php if ( $editors_pick ) : ?>
                                        <a href="<?php echo esc_url( $editors_pick->get_permalink() ); ?>" class="mega-feature-name">
                                            <?php echo esc_html( $editors_pick->get_name() ); ?>
                                        </a>
                                        <p class="mega-feature-desc">
                                            <?php
                                            $ep_short = wp_strip_all_tags( $editors_pick->get_short_description() );
                                            echo esc_html( $ep_short
                                                ? wp_trim_words( $ep_short, 8, '.' )
                                                : 'A signature expression of the house.'
                                            );
                                            ?>
                                        </p>
                                        <a href="<?php echo esc_url( $editors_pick->get_permalink() ); ?>" class="mega-feature-cta">Discover &rarr;</a>
                                    <?php else : ?>
                                        <span class="mega-feature-name">Noir Absolu</span>
                                        <p class="mega-feature-desc">A signature expression of the house.</p>
                                        <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="mega-feature-cta">Discover &rarr;</a>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </li>

                    <!-- MEGA MENU: Notes and Stories -->
                    <li class="has-mega-menu">
                        <a href="#" class="mega-trigger" aria-haspopup="true" aria-expanded="false">
                            Notes and Stories
                        </a>
                        <div class="mega-panel mega-panel--narrow" role="region" aria-label="Notes and Stories Menu">
                            <div class="mega-inner">
                                <div class="mega-col">
                                    <h4 class="mega-col-heading">The Journal</h4>
                                    <ul class="mega-links">
                                        <li><a href="<?php echo esc_url( home_url( '/art-of-layering/' ) ); ?>">The Art of Layering</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/understanding-oud/' ) ); ?>">Understanding Oud</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/fragrance-memory/' ) ); ?>">Fragrance and Memory</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/how-to-choose/' ) ); ?>">How to Choose</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col-divider"></div>
                                <div class="mega-col">
                                    <h4 class="mega-col-heading">About the House</h4>
                                    <ul class="mega-links">
                                        <li><a href="<?php echo esc_url( home_url( '/our-philosophy/' ) ); ?>">Our Philosophy</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/the-ingredients/' ) ); ?>">The Ingredients</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/sustainability/' ) ); ?>">Sustainability</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li><a href="<?php echo esc_url( home_url( '/our-philosophy/' ) ); ?>">About</a></li>

                </ul>
            </nav>

            <!-- Header Right Cluster -->
            <div class="header-right">
                <div class="header-search-wrap">
                    <button class="search-toggle-btn" aria-label="Search">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                    <div class="search-expanded-form">
                        <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <input type="search" class="search-field" placeholder="Search Scent..." value="" name="s" />
                        </form>
                    </div>
                </div>

                <!-- Account / Profile Link -->
                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="header-account-link" aria-label="My Account">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>

                <!-- React Cart Icon Mount -->
                <div id="header-cart-icon-root">
                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="fallback-cart-icon" aria-label="Cart">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <path d="M16 10a4 4 0 01-8 0" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Off-Canvas Panel -->
        <div class="mobile-nav-panel" aria-hidden="true">
            <div class="panel-header">
                <div class="panel-logo">DA SCIENTIST</div>
                <button class="panel-close-btn" aria-label="Close menu">✕</button>
            </div>
            <nav class="mobile-panel-nav">
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                    <!-- Mobile Accordion: Fragrances -->
                    <li class="mobile-accordion-item">
                        <button class="mobile-accordion-trigger" aria-expanded="false">
                            Fragrances
                            <span class="acc-indicator" aria-hidden="true">+</span>
                        </button>
                        <ul class="mobile-accordion-body">
                            <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">View All</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/shop/?collection=signature' ) ); ?>">Signature Series</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/shop/?collection=private' ) ); ?>">Private Blends</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/shop/?family=oud' ) ); ?>">Oud and Resinous</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/shop/?family=floral' ) ); ?>">Floral and Rose</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/shop/?family=citrus' ) ); ?>">Citrus and Fresh</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/shop/?family=woody' ) ); ?>">Woody and Musky</a></li>
                        </ul>
                    </li>

                    <!-- Mobile Accordion: Notes & Stories -->
                    <li class="mobile-accordion-item">
                        <button class="mobile-accordion-trigger" aria-expanded="false">
                            Notes and Stories
                            <span class="acc-indicator" aria-hidden="true">+</span>
                        </button>
                        <ul class="mobile-accordion-body">
                            <li><a href="#">The Art of Layering</a></li>
                            <li><a href="#">Understanding Oud</a></li>
                            <li><a href="#">Our Philosophy</a></li>
                        </ul>
                    </li>

                    <li><a href="#">About</a></li>
                </ul>
            </nav>
            <div class="panel-footer-note">Free shipping above ₨5,000</div>
        </div>
        <div class="mobile-panel-overlay"></div>
    </header>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var header  = document.getElementById('masthead');
        var toggle  = document.querySelector('.mobile-nav-toggle');
        var panel   = document.querySelector('.mobile-nav-panel');
        var overlay = document.querySelector('.mobile-panel-overlay');
        var closeBtn = document.querySelector('.panel-close-btn');
        var searchToggle = document.querySelector('.search-toggle-btn');
        var searchForm   = document.querySelector('.search-expanded-form');

        // ── Sticky scroll ──────────────────────────────────────────────────────
        window.addEventListener('scroll', function () {
            header.classList.toggle('is-sticky', window.scrollY > 80);
        }, { passive: true });

        // ── Mobile panel ───────────────────────────────────────────────────────
        function openMenu() {
            panel.classList.add('open');
            panel.setAttribute('aria-hidden', 'false');
            overlay.classList.add('visible');
            toggle.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }
        function closeMenu() {
            panel.classList.remove('open');
            panel.setAttribute('aria-hidden', 'true');
            overlay.classList.remove('visible');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
        if (toggle)   toggle.addEventListener('click', openMenu);
        if (overlay)  overlay.addEventListener('click', closeMenu);
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);

        // ── Mobile accordion ───────────────────────────────────────────────────
        document.querySelectorAll('.mobile-accordion-trigger').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var item = btn.closest('.mobile-accordion-item');
                var body = item.querySelector('.mobile-accordion-body');
                var isOpen = item.classList.contains('acc-open');
                // Close all
                document.querySelectorAll('.mobile-accordion-item.acc-open').forEach(function (el) {
                    el.classList.remove('acc-open');
                    el.querySelector('.mobile-accordion-trigger').setAttribute('aria-expanded', 'false');
                    el.querySelector('.mobile-accordion-body').style.maxHeight = '0';
                });
                if (!isOpen) {
                    item.classList.add('acc-open');
                    btn.setAttribute('aria-expanded', 'true');
                    body.style.maxHeight = body.scrollHeight + 'px';
                }
            });
        });

        // ── Desktop mega menu hover ────────────────────────────────────────────
        document.querySelectorAll('.has-mega-menu').forEach(function (item) {
            var trigger = item.querySelector('.mega-trigger');
            var megaPanel = item.querySelector('.mega-panel');
            var openTimer, closeTimer;

            function openMega() {
                clearTimeout(closeTimer);
                openTimer = setTimeout(function () {
                    item.classList.add('mega-open');
                    trigger.setAttribute('aria-expanded', 'true');
                }, 80);
            }
            function closeMega() {
                clearTimeout(openTimer);
                closeTimer = setTimeout(function () {
                    item.classList.remove('mega-open');
                    trigger.setAttribute('aria-expanded', 'false');
                }, 160);
            }

            item.addEventListener('mouseenter', openMega);
            item.addEventListener('mouseleave', closeMega);
            trigger.addEventListener('focus', openMega);
            megaPanel.addEventListener('focusout', function (e) {
                if (!item.contains(e.relatedTarget)) closeMega();
            });
            trigger.addEventListener('click', function (e) { e.preventDefault(); });
        });

        // ── Search toggle ──────────────────────────────────────────────────────
        if (searchToggle && searchForm) {
            searchToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                searchForm.classList.toggle('active');
                if (searchForm.classList.contains('active')) {
                    searchForm.querySelector('input').focus();
                }
            });
            document.addEventListener('click', function (e) {
                if (!searchForm.contains(e.target) && e.target !== searchToggle) {
                    searchForm.classList.remove('active');
                }
            });
        }
    });
    </script>
