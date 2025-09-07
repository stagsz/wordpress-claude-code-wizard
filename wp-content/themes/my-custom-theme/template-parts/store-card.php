<?php
/**
 * Store Card Template Part
 * 
 * Used to display store information in grid/list layouts
 */

$store_id = get_the_ID();
$address = get_post_meta($store_id, '_store_address', true);
$neighborhood = get_post_meta($store_id, '_store_neighborhood', true);
$phone = get_post_meta($store_id, '_store_phone', true);
$website = get_post_meta($store_id, '_store_website', true);
$rating = get_post_meta($store_id, '_store_rating', true);
$review_count = get_post_meta($store_id, '_store_review_count', true);
$price_range = get_post_meta($store_id, '_store_price_range', true);
$directions_link = get_post_meta($store_id, '_store_directions_link', true);
$specialties = get_post_meta($store_id, '_store_specialties', true);

// Get opening hours to check if store is open
$is_open = is_store_open($store_id);

// Get store categories
$categories = get_the_terms($store_id, 'store_category');
$locations = get_the_terms($store_id, 'store_location');
?>

<article class="store-card" data-store-id="<?php echo $store_id; ?>">
    
    <!-- Store Image -->
    <div class="store-card-image">
        <?php if (has_post_thumbnail()) : ?>
            <img src="<?php echo get_the_post_thumbnail_url($store_id, 'store-card'); ?>" 
                 alt="<?php echo esc_attr(get_the_title()); ?>" 
                 loading="lazy">
        <?php else : ?>
            <div class="store-placeholder">
                <i class="fas fa-store"></i>
            </div>
        <?php endif; ?>
        
        <!-- Store Status Badge -->
        <div class="store-status <?php echo $is_open ? 'open' : 'closed'; ?>">
            <?php echo $is_open ? __('Open', 'my-custom-theme') : __('Closed', 'my-custom-theme'); ?>
        </div>
        
        <!-- Rating Badge -->
        <?php if ($rating) : ?>
            <div class="store-rating-badge">
                <i class="fas fa-star"></i>
                <span><?php echo number_format($rating, 1); ?></span>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Store Content -->
    <div class="store-card-content">
        
        <!-- Store Header -->
        <div class="store-card-header">
            <h3 class="store-card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            
            <!-- Primary Category -->
            <?php if ($categories && !is_wp_error($categories)) : ?>
                <span class="store-category">
                    <?php echo esc_html($categories[0]->name); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <!-- Store Info -->
        <div class="store-card-info">
            
            <!-- Store Description -->
            <div class="store-description">
                <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
            </div>
            
            <!-- Store Details -->
            <div class="store-details">
                
                <!-- Location -->
                <?php if ($address) : ?>
                    <div class="store-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo esc_html($address); ?>
                            <?php if ($neighborhood) : ?>
                                <small>, <?php echo esc_html($neighborhood); ?></small>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <!-- Phone -->
                <?php if ($phone) : ?>
                    <div class="store-detail">
                        <i class="fas fa-phone"></i>
                        <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                    </div>
                <?php endif; ?>
                
                <!-- Price Range -->
                <?php if ($price_range) : ?>
                    <div class="store-detail">
                        <i class="fas fa-dollar-sign"></i>
                        <span><?php echo esc_html($price_range); ?> - 
                            <?php 
                            switch ($price_range) {
                                case '$':
                                    echo __('Budget-friendly', 'my-custom-theme');
                                    break;
                                case '$$':
                                    echo __('Moderate prices', 'my-custom-theme');
                                    break;
                                case '$$$':
                                    echo __('Higher-end', 'my-custom-theme');
                                    break;
                            }
                            ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <!-- Rating -->
                <?php if ($rating) : ?>
                    <div class="store-detail">
                        <div class="store-rating">
                            <?php echo get_star_rating($rating); ?>
                            <?php if ($review_count) : ?>
                                <span class="review-count">
                                    (<?php echo $review_count; ?> <?php _e('reviews', 'my-custom-theme'); ?>)
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            </div>
            
            <!-- Store Specialties -->
            <?php if ($specialties) : ?>
                <div class="store-specialties">
                    <?php 
                    $specialties_array = array_slice(explode(',', $specialties), 0, 3); // Show only first 3
                    foreach ($specialties_array as $specialty) : 
                    ?>
                        <span class="specialty-tag"><?php echo esc_html(trim($specialty)); ?></span>
                    <?php endforeach; ?>
                    
                    <?php if (count(explode(',', $specialties)) > 3) : ?>
                        <span class="specialty-tag more">+<?php echo count(explode(',', $specialties)) - 3; ?> <?php _e('more', 'my-custom-theme'); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        </div>
        
        <!-- Store Card Footer -->
        <div class="store-card-footer">
            
            <!-- Store Locations/Areas -->
            <?php if ($locations && !is_wp_error($locations)) : ?>
                <div class="store-locations">
                    <?php foreach (array_slice($locations, 0, 2) as $location) : ?>
                        <span class="location-tag">
                            <i class="fas fa-map-pin"></i>
                            <?php echo esc_html($location->name); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Action Buttons -->
            <div class="store-card-actions">
                <a href="<?php the_permalink(); ?>" class="btn-store-action primary">
                    <?php _e('View Details', 'my-custom-theme'); ?>
                </a>
                
                <?php if ($directions_link) : ?>
                    <a href="<?php echo esc_url($directions_link); ?>" 
                       target="_blank" 
                       class="btn-store-action secondary" 
                       title="<?php _e('Get Directions', 'my-custom-theme'); ?>">
                        <i class="fas fa-directions"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($phone) : ?>
                    <a href="tel:<?php echo esc_attr($phone); ?>" 
                       class="btn-store-action secondary" 
                       title="<?php _e('Call Store', 'my-custom-theme'); ?>">
                        <i class="fas fa-phone"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($website) : ?>
                    <a href="<?php echo esc_url($website); ?>" 
                       target="_blank" 
                       class="btn-store-action secondary" 
                       title="<?php _e('Visit Website', 'my-custom-theme'); ?>">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                <?php endif; ?>
            </div>
            
        </div>
        
    </div>
    
</article>