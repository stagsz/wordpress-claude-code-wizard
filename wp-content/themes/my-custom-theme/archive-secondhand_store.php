<?php
/**
 * Archive Template for Secondhand Stores
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <header class="page-header stores-header">
            <div class="container">
                <h1 class="page-title">Secondhand Stores & Boutiques in Gothenburg</h1>
                <p class="page-description">Discover the best secondhand treasures, vintage finds, and unique boutiques across Gothenburg's districts.</p>
            </div>
        </header>

        <div class="stores-directory-container">
            <div class="container">
                
                <!-- Search and Filter Controls -->
                <div class="directory-controls">
                    <div class="search-filter-row">
                        <div class="search-box">
                            <input type="text" id="store-search" placeholder="Search stores..." />
                            <button type="button" id="search-btn">
                                <span class="dashicons dashicons-search"></span>
                            </button>
                        </div>
                        
                        <div class="filter-controls">
                            <select id="category-filter" class="filter-select">
                                <option value="">All Categories</option>
                                <?php 
                                $categories = get_terms(array(
                                    'taxonomy' => 'store_category',
                                    'hide_empty' => false,
                                ));
                                foreach ($categories as $category): ?>
                                    <option value="<?php echo esc_attr($category->slug); ?>"><?php echo esc_html($category->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <select id="district-filter" class="filter-select">
                                <option value="">All Districts</option>
                                <?php 
                                $districts = get_terms(array(
                                    'taxonomy' => 'store_district',
                                    'hide_empty' => false,
                                ));
                                foreach ($districts as $district): ?>
                                    <option value="<?php echo esc_attr($district->slug); ?>"><?php echo esc_html($district->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <select id="rating-filter" class="filter-select">
                                <option value="">All Ratings</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4+ Stars</option>
                                <option value="3">3+ Stars</option>
                                <option value="2">2+ Stars</option>
                                <option value="1">1+ Stars</option>
                            </select>
                            
                            <select id="price-filter" class="filter-select">
                                <option value="">All Prices</option>
                                <option value="$">$ - Budget Friendly</option>
                                <option value="$$">$$ - Moderate</option>
                                <option value="$$$">$$$ - High End</option>
                                <option value="$$$$">$$$$ - Luxury</option>
                            </select>
                        </div>
                        
                        <div class="view-toggle">
                            <button id="grid-view" class="view-btn active" title="Grid View">
                                <span class="dashicons dashicons-grid-view"></span>
                            </button>
                            <button id="list-view" class="view-btn" title="List View">
                                <span class="dashicons dashicons-list-view"></span>
                            </button>
                            <button id="map-view" class="view-btn" title="Map View">
                                <span class="dashicons dashicons-location"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Directory Content -->
                <div class="directory-content">
                    
                    <!-- Map Container -->
                    <div id="stores-map" class="stores-map" style="display: none;">
                        <div id="map-canvas"></div>
                        <div class="map-controls">
                            <button id="close-map" class="close-map-btn">
                                <span class="dashicons dashicons-no"></span> Close Map
                            </button>
                        </div>
                    </div>

                    <!-- Stores Grid/List -->
                    <div id="stores-container" class="stores-grid">
                        
                        <?php if (have_posts()) : ?>
                            
                            <?php while (have_posts()) : the_post(); 
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
                                            <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
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
                            
                            <?php endwhile; ?>
                            
                            <!-- Pagination -->
                            <div class="stores-pagination">
                                <?php
                                    echo paginate_links(array(
                                        'prev_text' => __('← Previous'),
                                        'next_text' => __('Next →'),
                                    ));
                                ?>
                            </div>
                            
                        <?php else : ?>
                            
                            <div class="no-stores-found">
                                <h3>No stores found</h3>
                                <p>Try adjusting your search criteria or browse all stores.</p>
                            </div>
                            
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- Include Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initStoresMap"></script>

<script>
let storesMap;
let storeMarkers = [];

// Initialize Google Maps
function initStoresMap() {
    storesMap = new google.maps.Map(document.getElementById('map-canvas'), {
        zoom: 12,
        center: {lat: 57.7089, lng: 11.9746}, // Gothenburg coordinates
        styles: [
            {
                featureType: 'poi',
                elementType: 'labels',
                stylers: [{visibility: 'off'}]
            }
        ]
    });
    
    addStoreMarkers();
}

// Add markers for all stores
function addStoreMarkers() {
    const storeCards = document.querySelectorAll('.store-card[data-lat][data-lng]');
    
    storeCards.forEach(card => {
        const lat = parseFloat(card.dataset.lat);
        const lng = parseFloat(card.dataset.lng);
        
        if (lat && lng) {
            const marker = new google.maps.Marker({
                position: {lat: lat, lng: lng},
                map: storesMap,
                title: card.querySelector('.store-title a').textContent,
                icon: {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#e74c3c" width="32" height="32"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>'),
                    scaledSize: new google.maps.Size(32, 32)
                }
            });
            
            const infoWindow = new google.maps.InfoWindow({
                content: createMarkerContent(card)
            });
            
            marker.addListener('click', () => {
                infoWindow.open(storesMap, marker);
            });
            
            storeMarkers.push({marker: marker, card: card});
        }
    });
}

function createMarkerContent(card) {
    const title = card.querySelector('.store-title a').textContent;
    const address = card.querySelector('.store-detail span:nth-child(2)')?.textContent || '';
    const rating = card.dataset.rating;
    const permalink = card.querySelector('.store-title a').href;
    
    let ratingStars = '';
    if (rating) {
        for (let i = 1; i <= 5; i++) {
            ratingStars += `<span class="star ${i <= rating ? 'filled' : ''}">★</span>`;
        }
    }
    
    return `
        <div class="marker-info">
            <h4><a href="${permalink}">${title}</a></h4>
            ${address ? `<p><strong>Address:</strong> ${address}</p>` : ''}
            ${rating ? `<div class="rating">${ratingStars}</div>` : ''}
            <a href="${permalink}" class="btn btn-sm btn-primary">View Details</a>
        </div>
    `;
}

// Directory JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    
    // View toggle functionality
    const gridViewBtn = document.getElementById('grid-view');
    const listViewBtn = document.getElementById('list-view');
    const mapViewBtn = document.getElementById('map-view');
    const storesContainer = document.getElementById('stores-container');
    const storesMap = document.getElementById('stores-map');
    
    gridViewBtn.addEventListener('click', () => {
        setActiveView('grid');
        storesContainer.className = 'stores-grid';
        storesContainer.style.display = 'grid';
        storesMap.style.display = 'none';
    });
    
    listViewBtn.addEventListener('click', () => {
        setActiveView('list');
        storesContainer.className = 'stores-list';
        storesContainer.style.display = 'block';
        storesMap.style.display = 'none';
    });
    
    mapViewBtn.addEventListener('click', () => {
        setActiveView('map');
        storesContainer.style.display = 'none';
        storesMap.style.display = 'block';
        if (window.google && window.google.maps && storesMap) {
            google.maps.event.trigger(storesMap, 'resize');
        }
    });
    
    document.getElementById('close-map').addEventListener('click', () => {
        setActiveView('grid');
        storesContainer.className = 'stores-grid';
        storesContainer.style.display = 'grid';
        storesMap.style.display = 'none';
    });
    
    function setActiveView(view) {
        document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(view + '-view').classList.add('active');
    }
    
    // Search and filter functionality
    const searchInput = document.getElementById('store-search');
    const categoryFilter = document.getElementById('category-filter');
    const districtFilter = document.getElementById('district-filter');
    const ratingFilter = document.getElementById('rating-filter');
    const priceFilter = document.getElementById('price-filter');
    
    function filterStores() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedDistrict = districtFilter.value;
        const selectedRating = parseFloat(ratingFilter.value);
        const selectedPrice = priceFilter.value;
        
        const storeCards = document.querySelectorAll('.store-card');
        
        storeCards.forEach(card => {
            let visible = true;
            
            // Search term filter
            if (searchTerm) {
                const title = card.querySelector('.store-title').textContent.toLowerCase();
                const excerpt = card.querySelector('.store-excerpt').textContent.toLowerCase();
                visible = visible && (title.includes(searchTerm) || excerpt.includes(searchTerm));
            }
            
            // Category filter
            if (selectedCategory) {
                const categories = card.dataset.category.split(',');
                visible = visible && categories.includes(selectedCategory);
            }
            
            // District filter
            if (selectedDistrict) {
                const districts = card.dataset.district.split(',');
                visible = visible && districts.includes(selectedDistrict);
            }
            
            // Rating filter
            if (selectedRating) {
                const rating = parseFloat(card.dataset.rating);
                visible = visible && rating >= selectedRating;
            }
            
            // Price filter
            if (selectedPrice) {
                visible = visible && card.dataset.price === selectedPrice;
            }
            
            card.style.display = visible ? 'block' : 'none';
        });
    }
    
    // Attach event listeners
    searchInput.addEventListener('input', filterStores);
    categoryFilter.addEventListener('change', filterStores);
    districtFilter.addEventListener('change', filterStores);
    ratingFilter.addEventListener('change', filterStores);
    priceFilter.addEventListener('change', filterStores);
    
    document.getElementById('search-btn').addEventListener('click', filterStores);
    
});
</script>

<?php get_footer(); ?>