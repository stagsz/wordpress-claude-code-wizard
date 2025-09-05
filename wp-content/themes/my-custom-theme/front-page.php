<?php
/**
 * Home Page Template
 */

get_header(); ?>

<main class="site-main homepage">
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1><?php _e('Discover Gothenburg\'s Best Secondhand Stores', 'my-custom-theme'); ?></h1>
                <p class="hero-description">
                    <?php _e('Find unique treasures, vintage fashion, and sustainable shopping options in Gothenburg and surrounding areas. Browse our comprehensive directory of carefully curated secondhand stores and boutiques.', 'my-custom-theme'); ?>
                </p>
                
                <div class="hero-actions">
                    <a href="<?php echo get_post_type_archive_link('secondhand_store'); ?>" class="btn-primary btn-large">
                        <i class="fas fa-search"></i>
                        <?php _e('Browse All Stores', 'my-custom-theme'); ?>
                    </a>
                    <a href="#featured-stores" class="btn-secondary btn-large">
                        <i class="fas fa-star"></i>
                        <?php _e('Featured Stores', 'my-custom-theme'); ?>
                    </a>
                </div>
                
                <!-- Quick Stats -->
                <div class="hero-stats">
                    <?php
                    $store_count = wp_count_posts('secondhand_store')->publish;
                    $categories_count = wp_count_terms('store_category');
                    $locations_count = wp_count_terms('store_location');
                    ?>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $store_count; ?>+</span>
                        <span class="stat-label"><?php _e('Stores', 'my-custom-theme'); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $categories_count; ?>+</span>
                        <span class="stat-label"><?php _e('Categories', 'my-custom-theme'); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $locations_count; ?>+</span>
                        <span class="stat-label"><?php _e('Areas', 'my-custom-theme'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Quick Search Section -->
    <section class="quick-search-section">
        <div class="container">
            <div class="quick-search-content">
                <h2><?php _e('Find Your Perfect Store', 'my-custom-theme'); ?></h2>
                <p><?php _e('Use our advanced search to find exactly what you\'re looking for.', 'my-custom-theme'); ?></p>
                
                <form class="quick-search-form" action="<?php echo get_post_type_archive_link('secondhand_store'); ?>" method="get">
                    <div class="search-fields">
                        <div class="search-field">
                            <input type="text" name="search" placeholder="<?php _e('What are you looking for?', 'my-custom-theme'); ?>" class="search-input">
                        </div>
                        <div class="search-field">
                            <select name="category" class="search-select">
                                <option value=""><?php _e('Any Category', 'my-custom-theme'); ?></option>
                                <?php
                                $categories = get_terms(array('taxonomy' => 'store_category', 'hide_empty' => false));
                                foreach ($categories as $category) {
                                    echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="search-field">
                            <select name="location" class="search-select">
                                <option value=""><?php _e('Any Location', 'my-custom-theme'); ?></option>
                                <?php
                                $locations = get_terms(array('taxonomy' => 'store_location', 'hide_empty' => false));
                                foreach ($locations as $location) {
                                    echo '<option value="' . esc_attr($location->slug) . '">' . esc_html($location->name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="search-submit">
                            <i class="fas fa-search"></i>
                            <?php _e('Search', 'my-custom-theme'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    
    <!-- Featured Stores Section -->
    <section id="featured-stores" class="featured-stores-section">
        <div class="container">
            <div class="section-header">
                <h2><?php _e('Featured Stores', 'my-custom-theme'); ?></h2>
                <p><?php _e('Handpicked stores offering the best shopping experiences in Gothenburg.', 'my-custom-theme'); ?></p>
            </div>
            
            <div class="featured-stores-grid">
                <?php
                $featured_stores = new WP_Query(array(
                    'post_type' => 'secondhand_store',
                    'posts_per_page' => 6,
                    'meta_key' => '_store_rating',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC',
                    'meta_query' => array(
                        array(
                            'key' => '_store_rating',
                            'value' => 4.0,
                            'compare' => '>=',
                            'type' => 'NUMERIC'
                        )
                    )
                ));
                
                if ($featured_stores->have_posts()) :
                    while ($featured_stores->have_posts()) : $featured_stores->the_post();
                        get_template_part('template-parts/store-card');
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                    <div class="no-stores-message">
                        <p><?php _e('Featured stores will appear here once added to the directory.', 'my-custom-theme'); ?></p>
                        <a href="<?php echo get_post_type_archive_link('secondhand_store'); ?>" class="btn-primary">
                            <?php _e('Browse All Stores', 'my-custom-theme'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="section-footer">
                <a href="<?php echo get_post_type_archive_link('secondhand_store'); ?>" class="btn-outline">
                    <?php _e('View All Stores', 'my-custom-theme'); ?>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
    
    <!-- Store Categories Section -->
    <section class="store-categories-section">
        <div class="container">
            <div class="section-header">
                <h2><?php _e('Browse by Category', 'my-custom-theme'); ?></h2>
                <p><?php _e('Explore different types of secondhand stores and find exactly what you need.', 'my-custom-theme'); ?></p>
            </div>
            
            <div class="categories-grid">
                <?php
                $categories = get_terms(array(
                    'taxonomy' => 'store_category',
                    'hide_empty' => false,
                    'number' => 8,
                    'orderby' => 'count',
                    'order' => 'DESC'
                ));
                
                $category_icons = array(
                    'mixed-second-hand' => 'fas fa-shopping-bag',
                    'vintage-retro' => 'fas fa-crown',
                    'charity-shop' => 'fas fa-heart',
                    'designer-consignment' => 'fas fa-gem',
                    'sustainable-fashion' => 'fas fa-leaf',
                    'electronics-gadgets' => 'fas fa-laptop',
                    'childrens-specialist' => 'fas fa-baby',
                    'books-media' => 'fas fa-book'
                );
                
                foreach ($categories as $category) :
                    $category_link = get_term_link($category);
                    $icon_class = isset($category_icons[$category->slug]) ? $category_icons[$category->slug] : 'fas fa-store';
                ?>
                    <div class="category-card">
                        <a href="<?php echo esc_url($category_link); ?>" class="category-link">
                            <div class="category-icon">
                                <i class="<?php echo esc_attr($icon_class); ?>"></i>
                            </div>
                            <h3><?php echo esc_html($category->name); ?></h3>
                            <p class="store-count"><?php echo $category->count; ?> <?php _e('stores', 'my-custom-theme'); ?></p>
                            <span class="category-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Why Choose Section -->
    <section class="why-choose-section">
        <div class="container">
            <div class="section-header">
                <h2><?php _e('Why Choose Secondhand Shopping?', 'my-custom-theme'); ?></h2>
                <p><?php _e('Discover the benefits of sustainable shopping and unique finds.', 'my-custom-theme'); ?></p>
            </div>
            
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3><?php _e('Sustainable Choice', 'my-custom-theme'); ?></h3>
                    <p><?php _e('Reduce environmental impact by giving pre-loved items a new life and supporting circular economy.', 'my-custom-theme'); ?></p>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3><?php _e('Great Value', 'my-custom-theme'); ?></h3>
                    <p><?php _e('Find high-quality items at affordable prices, from everyday essentials to luxury designer pieces.', 'my-custom-theme'); ?></p>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h3><?php _e('Unique Finds', 'my-custom-theme'); ?></h3>
                    <p><?php _e('Discover one-of-a-kind vintage pieces and rare items you won\'t find in regular stores.', 'my-custom-theme'); ?></p>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3><?php _e('Support Local', 'my-custom-theme'); ?></h3>
                    <p><?php _e('Support local businesses and community organizations while finding great deals.', 'my-custom-theme'); ?></p>
                </div>
            </div>
        </div>
    </section>
    
</main>

<?php get_footer(); ?>