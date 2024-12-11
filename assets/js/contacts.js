// Complete contacts.js
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        document.querySelector(targetId).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Form Validation and Submission
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form values
    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const email = document.getElementById('email').value.trim();
    const message = document.getElementById('message').value.trim();

    // Validation
    if (!validateForm(name, phone, email, message)) {
        return;
    }

    // Create message for WhatsApp
    const whatsappMessage = formatWhatsAppMessage(name, phone, email, message);
    
    // Open WhatsApp with formatted message
    const whatsappNumber = '+254782540742';
    const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(whatsappMessage)}`;
    window.open(whatsappUrl, '_blank');

    // Show success message
    showNotification('Message sent successfully! We will contact you soon.', 'success');
    
    // Reset form
    this.reset();
});

function validateForm(name, phone, email, message) {
    // Name validation
    if (name.length < 2) {
        showNotification('Please enter a valid name', 'error');
        return false;
    }

    // Phone validation
    const phoneRegex = /^\+?[\d\s-]{10,}$/;
    if (!phoneRegex.test(phone)) {
        showNotification('Please enter a valid phone number', 'error');
        return false;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showNotification('Please enter a valid email address', 'error');
        return false;
    }

    // Message validation
    if (message.length < 10) {
        showNotification('Please enter a detailed message', 'error');
        return false;
    }

    return true;
}

function formatWhatsAppMessage(name, phone, email, message) {
    return `
*New Contact Form Submission*
--------------------------
*Name:* ${name}
*Phone:* ${phone}
*Email:* ${email}
*Message:*
${message}
--------------------------
Sent from Zocom Limited Website
    `.trim();
}

function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Create new notification
    const notification = document.createElement('div');
    notification.className = `notification ${type}-notification`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    document.body.appendChild(notification);

    // Add show class after a small delay for animation
    setTimeout(() => notification.classList.add('show'), 10);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Add smooth scroll to all anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add input validation as user types
const inputs = document.querySelectorAll('input, textarea');
inputs.forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('error');
        const errorMessage = this.parentElement.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    });
});