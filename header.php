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

                    <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Fragrances</a></li>

                    <!-- MEGA MENU: Collections -->
                    <li class="has-mega-menu">
                        <a href="#" class="mega-trigger" aria-haspopup="true" aria-expanded="false">
                            Collections
                        </a>
                        <div class="mega-panel" role="region" aria-label="Collections Menu">
                            <div class="mega-inner">
                                <div class="mega-col">
                                    <h4 class="mega-col-heading">New Arrivals</h4>
                                    <ul class="mega-links">
                                        <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">All Fragrances</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Latest Drops</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Limited Editions</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Gift Sets</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col-divider"></div>
                                <div class="mega-col">
                                    <h4 class="mega-col-heading">By Fragrance Family</h4>
                                    <ul class="mega-links">
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?product_tag=oud' ) ); ?>">Oud &amp; Resinous</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?product_tag=floral' ) ); ?>">Floral &amp; Rose</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?product_tag=citrus' ) ); ?>">Citrus &amp; Fresh</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/?product_tag=unisex' ) ); ?>">Woody &amp; Musky</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col-divider"></div>
                                <div class="mega-col">
                                    <h4 class="mega-col-heading">By Occasion</h4>
                                    <ul class="mega-links">
                                        <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Everyday Wear</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Evening &amp; Events</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Gifting</a></li>
                                        <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Signature Scents</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col mega-col-feature">
                                    <div class="mega-feature-label">Editor's Pick</div>
                                    <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="mega-feature-name">Noir Absolu</a>
                                    <p class="mega-feature-desc">Dark oud & vetiver.<br>Born from the dark hours.</p>
                                    <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="mega-feature-cta">Discover →</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- MEGA MENU: Notes & Stories -->
                    <li class="has-mega-menu">
                        <a href="#" class="mega-trigger" aria-haspopup="true" aria-expanded="false">
                            Notes and Stories
                        </a>
                        <div class="mega-panel mega-panel--narrow" role="region" aria-label="Notes and Stories Menu">
                            <div class="mega-inner">
                                <div class="mega-col">
                                    <h4 class="mega-col-heading">The Journal</h4>
                                    <ul class="mega-links">
                                        <li><a href="#">The Art of Layering</a></li>
                                        <li><a href="#">Understanding Oud</a></li>
                                        <li><a href="#">Fragrance &amp; Memory</a></li>
                                        <li><a href="#">How to Choose</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col-divider"></div>
                                <div class="mega-col">
                                    <h4 class="mega-col-heading">About the House</h4>
                                    <ul class="mega-links">
                                        <li><a href="#">Our Philosophy</a></li>
                                        <li><a href="#">The Ingredients</a></li>
                                        <li><a href="#">Sustainability</a></li>
                                        <li><a href="#">Contact</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li><a href="#">About</a></li>

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
                    <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Fragrances</a></li>

                    <!-- Mobile Accordion: Collections -->
                    <li class="mobile-accordion-item">
                        <button class="mobile-accordion-trigger" aria-expanded="false">
                            Collections
                            <span class="acc-indicator" aria-hidden="true">+</span>
                        </button>
                        <ul class="mobile-accordion-body">
                            <li><a href="<?php echo esc_url( home_url( '/shop/?product_tag=oud' ) ); ?>">Oud &amp; Resinous</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/shop/?product_tag=floral' ) ); ?>">Floral &amp; Rose</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/shop/?product_tag=citrus' ) ); ?>">Citrus &amp; Fresh</a></li>
                            <li><a href="<?php echo esc_url( home_url( '/shop/?product_tag=unisex' ) ); ?>">Woody &amp; Musky</a></li>
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
