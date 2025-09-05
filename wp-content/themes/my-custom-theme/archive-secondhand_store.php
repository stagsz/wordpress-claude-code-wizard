<?php
/**
 * Archive template for Secondhand Stores
 * 
 * This template displays the main directory page with all stores,
 * filters, search functionality, and Google Maps integration.
 */

get_header(); ?>

<main class="site-main store-directory-archive">
    
    <!-- Directory Hero Section -->
    <section class="directory-hero">
        <div class="container">
            <div class="directory-hero-content">
                <h1><?php _e('Secondhand Stores Directory', 'my-custom-theme'); ?></h1>
                <p><?php _e('Discover the best secondhand stores and boutiques in Gothenburg and surrounding areas. Find unique treasures, vintage fashion, and sustainable shopping options.', 'my-custom-theme'); ?></p>
                
                <div class="hero-stats">
                    <?php
                    $store_count = wp_count_posts('secondhand_store')->publish;
                    $categories_count = wp_count_terms('store_category');
                    $locations_count = wp_count_terms('store_location');
                    ?>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $store_count; ?></span>
                            <span class="stat-label"><?php _e('Stores', 'my-custom-theme'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $categories_count; ?></span>
                            <span class="stat-label"><?php _e('Categories', 'my-custom-theme'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $locations_count; ?></span>
                            <span class="stat-label"><?php _e('Locations', 'my-custom-theme'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Directory Filters -->
    <section class="directory-filters">
        <div class="container">
            <form id="store-filters" class="filters-container">
                
                <!-- Search Input -->
                <div class="filter-group">
                    <label for="store-search" class="filter-label">
                        <i class="fas fa-search"></i> <?php _e('Search Stores', 'my-custom-theme'); ?>
                    </label>
                    <input type="text" 
                           id="store-search" 
                           name="search" 
                           class="filter-input" 
                           placeholder="<?php _e('Enter store name or keyword...', 'my-custom-theme'); ?>"
                           value="<?php echo esc_attr(get_query_var('search')); ?>">
                </div>

                <!-- Category Filter -->
                <div class="filter-group">
                    <label for="category-filter" class="filter-label">
                        <i class="fas fa-tags"></i> <?php _e('Category', 'my-custom-theme'); ?>
                    </label>
                    <select id="category-filter" name="category" class="filter-select">
                        <option value=""><?php _e('All Categories', 'my-custom-theme'); ?></option>
                        <?php
                        $categories = get_terms(array(
                            'taxonomy' => 'store_category',
                            'hide_empty' => false,
                        ));
                        foreach ($categories as $category) {
                            $selected = (get_query_var('store_category') === $category->slug) ? 'selected' : '';
                            echo '<option value="' . esc_attr($category->slug) . '" ' . $selected . '>' . esc_html($category->name) . ' (' . $category->count . ')</option>';
                        }
                        ?>
                    </select>
                </div>

                <!-- Location Filter -->
                <div class="filter-group">
                    <label for="location-filter" class="filter-label">
                        <i class="fas fa-map-marker-alt"></i> <?php _e('Location', 'my-custom-theme'); ?>
                    </label>
                    <select id="location-filter" name="location" class="filter-select">
                        <option value=""><?php _e('All Locations', 'my-custom-theme'); ?></option>
                        <?php
                        $locations = get_terms(array(
                            'taxonomy' => 'store_location',
                            'hide_empty' => false,
                        ));
                        foreach ($locations as $location) {
                            $selected = (get_query_var('store_location') === $location->slug) ? 'selected' : '';
                            echo '<option value="' . esc_attr($location->slug) . '" ' . $selected . '>' . esc_html($location->name) . ' (' . $location->count . ')</option>';
                        }
                        ?>
                    </select>
                </div>

                <!-- Rating Filter -->
                <div class="filter-group">
                    <label for="rating-filter" class="filter-label">
                        <i class="fas fa-star"></i> <?php _e('Minimum Rating', 'my-custom-theme'); ?>
                    </label>
                    <select id="rating-filter" name="min_rating" class="filter-select">
                        <option value=""><?php _e('Any Rating', 'my-custom-theme'); ?></option>
                        <option value="4.5">4.5+ <?php _e('stars', 'my-custom-theme'); ?></option>
                        <option value="4.0">4.0+ <?php _e('stars', 'my-custom-theme'); ?></option>
                        <option value="3.5">3.5+ <?php _e('stars', 'my-custom-theme'); ?></option>
                        <option value="3.0">3.0+ <?php _e('stars', 'my-custom-theme'); ?></option>
                    </select>
                </div>

                <!-- Price Range Filter -->
                <div class="filter-group">
                    <label for="price-filter" class="filter-label">
                        <i class="fas fa-dollar-sign"></i> <?php _e('Price Range', 'my-custom-theme'); ?>
                    </label>
                    <select id="price-filter" name="price_range" class="filter-select">
                        <option value=""><?php _e('Any Price', 'my-custom-theme'); ?></option>
                        <option value="$">$ - <?php _e('Budget-friendly', 'my-custom-theme'); ?></option>
                        <option value="$$">$$ - <?php _e('Moderate', 'my-custom-theme'); ?></option>
                        <option value="$$$">$$$ - <?php _e('Higher-end', 'my-custom-theme'); ?></option>
                    </select>
                </div>

                <!-- Sort Options -->
                <div class="filter-group">
                    <label for="sort-filter" class="filter-label">
                        <i class="fas fa-sort"></i> <?php _e('Sort By', 'my-custom-theme'); ?>
                    </label>
                    <select id="sort-filter" name="orderby" class="filter-select">
                        <option value="rating"><?php _e('Highest Rated', 'my-custom-theme'); ?></option>
                        <option value="name"><?php _e('Name A-Z', 'my-custom-theme'); ?></option>
                        <option value="newest"><?php _e('Newest First', 'my-custom-theme'); ?></option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="filter-group">
                    <button type="submit" class="filter-button">
                        <i class="fas fa-filter"></i> <?php _e('Apply Filters', 'my-custom-theme'); ?>
                    </button>
                </div>

            </form>

            <!-- Active Filters Display -->
            <div id="active-filters" class="active-filters" style="display: none;">
                <h4><?php _e('Active Filters:', 'my-custom-theme'); ?></h4>
                <div class="filter-tags"></div>
                <button type="button" class="clear-filters">
                    <i class="fas fa-times"></i> <?php _e('Clear All', 'my-custom-theme'); ?>
                </button>
            </div>
        </div>
    </section>

    <!-- Directory Layout: Map + Store Listings -->
    <section class="directory-map-section">
        <div class="container">
            <div class="directory-layout">
                
                <!-- Store Listings -->
                <div class="stores-content">
                    
                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="results-count">
                            <h2 id="results-count">
                                <?php
                                global $wp_query;
                                printf(
                                    _n('Found %s store', 'Found %s stores', $wp_query->found_posts, 'my-custom-theme'),
                                    '<span class="count-number">' . number_format_i18n($wp_query->found_posts) . '</span>'
                                );
                                ?>
                            </h2>
                        </div>
                        
                        <!-- View Toggle -->
                        <div class="view-toggle">
                            <button type="button" class="view-btn active" data-view="grid">
                                <i class="fas fa-th-large"></i> <?php _e('Grid', 'my-custom-theme'); ?>
                            </button>
                            <button type="button" class="view-btn" data-view="list">
                                <i class="fas fa-list"></i> <?php _e('List', 'my-custom-theme'); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Store Grid -->
                    <div id="stores-container" class="stores-grid">
                        <?php if (have_posts()) : ?>
                            <?php while (have_posts()) : the_post(); ?>
                                <?php get_template_part('template-parts/store-card'); ?>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <div class="no-stores-found">
                                <div class="no-results-content">
                                    <i class="fas fa-search fa-3x"></i>
                                    <h3><?php _e('No stores found', 'my-custom-theme'); ?></h3>
                                    <p><?php _e('Try adjusting your search criteria or browse all stores.', 'my-custom-theme'); ?></p>
                                    <button type="button" class="btn-primary clear-filters">
                                        <?php _e('Clear Filters', 'my-custom-theme'); ?>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php
                    $big = 999999999;
                    $pagination = paginate_links(array(
                        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format' => '?paged=%#%',
                        'current' => max(1, get_query_var('paged')),
                        'total' => $wp_query->max_num_pages,
                        'prev_text' => '<i class="fas fa-chevron-left"></i> ' . __('Previous', 'my-custom-theme'),
                        'next_text' => __('Next', 'my-custom-theme') . ' <i class="fas fa-chevron-right"></i>',
                        'type' => 'array',
                        'mid_size' => 2,
                    ));

                    if ($pagination) : ?>
                        <nav class="pagination-nav" aria-label="<?php _e('Store listings pagination', 'my-custom-theme'); ?>">
                            <ul class="pagination">
                                <?php foreach ($pagination as $page) : ?>
                                    <li class="page-item"><?php echo $page; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>

                <!-- Interactive Map -->
                <div class="map-sidebar">
                    <div class="map-container">
                        <div class="map-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p><?php _e('Loading map...', 'my-custom-theme'); ?></p>
                        </div>
                        <div id="store-map" style="display: none;"></div>
                    </div>
                    
                    <!-- Map Legend -->
                    <div class="map-legend">
                        <h4><?php _e('Map Legend', 'my-custom-theme'); ?></h4>
                        <div class="legend-items">
                            <div class="legend-item">
                                <span class="legend-marker" style="background: #2c5aa0;"></span>
                                <span><?php _e('Secondhand Store', 'my-custom-theme'); ?></span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-marker" style="background: #f39c12;"></span>
                                <span><?php _e('Featured Store', 'my-custom-theme'); ?></span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-marker" style="background: #27ae60;"></span>
                                <span><?php _e('Currently Open', 'my-custom-theme'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Store Categories Section -->
    <section class="featured-categories">
        <div class="container">
            <div class="section-header">
                <h2><?php _e('Browse by Category', 'my-custom-theme'); ?></h2>
                <p><?php _e('Explore stores by specialty and find exactly what you\'re looking for.', 'my-custom-theme'); ?></p>
            </div>
            
            <div class="categories-grid">
                <?php
                $featured_categories = get_terms(array(
                    'taxonomy' => 'store_category',
                    'hide_empty' => false,
                    'number' => 8,
                    'orderby' => 'count',
                    'order' => 'DESC',
                ));

                foreach ($featured_categories as $category) :
                    $category_link = get_term_link($category);
                    $category_image = get_term_meta($category->term_id, 'category_image', true);
                ?>
                    <div class="category-card">
                        <a href="<?php echo esc_url($category_link); ?>" class="category-link">
                            <div class="category-image">
                                <?php if ($category_image) : ?>
                                    <img src="<?php echo esc_url($category_image); ?>" alt="<?php echo esc_attr($category->name); ?>">
                                <?php else : ?>
                                    <div class="category-placeholder">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="category-info">
                                <h3><?php echo esc_html($category->name); ?></h3>
                                <p><?php echo esc_html($category->description); ?></p>
                                <span class="store-count"><?php echo $category->count; ?> <?php _e('stores', 'my-custom-theme'); ?></span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<!-- Pass store data to JavaScript for map -->
<script type="text/javascript">
    window.storeMapData = <?php echo json_encode(get_all_stores_for_map()); ?>;
    window.mapCenter = {
        lat: 57.7089,
        lng: 11.9746
    }; // Gothenburg coordinates
</script>

<?php get_footer(); ?>