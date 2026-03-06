// HORIZON ALBASERVICE - JavaScript

// Toggle dropdown menu
function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const button = event.target.closest('button');
    
    if (dropdown && !dropdown.contains(event.target) && (!button || !button.onclick)) {
        dropdown.classList.remove('show');
    }
});

// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePassword');
    
    if (passwordInput && toggleBtn) {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>';
        } else {
            passwordInput.type = 'password';
            toggleBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
        }
    }
}

// Generate captcha for login
function generateCaptcha() {
    const num1 = Math.floor(Math.random() * 10) + 1;
    const num2 = Math.floor(Math.random() * 10) + 1;
    const answer = num1 + num2;
    
    document.getElementById('captchaQuestion').textContent = num1 + ' + ' + num2 + ' = ?';
    document.getElementById('captchaAnswer').value = answer;
    
    return answer;
}

// Add to cart
function addToCart(productId, name, price, image) {
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: name,
            price: price,
            image: image || '/images/products/placeholder.jpg',
            quantity: 1
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart count in header
    updateCartCount();
    
    // Show feedback
    showNotification('Produit ajouté au panier!', 'success');
}

// Remove from cart
function removeFromCart(productId) {
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Reload the page to update the cart display
    location.reload();
}

// Update quantity in cart
function updateQuantity(productId, change) {
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            cart = cart.filter(i => i.id !== productId);
        }
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    location.reload();
}

// Update cart count in header
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    const countElements = document.querySelectorAll('.cart-count');
    countElements.forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
    });
}

// Calculate cart total
function getCartTotal() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
}

// Clear cart
function clearCart() {
    localStorage.removeItem('cart');
    location.reload();
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = 'alert alert-' + type + ' fixed top-4 right-4 z-50';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Mobile menu toggle
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

// Close modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

// Open modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    }
}

// Profile image preview
function previewProfileImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById('profilePreview');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Get user GPS location - automatically fill latitude/longitude fields
function getUserLocation() {
    // Try different ID combinations for checkout page
    let latInput = document.getElementById('customer_latitude') || document.getElementById('latitude');
    let lngInput = document.getElementById('customer_longitude') || document.getElementById('longitude');
    
    // If inputs found, try to get location automatically
    if (latInput && lngInput && navigator.geolocation) {
        // Show loading message
        const gpsButton = document.querySelector('button[onclick*="getUserLocation"]');
        if (gpsButton) {
            gpsButton.innerHTML = '📍 Obtention de la position...';
            gpsButton.disabled = true;
        }
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                latInput.value = position.coords.latitude;
                lngInput.value = position.coords.longitude;
                console.log('GPS coordinates captured: ' + position.coords.latitude + ', ' + position.coords.longitude);
                
                // Update button to show success
                if (gpsButton) {
                    gpsButton.innerHTML = '✅ Position obtenue!';
                    gpsButton.classList.add('bg-green-500', 'hover:bg-green-600');
                    setTimeout(() => {
                        gpsButton.innerHTML = '📍 Position actuelle';
                        gpsButton.disabled = false;
                    }, 3000);
                }
            },
            function(error) {
                console.log('GPS error: ' + error.message);
                if (gpsButton) {
                    gpsButton.innerHTML = '📍 Réessayer';
                    gpsButton.disabled = false;
                }
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
}

// Auto-get location on page load for checkout
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count on every page
    updateCartCount();
    
    // Generate captcha if on login page
    const captchaQuestion = document.getElementById('captchaQuestion');
    if (captchaQuestion) {
        generateCaptcha();
    }
    
    // Check if we're on checkout page and auto-get location
    if (document.getElementById('customer_latitude') && document.getElementById('customer_longitude')) {
        // Small delay to ensure page is fully loaded
        setTimeout(getUserLocation, 1000);
    }
});

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('border-red-500');
            isValid = false;
        } else {
            input.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}

// Order status tracking
function updateOrderStatus(orderId, status) {
    fetch('/api/orders/update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ order_id: orderId, status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Statut mis à jour!', 'success');
            location.reload();
        } else {
            showNotification(data.error || 'Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(error => {
        showNotification('Erreur de connexion', 'error');
    });
}

// Export functions to global scope
window.toggleDropdown = toggleDropdown;
window.togglePassword = togglePassword;
window.generateCaptcha = generateCaptcha;
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateQuantity = updateQuantity;
window.updateCartCount = updateCartCount;
window.getCartTotal = getCartTotal;
window.clearCart = clearCart;
window.showNotification = showNotification;
window.toggleMobileMenu = toggleMobileMenu;
window.closeModal = closeModal;
window.openModal = openModal;
window.previewProfileImage = previewProfileImage;
window.validateForm = validateForm;
window.updateOrderStatus = updateOrderStatus;
window.getUserLocation = getUserLocation;
