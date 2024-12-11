// Main initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeMobileMenu();
    initializeCategorySwitch();
    initializeSearch();
    initializeModal();
});

// Mobile menu functionality
function initializeMobileMenu() {
    const categoryToggle = document.getElementById('categoryMenuToggle');
    const categoryDrawer = document.querySelector('.mobile-category-drawer');
    const drawerOverlay = document.querySelector('.drawer-overlay');
    const drawerClose = document.querySelector('.drawer-close');
    const originalCategories = document.querySelector('.shop__sidebar .shop__category-list');
    const drawerCategories = document.querySelector('.mobile-category-drawer .shop__category-list');

    if (originalCategories && drawerCategories) {
        drawerCategories.innerHTML = originalCategories.innerHTML;
    }

    function openDrawer() {
        categoryDrawer.classList.add('active');
        drawerOverlay.classList.add('active');
        categoryToggle.classList.add('active');
        document.body.classList.add('no-scroll');
    }

    function closeDrawer() {
        categoryDrawer.classList.remove('active');
        drawerOverlay.classList.remove('active');
        categoryToggle.classList.remove('active');
        document.body.classList.remove('no-scroll');
    }

    if (categoryToggle) {
        categoryToggle.addEventListener('click', function() {
            if (categoryDrawer.classList.contains('active')) {
                closeDrawer();
            } else {
                openDrawer();
            }
        });
    }

    if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
    if (drawerOverlay) drawerOverlay.addEventListener('click', closeDrawer);

    if (drawerCategories) {
        drawerCategories.addEventListener('click', handleCategoryClick);
    }
}

// Category handling
function initializeCategorySwitch() {
    document.querySelectorAll('.shop__category-link').forEach(link => {
        link.addEventListener('click', handleCategoryClick);
    });
}

function handleCategoryClick(e) {
    const link = e.target.closest('.shop__category-link');
    if (!link) return;

    e.preventDefault();
    const targetId = link.getAttribute('href').substring(1);

    document.querySelectorAll('.shop__category-link').forEach(l => 
        l.classList.remove('active'));
    link.classList.add('active');

    document.querySelectorAll('.shop__grid').forEach(grid => {
        grid.style.display = 'none';
        grid.classList.remove('active');
        if (grid.id === targetId) {
            grid.style.display = 'grid';
            grid.classList.add('active');
        }
    });

    const drawer = document.querySelector('.mobile-category-drawer');
    if (drawer.classList.contains('active')) {
        drawer.classList.remove('active');
        document.querySelector('.drawer-overlay').classList.remove('active');
        document.querySelector('.category-menu-toggle').classList.remove('active');
        document.body.classList.remove('no-scroll');
    }

    setTimeout(() => {
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            const offset = document.querySelector('.shop__container').offsetTop - 20;
            window.scrollTo({
                top: offset,
                behavior: 'smooth'
            });
        }
    }, 300);
}

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', 
            debounce(function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                performExtensiveSearch(searchTerm);
            }, 300)
        );
    }
}

function performExtensiveSearch(searchTerm) {
    // If search is empty, show all products
    if (!searchTerm) {
        document.querySelectorAll('.shop__card').forEach(card => {
            card.style.display = 'block';
        });
        updateNoProductsMessages(true);
        return;
    }

    let totalVisibleCards = 0;
    const searchTerms = searchTerm.split(' ').filter(term => term.length > 0);

    // Search through all grids, not just the active one
    document.querySelectorAll('.shop__grid').forEach(grid => {
        let hasVisibleCards = false;
        
        grid.querySelectorAll('.shop__card').forEach(card => {
            // Get all searchable content from the card
            const title = card.querySelector('.shop__card-title')?.textContent.toLowerCase() || '';
            const brand = card.querySelector('.shop__card-brand')?.textContent.toLowerCase() || '';
            const description = card.querySelector('.shop__card-description')?.textContent.toLowerCase() || '';
            const category = card.closest('.shop__grid')?.dataset.category.toLowerCase() || '';
            
            // Combine all searchable content
            const searchableContent = `${title} ${brand} ${description} ${category}`;

            // Check if all search terms are found in the content
            const isVisible = searchTerms.every(term => {
                // Create variations of the search term for flexible matching
                const variations = [
                    term,
                    term.replace(/-/g, ' '), // Replace hyphens with spaces
                    term.replace(/\s+/g, ''), // Remove spaces
                    term.replace(/[^a-z0-9]/g, '') // Remove special characters
                ];
                
                // Check if any variation matches
                return variations.some(variant => 
                    searchableContent.includes(variant)
                );
            });

            // Update card visibility
            card.style.display = isVisible ? 'block' : 'none';
            
            if (isVisible) {
                hasVisibleCards = true;
                totalVisibleCards++;
            }
        });

        // Update "No products found" message for each grid
        const noProducts = grid.querySelector('.shop__no-products');
        if (noProducts) {
            noProducts.style.display = hasVisibleCards ? 'none' : 'block';
        }

        // If this grid has visible products, make sure it's visible
        grid.style.display = hasVisibleCards ? 'grid' : 'none';
    });

    // Show appropriate messaging based on search results
    updateSearchResults(totalVisibleCards, searchTerm);
}

function updateSearchResults(totalVisibleCards, searchTerm) {
    // Remove any existing search results message
    const existingMessage = document.querySelector('.search-results-message');
    if (existingMessage) {
        existingMessage.remove();
    }

    // Create and show new message
    const messageDiv = document.createElement('div');
    messageDiv.className = 'search-results-message';
    
    if (totalVisibleCards === 0) {
        messageDiv.innerHTML = `
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i>
                No products found matching "${searchTerm}". Try different keywords or browse our categories.
            </div>
        `;
    } else {
        messageDiv.innerHTML = `
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i>
                Found ${totalVisibleCards} product${totalVisibleCards === 1 ? '' : 's'} matching "${searchTerm}"
            </div>
        `;
    }

    // Insert the message after the search input
    const searchSection = document.querySelector('.shop__search-section');
    searchSection.appendChild(messageDiv);
}

// Utility function to normalize text for searching
function normalizeText(text) {
    return text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') // Remove diacritics
        .replace(/[^a-z0-9\s]/g, ' ') // Replace special chars with space
        .replace(/\s+/g, ' ') // Replace multiple spaces with single space
        .trim();
}

// Debounce utility (keep your existing implementation)


function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Modal functionality
function initializeModal() {
    const productModal = document.getElementById('productModal');
    if (!productModal) return;

    productModal.addEventListener('hidden.bs.modal', function() {
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    });
}

function showProductModal(product) {
    const modalContent = document.getElementById('productModalContent');
    const addToCartBtn = document.getElementById('modalAddToCart');
    const whatsAppBtn = document.getElementById('modalWhatsApp');

    if (!modalContent || !addToCartBtn || !whatsAppBtn) return;

    modalContent.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="modal-image-container">
                    <img src="${product.product_image}" 
                         alt="${product.product_name}" 
                         class="modal-product-image">
                </div>
            </div>
            <div class="col-md-6">
                <div class="modal-product-details">
                    <h3 class="modal-product-title">${product.product_name}</h3>
                    <p class="modal-product-brand">${product.product_brand}</p>
                    ${product.product_description ? `
                        <div class="product-description">
                            <h4>Product Details:</h4>
                            <ul class="product-description-list">
                                ${product.product_description.split(';')
                                    .filter(point => point.trim())
                                    .map(point => `
                                        <li><i class="fas fa-check-circle"></i> ${point.trim()}</li>
                                    `).join('')}
                            </ul>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;

    addToCartBtn.onclick = (e) => {
        e.preventDefault();
        addToCart(e, product);
    };

    whatsAppBtn.href = `https://wa.me/+254782540742?text=${encodeURIComponent(`I'm interested in ${product.product_name}`)}`;
    whatsAppBtn.onclick = () => orderViaWhatsApp(product);

    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
}

// Notification and WhatsApp functionality
function showOrderNotification() {
    const toast = document.createElement('div');
    toast.className = 'custom-toast success-toast';
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-check-circle"></i>
            <span>Order initiated successfully! Opening WhatsApp...</span>
        </div>
    `;

    document.querySelector('.toast-container').appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function orderViaWhatsApp(product) {
    showOrderNotification();

    fetch('server/record_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            products: `Product: ${product.product_name}\nBrand: ${product.product_brand}`,
            from_cart: false
        })
    });

    const message = encodeURIComponent(
        `I'm interested in:\n\nProduct: ${product.product_name}\nBrand: ${product.product_brand}`
    );
    window.open(`https://wa.me/+254782540742?text=${message}`, '_blank');
}