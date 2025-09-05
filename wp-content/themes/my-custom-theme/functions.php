<?php
/**
 * Theme Functions - Enhanced for Secondhand Stores Directory
 */

if (!defined('ABSPATH')) {
    exit;
}

function my_custom_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo');
    
    // Add image sizes for directory
    add_image_size('store-thumbnail', 400, 300, true);
    add_image_size('store-card', 350, 200, true);
    add_image_size('store-hero', 1200, 600, true);
    
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my-custom-theme'),
        'footer' => __('Footer Menu', 'my-custom-theme'),
        'stores' => __('Stores Directory Menu', 'my-custom-theme'),
    ));
    
    // Add excerpt support to custom post types
    add_post_type_support('secondhand_store', 'excerpt');
}
add_action('after_setup_theme', 'my_custom_theme_setup');

function my_custom_theme_scripts() {
    wp_enqueue_style('my-custom-theme-style', get_stylesheet_uri(), array(), '1.1.0');
    wp_enqueue_style('dashicons');
    
    // Enqueue directory scripts for store archive and single pages
    if (is_post_type_archive('secondhand_store') || is_singular('secondhand_store')) {
        wp_enqueue_script('stores-directory', get_template_directory_uri() . '/js/stores-directory.js', array('jquery'), '1.0.0', true);
        
        // Localize script for AJAX
        wp_localize_script('stores-directory', 'storesAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('stores_filter_nonce')
        ));
    }
    
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
        'description'   => __('Appears on store directory pages.', 'my-custom-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'my_custom_theme_widgets_init');

// AJAX handler for store filtering
function filter_stores_ajax() {
    check_ajax_referer('stores_filter_nonce', 'nonce');
    
    $search = sanitize_text_field($_POST['search']);
    $category = sanitize_text_field($_POST['category']);
    $district = sanitize_text_field($_POST['district']);
    $rating = intval($_POST['rating']);
    $price = sanitize_text_field($_POST['price']);
    
    $args = array(
        'post_type' => 'secondhand_store',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    
    // Add search query
    if (!empty($search)) {
        $args['s'] = $search;
    }
    
    // Add taxonomy queries
    $tax_query = array();
    
    if (!empty($category)) {
        $tax_query[] = array(
            'taxonomy' => 'store_category',
            'field' => 'slug',
            'terms' => $category,
        );
    }
    
    if (!empty($district)) {
        $tax_query[] = array(
            'taxonomy' => 'store_district',
            'field' => 'slug',
            'terms' => $district,
        );
    }
    
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }
    
    // Add meta query for rating and price
    $meta_query = array();
    
    if (!empty($rating)) {
        $meta_query[] = array(
            'key' => '_store_rating',
            'value' => $rating,
            'compare' => '>='
        );
    }
    
    if (!empty($price)) {
        $meta_query[] = array(
            'key' => '_store_price_range',
            'value' => $price,
            'compare' => '='
        );
    }
    
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }
    
    $stores = new WP_Query($args);
    
    ob_start();
    
    if ($stores->have_posts()) {
        while ($stores->have_posts()) {
            $stores->the_post();
            // Include the store card template
            get_template_part('template-parts/store-card');
        }
        wp_reset_postdata();
    } else {
        echo '<div class="no-stores-found"><h3>No stores found</h3><p>Try adjusting your search criteria.</p></div>';
    }
    
    $response = ob_get_clean();
    
    wp_send_json_success($response);
}
add_action('wp_ajax_filter_stores', 'filter_stores_ajax');
add_action('wp_ajax_nopriv_filter_stores', 'filter_stores_ajax');

// Add custom excerpt length for stores
function custom_store_excerpt_length($length) {
    if (is_post_type_archive('secondhand_store') || is_singular('secondhand_store')) {
        return 25;
    }
    return $length;
}
add_filter('excerpt_length', 'custom_store_excerpt_length');

// Custom excerpt more text
function custom_store_excerpt_more($more) {
    if (is_post_type_archive('secondhand_store')) {
        return '...';
    }
    return $more;
}
add_filter('excerpt_more', 'custom_store_excerpt_more');

// Add body classes for directory pages
function stores_body_classes($classes) {
    if (is_post_type_archive('secondhand_store')) {
        $classes[] = 'stores-directory';
    }
    
    if (is_singular('secondhand_store')) {
        $classes[] = 'single-store';
    }
    
    return $classes;
}
add_filter('body_class', 'stores_body_classes');

// Modify main query for store archive
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

// Add custom fields to REST API
function add_store_meta_to_rest() {
    $meta_fields = array(
        '_store_address',
        '_store_phone',
        '_store_email',
        '_store_website',
        '_store_instagram',
        '_store_opening_hours',
        '_store_rating',
        '_store_price_range',
        '_store_latitude',
        '_store_longitude',
        '_store_google_maps_link'
    );
    
    foreach ($meta_fields as $field) {
        register_rest_field('secondhand_store', str_replace('_store_', '', $field), array(
            'get_callback' => function($post) use ($field) {
                return get_post_meta($post['id'], $field, true);
            },
            'update_callback' => function($value, $post) use ($field) {
                return update_post_meta($post->ID, $field, $value);
            },
            'schema' => array(
                'description' => 'Store meta field',
                'type' => 'string'
            ),
        ));
    }
}
add_action('rest_api_init', 'add_store_meta_to_rest');

// Add structured data for stores
function add_store_structured_data() {
    if (is_singular('secondhand_store')) {
        global $post;
        
        $address = get_post_meta($post->ID, '_store_address', true);
        $phone = get_post_meta($post->ID, '_store_phone', true);
        $website = get_post_meta($post->ID, '_store_website', true);
        $rating = get_post_meta($post->ID, '_store_rating', true);
        $opening_hours = get_post_meta($post->ID, '_store_opening_hours', true);
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Store',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'url' => get_permalink(),
        );
        
        if ($address) {
            $schema['address'] = array(
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
                'addressLocality' => 'Gothenburg',
                'addressCountry' => 'Sweden'
            );
        }
        
        if ($phone) {
            $schema['telephone'] = $phone;
        }
        
        if ($website) {
            $schema['sameAs'] = array($website);
        }
        
        if ($rating) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $rating,
                'bestRating' => '5',
                'ratingCount' => '1'
            );
        }
        
        if (has_post_thumbnail()) {
            $schema['image'] = get_the_post_thumbnail_url($post->ID, 'large');
        }
        
        echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
    }
}
add_action('wp_head', 'add_store_structured_data');

// Add custom admin columns for stores
function custom_store_columns($columns) {
    $columns['store_rating'] = __('Rating', 'my-custom-theme');
    $columns['store_district'] = __('District', 'my-custom-theme');
    $columns['store_category'] = __('Category', 'my-custom-theme');
    return $columns;
}
add_filter('manage_secondhand_store_posts_columns', 'custom_store_columns');

function custom_store_column_content($column, $post_id) {
    switch ($column) {
        case 'store_rating':
            $rating = get_post_meta($post_id, '_store_rating', true);
            if ($rating) {
                $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                echo $stars . ' (' . $rating . '/5)';
            } else {
                echo '—';
            }
            break;
            
        case 'store_district':
            $terms = get_the_terms($post_id, 'store_district');
            if ($terms && !is_wp_error($terms)) {
                echo esc_html($terms[0]->name);
            } else {
                echo '—';
            }
            break;
            
        case 'store_category':
            $terms = get_the_terms($post_id, 'store_category');
            if ($terms && !is_wp_error($terms)) {
                $categories = array();
                foreach ($terms as $term) {
                    $categories[] = $term->name;
                }
                echo esc_html(implode(', ', $categories));
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_secondhand_store_posts_custom_column', 'custom_store_column_content', 10, 2);

// Make custom columns sortable
function custom_store_sortable_columns($columns) {
    $columns['store_rating'] = 'store_rating';
    return $columns;
}
add_filter('manage_edit-secondhand_store_sortable_columns', 'custom_store_sortable_columns');

function custom_store_orderby($query) {
    if (!is_admin()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    if ('store_rating' == $orderby) {
        $query->set('meta_key', '_store_rating');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'custom_store_orderby');