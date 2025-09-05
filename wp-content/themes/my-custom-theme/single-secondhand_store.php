<?php
/**
 * Single Secondhand Store Template
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <?php while (have_posts()) : the_post(); 
            $store_id = get_the_ID();
            $address = get_post_meta($store_id, '_store_address', true);
            $phone = get_post_meta($store_id, '_store_phone', true);
            $email = get_post_meta($store_id, '_store_email', true);
            $website = get_post_meta($store_id, '_store_website', true);
            $instagram = get_post_meta($store_id, '_store_instagram', true);
            $opening_hours = get_post_meta($store_id, '_store_opening_hours', true);
            $rating = get_post_meta($store_id, '_store_rating', true);
            $price_range = get_post_meta($store_id, '_store_price_range', true);
            $latitude = get_post_meta($store_id, '_store_latitude', true);
            $longitude = get_post_meta($store_id, '_store_longitude', true);
            $google_maps_link = get_post_meta($store_id, '_store_google_maps_link', true);
            
            $categories = get_the_terms($store_id, 'store_category');
            $districts = get_the_terms($store_id, 'store_district');
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('store-single'); ?>>
            
            <!-- Store Header -->
            <header class="store-header">
                <div class="container">
                    <div class="store-header-content">
                        
                        <div class="store-title-section">
                            <h1 class="store-title"><?php the_title(); ?></h1>
                            
                            <!-- Store Rating -->
                            <?php if ($rating): ?>
                                <div class="store-rating-large">
                                    <div class="stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?php echo $i <= $rating ? 'filled' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="rating-text"><?php echo $rating; ?>/5</span>
                                    <?php if ($price_range): ?>
                                        <span class="price-range"><?php echo esc_html($price_range); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Store Categories -->
                            <?php if ($categories): ?>
                                <div class="store-categories-large">
                                    <?php foreach ($categories as $category): ?>
                                        <span class="category-tag-large"><?php echo esc_html($category->name); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Quick Actions -->
                        <div class="store-quick-actions">
                            <?php if ($phone): ?>
                                <a href="tel:<?php echo esc_attr($phone); ?>" class="btn btn-primary">
                                    <span class="dashicons dashicons-phone"></span> Call Now
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($google_maps_link): ?>
                                <a href="<?php echo esc_url($google_maps_link); ?>" target="_blank" class="btn btn-secondary">
                                    <span class="dashicons dashicons-location"></span> Get Directions
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($website): ?>
                                <a href="<?php echo esc_url($website); ?>" target="_blank" class="btn btn-secondary">
                                    <span class="dashicons dashicons-admin-links"></span> Visit Website
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Store Main Content -->
            <div class="store-main-content">
                <div class="container">
                    <div class="store-content-grid">
                        
                        <!-- Left Column: Main Content -->
                        <div class="store-content-main">
                            
                            <!-- Store Images -->
                            <?php if (has_post_thumbnail()): ?>
                                <div class="store-featured-image">
                                    <?php the_post_thumbnail('large', array('alt' => get_the_title())); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Store Description -->
                            <div class="store-description">
                                <h2>About <?php the_title(); ?></h2>
                                <div class="entry-content">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                            
                            <!-- Instagram Feed Section -->
                            <?php if ($instagram): ?>
                                <div class="store-instagram-section">
                                    <h3>Follow us on Instagram</h3>
                                    <div class="instagram-feed">
                                        <a href="https://instagram.com/<?php echo ltrim(esc_attr($instagram), '@'); ?>" target="_blank" class="instagram-link">
                                            <span class="dashicons dashicons-instagram"></span>
                                            <?php echo esc_html($instagram); ?>
                                        </a>
                                        <!-- Instagram feed widget would go here -->
                                        <div class="instagram-placeholder">
                                            <p>Connect with us on Instagram to see our latest finds and store updates!</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Related Stores -->
                            <?php if ($categories): 
                                $related_args = array(
                                    'post_type' => 'secondhand_store',
                                    'posts_per_page' => 3,
                                    'post__not_in' => array($store_id),
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'store_category',
                                            'field' => 'term_id',
                                            'terms' => wp_list_pluck($categories, 'term_id'),
                                        ),
                                    ),
                                );
                                $related_stores = new WP_Query($related_args);
                                
                                if ($related_stores->have_posts()): ?>
                                    <div class="related-stores">
                                        <h3>Similar Stores</h3>
                                        <div class="related-stores-grid">
                                            <?php while ($related_stores->have_posts()): $related_stores->the_post(); ?>
                                                <div class="related-store-card">
                                                    <a href="<?php the_permalink(); ?>">
                                                        <?php if (has_post_thumbnail()): ?>
                                                            <?php the_post_thumbnail('medium'); ?>
                                                        <?php endif; ?>
                                                        <h4><?php the_title(); ?></h4>
                                                    </a>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                    <?php wp_reset_postdata(); ?>
                                <?php endif;
                            endif; ?>
                            
                        </div>

                        <!-- Right Column: Store Info -->
                        <div class="store-sidebar">
                            
                            <!-- Store Details Card -->
                            <div class="store-info-card">
                                <h3>Store Information</h3>
                                
                                <?php if ($address): ?>
                                    <div class="store-info-item">
                                        <span class="dashicons dashicons-location"></span>
                                        <div class="info-content">
                                            <strong>Address</strong>
                                            <span><?php echo esc_html($address); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($phone): ?>
                                    <div class="store-info-item">
                                        <span class="dashicons dashicons-phone"></span>
                                        <div class="info-content">
                                            <strong>Phone</strong>
                                            <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($email): ?>
                                    <div class="store-info-item">
                                        <span class="dashicons dashicons-email"></span>
                                        <div class="info-content">
                                            <strong>Email</strong>
                                            <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($districts): ?>
                                    <div class="store-info-item">
                                        <span class="dashicons dashicons-admin-multisite"></span>
                                        <div class="info-content">
                                            <strong>District</strong>
                                            <span><?php echo esc_html($districts[0]->name); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($opening_hours): ?>
                                    <div class="store-info-item">
                                        <span class="dashicons dashicons-clock"></span>
                                        <div class="info-content">
                                            <strong>Opening Hours</strong>
                                            <div class="opening-hours"><?php echo nl2br(esc_html($opening_hours)); ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Map -->
                            <?php if ($latitude && $longitude): ?>
                                <div class="store-map-card">
                                    <h3>Location</h3>
                                    <div id="single-store-map" class="single-store-map"></div>
                                    <?php if ($google_maps_link): ?>
                                        <a href="<?php echo esc_url($google_maps_link); ?>" target="_blank" class="view-larger-map">
                                            View larger map
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Share Section -->
                            <div class="store-share-card">
                                <h3>Share This Store</h3>
                                <div class="share-buttons">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="share-btn facebook">
                                        <span class="dashicons dashicons-facebook"></span> Facebook
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode('Check out ' . get_the_title()); ?>" target="_blank" class="share-btn twitter">
                                        <span class="dashicons dashicons-twitter"></span> Twitter
                                    </a>
                                    <a href="mailto:?subject=<?php echo urlencode('Check out ' . get_the_title()); ?>&body=<?php echo urlencode(get_permalink()); ?>" class="share-btn email">
                                        <span class="dashicons dashicons-email"></span> Email
                                    </a>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="store-navigation">
                <div class="container">
                    <div class="nav-links">
                        <?php
                        $prev_post = get_previous_post(true, '', 'store_category');
                        $next_post = get_next_post(true, '', 'store_category');
                        ?>
                        
                        <?php if ($prev_post): ?>
                            <div class="nav-previous">
                                <a href="<?php echo get_permalink($prev_post->ID); ?>" rel="prev">
                                    <span class="nav-subtitle">Previous Store</span>
                                    <span class="nav-title"><?php echo get_the_title($prev_post->ID); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="nav-back">
                            <a href="<?php echo get_post_type_archive_link('secondhand_store'); ?>" class="btn btn-outline">
                                ← Back to All Stores
                            </a>
                        </div>
                        
                        <?php if ($next_post): ?>
                            <div class="nav-next">
                                <a href="<?php echo get_permalink($next_post->ID); ?>" rel="next">
                                    <span class="nav-subtitle">Next Store</span>
                                    <span class="nav-title"><?php echo get_the_title($next_post->ID); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>

        </article>
        
        <?php endwhile; ?>

    </main>
</div>

<!-- Single Store Map JavaScript -->
<?php if ($latitude && $longitude): ?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initSingleStoreMap"></script>

<script>
function initSingleStoreMap() {
    const storeLocation = {lat: <?php echo floatval($latitude); ?>, lng: <?php echo floatval($longitude); ?>};
    
    const map = new google.maps.Map(document.getElementById('single-store-map'), {
        zoom: 15,
        center: storeLocation,
        styles: [
            {
                featureType: 'poi',
                elementType: 'labels',
                stylers: [{visibility: 'off'}]
            }
        ]
    });
    
    const marker = new google.maps.Marker({
        position: storeLocation,
        map: map,
        title: '<?php echo esc_js(get_the_title()); ?>',
        icon: {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#e74c3c" width="32" height="32"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>'),
            scaledSize: new google.maps.Size(32, 32)
        }
    });
    
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div class="store-marker-info">
                <h4><?php echo esc_js(get_the_title()); ?></h4>
                <?php if ($address): ?>
                    <p><?php echo esc_js($address); ?></p>
                <?php endif; ?>
                <?php if ($phone): ?>
                    <p><a href="tel:<?php echo esc_js($phone); ?>"><?php echo esc_js($phone); ?></a></p>
                <?php endif; ?>
            </div>
        `
    });
    
    marker.addListener('click', () => {
        infoWindow.open(map, marker);
    });
}
</script>
<?php endif; ?>

<?php get_footer(); ?>