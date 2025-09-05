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
        // Store Categories (Vintage, Designer, Books, Furniture, etc.)
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
        
        // Store Districts/Areas in Gothenburg
        $labels = array(
            'name'              => _x('Districts', 'taxonomy general name', 'custom-post-types'),
            'singular_name'     => _x('District', 'taxonomy singular name', 'custom-post-types'),
            'search_items'      => __('Search Districts', 'custom-post-types'),
            'all_items'         => __('All Districts', 'custom-post-types'),
            'edit_item'         => __('Edit District', 'custom-post-types'),
            'update_item'       => __('Update District', 'custom-post-types'),
            'add_new_item'      => __('Add New District', 'custom-post-types'),
            'new_item_name'     => __('New District Name', 'custom-post-types'),
            'menu_name'         => __('Districts', 'custom-post-types'),
        );
        
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'district'),
            'show_in_rest'      => true,
        );
        
        register_taxonomy('store_district', array('secondhand_store'), $args);
    }
    
    public function add_secondhand_store_meta_boxes() {
        add_meta_box(
            'store_details',
            __('Store Details', 'custom-post-types'),
            array($this, 'store_details_meta_box'),
            'secondhand_store',
            'normal',
            'high'
        );
    }
    
    public function store_details_meta_box($post) {
        wp_nonce_field('store_details_nonce', 'store_details_nonce_field');
        
        $address = get_post_meta($post->ID, '_store_address', true);
        $phone = get_post_meta($post->ID, '_store_phone', true);
        $email = get_post_meta($post->ID, '_store_email', true);
        $website = get_post_meta($post->ID, '_store_website', true);
        $instagram = get_post_meta($post->ID, '_store_instagram', true);
        $opening_hours = get_post_meta($post->ID, '_store_opening_hours', true);
        $rating = get_post_meta($post->ID, '_store_rating', true);
        $price_range = get_post_meta($post->ID, '_store_price_range', true);
        $latitude = get_post_meta($post->ID, '_store_latitude', true);
        $longitude = get_post_meta($post->ID, '_store_longitude', true);
        $google_maps_link = get_post_meta($post->ID, '_store_google_maps_link', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="store_address"><?php _e('Address', 'custom-post-types'); ?></label></th>
                <td><input type="text" id="store_address" name="store_address" value="<?php echo esc_attr($address); ?>" style="width: 100%;" /></td>
            </tr>
            <tr>
                <th><label for="store_phone"><?php _e('Phone', 'custom-post-types'); ?></label></th>
                <td><input type="text" id="store_phone" name="store_phone" value="<?php echo esc_attr($phone); ?>" style="width: 100%;" /></td>
            </tr>
            <tr>
                <th><label for="store_email"><?php _e('Email', 'custom-post-types'); ?></label></th>
                <td><input type="email" id="store_email" name="store_email" value="<?php echo esc_attr($email); ?>" style="width: 100%;" /></td>
            </tr>
            <tr>
                <th><label for="store_website"><?php _e('Website URL', 'custom-post-types'); ?></label></th>
                <td><input type="url" id="store_website" name="store_website" value="<?php echo esc_attr($website); ?>" style="width: 100%;" /></td>
            </tr>
            <tr>
                <th><label for="store_instagram"><?php _e('Instagram Handle', 'custom-post-types'); ?></label></th>
                <td><input type="text" id="store_instagram" name="store_instagram" value="<?php echo esc_attr($instagram); ?>" placeholder="@storename" style="width: 100%;" /></td>
            </tr>
            <tr>
                <th><label for="store_opening_hours"><?php _e('Opening Hours', 'custom-post-types'); ?></label></th>
                <td><textarea id="store_opening_hours" name="store_opening_hours" rows="4" style="width: 100%;"><?php echo esc_textarea($opening_hours); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="store_rating"><?php _e('Rating (1-5)', 'custom-post-types'); ?></label></th>
                <td>
                    <select id="store_rating" name="store_rating">
                        <option value=""><?php _e('Select Rating', 'custom-post-types'); ?></option>
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>><?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?></option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="store_price_range"><?php _e('Price Range', 'custom-post-types'); ?></label></th>
                <td>
                    <select id="store_price_range" name="store_price_range">
                        <option value=""><?php _e('Select Price Range', 'custom-post-types'); ?></option>
                        <option value="$" <?php selected($price_range, '$'); ?>><?php _e('$ - Budget Friendly', 'custom-post-types'); ?></option>
                        <option value="$$" <?php selected($price_range, '$$'); ?>><?php _e('$$ - Moderate', 'custom-post-types'); ?></option>
                        <option value="$$$" <?php selected($price_range, '$$$'); ?>><?php _e('$$$ - High End', 'custom-post-types'); ?></option>
                        <option value="$$$$" <?php selected($price_range, '$$$$'); ?>><?php _e('$$$$ - Luxury', 'custom-post-types'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="store_latitude"><?php _e('Latitude', 'custom-post-types'); ?></label></th>
                <td><input type="number" step="any" id="store_latitude" name="store_latitude" value="<?php echo esc_attr($latitude); ?>" style="width: 100%;" /></td>
            </tr>
            <tr>
                <th><label for="store_longitude"><?php _e('Longitude', 'custom-post-types'); ?></label></th>
                <td><input type="number" step="any" id="store_longitude" name="store_longitude" value="<?php echo esc_attr($longitude); ?>" style="width: 100%;" /></td>
            </tr>
            <tr>
                <th><label for="store_google_maps_link"><?php _e('Google Maps Link', 'custom-post-types'); ?></label></th>
                <td><input type="url" id="store_google_maps_link" name="store_google_maps_link" value="<?php echo esc_attr($google_maps_link); ?>" style="width: 100%;" /></td>
            </tr>
        </table>
        <?php
    }
    
    public function save_secondhand_store_meta($post_id) {
        if (!isset($_POST['store_details_nonce_field']) || !wp_verify_nonce($_POST['store_details_nonce_field'], 'store_details_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $fields = array(
            'store_address',
            'store_phone', 
            'store_email',
            'store_website',
            'store_instagram',
            'store_opening_hours',
            'store_rating',
            'store_price_range',
            'store_latitude',
            'store_longitude',
            'store_google_maps_link'
        );
        
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