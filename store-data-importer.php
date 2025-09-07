<?php
/**
 * Store Data Import Script
 * 
 * This script imports secondhand store data from JSON files into WordPress
 * Run this script by accessing it directly or via WP-CLI
 */

// WordPress environment
if (!defined('ABSPATH')) {
    // If not called from WordPress, include wp-load.php
    $wp_load_path = '/var/www/html/wp-load.php';
    if (file_exists($wp_load_path)) {
        require_once($wp_load_path);
    } else {
        die('WordPress not found. Please run this script from WordPress environment.');
    }
}

class StoreDataImporter {
    
    private $json_file_path;
    private $imported_count = 0;
    private $errors = array();
    
    public function __construct($json_file_path = '/tmp/secondhand-stores-data/gothenburg_stores_research.json') {
        $this->json_file_path = $json_file_path;
    }
    
    /**
     * Main import function
     */
    public function import() {
        echo "Starting store data import...\n";
        
        // Load JSON data
        $store_data = $this->load_json_data();
        if (!$store_data) {
            echo "Failed to load store data.\n";
            return false;
        }
        
        // Create taxonomies first
        $this->create_taxonomies();
        
        // Import stores
        foreach ($store_data['stores'] as $store) {
            $this->import_store($store);
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        echo "\nImport completed!\n";
        echo "Imported: {$this->imported_count} stores\n";
        
        if (!empty($this->errors)) {
            echo "Errors encountered:\n";
            foreach ($this->errors as $error) {
                echo "- $error\n";
            }
        }
        
        return true;
    }
    
    /**
     * Load JSON data from file
     */
    private function load_json_data() {
        if (!file_exists($this->json_file_path)) {
            $this->errors[] = "JSON file not found: {$this->json_file_path}";
            return false;
        }
        
        $json_content = file_get_contents($this->json_file_path);
        $data = json_decode($json_content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->errors[] = "JSON decode error: " . json_last_error_msg();
            return false;
        }
        
        return $data;
    }
    
    /**
     * Create necessary taxonomies and terms
     */
    private function create_taxonomies() {
        echo "Creating taxonomy terms...\n";
        
        // Store categories
        $categories = array(
            'mixed-second-hand' => 'Mixed Second Hand',
            'vintage-retro' => 'Vintage & Retro',
            'charity-shop' => 'Charity Shop',
            'department-store-style' => 'Department Store Style',
            'womens-vintage' => 'Women\'s Vintage',
            'charity-chain' => 'Charity Chain',
            'designer-consignment' => 'Designer Consignment',
            'sustainable-fashion' => 'Sustainable Fashion',
            'bohemian-artistic' => 'Bohemian & Artistic',
            'electronics-gadgets' => 'Electronics & Gadgets',
            'childrens-specialist' => 'Children\'s Specialist',
            'sports-outdoor' => 'Sports & Outdoor',
            'books-media' => 'Books & Media',
            'home-garden' => 'Home & Garden',
            'music-specialist' => 'Music Specialist'
        );
        
        foreach ($categories as $slug => $name) {
            $term = wp_insert_term($name, 'store_category', array('slug' => $slug));
            if (is_wp_error($term)) {
                if ($term->get_error_code() !== 'term_exists') {
                    $this->errors[] = "Failed to create category '$name': " . $term->get_error_message();
                }
            }
        }
        
        // Store locations (Gothenburg neighborhoods)
        $locations = array(
            'linnestaden' => 'Linnéstaden',
            'centrum' => 'Centrum',
            'bergsjön' => 'Bergsjön',
            'vasastaden' => 'Vasastaden',
            'backa' => 'Backa',
            'haga' => 'Haga',
            'majorna' => 'Majorna',
            'partille' => 'Partille',
            'torslanda' => 'Torslanda'
        );
        
        foreach ($locations as $slug => $name) {
            $term = wp_insert_term($name, 'store_location', array('slug' => $slug));
            if (is_wp_error($term)) {
                if ($term->get_error_code() !== 'term_exists') {
                    $this->errors[] = "Failed to create location '$name': " . $term->get_error_message();
                }
            }
        }
        
        // Store features
        $features = array(
            'curated-selection' => 'Curated Selection',
            'designer-brands' => 'Designer Brands',
            'vintage-pieces' => 'Vintage Pieces',
            'consignment' => 'Consignment',
            'instagram-showcase' => 'Instagram Showcase',
            'wheelchair-accessible' => 'Wheelchair Accessible',
            'free-parking' => 'Free Parking',
            'student-discounts' => 'Student Discounts',
            'expert-authentication' => 'Expert Authentication',
            'alteration-services' => 'Alteration Services',
            'personal-styling' => 'Personal Styling',
            'layaway-options' => 'Layaway Options',
            'repair-services' => 'Repair Services',
            'workshops' => 'Workshops',
            'home-delivery' => 'Home Delivery'
        );
        
        foreach ($features as $slug => $name) {
            $term = wp_insert_term($name, 'store_feature', array('slug' => $slug));
            if (is_wp_error($term)) {
                if ($term->get_error_code() !== 'term_exists') {
                    $this->errors[] = "Failed to create feature '$name': " . $term->get_error_message();
                }
            }
        }
        
        echo "Taxonomy terms created.\n";
    }
    
    /**
     * Import individual store
     */
    private function import_store($store_data) {
        echo "Importing store: {$store_data['name']}...";
        
        // Check if store already exists
        $existing_store = get_posts(array(
            'post_type' => 'secondhand_store',
            'meta_query' => array(
                array(
                    'key' => '_store_import_id',
                    'value' => $store_data['id'],
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (!empty($existing_store)) {
            echo " (already exists)\n";
            return;
        }
        
        // Create post
        $post_data = array(
            'post_title' => $store_data['name'],
            'post_content' => $store_data['description'],
            'post_status' => 'publish',
            'post_type' => 'secondhand_store',
            'meta_input' => array(
                '_store_import_id' => $store_data['id'],
                '_store_address' => $store_data['address'],
                '_store_neighborhood' => $store_data['neighborhood'],
                '_store_latitude' => $store_data['coordinates']['lat'],
                '_store_longitude' => $store_data['coordinates']['lng'],
                '_store_phone' => $store_data['phone'],
                '_store_website' => $store_data['website'],
                '_store_directions_link' => $store_data['directions_link'],
                '_store_rating' => $store_data['rating'],
                '_store_review_count' => $store_data['review_count'],
                '_store_price_range' => $store_data['price_range'],
                '_store_specialties' => is_array($store_data['specialties']) ? implode(', ', $store_data['specialties']) : $store_data['specialties'],
                '_store_features' => is_array($store_data['features']) ? implode(', ', $store_data['features']) : $store_data['features'],
                '_store_payment_methods' => is_array($store_data['payment_methods']) ? implode(', ', $store_data['payment_methods']) : $store_data['payment_methods'],
                '_store_languages' => is_array($store_data['languages']) ? implode(', ', $store_data['languages']) : $store_data['languages'],
                '_store_parking' => $store_data['parking'],
                '_store_accessibility' => $store_data['accessibility'],
                '_store_instagram' => $store_data['instagram'],
            )
        );
        
        // Add opening hours
        if (!empty($store_data['opening_hours'])) {
            foreach ($store_data['opening_hours'] as $day => $hours) {
                $post_data['meta_input']["_store_hours_{$day}"] = $hours;
            }
        }
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            $this->errors[] = "Failed to create post for {$store_data['name']}: " . $post_id->get_error_message();
            echo " (error)\n";
            return;
        }
        
        // Set store category
        $category_slug = $this->get_category_slug($store_data['category']);
        if ($category_slug) {
            wp_set_object_terms($post_id, $category_slug, 'store_category');
        }
        
        // Set store location
        $location_slug = $this->get_location_slug($store_data['neighborhood']);
        if ($location_slug) {
            wp_set_object_terms($post_id, $location_slug, 'store_location');
        }
        
        // Set store features (extract from features array)
        if (!empty($store_data['features'])) {
            $feature_slugs = array();
            foreach ($store_data['features'] as $feature) {
                $slug = $this->get_feature_slug($feature);
                if ($slug) {
                    $feature_slugs[] = $slug;
                }
            }
            if (!empty($feature_slugs)) {
                wp_set_object_terms($post_id, $feature_slugs, 'store_feature');
            }
        }
        
        // Download and set featured image
        $this->set_featured_image($post_id, $store_data);
        
        $this->imported_count++;
        echo " (success)\n";
    }
    
    /**
     * Get category slug from category name
     */
    private function get_category_slug($category_name) {
        $category_mapping = array(
            'Mixed Second Hand' => 'mixed-second-hand',
            'Vintage & Retro' => 'vintage-retro',
            'Charity Shop' => 'charity-shop',
            'Department Store Style' => 'department-store-style',
            'Women\'s Vintage' => 'womens-vintage',
            'Charity Chain' => 'charity-chain',
            'Designer Consignment' => 'designer-consignment',
            'Sustainable Fashion' => 'sustainable-fashion',
            'Bohemian & Artistic' => 'bohemian-artistic',
            'Electronics & Gadgets' => 'electronics-gadgets',
            'Children\'s Specialist' => 'childrens-specialist',
            'Sports & Outdoor' => 'sports-outdoor',
            'Books & Media' => 'books-media',
            'Home & Garden' => 'home-garden',
            'Music Specialist' => 'music-specialist'
        );
        
        return isset($category_mapping[$category_name]) ? $category_mapping[$category_name] : null;
    }
    
    /**
     * Get location slug from neighborhood name
     */
    private function get_location_slug($neighborhood) {
        $location_mapping = array(
            'Linnéstaden' => 'linnestaden',
            'Centrum' => 'centrum',
            'Bergsjön' => 'bergsjön',
            'Vasastaden' => 'vasastaden',
            'Backa' => 'backa',
            'Haga' => 'haga',
            'Majorna' => 'majorna',
            'Partille' => 'partille',
            'Torslanda' => 'torslanda'
        );
        
        return isset($location_mapping[$neighborhood]) ? $location_mapping[$neighborhood] : null;
    }
    
    /**
     * Get feature slug from feature name
     */
    private function get_feature_slug($feature_name) {
        $feature_mapping = array(
            'Curated selection' => 'curated-selection',
            'Designer brands' => 'designer-brands',
            'Vintage pieces from 1960s-2000s' => 'vintage-pieces',
            'Accepts consignment' => 'consignment',
            'Instagram showcase' => 'instagram-showcase',
            'Wheelchair accessible' => 'wheelchair-accessible',
            'Free parking available' => 'free-parking',
            'Student discounts' => 'student-discounts',
            'Expert authentication' => 'expert-authentication',
            'Alteration services' => 'alteration-services',
            'Personal styling' => 'personal-styling',
            'Layaway options' => 'layaway-options',
            'Repair services' => 'repair-services',
            'Upcycling workshops' => 'workshops',
            'Home delivery for furniture' => 'home-delivery'
        );
        
        return isset($feature_mapping[$feature_name]) ? $feature_mapping[$feature_name] : null;
    }
    
    /**
     * Set featured image for store
     */
    private function set_featured_image($post_id, $store_data) {
        // For now, we'll use placeholder images based on store category
        $placeholder_images = array(
            'Mixed Second Hand' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Vintage & Retro' => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Charity Shop' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Department Store Style' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Women\'s Vintage' => 'https://images.unsplash.com/photo-1594633313593-bab3825d0caf?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Designer Consignment' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Sustainable Fashion' => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Electronics & Gadgets' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Children\'s Specialist' => 'https://images.unsplash.com/photo-1522771930-78848d9293e8?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Books & Media' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Sports & Outdoor' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Home & Garden' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?ixlib=rb-4.0.3&w=800&h=600&fit=crop',
            'Music Specialist' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?ixlib=rb-4.0.3&w=800&h=600&fit=crop'
        );
        
        $image_url = isset($placeholder_images[$store_data['category']]) 
            ? $placeholder_images[$store_data['category']] 
            : 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&w=800&h=600&fit=crop';
        
        // Download and attach image
        $image_id = $this->download_image($image_url, $store_data['name']);
        if ($image_id) {
            set_post_thumbnail($post_id, $image_id);
        }
    }
    
    /**
     * Download image and create attachment
     */
    private function download_image($image_url, $title) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        $tmp = download_url($image_url);
        if (is_wp_error($tmp)) {
            return false;
        }
        
        $file_array = array(
            'name' => basename($image_url) . '.jpg',
            'tmp_name' => $tmp
        );
        
        $id = media_handle_sideload($file_array, 0, $title);
        
        if (is_wp_error($id)) {
            @unlink($tmp);
            return false;
        }
        
        return $id;
    }
}

// Run the importer
$importer = new StoreDataImporter();
$importer->import();

?>