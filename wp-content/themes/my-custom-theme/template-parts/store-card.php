<?php
/**
 * Template part for displaying a store card
 */

$store_id = get_the_ID();
$address = get_post_meta($store_id, '_store_address', true);
$phone = get_post_meta($store_id, '_store_phone', true);
$website = get_post_meta($store_id, '_store_website', true);
$instagram = get_post_meta($store_id, '_store_instagram', true);
$opening_hours = get_post_meta($store_id, '_store_opening_hours', true);
$rating = get_post_meta($store_id, '_store_rating', true);
$price_range = get_post_meta($store_id, '_store_price_range', true);
$latitude = get_post_meta($store_id, '_store_latitude', true);
$longitude = get_post_meta($store_id, '_store_longitude', true);

$categories = get_the_terms($store_id, 'store_category');
$districts = get_the_terms($store_id, 'store_district');
?>

<article class="store-card" 
         data-id="<?php echo $store_id; ?>"
         data-category="<?php echo $categories ? implode(',', wp_list_pluck($categories, 'slug')) : ''; ?>"
         data-district="<?php echo $districts ? implode(',', wp_list_pluck($districts, 'slug')) : ''; ?>"
         data-rating="<?php echo esc_attr($rating); ?>"
         data-price="<?php echo esc_attr($price_range); ?>"
         data-lat="<?php echo esc_attr($latitude); ?>"
         data-lng="<?php echo esc_attr($longitude); ?>">
         
    <div class="store-card-inner">
        
        <!-- Store Image -->
        <div class="store-image">
            <?php if (has_post_thumbnail()): ?>
                <?php the_post_thumbnail('store-card', array('alt' => get_the_title())); ?>
            <?php else: ?>
                <div class="placeholder-image">
                    <span class="dashicons dashicons-store"></span>
                </div>
            <?php endif; ?>
            
            <!-- Price Range Badge -->
            <?php if ($price_range): ?>
                <div class="price-badge"><?php echo esc_html($price_range); ?></div>
            <?php endif; ?>
        </div>

        <!-- Store Content -->
        <div class="store-content">
            
            <header class="store-header">
                <h3 class="store-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                
                <?php if ($rating): ?>
                    <div class="store-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?php echo $i <= $rating ? 'filled' : ''; ?>">★</span>
                        <?php endfor; ?>
                        <span class="rating-text">(<?php echo $rating; ?>/5)</span>
                    </div>
                <?php endif; ?>
            </header>

            <!-- Store Categories -->
            <?php if ($categories): ?>
                <div class="store-categories">
                    <?php foreach ($categories as $category): ?>
                        <span class="category-tag"><?php echo esc_html($category->name); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Store Excerpt -->
            <div class="store-excerpt">
                <?php the_excerpt(); ?>
            </div>

            <!-- Store Details -->
            <div class="store-details">
                
                <?php if ($address): ?>
                    <div class="store-detail">
                        <span class="dashicons dashicons-location"></span>
                        <span><?php echo esc_html($address); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($phone): ?>
                    <div class="store-detail">
                        <span class="dashicons dashicons-phone"></span>
                        <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                    </div>
                <?php endif; ?>
                
                <?php if ($districts): ?>
                    <div class="store-detail">
                        <span class="dashicons dashicons-admin-multisite"></span>
                        <span><?php echo esc_html($districts[0]->name); ?></span>
                    </div>
                <?php endif; ?>
                
            </div>

            <!-- Store Actions -->
            <div class="store-actions">
                <a href="<?php the_permalink(); ?>" class="btn btn-primary">View Details</a>
                
                <?php if ($website): ?>
                    <a href="<?php echo esc_url($website); ?>" target="_blank" class="btn btn-secondary">Visit Website</a>
                <?php endif; ?>
                
                <?php if ($instagram): ?>
                    <a href="https://instagram.com/<?php echo ltrim(esc_attr($instagram), '@'); ?>" target="_blank" class="btn btn-instagram">
                        <span class="dashicons dashicons-instagram"></span>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</article>