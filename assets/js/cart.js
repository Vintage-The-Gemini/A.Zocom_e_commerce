// Cart functionality

// Toast notifications
function showSuccessToast(message) {
  createToast(message, "success", "check-circle");
}

function showErrorToast(message) {
  createToast(message, "error", "exclamation-circle");
}

function createToast(message, type, icon) {
  const toast = document.createElement("div");
  toast.className = `custom-toast ${type}-toast`;
  toast.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;

  const container = document.querySelector(".toast-container");
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = "slideOut 0.3s ease forwards";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Update cart count
function updateCartCount(count) {
  document.querySelectorAll(".cart-count").forEach((element) => {
    element.textContent = count;
  });
}

// Add to cart function
function addToCart(event, product) {
  event.preventDefault();

  if (!product || !product.product_id) {
    console.error("Invalid product data", product);
    showErrorToast("Invalid product data");
    return;
  }

  fetch("handlers/cart_handler.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      action: "add",
      productId: product.product_id,
      productName: product.product_name,
      productBrand: product.product_brand,
      productImage: product.product_image,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        updateCartCount(data.cartCount);
        const modal = bootstrap.Modal.getInstance(
          document.getElementById("productModal")
        );
        if (modal) modal.hide();
        showSuccessToast(`${product.product_name} added to cart`);
      } else {
        showErrorToast(data.error || "Failed to add product to cart");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showErrorToast("An error occurred while adding to cart");
    });
}

// Update quantity
function updateQuantity(productId, change) {
  const cartItem = document.querySelector(`[data-id="${productId}"]`);
  const quantitySpan = cartItem.querySelector(".quantity");
  const currentQty = parseInt(quantitySpan.textContent);
  const newQty = currentQty + change;

  if (newQty <= 0) {
    removeFromCart(productId);
    return;
  }

  fetch("handlers/cart_handler.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      action: "update",
      productId: productId,
      quantity: newQty,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        quantitySpan.textContent = newQty;
        updateCartCount(data.cartCount);
        showSuccessToast("Cart updated");
      } else {
        showErrorToast(data.error || "Failed to update cart");
      }
    })
    .catch((error) => {
      console.error("Error updating quantity:", error);
      showErrorToast("Failed to update cart");
    });
}

// Remove from cart
function removeFromCart(productId) {
  if (!confirm("Remove this item from cart?")) return;

  fetch("handlers/cart_handler.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      action: "remove",
      productId: productId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const item = document.querySelector(`[data-id="${productId}"]`);
        item.style.animation = "slideOut 0.3s ease forwards";
        setTimeout(() => {
          item.remove();
          if (data.cartCount === 0) location.reload();
        }, 300);
        updateCartCount(data.cartCount);
        showSuccessToast("Item removed from cart");
      } else {
        showErrorToast(data.error || "Failed to remove item");
      }
    })
    .catch((error) => {
      console.error("Error removing item:", error);
      showErrorToast("Failed to remove item");
    });
}

// Clear cart
function clearCart() {
  if (!confirm("Are you sure you want to clear your cart?")) return;

  fetch("handlers/cart_handler.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "clear" }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        location.reload();
      } else {
        showErrorToast(data.error || "Failed to clear cart");
      }
    })
    .catch((error) => {
      console.error("Error clearing cart:", error);
      showErrorToast("Failed to clear cart");
    });
}

// Order via WhatsApp - FIXED IMPLEMENTATION
function orderViaWhatsApp() {
  // Show notification
  showSuccessToast("Preparing your order for WhatsApp...");

  // Get all cart items
  const items = Array.from(document.querySelectorAll(".cart-item"))
    .map((item) => {
      const name = item.querySelector("h3").textContent;
      const brand = item.querySelector(".brand").textContent;
      const quantity = item.querySelector(".quantity").textContent;
      return `• ${quantity}x ${name}\n   Brand: ${brand}`;
    })
    .join("\n\n");

  // Create the WhatsApp message
  const message = `Hello! I would like to request a quote for the following items:\n\n${items}\n\nPlease contact me with pricing and availability.\nThank you!`;

  // Record the order in the backend (if you have this functionality)
  fetch("server/record_order.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      products: items,
      from_cart: true,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      // Open WhatsApp with formatted message
      window.open(
        `https://wa.me/+254782540742?text=${encodeURIComponent(message)}`,
        "_blank"
      );
    })
    .catch((error) => {
      console.error("Error recording order:", error);
      // Still open WhatsApp even if recording fails
      window.open(
        `https://wa.me/+254782540742?text=${encodeURIComponent(message)}`,
        "_blank"
      );
    });
}

// Product modal functionality
function showProductModal(product) {
  if (!product) return;

  const modalContent = document.getElementById("productModalContent");
  if (!modalContent) return;

  let descriptionHtml = "";
  if (product.product_description) {
    const points = product.product_description
      .split(";")
      .filter((point) => point.trim());
    descriptionHtml =
      points.length > 1
        ? `<ul class="product-description-list">${points
            .map((point) => `<li>${point.trim()}</li>`)
            .join("")}</ul>`
        : `<p>${product.product_description}</p>`;
  }

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
                    <div class="modal-product-description">
                        ${descriptionHtml}
                    </div>
                </div>
            </div>
        </div>
    `;

  const modalAddToCartBtn = document.getElementById("modalAddToCart");
  const modalWhatsAppBtn = document.getElementById("modalWhatsAppBtn");

  if (modalAddToCartBtn) {
    modalAddToCartBtn.onclick = (e) => addToCart(e, product);
  }

  if (modalWhatsAppBtn) {
    const message = `I'm interested in ${product.product_name} (${product.product_brand})`;
    modalWhatsAppBtn.href = `https://wa.me/+254782540742?text=${encodeURIComponent(
      message
    )}`;
  }

  const modal = new bootstrap.Modal(document.getElementById("productModal"));
  modal.show();
}

// Initialize event listeners when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
  // Add any initialization code here if needed
});
