/* =====================================
Header Loader - Dynamically loads Header 4 across all pages
Uses embedded content to avoid AJAX issues with file:// protocol
======================================*/

(function ($) {
    'use strict';
    
    /**
     * Load header component dynamically
     */
    function loadHeader() {
        var $placeholder = $('#site-header-placeholder');
        
        if ($placeholder.length === 0) {
            console.warn('Header placeholder not found');
            return;
        }
        
        // First, try to use embedded content if available
        if (typeof HEADER_4_CONTENT !== 'undefined') {
            $placeholder.html(HEADER_4_CONTENT);
            initializeHeader();
            return;
        }
        
        // Fallback: Try AJAX loading (requires web server)
        var headerPath = 'includes/header-4.html';
        
        $.ajax({
            url: headerPath,
            type: 'GET',
            dataType: 'html',
            cache: false,
            async: true,
            success: function(data) {
                if (!data || data.trim() === '') {
                    console.error('Header file loaded but is empty');
                    return;
                }
                
                $placeholder.html(data);
                initializeHeader();
            },
            error: function(xhr, status, error) {
                console.error('Failed to load header from:', headerPath);
                console.error('Error:', error);
                console.error('Status:', status);
                console.error('Make sure header-content.js is loaded or use a web server');
            }
        });
    }
    
    /**
     * Set active state for menu items based on current page
     */
    function setActiveMenuState() {
        // Get current page filename
        var currentPage = window.location.pathname.split('/').pop() || 'index.html';
        
        // Remove query string and hash if present
        currentPage = currentPage.split('?')[0].split('#')[0];
        
        // Map page files to menu items
        var menuMapping = {
            'index.html': 'index.html',
            'about-1.html': 'about-1.html',
            'about-2.html': 'about-1.html',
            'services-1.html': 'services-1.html',
            'services-2.html': 'services-1.html',
            'services-detail.html': 'services-1.html',
            'gallery-grid-1.html': 'gallery-grid-1.html',
            'gallery-grid-2.html': 'gallery-grid-1.html',
            'gallery-grid-3.html': 'gallery-grid-1.html',
            'blog-grid-2.html': 'blog-grid-2.html',
            'blog-grid-3.html': 'blog-grid-2.html',
            'blog-grid-4.html': 'blog-grid-2.html',
            'blog-half-img.html': 'blog-grid-2.html',
            'blog-large-img.html': 'blog-grid-2.html',
            'blog-media-list.html': 'blog-grid-2.html',
            'blog-media-grid.html': 'blog-grid-2.html',
            'blog-single.html': 'blog-grid-2.html',
            'contact-1.html': 'contact-1.html',
            'contact-2.html': 'contact-1.html',
            'contact-3.html': 'contact-1.html'
        };
        
        // Find the menu item to activate
        var targetHref = menuMapping[currentPage] || currentPage;
        
        // Remove active class from all menu items first
        $('.header-nav .nav li').removeClass('active');
        
        // Find and activate the matching menu item
        $('.header-nav .nav li a').each(function() {
            var $link = $(this);
            var href = $link.attr('href');
            
            if (href) {
                // Get just the filename from href
                var linkPage = href.split('/').pop().split('?')[0].split('#')[0];
                
                // Check if this link matches the current page
                if (linkPage === targetHref || linkPage === currentPage) {
                    $link.closest('li').addClass('active');
                    return false; // Break the loop
                }
            }
        });
    }
    
    /**
     * Initialize header functionality after header is loaded
     */
    function initializeHeader() {
        // Set active menu state
        setActiveMenuState();
        
        // Initialize sticky header
        if (typeof sticky_header === 'function') {
            sticky_header();
        }
        
        // Initialize site search functionality
        if (typeof site_search === 'function') {
            site_search();
        }
        
        // Trigger custom event for other scripts that depend on header
        $(document).trigger('headerLoaded');
    }
    
    // Load header when DOM is ready
    $(document).ready(function() {
        loadHeader();
    });
    
})(jQuery);

