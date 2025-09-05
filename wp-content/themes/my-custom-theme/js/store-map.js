/**
 * Google Maps Integration for Secondhand Stores Directory
 * 
 * Handles map display, markers, info windows, and store interactions
 */

(function() {
    'use strict';
    
    let map;
    let markers = [];
    let infoWindow;
    let markerCluster;
    
    // Default map options
    const defaultMapOptions = {
        zoom: 12,
        center: { lat: 57.7089, lng: 11.9746 }, // Gothenburg
        styles: [
            {
                "featureType": "all",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "weight": "2.00"
                    }
                ]
            },
            {
                "featureType": "all",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#9c9c9c"
                    }
                ]
            },
            {
                "featureType": "all",
                "elementType": "labels.text",
                "stylers": [
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "landscape",
                "elementType": "all",
                "stylers": [
                    {
                        "color": "#f2f2f2"
                    }
                ]
            },
            {
                "featureType": "landscape",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#ffffff"
                    }
                ]
            },
            {
                "featureType": "landscape.man_made",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#ffffff"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": -100
                    },
                    {
                        "lightness": 45
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#eeeeee"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#7b7b7b"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#ffffff"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "all",
                "stylers": [
                    {
                        "color": "#46bcec"
                    },
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#c8d7d4"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#070707"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#ffffff"
                    }
                ]
            }
        ],
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
        zoomControl: true,
        scrollwheel: true,
        gestureHandling: 'cooperative'
    };
    
    /**
     * Initialize the map when Google Maps API is loaded
     */
    function initMap() {
        const mapContainer = document.getElementById('store-map');
        const singleStoreMapContainer = document.getElementById('single-store-map');
        
        if (mapContainer) {
            initDirectoryMap(mapContainer);
        }
        
        if (singleStoreMapContainer) {
            initSingleStoreMap(singleStoreMapContainer);
        }
    }
    
    /**
     * Initialize directory map with all stores
     */
    function initDirectoryMap(container) {
        try {
            // Hide loading indicator
            const loadingElement = container.parentElement.querySelector('.map-loading');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
            
            // Show map container
            container.style.display = 'block';
            
            // Create map
            map = new google.maps.Map(container, {
                ...defaultMapOptions,
                center: window.mapCenter || defaultMapOptions.center
            });
            
            // Initialize info window
            infoWindow = new google.maps.InfoWindow();
            
            // Load store markers
            if (window.storeMapData && window.storeMapData.length > 0) {
                loadStoreMarkers(window.storeMapData);
            } else {
                console.warn('No store data available for map');
            }
            
            // Add map controls
            addMapControls();
            
        } catch (error) {
            console.error('Error initializing directory map:', error);
            showMapError(container);
        }
    }
    
    /**
     * Initialize single store map
     */
    function initSingleStoreMap(container) {
        try {
            const storeData = window.singleStoreData;
            
            if (!storeData || !storeData.lat || !storeData.lng) {
                container.innerHTML = '<p>Map location not available for this store.</p>';
                return;
            }
            
            const storeLocation = { lat: storeData.lat, lng: storeData.lng };
            
            // Create map centered on store
            const singleMap = new google.maps.Map(container, {
                ...defaultMapOptions,
                zoom: 15,
                center: storeLocation
            });
            
            // Create marker for store
            const marker = new google.maps.Marker({
                position: storeLocation,
                map: singleMap,
                title: storeData.title,
                icon: {
                    url: createMarkerIcon('#2c5aa0', 'fas fa-store'),
                    scaledSize: new google.maps.Size(40, 40),
                    anchor: new google.maps.Point(20, 40)
                },
                animation: google.maps.Animation.DROP
            });
            
            // Create info window
            const infoWindow = new google.maps.InfoWindow({
                content: createSingleStoreInfoWindow(storeData)
            });
            
            // Show info window on marker click
            marker.addListener('click', () => {
                infoWindow.open(singleMap, marker);
            });
            
            // Auto-open info window
            setTimeout(() => {
                infoWindow.open(singleMap, marker);
            }, 1000);
            
        } catch (error) {
            console.error('Error initializing single store map:', error);
            container.innerHTML = '<p>Error loading map. Please try again later.</p>';
        }
    }
    
    /**
     * Load store markers on the map
     */
    function loadStoreMarkers(storeData) {
        // Clear existing markers
        clearMarkers();
        
        const bounds = new google.maps.LatLngBounds();
        
        storeData.forEach((store, index) => {
            if (!store.lat || !store.lng) return;
            
            const position = { lat: store.lat, lng: store.lng };
            
            // Determine marker color based on rating
            let markerColor = '#2c5aa0'; // Default blue
            if (store.rating >= 4.5) {
                markerColor = '#27ae60'; // Green for high rated
            } else if (store.rating >= 4.0) {
                markerColor = '#f39c12'; // Orange for good rated
            }
            
            // Create marker
            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: store.title,
                icon: {
                    url: createMarkerIcon(markerColor, 'fas fa-store'),
                    scaledSize: new google.maps.Size(35, 35),
                    anchor: new google.maps.Point(17, 35)
                },
                animation: google.maps.Animation.DROP,
                zIndex: index
            });
            
            // Add click listener
            marker.addListener('click', () => {
                showStoreInfoWindow(marker, store);
                
                // Highlight corresponding store card
                highlightStoreCard(store.id);
            });
            
            // Store reference
            marker.storeId = store.id;
            markers.push(marker);
            
            // Extend bounds
            bounds.extend(position);
        });
        
        // Fit map to bounds if we have markers
        if (markers.length > 0) {
            map.fitBounds(bounds);
            
            // Set max zoom level
            google.maps.event.addListenerOnce(map, 'bounds_changed', () => {
                if (map.getZoom() > 15) {
                    map.setZoom(15);
                }
            });
        }
        
        // Initialize marker clustering for better performance
        if (window.MarkerClusterer && markers.length > 10) {
            markerCluster = new MarkerClusterer(map, markers, {
                imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
                maxZoom: 14
            });
        }
    }
    
    /**
     * Create custom marker icon
     */
    function createMarkerIcon(color, iconClass) {
        const svg = `
            <svg width="35" height="35" viewBox="0 0 35 35" xmlns="http://www.w3.org/2000/svg">
                <circle cx="17.5" cy="17.5" r="15" fill="${color}" stroke="#ffffff" stroke-width="3"/>
                <text x="17.5" y="21" text-anchor="middle" fill="#ffffff" font-family="Font Awesome 5 Free" font-size="12" font-weight="900">&#xf54f;</text>
            </svg>
        `;
        
        return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg);
    }
    
    /**
     * Show info window for store
     */
    function showStoreInfoWindow(marker, store) {
        const content = createStoreInfoWindow(store);
        infoWindow.setContent(content);
        infoWindow.open(map, marker);
    }
    
    /**
     * Create info window content for store
     */
    function createStoreInfoWindow(store) {
        const ratingStars = createStarRating(store.rating);
        const thumbnail = store.thumbnail ? `<img src="${store.thumbnail}" alt="${store.title}" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;">` : '';
        
        return `
            <div class="map-info-window">
                ${thumbnail}
                <h4 style="margin: 0 0 8px 0; color: #2c5aa0;">${store.title}</h4>
                <div class="store-rating" style="margin-bottom: 8px;">
                    ${ratingStars}
                </div>
                <p style="margin: 0 0 8px 0; color: #666; font-size: 14px;">
                    <i class="fas fa-map-marker-alt" style="color: #2c5aa0; margin-right: 5px;"></i>
                    ${store.address}
                </p>
                <div style="display: flex; gap: 8px; margin-top: 12px;">
                    <a href="${store.url}" class="btn btn-primary" style="background: #2c5aa0; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; flex: 1; text-align: center;">
                        View Details
                    </a>
                    <a href="https://maps.google.com/?q=${encodeURIComponent(store.address)}" target="_blank" class="btn btn-secondary" style="background: #f39c12; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                        <i class="fas fa-directions"></i>
                    </a>
                </div>
            </div>
        `;
    }
    
    /**
     * Create info window content for single store
     */
    function createSingleStoreInfoWindow(store) {
        const ratingStars = store.rating ? createStarRating(store.rating) : '';
        
        return `
            <div class="map-info-window">
                <h4 style="margin: 0 0 8px 0; color: #2c5aa0;">${store.title}</h4>
                ${ratingStars ? `<div class="store-rating" style="margin-bottom: 8px;">${ratingStars}</div>` : ''}
                <p style="margin: 0 0 8px 0; color: #666; font-size: 14px;">
                    <i class="fas fa-map-marker-alt" style="color: #2c5aa0; margin-right: 5px;"></i>
                    ${store.address}
                </p>
                <div style="margin-top: 12px;">
                    <a href="https://maps.google.com/?q=${encodeURIComponent(store.address)}" target="_blank" class="btn btn-primary" style="background: #2c5aa0; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">
                        <i class="fas fa-directions" style="margin-right: 5px;"></i>
                        Get Directions
                    </a>
                </div>
            </div>
        `;
    }
    
    /**
     * Create star rating HTML
     */
    function createStarRating(rating) {
        if (!rating) return '';
        
        const stars = [];
        const fullStars = Math.floor(rating);
        const hasHalfStar = (rating % 1) >= 0.5;
        
        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                stars.push('<i class="fas fa-star" style="color: #f39c12;"></i>');
            } else if (i === fullStars && hasHalfStar) {
                stars.push('<i class="fas fa-star-half-alt" style="color: #f39c12;"></i>');
            } else {
                stars.push('<i class="far fa-star" style="color: #f39c12;"></i>');
            }
        }
        
        return `<div style="display: inline-flex; align-items: center; gap: 2px;">${stars.join('')}<span style="margin-left: 8px; color: #666; font-size: 13px;">${rating.toFixed(1)}</span></div>`;
    }
    
    /**
     * Highlight store card when marker is clicked
     */
    function highlightStoreCard(storeId) {
        // Remove existing highlights
        document.querySelectorAll('.store-card.highlighted').forEach(card => {
            card.classList.remove('highlighted');
        });
        
        // Highlight matching card
        const storeCard = document.querySelector(`[data-store-id="${storeId}"]`);
        if (storeCard) {
            storeCard.classList.add('highlighted');
            
            // Scroll to card if not visible
            const cardRect = storeCard.getBoundingClientRect();
            if (cardRect.top < 0 || cardRect.bottom > window.innerHeight) {
                storeCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            // Remove highlight after 3 seconds
            setTimeout(() => {
                storeCard.classList.remove('highlighted');
            }, 3000);
        }
    }
    
    /**
     * Add custom map controls
     */
    function addMapControls() {
        // Create reset view button
        const resetButton = document.createElement('button');
        resetButton.textContent = 'Reset View';
        resetButton.classList.add('map-control-button');
        resetButton.style.cssText = `
            background: white;
            border: 2px solid #2c5aa0;
            color: #2c5aa0;
            padding: 8px 16px;
            margin: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        `;
        
        resetButton.addEventListener('click', () => {
            if (markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => {
                    bounds.extend(marker.getPosition());
                });
                map.fitBounds(bounds);
            } else {
                map.setCenter(defaultMapOptions.center);
                map.setZoom(defaultMapOptions.zoom);
            }
        });
        
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(resetButton);
    }
    
    /**
     * Clear all markers from map
     */
    function clearMarkers() {
        markers.forEach(marker => {
            marker.setMap(null);
        });
        markers = [];
        
        if (markerCluster) {
            markerCluster.clearMarkers();
        }
    }
    
    /**
     * Show map error
     */
    function showMapError(container) {
        container.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; min-height: 300px; color: #666;">
                <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 16px; color: #e74c3c;"></i>
                <h3 style="margin: 0 0 8px 0;">Map Error</h3>
                <p style="margin: 0; text-align: center;">Unable to load map. Please check your internet connection and try again.</p>
                <button onclick="location.reload()" style="margin-top: 16px; padding: 8px 16px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Retry
                </button>
            </div>
        `;
    }
    
    /**
     * Filter markers based on current filters
     */
    function filterMarkers(filters) {
        // This would be called when filters change
        // For now, we'll just show/hide all markers
        markers.forEach(marker => {
            marker.setVisible(true);
        });
    }
    
    /**
     * Update map when store data changes
     */
    function updateMapData(newStoreData) {
        if (map && newStoreData) {
            loadStoreMarkers(newStoreData);
        }
    }
    
    // Export functions for external use
    window.StoreMap = {
        init: initMap,
        updateData: updateMapData,
        filterMarkers: filterMarkers,
        clearMarkers: clearMarkers
    };
    
    // Initialize when Google Maps is loaded
    if (typeof google !== 'undefined' && google.maps) {
        initMap();
    } else {
        // Wait for Google Maps to load
        window.initMap = initMap;
    }
    
})();