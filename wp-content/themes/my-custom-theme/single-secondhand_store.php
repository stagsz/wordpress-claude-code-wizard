<?php
/**
 * Single Store Template
 * 
 * Displays detailed information about a single secondhand store
 */

get_header(); ?>

<?php while (have_posts()) : the_post(); 
    // Get store meta data
    $store_id = get_the_ID();
    $address = get_post_meta($store_id, '_store_address', true);
    $neighborhood = get_post_meta($store_id, '_store_neighborhood', true);
    $phone = get_post_meta($store_id, '_store_phone', true);
    $website = get_post_meta($store_id, '_store_website', true);
    $email = get_post_meta($store_id, '_store_email', true);
    $rating = get_post_meta($store_id, '_store_rating', true);
    $review_count = get_post_meta($store_id, '_store_review_count', true);
    $price_range = get_post_meta($store_id, '_store_price_range', true);
    $directions_link = get_post_meta($store_id, '_store_directions_link', true);
    $instagram = get_post_meta($store_id, '_store_instagram', true);
    $facebook = get_post_meta($store_id, '_store_facebook', true);
    $specialties = get_post_meta($store_id, '_store_specialties', true);
    $features = get_post_meta($store_id, '_store_features', true);
    $payment_methods = get_post_meta($store_id, '_store_payment_methods', true);
    $languages = get_post_meta($store_id, '_store_languages', true);
    $parking = get_post_meta($store_id, '_store_parking', true);
    $accessibility = get_post_meta($store_id, '_store_accessibility', true);
    $latitude = get_post_meta($store_id, '_store_latitude', true);
    $longitude = get_post_meta($store_id, '_store_longitude', true);
    
    // Get opening hours
    $opening_hours = get_store_opening_hours($store_id);
    $is_open = is_store_open($store_id);
    
    // Get store categories and locations
    $categories = get_the_terms($store_id, 'store_category');
    $locations = get_the_terms($store_id, 'store_location');
    $store_features = get_the_terms($store_id, 'store_feature');
?>

<main class="site-main single-store">

    <!-- Store Hero Section -->
    <section class="store-hero">
        <?php if (has_post_thumbnail()) : ?>
            <img src="<?php echo get_the_post_thumbnail_url($store_id, 'full'); ?>" 
                 alt="<?php echo esc_attr(get_the_title()); ?>" 
                 class="store-hero-bg">
        <?php endif; ?>
        <div class="store-hero-overlay"></div>
        
        <div class="container">
            <div class="store-hero-content">
                
                <!-- Breadcrumbs -->
                <?php store_directory_breadcrumbs(); ?>
                
                <h1><?php the_title(); ?></h1>
                
                <!-- Store Meta Information -->
                <div class="store-hero-meta">
                    
                    <!-- Rating -->
                    <?php if ($rating) : ?>
                        <div class="store-hero-rating">
                            <?php echo get_star_rating($rating); ?>
                            <?php if ($review_count) : ?>
                                <span class="review-count"><?php echo $review_count; ?> <?php _e('reviews', 'my-custom-theme'); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Price Range -->
                    <?php if ($price_range) : ?>
                        <div class="price-range">
                            <span class="price-symbols"><?php echo esc_html($price_range); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Store Status -->
                    <div class="store-status <?php echo $is_open ? 'open' : 'closed'; ?>">
                        <i class="fas fa-clock"></i>
                        <?php echo $is_open ? __('Open Now', 'my-custom-theme') : __('Closed', 'my-custom-theme'); ?>
                    </div>
                    
                    <!-- Categories -->
                    <?php if ($categories) : ?>
                        <div class="store-categories">
                            <?php foreach ($categories as $category) : ?>
                                <a href="<?php echo get_term_link($category); ?>" class="category-tag">
                                    <?php echo esc_html($category->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
                <!-- Hero Actions -->
                <div class="store-hero-actions">
                    <?php if ($directions_link) : ?>
                        <a href="<?php echo esc_url($directions_link); ?>" 
                           target="_blank" 
                           class="btn-store-action primary">
                            <i class="fas fa-directions"></i>
                            <?php _e('Get Directions', 'my-custom-theme'); ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($phone) : ?>
                        <a href="tel:<?php echo esc_attr($phone); ?>" 
                           class="btn-store-action secondary">
                            <i class="fas fa-phone"></i>
                            <?php _e('Call Now', 'my-custom-theme'); ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($website) : ?>
                        <a href="<?php echo esc_url($website); ?>" 
                           target="_blank" 
                           class="btn-store-action secondary">
                            <i class="fas fa-external-link-alt"></i>
                            <?php _e('Visit Website', 'my-custom-theme'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Store Information Section -->
    <section class="store-info-section">
        <div class="container">
            <div class="store-info-grid">
                
                <!-- Main Content -->
                <div class="store-main-content">
                    
                    <!-- Store Description -->
                    <div class="info-card">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            <?php _e('About This Store', 'my-custom-theme'); ?>
                        </h3>
                        <div class="store-description">
                            <?php the_content(); ?>
                        </div>
                        
                        <!-- Specialties -->
                        <?php if ($specialties) : ?>
                            <div class="store-specialties">
                                <h4><?php _e('Specialties', 'my-custom-theme'); ?></h4>
                                <div class="specialties-list">
                                    <?php 
                                    $specialties_array = explode(',', $specialties);
                                    foreach ($specialties_array as $specialty) : 
                                    ?>
                                        <span class="specialty-tag"><?php echo esc_html(trim($specialty)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Features -->
                        <?php if ($features) : ?>
                            <div class="store-features">
                                <h4><?php _e('Store Features', 'my-custom-theme'); ?></h4>
                                <div class="features-list">
                                    <?php 
                                    $features_array = explode(',', $features);
                                    foreach ($features_array as $feature) : 
                                    ?>
                                        <div class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span><?php echo esc_html(trim($feature)); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Store Gallery -->
                    <?php
                    $gallery_images = get_post_meta($store_id, 'store_gallery', true);
                    if ($gallery_images) :
                    ?>
                        <div class="info-card">
                            <h3>
                                <i class="fas fa-images"></i>
                                <?php _e('Store Gallery', 'my-custom-theme'); ?>
                            </h3>
                            <div class="store-gallery">
                                <?php foreach ($gallery_images as $image_id) : ?>
                                    <div class="gallery-item">
                                        <img src="<?php echo wp_get_attachment_image_url($image_id, 'store-gallery'); ?>" 
                                             alt="<?php echo esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true)); ?>"
                                             data-lightbox="store-gallery">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Customer Reviews -->
                    <div class="info-card">
                        <h3>
                            <i class="fas fa-star"></i>
                            <?php _e('Customer Reviews', 'my-custom-theme'); ?>
                        </h3>
                        
                        <!-- Review Summary -->
                        <?php if ($rating && $review_count) : ?>
                            <div class="review-summary">
                                <div class="average-rating">
                                    <span class="rating-large"><?php echo number_format($rating, 1); ?></span>
                                    <?php echo get_star_rating($rating); ?>
                                    <p><?php echo $review_count; ?> <?php _e('customer reviews', 'my-custom-theme'); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Add Review Button -->
                        <div class="review-actions">
                            <button type="button" class="btn-primary" data-toggle="review-form">
                                <i class="fas fa-plus"></i>
                                <?php _e('Write a Review', 'my-custom-theme'); ?>
                            </button>
                        </div>
                        
                        <!-- Review Form (Hidden by default) -->
                        <div id="review-form" class="review-form" style="display: none;">
                            <h4><?php _e('Share Your Experience', 'my-custom-theme'); ?></h4>
                            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                                <input type="hidden" name="action" value="submit_store_review">
                                <input type="hidden" name="store_id" value="<?php echo $store_id; ?>">
                                <?php wp_nonce_field('submit_store_review', 'review_nonce'); ?>
                                
                                <div class="form-group">
                                    <label for="reviewer_name"><?php _e('Your Name', 'my-custom-theme'); ?></label>
                                    <input type="text" id="reviewer_name" name="reviewer_name" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="reviewer_email"><?php _e('Email Address', 'my-custom-theme'); ?></label>
                                    <input type="email" id="reviewer_email" name="reviewer_email" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="review_rating"><?php _e('Overall Rating', 'my-custom-theme'); ?></label>
                                    <div class="rating-input">
                                        <?php for ($i = 5; $i >= 1; $i--) : ?>
                                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>">
                                            <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="review_text"><?php _e('Your Review', 'my-custom-theme'); ?></label>
                                    <textarea id="review_text" name="review_text" rows="4" required placeholder="<?php _e('Tell others about your experience...', 'my-custom-theme'); ?>"></textarea>
                                </div>
                                
                                <button type="submit" class="btn-primary">
                                    <?php _e('Submit Review', 'my-custom-theme'); ?>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Recent Reviews -->
                        <?php
                        $recent_reviews = get_posts(array(
                            'post_type' => 'store_review',
                            'posts_per_page' => 5,
                            'meta_query' => array(
                                array(
                                    'key' => '_review_store_id',
                                    'value' => $store_id,
                                    'compare' => '='
                                )
                            ),
                            'post_status' => 'publish'
                        ));
                        
                        if ($recent_reviews) :
                        ?>
                            <div class="recent-reviews">
                                <h4><?php _e('Recent Reviews', 'my-custom-theme'); ?></h4>
                                <?php foreach ($recent_reviews as $review) : 
                                    $review_rating = get_post_meta($review->ID, '_review_rating', true);
                                    $reviewer_name = get_post_meta($review->ID, '_reviewer_name', true);
                                ?>
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <strong><?php echo esc_html($reviewer_name); ?></strong>
                                                <span class="review-date"><?php echo get_the_date('', $review); ?></span>
                                            </div>
                                            <?php if ($review_rating) : ?>
                                                <?php echo get_star_rating($review_rating); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="review-content">
                                            <?php echo wpautop($review->post_content); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
                
                <!-- Sidebar -->
                <div class="store-sidebar">
                    
                    <!-- Opening Hours -->
                    <?php if ($opening_hours) : ?>
                        <div class="info-card">
                            <h3>
                                <i class="fas fa-clock"></i>
                                <?php _e('Opening Hours', 'my-custom-theme'); ?>
                            </h3>
                            <table class="hours-table">
                                <?php 
                                $current_day = strtolower(date('l'));
                                foreach ($opening_hours as $day => $hours) : 
                                    $is_current = ($day === $current_day);
                                    $is_open_day = ($hours['hours'] !== 'Closed');
                                ?>
                                    <tr <?php echo $is_current ? 'class="current-day"' : ''; ?>>
                                        <td><?php echo esc_html($hours['label']); ?></td>
                                        <td class="<?php echo $is_open_day ? 'open-now' : 'closed'; ?>">
                                            <?php echo esc_html($hours['hours']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Contact Information -->
                    <div class="info-card">
                        <h3>
                            <i class="fas fa-address-book"></i>
                            <?php _e('Contact Information', 'my-custom-theme'); ?>
                        </h3>
                        <div class="contact-info">
                            
                            <?php if ($address) : ?>
                                <div class="contact-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <strong><?php _e('Address', 'my-custom-theme'); ?></strong><br>
                                        <?php echo esc_html($address); ?>
                                        <?php if ($neighborhood) : ?>
                                            <br><small><?php echo esc_html($neighborhood); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($phone) : ?>
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <div>
                                        <strong><?php _e('Phone', 'my-custom-theme'); ?></strong><br>
                                        <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($email) : ?>
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <div>
                                        <strong><?php _e('Email', 'my-custom-theme'); ?></strong><br>
                                        <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($website) : ?>
                                <div class="contact-item">
                                    <i class="fas fa-globe"></i>
                                    <div>
                                        <strong><?php _e('Website', 'my-custom-theme'); ?></strong><br>
                                        <a href="<?php echo esc_url($website); ?>" target="_blank"><?php _e('Visit Website', 'my-custom-theme'); ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                        
                        <!-- Social Media Links -->
                        <?php if ($instagram || $facebook) : ?>
                            <div class="social-links">
                                <h4><?php _e('Follow Us', 'my-custom-theme'); ?></h4>
                                <div class="social-buttons">
                                    <?php if ($instagram) : ?>
                                        <a href="https://instagram.com/<?php echo esc_attr(ltrim($instagram, '@')); ?>" 
                                           target="_blank" 
                                           class="social-btn instagram">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($facebook) : ?>
                                        <a href="<?php echo esc_url($facebook); ?>" 
                                           target="_blank" 
                                           class="social-btn facebook">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Store Details -->
                    <div class="info-card">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            <?php _e('Store Details', 'my-custom-theme'); ?>
                        </h3>
                        
                        <?php if ($payment_methods) : ?>
                            <div class="detail-item">
                                <strong><?php _e('Payment Methods', 'my-custom-theme'); ?></strong>
                                <p><?php echo esc_html($payment_methods); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($languages) : ?>
                            <div class="detail-item">
                                <strong><?php _e('Languages', 'my-custom-theme'); ?></strong>
                                <p><?php echo esc_html($languages); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($parking) : ?>
                            <div class="detail-item">
                                <strong><?php _e('Parking', 'my-custom-theme'); ?></strong>
                                <p><?php echo esc_html($parking); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($accessibility) : ?>
                            <div class="detail-item">
                                <strong><?php _e('Accessibility', 'my-custom-theme'); ?></strong>
                                <p><?php echo esc_html($accessibility); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Store Location Map -->
                    <?php if ($latitude && $longitude) : ?>
                        <div class="info-card">
                            <h3>
                                <i class="fas fa-map"></i>
                                <?php _e('Location', 'my-custom-theme'); ?>
                            </h3>
                            <div class="single-store-map">
                                <div id="single-store-map" style="height: 300px; border-radius: 8px;"></div>
                            </div>
                            <?php if ($directions_link) : ?>
                                <div class="map-actions">
                                    <a href="<?php echo esc_url($directions_link); ?>" 
                                       target="_blank" 
                                       class="btn-primary">
                                        <i class="fas fa-directions"></i>
                                        <?php _e('Get Directions', 'my-custom-theme'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
            </div>
        </div>
    </section>

    <!-- Related Stores -->
    <section class="related-stores">
        <div class="container">
            <div class="section-header">
                <h2><?php _e('Similar Stores', 'my-custom-theme'); ?></h2>
                <p><?php _e('Discover other stores you might be interested in.', 'my-custom-theme'); ?></p>
            </div>
            
            <?php
            // Get related stores based on categories and location
            $related_args = array(
                'post_type' => 'secondhand_store',
                'posts_per_page' => 3,
                'post__not_in' => array($store_id),
                'meta_key' => '_store_rating',
                'orderby' => 'meta_value_num',
                'order' => 'DESC'
            );
            
            // Add tax query for same categories or locations
            if ($categories || $locations) {
                $tax_query = array('relation' => 'OR');
                
                if ($categories) {
                    $tax_query[] = array(
                        'taxonomy' => 'store_category',
                        'field' => 'term_id',
                        'terms' => wp_list_pluck($categories, 'term_id')
                    );
                }
                
                if ($locations) {
                    $tax_query[] = array(
                        'taxonomy' => 'store_location',
                        'field' => 'term_id',
                        'terms' => wp_list_pluck($locations, 'term_id')
                    );
                }
                
                $related_args['tax_query'] = $tax_query;
            }
            
            $related_stores = new WP_Query($related_args);
            
            if ($related_stores->have_posts()) :
            ?>
                <div class="related-stores-grid">
                    <?php while ($related_stores->have_posts()) : $related_stores->the_post(); ?>
                        <?php get_template_part('template-parts/store-card'); ?>
                    <?php endwhile; ?>
                </div>
            <?php 
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </section>

</main>

<!-- Pass single store data to JavaScript -->
<script type="text/javascript">
    window.singleStoreData = {
        lat: <?php echo $latitude ? floatval($latitude) : 'null'; ?>,
        lng: <?php echo $longitude ? floatval($longitude) : 'null'; ?>,
        title: <?php echo json_encode(get_the_title()); ?>,
        address: <?php echo json_encode($address); ?>,
        rating: <?php echo $rating ? floatval($rating) : 'null'; ?>
    };
</script>

<?php endwhile; ?>

<?php get_footer(); ?>