/**
 * Stores Directory JavaScript
 * Enhanced functionality for secondhand stores directory
 */

(function($) {
    'use strict';
    
    let storesMap;
    let storeMarkers = [];
    let allStores = [];
    
    $(document).ready(function() {
        initializeDirectory();
        setupEventListeners();
        loadStoresData();
    });
    
    function initializeDirectory() {
        console.log('Initializing Stores Directory...');
        
        // Cache jQuery objects
        window.storesElements = {
            container: $('#stores-container'),
            mapContainer: $('#stores-map'),
            searchInput: $('#store-search'),
            categoryFilter: $('#category-filter'),
            districtFilter: $('#district-filter'),
            ratingFilter: $('#rating-filter'),
            priceFilter: $('#price-filter'),
            gridViewBtn: $('#grid-view'),
            listViewBtn: $('#list-view'),
            mapViewBtn: $('#map-view'),
            searchBtn: $('#search-btn'),
            closeMapBtn: $('#close-map')
        };
    }
    
    function setupEventListeners() {
        const elements = window.storesElements;
        
        // View toggle functionality
        elements.gridViewBtn.on('click', function() {
            setActiveView('grid');
            elements.container.removeClass('stores-list').addClass('stores-grid');
            showContainer();
        });
        
        elements.listViewBtn.on('click', function() {
            setActiveView('list');
            elements.container.removeClass('stores-grid').addClass('stores-list');
            showContainer();
        });
        
        elements.mapViewBtn.on('click', function() {
            setActiveView('map');
            showMap();
        });
        
        elements.closeMapBtn.on('click', function() {
            setActiveView('grid');
            elements.container.removeClass('stores-list').addClass('stores-grid');
            showContainer();
        });
        
        // Search and filter functionality
        elements.searchInput.on('input', debounce(filterStores, 300));
        elements.categoryFilter.on('change', filterStores);
        elements.districtFilter.on('change', filterStores);
        elements.ratingFilter.on('change', filterStores);
        elements.priceFilter.on('change', filterStores);
        elements.searchBtn.on('click', filterStores);
        
        // Map marker hover effects
        $(document).on('mouseenter', '.store-card', function() {
            const storeId = $(this).data('id');
            highlightMarker(storeId);
        });
        
        $(document).on('mouseleave', '.store-card', function() {
            resetMarkerHighlight();
        });
        
        // Smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 800);
            }
        });
        
        // Loading animation for AJAX
        $(document).ajaxStart(function() {
            elements.container.addClass('loading');
        }).ajaxStop(function() {
            elements.container.removeClass('loading');
        });
    }
    
    function setActiveView(view) {
        $('.view-btn').removeClass('active');
        $('#' + view + '-view').addClass('active');
        
        // Store user preference
        localStorage.setItem('storesViewPreference', view);
    }
    
    function showContainer() {
        window.storesElements.container.show();
        window.storesElements.mapContainer.hide();
    }
    
    function showMap() {
        window.storesElements.container.hide();
        window.storesElements.mapContainer.show();
        
        // Trigger map resize if Google Maps is loaded
        if (window.google && window.google.maps && storesMap) {
            setTimeout(function() {
                google.maps.event.trigger(storesMap, 'resize');
                centerMapOnStores();
            }, 100);
        }
    }
    
    function loadStoresData() {
        // Extract store data from DOM for JavaScript manipulation
        allStores = [];
        $('.store-card').each(function() {
            const $card = $(this);
            const store = {
                id: $card.data('id'),
                title: $card.find('.store-title a').text(),
                category: $card.data('category') ? $card.data('category').split(',') : [],
                district: $card.data('district') ? $card.data('district').split(',') : [],
                rating: parseFloat($card.data('rating')) || 0,
                price: $card.data('price') || '',
                lat: parseFloat($card.data('lat')) || null,
                lng: parseFloat($card.data('lng')) || null,
                element: $card
            };
            allStores.push(store);
        });
        
        console.log('Loaded ' + allStores.length + ' stores');
    }
    
    function filterStores() {
        const filters = {
            search: window.storesElements.searchInput.val().toLowerCase().trim(),
            category: window.storesElements.categoryFilter.val(),
            district: window.storesElements.districtFilter.val(),
            rating: parseFloat(window.storesElements.ratingFilter.val()) || 0,
            price: window.storesElements.priceFilter.val()
        };
        
        let visibleCount = 0;
        
        allStores.forEach(function(store) {
            let visible = true;
            
            // Search filter
            if (filters.search && !store.title.toLowerCase().includes(filters.search)) {
                const excerpt = store.element.find('.store-excerpt').text().toLowerCase();
                const address = store.element.find('.store-detail span:nth-child(2)').text().toLowerCase();
                visible = visible && (excerpt.includes(filters.search) || address.includes(filters.search));
            }
            
            // Category filter
            if (filters.category && !store.category.includes(filters.category)) {
                visible = false;
            }
            
            // District filter
            if (filters.district && !store.district.includes(filters.district)) {
                visible = false;
            }
            
            // Rating filter
            if (filters.rating && store.rating < filters.rating) {
                visible = false;
            }
            
            // Price filter
            if (filters.price && store.price !== filters.price) {
                visible = false;
            }
            
            // Show/hide store card
            if (visible) {
                store.element.fadeIn(300);
                visibleCount++;
            } else {
                store.element.fadeOut(300);
            }
        });
        
        // Update map markers
        updateMapMarkers(filters);
        
        // Show/hide no results message
        updateNoResultsMessage(visibleCount);
        
        // Update URL with filters (for bookmarking)
        updateURL(filters);
    }
    
    function updateMapMarkers(filters) {
        if (!storeMarkers.length) return;
        
        storeMarkers.forEach(function(markerData) {
            const store = allStores.find(s => s.id == markerData.storeId);
            if (store && store.element.is(':visible')) {
                markerData.marker.setVisible(true);
            } else {
                markerData.marker.setVisible(false);
            }
        });
    }
    
    function updateNoResultsMessage(visibleCount) {
        const existingMessage = $('.no-stores-found');
        
        if (visibleCount === 0) {
            if (!existingMessage.length) {
                const message = $('<div class="no-stores-found"><h3>No stores found</h3><p>Try adjusting your search criteria or browse all stores.</p></div>');
                window.storesElements.container.append(message);
            }
        } else {
            existingMessage.remove();
        }
    }
    
    function updateURL(filters) {
        const url = new URL(window.location);
        
        // Clear existing filter parameters
        url.searchParams.delete('search');
        url.searchParams.delete('category');
        url.searchParams.delete('district');
        url.searchParams.delete('rating');
        url.searchParams.delete('price');
        
        // Add active filters
        if (filters.search) url.searchParams.set('search', filters.search);
        if (filters.category) url.searchParams.set('category', filters.category);
        if (filters.district) url.searchParams.set('district', filters.district);
        if (filters.rating) url.searchParams.set('rating', filters.rating);
        if (filters.price) url.searchParams.set('price', filters.price);
        
        // Update URL without reloading page
        window.history.replaceState({}, '', url.toString());
    }
    
    function loadFiltersFromURL() {
        const url = new URL(window.location);
        
        if (url.searchParams.has('search')) {
            window.storesElements.searchInput.val(url.searchParams.get('search'));
        }
        if (url.searchParams.has('category')) {
            window.storesElements.categoryFilter.val(url.searchParams.get('category'));
        }
        if (url.searchParams.has('district')) {
            window.storesElements.districtFilter.val(url.searchParams.get('district'));
        }
        if (url.searchParams.has('rating')) {
            window.storesElements.ratingFilter.val(url.searchParams.get('rating'));
        }
        if (url.searchParams.has('price')) {
            window.storesElements.priceFilter.val(url.searchParams.get('price'));
        }
        
        // Apply filters if any were found in URL
        if (url.search) {
            filterStores();
        }
    }
    
    function highlightMarker(storeId) {
        const markerData = storeMarkers.find(m => m.storeId == storeId);
        if (markerData) {
            markerData.marker.setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(() => {
                markerData.marker.setAnimation(null);
            }, 1500);
        }
    }
    
    function resetMarkerHighlight() {
        storeMarkers.forEach(function(markerData) {
            markerData.marker.setAnimation(null);
        });
    }
    
    function centerMapOnStores() {
        if (!storesMap || !storeMarkers.length) return;
        
        const bounds = new google.maps.LatLngBounds();
        let hasVisibleMarkers = false;
        
        storeMarkers.forEach(function(markerData) {
            if (markerData.marker.getVisible()) {
                bounds.extend(markerData.marker.getPosition());
                hasVisibleMarkers = true;
            }
        });
        
        if (hasVisibleMarkers) {
            storesMap.fitBounds(bounds);
            
            // Ensure minimum zoom level
            google.maps.event.addListenerOnce(storesMap, 'bounds_changed', function() {
                if (storesMap.getZoom() > 15) {
                    storesMap.setZoom(15);
                }
            });
        }
    }
    
    // Utility function for debouncing
    function debounce(func, wait, immediate) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
    
    // Initialize when page loads
    $(window).on('load', function() {
        loadFiltersFromURL();
        
        // Restore view preference
        const savedView = localStorage.getItem('storesViewPreference');
        if (savedView && ['grid', 'list', 'map'].includes(savedView)) {
            $('#' + savedView + '-view').click();
        }
    });
    
    // Google Maps integration
    window.initStoresMap = function() {
        if (!document.getElementById('map-canvas')) return;
        
        storesMap = new google.maps.Map(document.getElementById('map-canvas'), {
            zoom: 12,
            center: {lat: 57.7089, lng: 11.9746}, // Gothenburg coordinates
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{visibility: 'off'}]
                },
                {
                    featureType: 'water',
                    elementType: 'geometry',
                    stylers: [{color: '#C6E2FF'}]
                },
                {
                    featureType: 'landscape',
                    elementType: 'geometry',
                    stylers: [{color: '#f5f5f2'}, {lightness: 20}]
                }
            ],
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true
        });
        
        addStoreMarkers();
        
        // Handle map resize
        google.maps.event.addDomListener(window, 'resize', function() {
            if (storesMap) {
                google.maps.event.trigger(storesMap, 'resize');
            }
        });
    };
    
    function addStoreMarkers() {
        allStores.forEach(function(store) {
            if (store.lat && store.lng) {
                const marker = new google.maps.Marker({
                    position: {lat: store.lat, lng: store.lng},
                    map: storesMap,
                    title: store.title,
                    icon: {
                        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#e74c3c" width="32" height="32">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        `),
                        scaledSize: new google.maps.Size(32, 32),
                        anchor: new google.maps.Point(16, 32)
                    }
                });
                
                const infoWindow = new google.maps.InfoWindow({
                    content: createMarkerContent(store)
                });
                
                marker.addListener('click', function() {
                    // Close other info windows
                    storeMarkers.forEach(m => m.infoWindow && m.infoWindow.close());
                    infoWindow.open(storesMap, marker);
                });
                
                // Store marker data
                storeMarkers.push({
                    marker: marker,
                    infoWindow: infoWindow,
                    storeId: store.id,
                    store: store
                });
            }
        });
    }
    
    function createMarkerContent(store) {
        const address = store.element.find('.store-detail span:nth-child(2)').text() || '';
        const rating = store.rating;
        const permalink = store.element.find('.store-title a').attr('href');
        const thumbnail = store.element.find('.store-image img').attr('src') || '';
        
        let ratingStars = '';
        if (rating) {
            for (let i = 1; i <= 5; i++) {
                ratingStars += `<span class="star ${i <= rating ? 'filled' : ''}">★</span>`;
            }
        }
        
        return `
            <div class="marker-info" style="max-width: 300px; padding: 16px;">
                ${thumbnail ? `<img src="${thumbnail}" alt="${store.title}" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">` : ''}
                <h4 style="margin: 0 0 8px 0; font-size: 16px;">
                    <a href="${permalink}" style="color: #333; text-decoration: none;">${store.title}</a>
                </h4>
                ${address ? `<p style="margin: 0 0 8px 0; font-size: 14px; color: #666;"><strong>Address:</strong> ${address}</p>` : ''}
                ${rating ? `<div class="rating" style="margin-bottom: 12px;">${ratingStars}</div>` : ''}
                <a href="${permalink}" class="btn btn-sm btn-primary" style="display: inline-block; padding: 8px 16px; background: #0073aa; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">View Details</a>
            </div>
        `;
    }
    
    // Expose functions for global access
    window.storesDirectory = {
        filterStores: filterStores,
        setActiveView: setActiveView,
        centerMapOnStores: centerMapOnStores
    };
    
})(jQuery);