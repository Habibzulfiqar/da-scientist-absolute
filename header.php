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
            <!-- Mobile Menu Toggle (Left on Mobile) -->
            <button class="mobile-nav-toggle" aria-label="Toggle navigation">
                <span class="line line-1"></span>
                <span class="line line-2"></span>
            </button>

            <!-- Brand Logo (Left on Desktop, Center on Mobile) -->
            <div class="site-branding">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo-link">
                    DA SCIENTIST
                </a>
                <p class="logo-tagline">ABSOLUTE</p>
            </div>

            <!-- Desktop Navigation Menu (Center) -->
            <nav class="desktop-navigation">
                <ul class="nav-menu">
                    <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Shop All</a></li>
                    <li><a href="#">Collections</a></li>
                    <li><a href="#">Notes & Stories</a></li>
                    <li><a href="#">About</a></li>
                </ul>
            </nav>

            <!-- Header Right Cluster (Search + Cart) -->
            <div class="header-right">
                <!-- Search Toggle -->
                <div class="header-search-wrap">
                    <button class="search-toggle-btn" aria-label="Search">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                    <!-- Small inline search input that expands -->
                    <div class="search-expanded-form">
                        <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <input type="search" class="search-field" placeholder="Search Scent..." value="" name="s" />
                        </form>
                    </div>
                </div>

                <!-- React Cart Icon Mount Target -->
                <div id="header-cart-icon-root">
                    <!-- Hydrated by React, Fallback standard cart icon for SEO/no-JS -->
                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="fallback-cart-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <path d="M16 10a4 4 0 01-8 0" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Slide-out Off-canvas Panel -->
        <div class="mobile-nav-panel">
            <div class="panel-header">
                <button class="panel-close-btn" aria-label="Close menu">✕</button>
            </div>
            <nav class="mobile-panel-nav">
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Shop All</a></li>
                    <li><a href="#">Collections</a></li>
                    <li><a href="#">Notes & Stories</a></li>
                    <li><a href="#">About</a></li>
                </ul>
            </nav>
        </div>
        <!-- Mobile Panel Overlay -->
        <div class="mobile-panel-overlay"></div>
    </header>

    <!-- Vanilla Javascript Scroll Sticky Listener & Mobile Panel Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var header = document.getElementById('masthead');
            var toggle = document.querySelector('.mobile-nav-toggle');
            var panel = document.querySelector('.mobile-nav-panel');
            var overlay = document.querySelector('.mobile-panel-overlay');
            var closeBtn = document.querySelector('.panel-close-btn');
            var searchToggle = document.querySelector('.search-toggle-btn');
            var searchForm = document.querySelector('.search-expanded-form');

            // Scroll sticky logic
            window.addEventListener('scroll', function() {
                if (window.scrollY > 80) {
                    header.classList.add('is-sticky');
                } else {
                    header.classList.remove('is-sticky');
                }
            });

            // Mobile off-canvas toggle logic
            function openMenu() {
                panel.classList.add('open');
                overlay.classList.add('visible');
                document.body.style.overflow = 'hidden';
            }

            function closeMenu() {
                panel.classList.remove('open');
                overlay.classList.remove('visible');
                document.body.style.overflow = '';
            }

            if (toggle) toggle.addEventListener('click', openMenu);
            if (overlay) overlay.addEventListener('click', closeMenu);
            if (closeBtn) closeBtn.addEventListener('click', closeMenu);

            // Search input toggle logic
            if (searchToggle && searchForm) {
                searchToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    searchForm.classList.toggle('active');
                    if (searchForm.classList.contains('active')) {
                        searchForm.querySelector('input').focus();
                    }
                });
                document.addEventListener('click', function(e) {
                    if (!searchForm.contains(e.target) && e.target !== searchToggle) {
                        searchForm.classList.remove('active');
                    }
                });
            }
        });
    </script>
