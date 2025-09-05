<?php
/**
 * Theme Functions for Secondhand Stores Directory
 */

if (!defined('ABSPATH')) {
    exit;
}

function my_custom_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo');
    
    // Custom image sizes for secondhand stores
    add_image_size('store-card', 400, 300, true);
    add_image_size('store-hero', 800, 400, true);
    add_image_size('store-gallery', 600, 450, true);
    
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my-custom-theme'),
        'footer' => __('Footer Menu', 'my-custom-theme'),
        'categories' => __('Store Categories Menu', 'my-custom-theme'),
        'locations' => __('Locations Menu', 'my-custom-theme'),
    ));
}
add_action('after_setup_theme', 'my_custom_theme_setup');

function my_custom_theme_scripts() {
    wp_enqueue_style('my-custom-theme-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', array(), '6.0.0');
    
    wp_enqueue_script('my-custom-theme-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
    
    // Enqueue Google Maps API if we have a Google Maps API key
    if (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY) {
        wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . GOOGLE_MAPS_API_KEY . '&libraries=places', array(), '1.0.0', true);
        wp_enqueue_script('store-map', get_template_directory_uri() . '/js/store-map.js', array('google-maps', 'jquery'), '1.0.0', true);
    }
    
    // Localize script for AJAX
    wp_localize_script('my-custom-theme-main', 'storeDirectory', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('store_directory_nonce')
    ));
    
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'my_custom_theme_scripts');

function my_custom_theme_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'my-custom-theme'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here.', 'my-custom-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
    
    register_sidebar(array(
        'name'          => __('Store Directory Sidebar', 'my-custom-theme'),
        'id'            => 'store-sidebar',
        'description'   => __('Sidebar for store directory pages.', 'my-custom-theme'),
        'before_widget' => '<section id="%1$s" class="widget store-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'my_custom_theme_widgets_init');

// Custom function to get store opening hours
function get_store_opening_hours($post_id) {
    $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
    $day_labels = array(
        'monday' => __('Monday', 'my-custom-theme'),
        'tuesday' => __('Tuesday', 'my-custom-theme'),
        'wednesday' => __('Wednesday', 'my-custom-theme'),
        'thursday' => __('Thursday', 'my-custom-theme'),
        'friday' => __('Friday', 'my-custom-theme'),
        'saturday' => __('Saturday', 'my-custom-theme'),
        'sunday' => __('Sunday', 'my-custom-theme'),
    );
    
    $hours = array();
    foreach ($days as $day) {
        $day_hours = get_post_meta($post_id, '_store_hours_' . $day, true);
        if ($day_hours) {
            $hours[$day] = array(
                'label' => $day_labels[$day],
                'hours' => $day_hours
            );
        }
    }
    
    return $hours;
}

// Check if store is currently open
function is_store_open($post_id) {
    $current_day = strtolower(date('l'));
    $current_time = date('H:i');
    
    $hours = get_post_meta($post_id, '_store_hours_' . $current_day, true);
    
    if (!$hours || $hours === 'Closed') {
        return false;
    }
    
    if (strpos($hours, '-') !== false) {
        list($open, $close) = explode('-', $hours);
        return ($current_time >= trim($open) && $current_time <= trim($close));
    }
    
    return false;
}

// Get star rating HTML
function get_star_rating($rating, $max_rating = 5) {
    $rating = floatval($rating);
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0;
    $empty_stars = $max_rating - $full_stars - $half_star;
    
    $html = '<div class="star-rating" data-rating="' . $rating . '">';
    
    // Full stars
    for ($i = 0; $i < $full_stars; $i++) {
        $html .= '<i class="fas fa-star"></i>';
    }
    
    // Half star
    if ($half_star) {
        $html .= '<i class="fas fa-star-half-alt"></i>';
    }
    
    // Empty stars
    for ($i = 0; $i < $empty_stars; $i++) {
        $html .= '<i class="far fa-star"></i>';
    }
    
    $html .= '<span class="rating-number">(' . $rating . ')</span>';
    $html .= '</div>';
    
    return $html;
}

// Custom query for store directory
function get_stores_by_filters($filters = array()) {
    $args = array(
        'post_type' => 'secondhand_store',
        'post_status' => 'publish',
        'posts_per_page' => isset($filters['per_page']) ? $filters['per_page'] : 12,
        'paged' => isset($filters['page']) ? $filters['page'] : 1,
    );
    
    // Add meta query for search
    if (!empty($filters['search'])) {
        $args['s'] = $filters['search'];
    }
    
    // Add taxonomy queries
    $tax_query = array();
    
    if (!empty($filters['category'])) {
        $tax_query[] = array(
            'taxonomy' => 'store_category',
            'field'    => 'slug',
            'terms'    => $filters['category'],
        );
    }
    
    if (!empty($filters['location'])) {
        $tax_query[] = array(
            'taxonomy' => 'store_location',
            'field'    => 'slug',
            'terms'    => $filters['location'],
        );
    }
    
    if (!empty($filters['features'])) {
        $tax_query[] = array(
            'taxonomy' => 'store_feature',
            'field'    => 'slug',
            'terms'    => $filters['features'],
            'operator' => 'IN',
        );
    }
    
    if (!empty($tax_query)) {
        $tax_query['relation'] = 'AND';
        $args['tax_query'] = $tax_query;
    }
    
    // Add meta queries for rating and other fields
    $meta_query = array();
    
    if (!empty($filters['min_rating'])) {
        $meta_query[] = array(
            'key'     => '_store_rating',
            'value'   => floatval($filters['min_rating']),
            'compare' => '>=',
            'type'    => 'NUMERIC',
        );
    }
    
    if (!empty($filters['price_range'])) {
        $meta_query[] = array(
            'key'     => '_store_price_range',
            'value'   => $filters['price_range'],
            'compare' => '=',
        );
    }
    
    if (!empty($filters['open_now'])) {
        // This would need more complex logic to check current time
        // For now, we'll skip this filter
    }
    
    if (!empty($meta_query)) {
        $meta_query['relation'] = 'AND';
        $args['meta_query'] = $meta_query;
    }
    
    // Ordering
    if (!empty($filters['orderby'])) {
        switch ($filters['orderby']) {
            case 'rating':
                $args['meta_key'] = '_store_rating';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'name':
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
                break;
            case 'newest':
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
            default:
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
        }
    }
    
    return new WP_Query($args);
}

// AJAX handler for store filtering
function ajax_filter_stores() {
    check_ajax_referer('store_directory_nonce', 'nonce');
    
    $filters = array();
    if (isset($_POST['category'])) $filters['category'] = sanitize_text_field($_POST['category']);
    if (isset($_POST['location'])) $filters['location'] = sanitize_text_field($_POST['location']);
    if (isset($_POST['min_rating'])) $filters['min_rating'] = floatval($_POST['min_rating']);
    if (isset($_POST['price_range'])) $filters['price_range'] = sanitize_text_field($_POST['price_range']);
    if (isset($_POST['search'])) $filters['search'] = sanitize_text_field($_POST['search']);
    if (isset($_POST['orderby'])) $filters['orderby'] = sanitize_text_field($_POST['orderby']);
    if (isset($_POST['page'])) $filters['page'] = intval($_POST['page']);
    
    $query = get_stores_by_filters($filters);
    
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/store-card');
        }
    } else {
        echo '<div class="no-stores-found"><p>' . __('No stores found matching your criteria.', 'my-custom-theme') . '</p></div>';
    }
    $html = ob_get_clean();
    
    wp_reset_postdata();
    
    wp_send_json_success(array(
        'html' => $html,
        'found_posts' => $query->found_posts,
        'max_pages' => $query->max_num_pages
    ));
}
add_action('wp_ajax_filter_stores', 'ajax_filter_stores');
add_action('wp_ajax_nopriv_filter_stores', 'ajax_filter_stores');

// Get all stores for map display
function get_all_stores_for_map() {
    $stores = get_posts(array(
        'post_type' => 'secondhand_store',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_store_latitude',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => '_store_longitude',
                'compare' => 'EXISTS'
            )
        )
    ));
    
    $map_data = array();
    foreach ($stores as $store) {
        $lat = get_post_meta($store->ID, '_store_latitude', true);
        $lng = get_post_meta($store->ID, '_store_longitude', true);
        $rating = get_post_meta($store->ID, '_store_rating', true);
        $address = get_post_meta($store->ID, '_store_address', true);
        
        if ($lat && $lng) {
            $map_data[] = array(
                'id' => $store->ID,
                'title' => $store->post_title,
                'lat' => floatval($lat),
                'lng' => floatval($lng),
                'rating' => floatval($rating),
                'address' => $address,
                'url' => get_permalink($store->ID),
                'thumbnail' => get_the_post_thumbnail_url($store->ID, 'store-card')
            );
        }
    }
    
    return $map_data;
}

// Custom breadcrumbs function
function store_directory_breadcrumbs() {
    if (is_front_page()) return;
    
    echo '<nav class="breadcrumbs">';
    echo '<a href="' . home_url() . '">' . __('Home', 'my-custom-theme') . '</a>';
    
    if (is_post_type_archive('secondhand_store')) {
        echo ' / ' . __('Store Directory', 'my-custom-theme');
    } elseif (is_singular('secondhand_store')) {
        echo ' / <a href="' . get_post_type_archive_link('secondhand_store') . '">' . __('Store Directory', 'my-custom-theme') . '</a>';
        echo ' / ' . get_the_title();
    } elseif (is_tax('store_category') || is_tax('store_location') || is_tax('store_feature')) {
        echo ' / <a href="' . get_post_type_archive_link('secondhand_store') . '">' . __('Store Directory', 'my-custom-theme') . '</a>';
        echo ' / ' . single_term_title('', false);
    }
    
    echo '</nav>';
}

// Add custom body classes
function store_directory_body_classes($classes) {
    if (is_post_type_archive('secondhand_store') || is_singular('secondhand_store') || 
        is_tax('store_category') || is_tax('store_location') || is_tax('store_feature')) {
        $classes[] = 'store-directory';
    }
    
    if (is_singular('secondhand_store')) {
        $classes[] = 'single-store';
    }
    
    return $classes;
}
add_filter('body_class', 'store_directory_body_classes');

// Modify main query for store archives
function modify_store_archive_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('secondhand_store')) {
            $query->set('posts_per_page', 12);
            $query->set('meta_key', '_store_rating');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC');
        }
    }
}
add_action('pre_get_posts', 'modify_store_archive_query');

// Custom excerpt length for store cards
function store_excerpt_length($length) {
    if (is_post_type_archive('secondhand_store') || is_tax('store_category') || 
        is_tax('store_location') || is_tax('store_feature')) {
        return 20;
    }
    return $length;
}
add_filter('excerpt_length', 'store_excerpt_length');

// Add custom review post type
function register_store_review_post_type() {
    $labels = array(
        'name'                  => _x('Store Reviews', 'Post type general name', 'my-custom-theme'),
        'singular_name'         => _x('Store Review', 'Post type singular name', 'my-custom-theme'),
        'menu_name'             => _x('Store Reviews', 'Admin Menu text', 'my-custom-theme'),
        'add_new'               => __('Add New', 'my-custom-theme'),
        'add_new_item'          => __('Add New Review', 'my-custom-theme'),
        'edit_item'             => __('Edit Review', 'my-custom-theme'),
        'new_item'              => __('New Review', 'my-custom-theme'),
        'view_item'             => __('View Review', 'my-custom-theme'),
        'view_items'            => __('View Reviews', 'my-custom-theme'),
        'search_items'          => __('Search Reviews', 'my-custom-theme'),
        'not_found'             => __('No reviews found', 'my-custom-theme'),
        'not_found_in_trash'    => __('No reviews found in Trash', 'my-custom-theme'),
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'store-reviews'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 8,
        'menu_icon'          => 'dashicons-star-filled',
        'supports'           => array('title', 'editor', 'author', 'custom-fields'),
        'show_in_rest'       => true,
    );
    
    register_post_type('store_review', $args);
}
add_action('init', 'register_store_review_post_type');