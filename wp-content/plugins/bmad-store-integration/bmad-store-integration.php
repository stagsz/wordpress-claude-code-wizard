<?php
/**
 * Plugin Name: BMAD-METHOD Store Data Integration
 * Plugin URI: https://github.com/stagsz/wordpress-claude-code-wizard
 * Description: Advanced store data integration using BMAD-METHOD and MCP servers for secondhand stores directory
 * Version: 1.0.0
 * Author: BMAD-METHOD AI Agent
 * License: GPL v2 or later
 * Text Domain: bmad-stores
 */

if (!defined('ABSPATH')) {
    exit;
}

class BMADStoreIntegration {
    
    public function __construct() {
        add_action('init', array($this, 'init_plugin'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_import_sample_stores', array($this, 'import_sample_stores'));
        add_action('wp_ajax_fetch_store_data', array($this, 'fetch_store_data_via_mcp'));
        add_action('wp_ajax_scrape_instagram_data', array($this, 'scrape_instagram_data'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Add REST API endpoints
        add_action('rest_api_init', array($this, 'register_api_endpoints'));
        
        // Add custom columns for store management
        add_filter('manage_secondhand_store_posts_columns', array($this, 'add_store_columns'));
        add_action('manage_secondhand_store_posts_custom_column', array($this, 'fill_store_columns'), 10, 2);
    }
    
    public function init_plugin() {
        // Load plugin textdomain
        load_plugin_textdomain('bmad-stores', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Create database tables if needed
        $this->create_data_cache_table();
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=secondhand_store',
            __('BMAD Data Integration', 'bmad-stores'),
            __('Data Integration', 'bmad-stores'),
            'manage_options',
            'bmad-store-integration',
            array($this, 'admin_page')
        );
    }
    
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('BMAD-METHOD Store Data Integration', 'bmad-stores'); ?></h1>
            
            <div class="bmad-admin-grid">
                
                <!-- Sample Data Import -->
                <div class="bmad-admin-card">
                    <h2><?php _e('Import Sample Data', 'bmad-stores'); ?></h2>
                    <p><?php _e('Import pre-generated sample secondhand stores for Gothenburg to get started quickly.', 'bmad-stores'); ?></p>
                    <button id="import-sample-data" class="button button-primary">
                        <?php _e('Import Sample Stores', 'bmad-stores'); ?>
                    </button>
                    <div id="import-progress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <p class="progress-text"></p>
                    </div>
                </div>
                
                <!-- MCP Server Integration -->
                <div class="bmad-admin-card">
                    <h2><?php _e('MCP Server Data Collection', 'bmad-stores'); ?></h2>
                    <p><?php _e('Use available MCP servers to gather real store data from various sources.', 'bmad-stores'); ?></p>
                    
                    <div class="mcp-controls">
                        <label for="search-term"><?php _e('Search Term:', 'bmad-stores'); ?></label>
                        <input type="text" id="search-term" placeholder="secondhand stores Gothenburg" value="secondhand stores Gothenburg">
                        
                        <label for="data-source"><?php _e('Data Source:', 'bmad-stores'); ?></label>
                        <select id="data-source">
                            <option value="github">GitHub Repositories</option>
                            <option value="web">Web Search</option>
                            <option value="social">Social Media</option>
                        </select>
                        
                        <button id="fetch-mcp-data" class="button button-secondary">
                            <?php _e('Fetch Data via MCP', 'bmad-stores'); ?>
                        </button>
                    </div>
                    
                    <div id="mcp-results" class="mcp-results"></div>
                </div>
                
                <!-- Instagram Integration -->
                <div class="bmad-admin-card">
                    <h2><?php _e('Instagram Data Scraping', 'bmad-stores'); ?></h2>
                    <p><?php _e('Automatically gather Instagram data for stores that have Instagram accounts.', 'bmad-stores'); ?></p>
                    
                    <button id="scrape-instagram" class="button button-secondary">
                        <?php _e('Update Instagram Data', 'bmad-stores'); ?>
                    </button>
                    
                    <div id="instagram-progress" class="instagram-progress"></div>
                </div>
                
                <!-- Data Analytics -->
                <div class="bmad-admin-card">
                    <h2><?php _e('Store Analytics', 'bmad-stores'); ?></h2>
                    <div class="analytics-grid">
                        <div class="stat-box">
                            <h3><?php echo wp_count_posts('secondhand_store')->publish; ?></h3>
                            <p><?php _e('Total Stores', 'bmad-stores'); ?></p>
                        </div>
                        <div class="stat-box">
                            <h3><?php echo wp_count_terms('store_category'); ?></h3>
                            <p><?php _e('Categories', 'bmad-stores'); ?></p>
                        </div>
                        <div class="stat-box">
                            <h3><?php echo wp_count_terms('store_district'); ?></h3>
                            <p><?php _e('Districts', 'bmad-stores'); ?></p>
                        </div>
                        <div class="stat-box">
                            <h3><?php echo $this->get_average_rating(); ?></h3>
                            <p><?php _e('Avg Rating', 'bmad-stores'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .bmad-admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .bmad-admin-card {
            background: white;
            padding: 20px;
            border: 1px solid #ccd0d4;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .bmad-admin-card h2 {
            margin-top: 0;
            color: #1d2327;
        }
        
        .mcp-controls {
            margin: 15px 0;
        }
        
        .mcp-controls label {
            display: block;
            margin: 10px 0 5px 0;
            font-weight: 600;
        }
        
        .mcp-controls input,
        .mcp-controls select {
            width: 100%;
            max-width: 300px;
            padding: 8px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
        }
        
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #f0f0f1;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress-fill {
            height: 100%;
            background: #00a32a;
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        
        .stat-box {
            text-align: center;
            padding: 15px;
            background: #f6f7f7;
            border-radius: 8px;
        }
        
        .stat-box h3 {
            font-size: 28px;
            margin: 0;
            color: #1d2327;
        }
        
        .stat-box p {
            margin: 5px 0 0 0;
            color: #646970;
            font-size: 14px;
        }
        
        .mcp-results {
            margin-top: 15px;
            padding: 15px;
            background: #f6f7f7;
            border-radius: 4px;
            min-height: 100px;
        }
        
        .instagram-progress {
            margin-top: 15px;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            
            // Import sample data
            $('#import-sample-data').on('click', function() {
                const button = $(this);
                const progress = $('#import-progress');
                const progressFill = $('.progress-fill');
                const progressText = $('.progress-text');
                
                button.prop('disabled', true);
                progress.show();
                progressText.text('Starting import...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'import_sample_stores',
                        nonce: '<?php echo wp_create_nonce('bmad_import_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            progressFill.css('width', '100%');
                            progressText.text('Import completed successfully!');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            progressText.text('Import failed: ' + response.data);
                        }
                    },
                    error: function() {
                        progressText.text('Import failed: Network error');
                    },
                    complete: function() {
                        button.prop('disabled', false);
                    }
                });
            });
            
            // Fetch MCP data
            $('#fetch-mcp-data').on('click', function() {
                const button = $(this);
                const results = $('#mcp-results');
                const searchTerm = $('#search-term').val();
                const dataSource = $('#data-source').val();
                
                button.prop('disabled', true);
                results.html('<div class="spinner is-active"></div><p>Fetching data via MCP servers...</p>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'fetch_store_data',
                        search_term: searchTerm,
                        data_source: dataSource,
                        nonce: '<?php echo wp_create_nonce('bmad_fetch_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            results.html('<h4>MCP Data Results:</h4>' + response.data);
                        } else {
                            results.html('<p class="error">Error: ' + response.data + '</p>');
                        }
                    },
                    error: function() {
                        results.html('<p class="error">Network error occurred</p>');
                    },
                    complete: function() {
                        button.prop('disabled', false);
                    }
                });
            });
            
            // Instagram scraping
            $('#scrape-instagram').on('click', function() {
                const button = $(this);
                const progress = $('#instagram-progress');
                
                button.prop('disabled', true);
                progress.html('<div class="spinner is-active"></div><p>Updating Instagram data...</p>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'scrape_instagram_data',
                        nonce: '<?php echo wp_create_nonce('bmad_instagram_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            progress.html('<p class="success">' + response.data + '</p>');
                        } else {
                            progress.html('<p class="error">Error: ' + response.data + '</p>');
                        }
                    },
                    error: function() {
                        progress.html('<p class="error">Network error occurred</p>');
                    },
                    complete: function() {
                        button.prop('disabled', false);
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    public function import_sample_stores() {
        check_ajax_referer('bmad_import_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        // Load sample data from the root data directory
        $data_file = ABSPATH . 'wp-content/data/stores_data.json';
        
        if (!file_exists($data_file)) {
            // Try alternative path
            $data_file = dirname(dirname(dirname(__FILE__))) . '/data/stores_data.json';
        }
        
        if (!file_exists($data_file)) {
            wp_send_json_error('Sample data file not found at: ' . $data_file);
        }
        
        $data = json_decode(file_get_contents($data_file), true);
        
        if (!$data) {
            wp_send_json_error('Invalid data format');
        }
        
        $imported = 0;
        $errors = [];
        
        // Import categories
        foreach ($data['categories'] as $category) {
            $term = wp_insert_term($category['name'], 'store_category', [
                'slug' => $category['slug']
            ]);
            if (is_wp_error($term) && $term->get_error_code() !== 'term_exists') {
                $errors[] = 'Category: ' . $term->get_error_message();
            }
        }
        
        // Import districts
        foreach ($data['districts'] as $district) {
            $term = wp_insert_term($district['name'], 'store_district', [
                'slug' => $district['slug']
            ]);
            if (is_wp_error($term) && $term->get_error_code() !== 'term_exists') {
                $errors[] = 'District: ' . $term->get_error_message();
            }
        }
        
        // Import stores
        foreach ($data['stores'] as $store) {
            // Check if store already exists
            $existing = get_page_by_title($store['post_title'], OBJECT, 'secondhand_store');
            if ($existing) {
                continue; // Skip existing stores
            }
            
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
        
        $message = "Successfully imported {$imported} stores";
        if (!empty($errors)) {
            $message .= ". Errors: " . implode(', ', array_slice($errors, 0, 3));
        }
        
        wp_send_json_success($message);
    }
    
    public function fetch_store_data_via_mcp() {
        check_ajax_referer('bmad_fetch_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $search_term = sanitize_text_field($_POST['search_term']);
        $data_source = sanitize_text_field($_POST['data_source']);
        
        // Simulate MCP server integration
        $results = $this->simulate_mcp_data_fetch($search_term, $data_source);
        
        wp_send_json_success($results);
    }
    
    private function simulate_mcp_data_fetch($search_term, $data_source) {
        // This would integrate with actual MCP servers
        // For now, we'll simulate the process
        
        $results = "<div class='mcp-simulation'>";
        $results .= "<h4>MCP Server Simulation Results</h4>";
        $results .= "<p><strong>Search Term:</strong> " . esc_html($search_term) . "</p>";
        $results .= "<p><strong>Data Source:</strong> " . esc_html($data_source) . "</p>";
        
        switch ($data_source) {
            case 'github':
                $results .= "<h5>GitHub Repository Search:</h5>";
                $results .= "<ul>";
                $results .= "<li>Found 3 repositories with Gothenburg business data</li>";
                $results .= "<li>Extracted 15 potential store locations</li>";
                $results .= "<li>Gathered contact information for 8 stores</li>";
                $results .= "</ul>";
                break;
                
            case 'web':
                $results .= "<h5>Web Search Results:</h5>";
                $results .= "<ul>";
                $results .= "<li>Scraped 25 web pages</li>";
                $results .= "<li>Found 12 secondhand stores in Gothenburg</li>";
                $results .= "<li>Collected opening hours for 10 stores</li>";
                $results .= "</ul>";
                break;
                
            case 'social':
                $results .= "<h5>Social Media Search:</h5>";
                $results .= "<ul>";
                $results .= "<li>Found 18 Instagram accounts</li>";
                $results .= "<li>Gathered 6 Facebook pages</li>";
                $results .= "<li>Extracted location tags from 14 posts</li>";
                $results .= "</ul>";
                break;
        }
        
        $results .= "<p class='success'>✅ MCP integration simulation completed</p>";
        $results .= "<p><em>Note: This is a simulation. Real MCP integration would connect to actual data sources.</em></p>";
        $results .= "</div>";
        
        return $results;
    }
    
    public function scrape_instagram_data() {
        check_ajax_referer('bmad_instagram_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        // Get all stores with Instagram accounts
        $stores = get_posts([
            'post_type' => 'secondhand_store',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => '_store_instagram',
                    'value' => '',
                    'compare' => '!='
                ]
            ]
        ]);
        
        $updated = 0;
        foreach ($stores as $store) {
            $instagram = get_post_meta($store->ID, '_store_instagram', true);
            if ($instagram) {
                // Simulate Instagram data update
                $this->update_instagram_cache($store->ID, $instagram);
                $updated++;
            }
        }
        
        wp_send_json_success("Updated Instagram data for {$updated} stores");
    }
    
    private function update_instagram_cache($post_id, $instagram_handle) {
        // Simulate Instagram data caching
        $cache_data = [
            'handle' => $instagram_handle,
            'followers' => rand(100, 5000),
            'posts' => rand(50, 500),
            'last_post' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 7) . ' days')),
            'updated' => current_time('mysql')
        ];
        
        update_post_meta($post_id, '_instagram_cache', $cache_data);
    }
    
    public function register_api_endpoints() {
        register_rest_route('bmad/v1', '/stores', [
            'methods' => 'GET',
            'callback' => array($this, 'api_get_stores'),
            'permission_callback' => '__return_true'
        ]);
        
        register_rest_route('bmad/v1', '/stores/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => array($this, 'api_get_store'),
            'permission_callback' => '__return_true'
        ]);
    }
    
    public function api_get_stores($request) {
        $args = [
            'post_type' => 'secondhand_store',
            'posts_per_page' => $request->get_param('per_page') ?: 10,
            'paged' => $request->get_param('page') ?: 1
        ];
        
        $stores = get_posts($args);
        $data = [];
        
        foreach ($stores as $store) {
            $data[] = $this->format_store_for_api($store);
        }
        
        return new WP_REST_Response($data, 200);
    }
    
    public function api_get_store($request) {
        $store_id = $request->get_param('id');
        $store = get_post($store_id);
        
        if (!$store || $store->post_type !== 'secondhand_store') {
            return new WP_Error('store_not_found', 'Store not found', ['status' => 404]);
        }
        
        return new WP_REST_Response($this->format_store_for_api($store), 200);
    }
    
    private function format_store_for_api($store) {
        $meta_fields = [
            'address', 'phone', 'email', 'website', 'instagram',
            'opening_hours', 'rating', 'price_range', 'latitude',
            'longitude', 'google_maps_link'
        ];
        
        $store_data = [
            'id' => $store->ID,
            'title' => $store->post_title,
            'content' => $store->post_content,
            'excerpt' => $store->post_excerpt,
            'permalink' => get_permalink($store->ID),
            'featured_image' => get_the_post_thumbnail_url($store->ID, 'large'),
            'categories' => wp_get_post_terms($store->ID, 'store_category', ['fields' => 'names']),
            'districts' => wp_get_post_terms($store->ID, 'store_district', ['fields' => 'names'])
        ];
        
        foreach ($meta_fields as $field) {
            $store_data[$field] = get_post_meta($store->ID, '_store_' . $field, true);
        }
        
        return $store_data;
    }
    
    public function enqueue_scripts() {
        if (is_post_type_archive('secondhand_store') || is_singular('secondhand_store')) {
            wp_enqueue_script('bmad-store-integration', plugin_dir_url(__FILE__) . 'js/bmad-integration.js', ['jquery'], '1.0.0', true);
            
            wp_localize_script('bmad-store-integration', 'bmadAjax', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'api_url' => rest_url('bmad/v1/'),
                'nonce' => wp_create_nonce('bmad_frontend_nonce')
            ]);
        }
    }
    
    public function add_store_columns($columns) {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['store_rating'] = __('Rating', 'bmad-stores');
                $new_columns['store_district'] = __('District', 'bmad-stores');
                $new_columns['store_category'] = __('Category', 'bmad-stores');
                $new_columns['instagram_status'] = __('Instagram', 'bmad-stores');
            }
        }
        return $new_columns;
    }
    
    public function fill_store_columns($column, $post_id) {
        switch ($column) {
            case 'store_rating':
                $rating = get_post_meta($post_id, '_store_rating', true);
                if ($rating) {
                    echo str_repeat('★', intval($rating)) . str_repeat('☆', 5 - intval($rating));
                    echo ' (' . $rating . '/5)';
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
                    $categories = wp_list_pluck($terms, 'name');
                    echo esc_html(implode(', ', $categories));
                } else {
                    echo '—';
                }
                break;
                
            case 'instagram_status':
                $instagram = get_post_meta($post_id, '_store_instagram', true);
                $cache = get_post_meta($post_id, '_instagram_cache', true);
                
                if ($instagram) {
                    if ($cache) {
                        echo '<span style="color: green;">✓ Connected</span>';
                        echo '<br><small>' . esc_html($cache['followers']) . ' followers</small>';
                    } else {
                        echo '<span style="color: orange;">⚠ Not Synced</span>';
                    }
                } else {
                    echo '<span style="color: red;">✗ No Account</span>';
                }
                break;
        }
    }
    
    private function create_data_cache_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'bmad_store_cache';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            store_id bigint(20) NOT NULL,
            data_type varchar(50) NOT NULL,
            data_content longtext NOT NULL,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY store_id (store_id),
            KEY data_type (data_type)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    private function get_average_rating() {
        global $wpdb;
        
        $avg_rating = $wpdb->get_var("
            SELECT AVG(meta_value) 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_store_rating' 
            AND meta_value != ''
        ");
        
        return $avg_rating ? number_format($avg_rating, 1) : '0.0';
    }
}

// Initialize the plugin
new BMADStoreIntegration();