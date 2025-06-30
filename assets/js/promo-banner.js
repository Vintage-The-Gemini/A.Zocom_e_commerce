/**
 * Simple promo banner JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    var banner = document.querySelector('.promo-banner');
    var closeBtn = document.querySelector('.promo-banner__close');
    var navbar = document.querySelector('.navbar');
    
    // Only proceed if elements exist
    if (!banner || !closeBtn || !navbar) return;
    
    // Check if banner was previously closed
    var isClosed = localStorage.getItem('banner_closed') === 'true';
    
    if (isClosed) {
        // Hide banner
        banner.style.display = 'none';
        
        // Add classes for closed state
        navbar.classList.add('banner-closed');
        document.body.classList.add('banner-closed');
    }
    
    // Handle close button click
    closeBtn.addEventListener('click', function() {
        // Hide banner
        banner.style.display = 'none';
        
        // Add classes for closed state
        navbar.classList.add('banner-closed');
        document.body.classList.add('banner-closed');
        
        // Save closed state
        localStorage.setItem('banner_closed', 'true');
        localStorage.setItem('banner_closed_time', Date.now().toString());
    });
    
    // Check if 24 hours have passed since banner was closed
    var closedTime = localStorage.getItem('banner_closed_time');
    if (closedTime) {
        var now = Date.now();
        var elapsed = now - parseInt(closedTime, 10);
        
        // 24 hours = 86400000 milliseconds
        if (elapsed > 86400000) {
            // Reset banner state
            localStorage.removeItem('banner_closed');
            localStorage.removeItem('banner_closed_time');
        }
    }
});