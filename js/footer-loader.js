/* =====================================
Footer Loader - Dynamically loads Footer across all pages
Uses embedded content to avoid AJAX issues with file:// protocol
======================================*/

(function ($) {
    'use strict';
    
    /**
     * Load footer component dynamically
     */
    function loadFooter() {
        var $placeholder = $('#site-footer-placeholder');
        
        if ($placeholder.length === 0) {
            console.warn('Footer placeholder not found');
            return;
        }
        
        // First, try to use embedded content if available
        if (typeof FOOTER_CONTENT !== 'undefined') {
            $placeholder.html(FOOTER_CONTENT);
            initializeFooter();
            return;
        }
        
        // Fallback: Try AJAX loading (requires web server)
        var footerPath = 'includes/footer.html';
        
        $.ajax({
            url: footerPath,
            type: 'GET',
            dataType: 'html',
            cache: false,
            async: true,
            success: function(data) {
                if (!data || data.trim() === '') {
                    console.error('Footer file loaded but is empty');
                    return;
                }
                
                $placeholder.html(data);
                initializeFooter();
            },
            error: function(xhr, status, error) {
                console.error('Failed to load footer from:', footerPath);
                console.error('Error:', error);
                console.error('Status:', status);
                console.error('Make sure footer-content.js is loaded or use a web server');
            }
        });
    }
    
    /**
     * Initialize footer functionality after footer is loaded
     */
    function initializeFooter() {
        // Trigger custom event for other scripts that depend on footer
        $(document).trigger('footerLoaded');
    }
    
    // Load footer when DOM is ready
    $(document).ready(function() {
        loadFooter();
    });
    
})(jQuery);

