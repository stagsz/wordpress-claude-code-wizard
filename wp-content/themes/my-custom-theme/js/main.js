/**
 * Main JavaScript for Secondhand Stores Directory
 * 
 * Handles filtering, search, AJAX, and general interactions
 */

(function($) {
    'use strict';
    
    // Global variables
    let currentFilters = {};
    let currentPage = 1;
    let isLoading = false;
    
    // Initialize when document is ready
    $(document).ready(function() {
        initializeDirectory();
        initializeFilters();
        initializeMobileMenu();
        initializeViewToggle();
        initializeReviewForm();
        initializeStickyHeader();
        initializeLoadingStates();
    });
    
    /**
     * Initialize directory functionality
     */
    function initializeDirectory() {
        // Add loading states to initial page load
        addLoadingAnimation();
        
        // Initialize any existing filters from URL
        parseURLFilters();
        
        // Trigger initial filter update if needed
        if (Object.keys(currentFilters).length > 0) {
            updateActiveFilters();
        }
    }
    
    /**
     * Initialize filter functionality
     */
    function initializeFilters() {
        const $filterForm = $('#store-filters');
        const $clearFilters = $('.clear-filters');
        
        // Handle form submission
        $filterForm.on('submit', function(e) {
            e.preventDefault();
            handleFilterSubmission();
        });
        
        // Handle individual filter changes
        $filterForm.find('select, input').on('change', function() {
            if ($(this).attr('type') !== 'text') {
                handleFilterSubmission();
            }
        });
        
        // Handle search input with debounce
        let searchTimeout;
        $('#store-search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                handleFilterSubmission();
            }, 500);
        });
        
        // Clear filters
        $clearFilters.on('click', function() {
            clearAllFilters();
        });
        
        // Handle filter tag removal
        $(document).on('click', '.filter-tag .remove', function() {
            const filterType = $(this).closest('.filter-tag').data('filter');
            removeFilter(filterType);
        });
    }
    
    /**
     * Handle filter form submission
     */
    function handleFilterSubmission() {
        if (isLoading) return;
        
        const formData = $('#store-filters').serializeArray();
        const newFilters = {};
        
        // Build filters object
        formData.forEach(function(item) {
            if (item.value && item.value.trim() !== '') {
                newFilters[item.name] = item.value.trim();
            }
        });
        
        // Update current filters
        currentFilters = newFilters;
        currentPage = 1;
        
        // Update UI and fetch results
        updateActiveFilters();
        fetchFilteredStores();
        updateURL();
    }
    
    /**
     * Fetch filtered stores via AJAX
     */
    function fetchFilteredStores() {
        if (isLoading) return;
        
        isLoading = true;
        showLoadingState();
        
        const requestData = {
            action: 'filter_stores',
            nonce: storeDirectory.nonce,
            page: currentPage,
            ...currentFilters
        };
        
        $.ajax({
            url: storeDirectory.ajax_url,
            type: 'POST',
            data: requestData,
            success: function(response) {
                if (response.success) {
                    updateStoreGrid(response.data.html);
                    updateResultsCount(response.data.found_posts);
                    updatePagination(response.data.max_pages);
                    
                    // Scroll to results
                    $('html, body').animate({
                        scrollTop: $('#stores-container').offset().top - 100
                    }, 500);
                } else {
                    showError('Failed to load stores. Please try again.');
                }
            },
            error: function() {
                showError('Network error. Please check your connection and try again.');
            },
            complete: function() {
                isLoading = false;
                hideLoadingState();
            }
        });
    }
    
    /**
     * Update store grid with new content
     */
    function updateStoreGrid(html) {
        const $container = $('#stores-container');
        
        // Fade out old content
        $container.fadeOut(300, function() {
            $container.html(html);
            
            // Trigger animations for new cards
            $container.find('.store-card').each(function(index) {
                $(this).css('opacity', 0).delay(index * 50).animate({
                    opacity: 1
                }, 300);
            });
            
            $container.fadeIn(300);
        });
    }
    
    /**
     * Update results count
     */
    function updateResultsCount(count) {
        const $countElement = $('#results-count .count-number');
        if ($countElement.length) {
            $countElement.text(count.toLocaleString());
        }
    }
    
    /**
     * Update active filters display
     */
    function updateActiveFilters() {
        const $activeFilters = $('#active-filters');
        const $filterTags = $activeFilters.find('.filter-tags');
        
        $filterTags.empty();
        
        if (Object.keys(currentFilters).length === 0) {
            $activeFilters.hide();
            return;
        }
        
        // Create filter tags
        Object.entries(currentFilters).forEach(([key, value]) => {
            const filterLabel = getFilterLabel(key, value);
            if (filterLabel) {
                const $tag = $(`
                    <span class="filter-tag" data-filter="${key}">
                        ${filterLabel}
                        <button type="button" class="remove" aria-label="Remove filter">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                `);
                $filterTags.append($tag);
            }
        });
        
        $activeFilters.show();
    }
    
    /**
     * Get human-readable filter label
     */
    function getFilterLabel(key, value) {
        const labels = {
            search: `Search: "${value}"`,
            category: `Category: ${value}`,
            location: `Location: ${value}`,
            min_rating: `Rating: ${value}+ stars`,
            price_range: `Price: ${value}`,
            orderby: `Sort: ${value}`
        };
        
        return labels[key] || null;
    }
    
    /**
     * Remove specific filter
     */
    function removeFilter(filterType) {
        delete currentFilters[filterType];
        
        // Reset form field
        $(`#store-filters [name="${filterType}"]`).val('');
        
        // Update UI and fetch results
        updateActiveFilters();
        fetchFilteredStores();
        updateURL();
    }
    
    /**
     * Clear all filters
     */
    function clearAllFilters() {
        currentFilters = {};
        currentPage = 1;
        
        // Reset form
        $('#store-filters')[0].reset();
        
        // Update UI and fetch results
        updateActiveFilters();
        fetchFilteredStores();
        updateURL();
    }
    
    /**
     * Initialize mobile menu
     */
    function initializeMobileMenu() {
        const $toggle = $('.mobile-menu-toggle');
        const $mobileNav = $('.mobile-navigation');
        
        $toggle.on('click', function() {
            $toggle.toggleClass('active');
            $mobileNav.toggleClass('active');
            $('body').toggleClass('mobile-menu-open');
        });
        
        // Close mobile menu when clicking on links
        $mobileNav.find('a').on('click', function() {
            $toggle.removeClass('active');
            $mobileNav.removeClass('active');
            $('body').removeClass('mobile-menu-open');
        });
        
        // Close mobile menu on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $mobileNav.hasClass('active')) {
                $toggle.removeClass('active');
                $mobileNav.removeClass('active');
                $('body').removeClass('mobile-menu-open');
            }
        });
    }
    
    /**
     * Initialize view toggle (grid/list)
     */
    function initializeViewToggle() {
        $('.view-btn').on('click', function() {
            const view = $(this).data('view');
            
            // Update active button
            $('.view-btn').removeClass('active');
            $(this).addClass('active');
            
            // Update container class
            const $container = $('#stores-container');
            $container.removeClass('stores-grid stores-list');
            $container.addClass(`stores-${view}`);
            
            // Save preference
            localStorage.setItem('storeDirectoryView', view);
        });
        
        // Load saved preference
        const savedView = localStorage.getItem('storeDirectoryView');
        if (savedView) {
            $(`.view-btn[data-view="${savedView}"]`).click();
        }
    }
    
    /**
     * Initialize review form
     */
    function initializeReviewForm() {
        // Toggle review form
        $('[data-toggle="review-form"]').on('click', function() {
            $('#review-form').slideToggle(300);
        });
        
        // Star rating interaction
        $('.rating-input input[type="radio"]').on('change', function() {
            const rating = $(this).val();
            updateStarDisplay(rating);
        });
        
        // Star rating hover effects
        $('.rating-input label').on('mouseenter', function() {
            const rating = $(this).prev('input').val();
            updateStarDisplay(rating, true);
        });
        
        $('.rating-input').on('mouseleave', function() {
            const checkedRating = $(this).find('input:checked').val() || 0;
            updateStarDisplay(checkedRating);
        });
    }
    
    /**
     * Update star rating display
     */
    function updateStarDisplay(rating, isHover = false) {
        $('.rating-input label').each(function(index) {
            const starValue = 5 - index;
            const $star = $(this).find('i');
            
            if (starValue <= rating) {
                $star.removeClass('far').addClass('fas');
                $(this).addClass(isHover ? 'hover' : 'active');
            } else {
                $star.removeClass('fas').addClass('far');
                $(this).removeClass('hover active');
            }
        });
    }
    
    /**
     * Initialize sticky header
     */
    function initializeStickyHeader() {
        const $header = $('.site-header');
        let lastScrollTop = 0;
        
        $(window).on('scroll', function() {
            const scrollTop = $(this).scrollTop();
            
            if (scrollTop > 100) {
                $header.addClass('scrolled');
            } else {
                $header.removeClass('scrolled');
            }
            
            lastScrollTop = scrollTop;
        });
    }
    
    /**
     * Initialize loading states
     */
    function initializeLoadingStates() {
        // Add loading class to buttons on click
        $(document).on('click', '.btn-loading', function() {
            $(this).addClass('loading').prop('disabled', true);
        });
    }
    
    /**
     * Show loading state
     */
    function showLoadingState() {
        $('#stores-container').addClass('loading');
        $('.filter-button').addClass('loading').prop('disabled', true);
    }
    
    /**
     * Hide loading state
     */
    function hideLoadingState() {
        $('#stores-container').removeClass('loading');
        $('.filter-button').removeClass('loading').prop('disabled', false);
    }
    
    /**
     * Add loading animation to page
     */
    function addLoadingAnimation() {
        $('.store-card').each(function(index) {
            $(this).css({
                opacity: 0,
                transform: 'translateY(30px)'
            }).delay(index * 100).animate({
                opacity: 1,
                transform: 'translateY(0)'
            }, 500);
        });
    }
    
    /**
     * Show error message
     */
    function showError(message) {
        const $error = $(`
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                ${message}
                <button type="button" class="close" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `);
        
        $('main').prepend($error);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $error.fadeOut(300, function() {
                $error.remove();
            });
        }, 5000);
        
        // Manual close
        $error.find('.close').on('click', function() {
            $error.fadeOut(300, function() {
                $error.remove();
            });
        });
    }
    
    /**
     * Parse filters from URL parameters
     */
    function parseURLFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        
        urlParams.forEach((value, key) => {
            if (value.trim()) {
                currentFilters[key] = value.trim();
                $(`#store-filters [name="${key}"]`).val(value.trim());
            }
        });
    }
    
    /**
     * Update URL with current filters
     */
    function updateURL() {
        const url = new URL(window.location);
        
        // Clear existing parameters
        url.search = '';
        
        // Add current filters
        Object.entries(currentFilters).forEach(([key, value]) => {
            url.searchParams.set(key, value);
        });
        
        // Update URL without page reload
        window.history.replaceState({}, '', url);
    }
    
    /**
     * Update pagination
     */
    function updatePagination(maxPages) {
        // This would be implemented based on your pagination structure
        // For now, we'll just log it
        console.log('Max pages:', maxPages);
    }
    
    // Utility functions
    
    /**
     * Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    /**
     * Throttle function
     */
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
    
    // Expose some functions globally for other scripts
    window.StoreDirectory = {
        clearFilters: clearAllFilters,
        applyFilter: function(filterType, value) {
            currentFilters[filterType] = value;
            $(`#store-filters [name="${filterType}"]`).val(value);
            updateActiveFilters();
            fetchFilteredStores();
            updateURL();
        },
        getCurrentFilters: function() {
            return { ...currentFilters };
        }
    };
    
})(jQuery);