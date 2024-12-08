// Modal cleanup helper
function cleanupModal(modalId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
    if (modal) {
        modal.hide();
    }
    document.body.classList.remove('modal-open');
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
}

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}-toast`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    const container = document.querySelector('.toast-container');
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Save new product
function saveProduct() {
    const form = document.getElementById('addProductForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    
    fetch('server/product_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product added successfully', 'success');
            cleanupModal('addProductModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.error || 'Failed to add product', 'error');
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        showToast('An error occurred', 'error');
    });
}

// Edit product
function editProduct(product) {
    const form = document.getElementById('editProductForm');
    
    // Set form values
    form.product_id.value = product.product_id;
    form.product_name.value = product.product_name;
    form.product_brand.value = product.product_brand;
    form.category.value = product.category;
    form.product_price.value = product.product_price;
    form.product_description.value = product.product_description || '';
    form.links.value = product.links || '';

    // Show image preview
    const preview = document.getElementById('editImagePreview');
    if (preview) {
        preview.src = product.product_image;
        preview.style.display = 'block';
    }

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
    modal.show();
}

// Update product
function updateProduct() {
    const form = document.getElementById('editProductForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);

    fetch('server/product_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product updated successfully', 'success');
            cleanupModal('editProductModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.error || 'Failed to update product', 'error');
        }
    })
    .catch(error => {
        console.error('Update error:', error);
        showToast('An error occurred', 'error');
    });
}

// Delete product
function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('product_id', productId);

    fetch('server/product_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.error || 'Failed to delete product', 'error');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showToast('An error occurred', 'error');
    });
}

// View product details
function viewProduct(product) {
    const modalBody = document.querySelector('#viewProductModal .modal-body');
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <img src="${product.product_image}" class="img-fluid" alt="${product.product_name}">
            </div>
            <div class="col-md-6">
                <h4>${product.product_name}</h4>
                <p><strong>Brand:</strong> ${product.product_brand}</p>
                <p><strong>Category:</strong> ${product.category}</p>
                <p><strong>Price:</strong> KSH ${Number(product.product_price).toLocaleString()}</p>
                <p><strong>Description:</strong></p>
                <p>${product.product_description || 'No description available'}</p>
                ${product.links ? `<p><strong>Additional Links:</strong></p><p>${product.links}</p>` : ''}
            </div>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('viewProductModal'));
    modal.show();
}

// Initialize when document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Handle all modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            cleanupModal(this.id);
        });
    });

    // Handle image previews
    const imageInputs = document.querySelectorAll('input[type="file"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const preview = this.parentElement.querySelector('.image-preview');
            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
});

