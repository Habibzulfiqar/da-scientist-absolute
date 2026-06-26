    <footer id="colophon" class="site-footer">
        <div class="footer-inner container">

            <!-- Col 1: Brand -->
            <div class="footer-col footer-col--brand">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo">DA SCIENTIST</a>
                <p class="footer-sub">ABSOLUTE</p>
                <p class="footer-manifesto">
                    Every bottle is a private confession.<br>
                    Wear it like you mean it.
                </p>
                <div class="footer-social">
                    <a href="https://instagram.com/dascentist" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                            <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                        </svg>
                        @dascentist
                    </a>
                </div>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="footer-col">
                <h4 class="footer-heading">Explore</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Shop All Fragrances</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/shop/?product_tag=oud' ) ); ?>">Oud &amp; Resinous</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/shop/?product_tag=floral' ) ); ?>">Floral &amp; Rose</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/shop/?product_tag=citrus' ) ); ?>">Citrus &amp; Fresh</a></li>
                    <li><a href="#">Gift Sets</a></li>
                    <li><a href="#">New Arrivals</a></li>
                </ul>
            </div>

            <!-- Col 3: Customer Care -->
            <div class="footer-col">
                <h4 class="footer-heading">Customer Care</h4>
                <ul class="footer-links">
                    <li><a href="#">Track Your Order</a></li>
                    <li><a href="#">Returns &amp; Exchange</a></li>
                    <li><a href="#">Shipping Policy</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>

            <!-- Col 4: Shipping + Pledge -->
            <div class="footer-col footer-col--pledge">
                <h4 class="footer-heading">Our Promise</h4>
                <ul class="footer-pledge-list">
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                        Free delivery above ₨5,000
                    </li>
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Cash on Delivery — nationwide
                    </li>
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Dispatched within 24 hours
                    </li>
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        Sealed &amp; tamper-proof packaging
                    </li>
                </ul>
            </div>

        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
            <div class="container footer-bottom-inner">
                <p class="footer-copy">
                    &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.
                </p>
                <div class="footer-payment-badges">
                    <span class="payment-badge">COD</span>
                    <span class="payment-badge">Jazz Cash</span>
                    <span class="payment-badge">Easy Paisa</span>
                    <span class="payment-badge">Bank Transfer</span>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
