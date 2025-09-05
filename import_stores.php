<?php
/**
 * WordPress Stores Import Script
 * Run this from WordPress admin or via WP-CLI
 */

if (!defined('ABSPATH')) {
    exit('Direct access not allowed');
}

function import_secondhand_stores() {
    $json_data = file_get_contents(__DIR__ . '/data/stores_data.json');
    $data = json_decode($json_data, true);
    
    if (!$data) {
        wp_die('Could not load store data');
    }
    
    $imported = 0;
    $errors = [];
    
    // Import categories first
    foreach ($data['categories'] as $category) {
        $term = wp_insert_term($category['name'], 'store_category', [
            'slug' => $category['slug']
        ]);
        if (is_wp_error($term)) {
            $errors[] = 'Category: ' . $term->get_error_message();
        }
    }
    
    // Import districts
    foreach ($data['districts'] as $district) {
        $term = wp_insert_term($district['name'], 'store_district', [
            'slug' => $district['slug']
        ]);
        if (is_wp_error($term)) {
            $errors[] = 'District: ' . $term->get_error_message();
        }
    }
    
    // Import stores
    foreach ($data['stores'] as $store) {
        $post_id = wp_insert_post([
            'post_title' => $store['post_title'],
            'post_content' => $store['post_content'],
            'post_excerpt' => $store['post_excerpt'],
            'post_status' => $store['post_status'],
            'post_type' => $store['post_type']
        ]);
        
        if ($post_id && !is_wp_error($post_id)) {
            // Add meta fields
            foreach ($store['meta_fields'] as $key => $value) {
                update_post_meta($post_id, $key, $value);
            }
            
            // Add taxonomies
            foreach ($store['taxonomies'] as $taxonomy => $terms) {
                wp_set_object_terms($post_id, $terms, $taxonomy);
            }
            
            $imported++;
        } else {
            $errors[] = 'Store: ' . $store['post_title'];
        }
    }
    
    echo "<div class='notice notice-success'>";
    echo "<p>Successfully imported $imported stores</p>";
    if (!empty($errors)) {
        echo "<p>Errors: " . implode(', ', $errors) . "</p>";
    }
    echo "</div>";
}

// Uncomment to run import
// import_secondhand_stores();
?>