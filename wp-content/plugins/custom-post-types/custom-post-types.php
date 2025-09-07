<?php
/**
 * Plugin Name: Custom Post Types
 * Plugin URI: https://example.com/
 * Description: Adds custom post types to WordPress
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

class CustomPostTypes {
    
    public function __construct() {
        add_action('init', array($this, 'register_portfolio_post_type'));
        add_action('init', array($this, 'register_portfolio_taxonomies'));
        add_action('init', array($this, 'register_testimonial_post_type'));
        add_action('init', array($this, 'register_secondhand_store_post_type'));
        add_action('init', array($this, 'register_secondhand_store_taxonomies'));
        add_action('add_meta_boxes', array($this, 'add_secondhand_store_meta_boxes'));
        add_action('save_post', array($this, 'save_secondhand_store_meta'));
    }
    
    public function register_portfolio_post_type() {
        $labels = array(
            'name'                  => _x('Portfolio', 'Post type general name', 'custom-post-types'),
            'singular_name'         => _x('Portfolio Item', 'Post type singular name', 'custom-post-types'),
            'menu_name'             => _x('Portfolio', 'Admin Menu text', 'custom-post-types'),
            'add_new'               => __('Add New', 'custom-post-types'),
            'add_new_item'          => __('Add New Portfolio Item', 'custom-post-types'),
            'edit_item'             => __('Edit Portfolio Item', 'custom-post-types'),
            'new_item'              => __('New Portfolio Item', 'custom-post-types'),
            'view_item'             => __('View Portfolio Item', 'custom-post-types'),
            'view_items'            => __('View Portfolio', 'custom-post-types'),
            'search_items'          => __('Search Portfolio', 'custom-post-types'),
            'not_found'             => __('No portfolio items found', 'custom-post-types'),
            'not_found_in_trash'    => __('No portfolio items found in Trash', 'custom-post-types'),
        );
        
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'portfolio'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'show_in_rest'       => true,
        );
        
        register_post_type('portfolio', $args);
    }
    
    public function register_portfolio_taxonomies() {
        // Portfolio Categories
        $labels = array(
            'name'              => _x('Portfolio Categories', 'taxonomy general name', 'custom-post-types'),
            'singular_name'     => _x('Portfolio Category', 'taxonomy singular name', 'custom-post-types'),
            'search_items'      => __('Search Categories', 'custom-post-types'),
            'all_items'         => __('All Categories', 'custom-post-types'),
            'edit_item'         => __('Edit Category', 'custom-post-types'),
            'update_item'       => __('Update Category', 'custom-post-types'),
            'add_new_item'      => __('Add New Category', 'custom-post-types'),
            'new_item_name'     => __('New Category Name', 'custom-post-types'),
            'menu_name'         => __('Categories', 'custom-post-types'),
        );
        
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'portfolio-category'),
            'show_in_rest'      => true,
        );
        
        register_taxonomy('portfolio_category', array('portfolio'), $args);
        
        // Portfolio Tags
        $labels = array(
            'name'              => _x('Portfolio Tags', 'taxonomy general name', 'custom-post-types'),
            'singular_name'     => _x('Portfolio Tag', 'taxonomy singular name', 'custom-post-types'),
            'search_items'      => __('Search Tags', 'custom-post-types'),
            'all_items'         => __('All Tags', 'custom-post-types'),
            'edit_item'         => __('Edit Tag', 'custom-post-types'),
            'update_item'       => __('Update Tag', 'custom-post-types'),
            'add_new_item'      => __('Add New Tag', 'custom-post-types'),
            'new_item_name'     => __('New Tag Name', 'custom-post-types'),
            'menu_name'         => __('Tags', 'custom-post-types'),
        );
        
        $args = array(
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'portfolio-tag'),
            'show_in_rest'      => true,
        );
        
        register_taxonomy('portfolio_tag', array('portfolio'), $args);
    }
    
    public function register_testimonial_post_type() {
        $labels = array(
            'name'                  => _x('Testimonials', 'Post type general name', 'custom-post-types'),
            'singular_name'         => _x('Testimonial', 'Post type singular name', 'custom-post-types'),
            'menu_name'             => _x('Testimonials', 'Admin Menu text', 'custom-post-types'),
            'add_new'               => __('Add New', 'custom-post-types'),
            'add_new_item'          => __('Add New Testimonial', 'custom-post-types'),
            'edit_item'             => __('Edit Testimonial', 'custom-post-types'),
            'new_item'              => __('New Testimonial', 'custom-post-types'),
            'view_item'             => __('View Testimonial', 'custom-post-types'),
            'view_items'            => __('View Testimonials', 'custom-post-types'),
            'search_items'          => __('Search Testimonials', 'custom-post-types'),
            'not_found'             => __('No testimonials found', 'custom-post-types'),
            'not_found_in_trash'    => __('No testimonials found in Trash', 'custom-post-types'),
        );
        
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'testimonials'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 6,
            'menu_icon'          => 'dashicons-format-quote',
            'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'show_in_rest'       => true,
        );
        
        register_post_type('testimonial', $args);
    }
    
    public function register_secondhand_store_post_type() {
        $labels = array(
            'name'                  => _x('Secondhand Stores', 'Post type general name', 'custom-post-types'),
            'singular_name'         => _x('Secondhand Store', 'Post type singular name', 'custom-post-types'),
            'menu_name'             => _x('Secondhand Stores', 'Admin Menu text', 'custom-post-types'),
            'add_new'               => __('Add New', 'custom-post-types'),
            'add_new_item'          => __('Add New Store', 'custom-post-types'),
            'edit_item'             => __('Edit Store', 'custom-post-types'),
            'new_item'              => __('New Store', 'custom-post-types'),
            'view_item'             => __('View Store', 'custom-post-types'),
            'view_items'            => __('View Stores', 'custom-post-types'),
            'search_items'          => __('Search Stores', 'custom-post-types'),
            'not_found'             => __('No stores found', 'custom-post-types'),
            'not_found_in_trash'    => __('No stores found in Trash', 'custom-post-types'),
        );
        
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'stores'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 7,
            'menu_icon'          => 'dashicons-store',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'show_in_rest'       => true,
        );
        
        register_post_type('secondhand_store', $args);
    }
    
    public function register_secondhand_store_taxonomies() {
        // Store Categories
        $labels = array(
            'name'              => _x('Store Categories', 'taxonomy general name', 'custom-post-types'),
            'singular_name'     => _x('Store Category', 'taxonomy singular name', 'custom-post-types'),
            'search_items'      => __('Search Categories', 'custom-post-types'),
            'all_items'         => __('All Categories', 'custom-post-types'),
            'edit_item'         => __('Edit Category', 'custom-post-types'),
            'update_item'       => __('Update Category', 'custom-post-types'),
            'add_new_item'      => __('Add New Category', 'custom-post-types'),
            'new_item_name'     => __('New Category Name', 'custom-post-types'),
            'menu_name'         => __('Categories', 'custom-post-types'),
        );
        
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'store-category'),
            'show_in_rest'      => true,
        );
        
        register_taxonomy('store_category', array('secondhand_store'), $args);
        
        // Locations
        $labels = array(
            'name'              => _x('Locations', 'taxonomy general name', 'custom-post-types'),
            'singular_name'     => _x('Location', 'taxonomy singular name', 'custom-post-types'),
            'search_items'      => __('Search Locations', 'custom-post-types'),
            'all_items'         => __('All Locations', 'custom-post-types'),
            'edit_item'         => __('Edit Location', 'custom-post-types'),
            'update_item'       => __('Update Location', 'custom-post-types'),
            'add_new_item'      => __('Add New Location', 'custom-post-types'),
            'new_item_name'     => __('New Location Name', 'custom-post-types'),
            'menu_name'         => __('Locations', 'custom-post-types'),
        );
        
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'location'),
            'show_in_rest'      => true,
        );
        
        register_taxonomy('store_location', array('secondhand_store'), $args);
        
        // Store Features
        $labels = array(
            'name'              => _x('Store Features', 'taxonomy general name', 'custom-post-types'),
            'singular_name'     => _x('Store Feature', 'taxonomy singular name', 'custom-post-types'),
            'search_items'      => __('Search Features', 'custom-post-types'),
            'all_items'         => __('All Features', 'custom-post-types'),
            'edit_item'         => __('Edit Feature', 'custom-post-types'),
            'update_item'       => __('Update Feature', 'custom-post-types'),
            'add_new_item'      => __('Add New Feature', 'custom-post-types'),
            'new_item_name'     => __('New Feature Name', 'custom-post-types'),
            'menu_name'         => __('Features', 'custom-post-types'),
        );
        
        $args = array(
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'store-feature'),
            'show_in_rest'      => true,
        );
        
        register_taxonomy('store_feature', array('secondhand_store'), $args);
    }
    
    public function add_secondhand_store_meta_boxes() {
        add_meta_box(
            'store_details',
            __('Store Details', 'custom-post-types'),
            array($this, 'store_details_meta_box_callback'),
            'secondhand_store',
            'normal',
            'high'
        );
        
        add_meta_box(
            'store_contact',
            __('Contact Information', 'custom-post-types'),
            array($this, 'store_contact_meta_box_callback'),
            'secondhand_store',
            'normal',
            'high'
        );
        
        add_meta_box(
            'store_hours',
            __('Opening Hours', 'custom-post-types'),
            array($this, 'store_hours_meta_box_callback'),
            'secondhand_store',
            'normal',
            'high'
        );
        
        add_meta_box(
            'store_social',
            __('Social Media & Reviews', 'custom-post-types'),
            array($this, 'store_social_meta_box_callback'),
            'secondhand_store',
            'normal',
            'high'
        );
    }
    
    public function store_details_meta_box_callback($post) {
        wp_nonce_field('store_details_nonce', 'store_details_nonce');
        
        $address = get_post_meta($post->ID, '_store_address', true);
        $neighborhood = get_post_meta($post->ID, '_store_neighborhood', true);
        $latitude = get_post_meta($post->ID, '_store_latitude', true);
        $longitude = get_post_meta($post->ID, '_store_longitude', true);
        $directions_link = get_post_meta($post->ID, '_store_directions_link', true);
        $rating = get_post_meta($post->ID, '_store_rating', true);
        $review_count = get_post_meta($post->ID, '_store_review_count', true);
        $price_range = get_post_meta($post->ID, '_store_price_range', true);
        $specialties = get_post_meta($post->ID, '_store_specialties', true);
        $features = get_post_meta($post->ID, '_store_features', true);
        $payment_methods = get_post_meta($post->ID, '_store_payment_methods', true);
        $languages = get_post_meta($post->ID, '_store_languages', true);
        $parking = get_post_meta($post->ID, '_store_parking', true);
        $accessibility = get_post_meta($post->ID, '_store_accessibility', true);
        
        echo '<table class="form-table">';
        echo '<tr><td><label for="store_address">' . __('Address', 'custom-post-types') . '</label></td>';
        echo '<td><input type="text" id="store_address" name="store_address" value="' . esc_attr($address) . '" class="regular-text" /></td></tr>';
        
        echo '<tr><td><label for="store_neighborhood">' . __('Neighborhood', 'custom-post-types') . '</label></td>';
        echo '<td><input type="text" id="store_neighborhood" name="store_neighborhood" value="' . esc_attr($neighborhood) . '" class="regular-text" /></td></tr>';
        
        echo '<tr><td><label for="store_latitude">' . __('Latitude', 'custom-post-types') . '</label></td>';
        echo '<td><input type="text" id="store_latitude" name="store_latitude" value="' . esc_attr($latitude) . '" class="regular-text" /></td></tr>';
        
        echo '<tr><td><label for="store_longitude">' . __('Longitude', 'custom-post-types') . '</label></td>';
        echo '<td><input type="text" id="store_longitude" name="store_longitude" value="' . esc_attr($longitude) . '" class="regular-text" /></td></tr>';
        
        echo '<tr><td><label for="store_directions_link">' . __('Google Maps Directions Link', 'custom-post-types') . '</label></td>';
        echo '<td><input type="url" id="store_directions_link" name="store_directions_link" value="' . esc_attr($directions_link) . '" class="regular-text" /></td></tr>';
        
        echo '<tr><td><label for="store_rating">' . __('Rating (1-5)', 'custom-post-types') . '</label></td>';
        echo '<td><input type="number" id="store_rating" name="store_rating" value="' . esc_attr($rating) . '" min="1" max="5" step="0.1" /></td></tr>';
        
        echo '<tr><td><label for="store_review_count">' . __('Review Count', 'custom-post-types') . '</label></td>';
        echo '<td><input type="number" id="store_review_count" name="store_review_count" value="' . esc_attr($review_count) . '" /></td></tr>';
        
        echo '<tr><td><label for="store_price_range">' . __('Price Range', 'custom-post-types') . '</label></td>';
        echo '<td><select id="store_price_range" name="store_price_range">';
        echo '<option value="$"' . selected($price_range, '$', false) . '>$ - Budget-friendly</option>';
        echo '<option value="$$"' . selected($price_range, '$$', false) . '>$$ - Moderate</option>';
        echo '<option value="$$$"' . selected($price_range, '$$$', false) . '>$$$ - Higher-end</option>';
        echo '</select></td></tr>';
        
        echo '<tr><td><label for="store_specialties">' . __('Specialties (comma-separated)', 'custom-post-types') . '</label></td>';
        echo '<td><textarea id="store_specialties" name="store_specialties" rows="3" class="large-text">' . esc_textarea($specialties) . '</textarea></td></tr>';
        
        echo '<tr><td><label for="store_features">' . __('Features (comma-separated)', 'custom-post-types') . '</label></td>';
        echo '<td><textarea id="store_features" name="store_features" rows="3" class="large-text">' . esc_textarea($features) . '</textarea></td></tr>';
        
        echo '<tr><td><label for="store_payment_methods">' . __('Payment Methods (comma-separated)', 'custom-post-types') . '</label></td>';
        echo '<td><input type="text" id="store_payment_methods" name="store_payment_methods" value="' . esc_attr($payment_methods) . '" class="regular-text" /></td></tr>';
        
        echo '<tr><td><label for="store_languages">' . __('Languages Spoken (comma-separated)', 'custom-post-types') . '</label></td>';
        echo '<td><input type="text" id="store_languages" name="store_languages" value="' . esc_attr($languages) . '" class="regular-text" /></td></tr>';
        
        echo '<tr><td><label for="store_parking">' . __('Parking Information', 'custom-post-types') . '</label></td>';
        echo '<td><input type="text" id="store_parking" name="store_parking" value="' . esc_attr($parking) . '" class="regular-text" /></td></tr>';
        
        echo '<tr><td><label for="store_accessibility">' . __('Accessibility Information', 'custom-post-types') . '</label></td>';
        echo '<td><input type="text" id="store_accessibility" name="store_accessibility" value="' . esc_attr($accessibility) . '" class="regular-text" /></td></tr>';
        
        echo '</table>';
    }
    
    public function store_contact_meta_box_callback($post) {
        $phone = get_post_meta($post->ID, '_store_phone', true);
        $website = get_post_meta($post->ID, '_store_website', true);
        $email = get_post_meta($post->ID, '_store_email', true);
        
        echo '<table class="form-table">';
        echo '<tr><td><label for="store_phone">' . __('Phone Number', 'custom-post-types') . '</label></td>';
        echo '<td><input type="tel" id="store_phone" name="store_phone" value="' . esc_attr($phone) . '" class="regular-text" /></td></tr>';
        
        echo '<tr><td><label for="store_website">' . __('Website URL', 'custom-post-types') . '</label></td>';
        echo '<td><input type="url" id="store_website" name="store_website" value="' . esc_attr($website) . '" class="regular-text" /></td></tr>';
        
        echo '<tr><td><label for="store_email">' . __('Email Address', 'custom-post-types') . '</label></td>';
        echo '<td><input type="email" id="store_email" name="store_email" value="' . esc_attr($email) . '" class="regular-text" /></td></tr>';
        
        echo '</table>';
    }
    
    public function store_hours_meta_box_callback($post) {
        $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        $day_labels = array(
            'monday' => __('Monday', 'custom-post-types'),
            'tuesday' => __('Tuesday', 'custom-post-types'),
            'wednesday' => __('Wednesday', 'custom-post-types'),
            'thursday' => __('Thursday', 'custom-post-types'),
            'friday' => __('Friday', 'custom-post-types'),
            'saturday' => __('Saturday', 'custom-post-types'),
            'sunday' => __('Sunday', 'custom-post-types'),
        );
        
        echo '<table class="form-table">';
        foreach ($days as $day) {
            $hours = get_post_meta($post->ID, '_store_hours_' . $day, true);
            echo '<tr><td><label for="store_hours_' . $day . '">' . $day_labels[$day] . '</label></td>';
            echo '<td><input type="text" id="store_hours_' . $day . '" name="store_hours_' . $day . '" value="' . esc_attr($hours) . '" class="regular-text" placeholder="10:00-18:00 or Closed" /></td></tr>';
        }
        echo '</table>';
        echo '<p><em>' . __('Format: HH:MM-HH:MM (24-hour format) or "Closed"', 'custom-post-types') . '</em></p>';
    }
    
    public function store_social_meta_box_callback($post) {
        $instagram = get_post_meta($post->ID, '_store_instagram', true);
        $facebook = get_post_meta($post->ID, '_store_facebook', true);
        $google_reviews = get_post_meta($post->ID, '_store_google_reviews', true);
        
        echo '<table class="form-table">';
        echo '<tr><td><label for="store_instagram">' . __('Instagram Username', 'custom-post-types') . '</label></td>';
        echo '<td><input type="text" id="store_instagram" name="store_instagram" value="' . esc_attr($instagram) . '" class="regular-text" placeholder="@username" /></td></tr>';
        
        echo '<tr><td><label for="store_facebook">' . __('Facebook Page URL', 'custom-post-types') . '</label></td>';
        echo '<td><input type="url" id="store_facebook" name="store_facebook" value="' . esc_attr($facebook) . '" class="regular-text" /></td></tr>';
        
        echo '<tr><td><label for="store_google_reviews">' . __('Google Reviews URL', 'custom-post-types') . '</label></td>';
        echo '<td><input type="url" id="store_google_reviews" name="store_google_reviews" value="' . esc_attr($google_reviews) . '" class="regular-text" /></td></tr>';
        
        echo '</table>';
    }
    
    public function save_secondhand_store_meta($post_id) {
        if (!isset($_POST['store_details_nonce']) || !wp_verify_nonce($_POST['store_details_nonce'], 'store_details_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $fields = array(
            'store_address', 'store_neighborhood', 'store_latitude', 'store_longitude',
            'store_directions_link', 'store_rating', 'store_review_count', 'store_price_range',
            'store_specialties', 'store_features', 'store_payment_methods', 'store_languages',
            'store_parking', 'store_accessibility', 'store_phone', 'store_website', 'store_email',
            'store_instagram', 'store_facebook', 'store_google_reviews'
        );
        
        $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        foreach ($days as $day) {
            $fields[] = 'store_hours_' . $day;
        }
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}

// Initialize the plugin
new CustomPostTypes();

// Flush rewrite rules on activation
register_activation_hook(__FILE__, function() {
    $cpt = new CustomPostTypes();
    $cpt->register_portfolio_post_type();
    $cpt->register_portfolio_taxonomies();
    $cpt->register_testimonial_post_type();
    $cpt->register_secondhand_store_post_type();
    $cpt->register_secondhand_store_taxonomies();
    flush_rewrite_rules();
});

// Flush rewrite rules on deactivation
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});