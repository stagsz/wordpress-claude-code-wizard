<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-content">
            
            <!-- About Section -->
            <div class="footer-section">
                <h3><?php _e('About Our Directory', 'my-custom-theme'); ?></h3>
                <p><?php _e('Discover the best secondhand stores and boutiques in Gothenburg. Find unique treasures, vintage fashion, and sustainable shopping options in your area.', 'my-custom-theme'); ?></p>
                
                <!-- Social Links -->
                <div class="footer-social">
                    <a href="#" class="social-link" aria-label="<?php _e('Follow us on Facebook', 'my-custom-theme'); ?>">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="<?php _e('Follow us on Instagram', 'my-custom-theme'); ?>">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="<?php _e('Follow us on Twitter', 'my-custom-theme'); ?>">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="footer-section">
                <h3><?php _e('Quick Links', 'my-custom-theme'); ?></h3>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'my-custom-theme'); ?></a></li>
                    <?php if (post_type_exists('secondhand_store')) : ?>
                        <li><a href="<?php echo get_post_type_archive_link('secondhand_store'); ?>"><?php _e('Browse Stores', 'my-custom-theme'); ?></a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php _e('About Us', 'my-custom-theme'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php _e('Contact', 'my-custom-theme'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/submit-store')); ?>"><?php _e('Submit a Store', 'my-custom-theme'); ?></a></li>
                </ul>
            </div>
            
            <!-- Store Categories -->
            <div class="footer-section">
                <h3><?php _e('Store Categories', 'my-custom-theme'); ?></h3>
                <ul class="footer-links">
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'store_category',
                        'hide_empty' => false,
                        'number' => 6,
                        'orderby' => 'count',
                        'order' => 'DESC'
                    ));
                    
                    if (!is_wp_error($categories) && !empty($categories)) :
                        foreach ($categories as $category) :
                    ?>
                        <li><a href="<?php echo get_term_link($category); ?>"><?php echo esc_html($category->name); ?></a></li>
                    <?php 
                        endforeach;
                    else :
                    ?>
                        <li><a href="#"><?php _e('Vintage & Retro', 'my-custom-theme'); ?></a></li>
                        <li><a href="#"><?php _e('Designer Consignment', 'my-custom-theme'); ?></a></li>
                        <li><a href="#"><?php _e('Charity Shops', 'my-custom-theme'); ?></a></li>
                        <li><a href="#"><?php _e('Children\'s Items', 'my-custom-theme'); ?></a></li>
                        <li><a href="#"><?php _e('Books & Media', 'my-custom-theme'); ?></a></li>
                        <li><a href="#"><?php _e('Home & Garden', 'my-custom-theme'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="footer-section">
                <h3><?php _e('Contact Info', 'my-custom-theme'); ?></h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php _e('Gothenburg, Sweden', 'my-custom-theme'); ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:info@secondhandgbg.se">info@secondhandgbg.se</a>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <a href="tel:+46123456789">+46 123 456 789</a>
                    </div>
                </div>
                
                <!-- Newsletter Signup -->
                <div class="newsletter-signup">
                    <h4><?php _e('Stay Updated', 'my-custom-theme'); ?></h4>
                    <form class="newsletter-form" method="post" action="#" aria-label="<?php _e('Newsletter signup', 'my-custom-theme'); ?>">
                        <input type="email" 
                               placeholder="<?php _e('Your email address', 'my-custom-theme'); ?>" 
                               required 
                               aria-label="<?php _e('Email address', 'my-custom-theme'); ?>">
                        <button type="submit"><?php _e('Subscribe', 'my-custom-theme'); ?></button>
                    </form>
                </div>
            </div>
            
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-copyright">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'my-custom-theme'); ?></p>
            </div>
            
            <!-- Footer Navigation -->
            <?php if (has_nav_menu('footer')) : ?>
                <nav class="footer-navigation" aria-label="<?php _e('Footer Navigation', 'my-custom-theme'); ?>">
                    <?php wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_class' => 'footer-menu',
                        'container' => false,
                        'depth' => 1,
                    )); ?>
                </nav>
            <?php else : ?>
                <nav class="footer-navigation" aria-label="<?php _e('Footer Navigation', 'my-custom-theme'); ?>">
                    <ul class="footer-menu">
                        <li><a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php _e('Privacy Policy', 'my-custom-theme'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/terms-of-service')); ?>"><?php _e('Terms of Service', 'my-custom-theme'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/sitemap')); ?>"><?php _e('Sitemap', 'my-custom-theme'); ?></a></li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>