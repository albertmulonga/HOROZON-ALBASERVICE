<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pageTitle = 'Checkout - HOROZON ALBASERVICE';

// Require login for checkout
if (!isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

$user = getCurrentUser();
$error = '';
$success = false;

// Get cart from localStorage via JavaScript
// For now, we'll handle this via API

include 'components/header.php';
?>

<div class="container py-8">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-6">
        <a href="/" class="breadcrumb-item">Accueil</a>
        <span class="breadcrumb-separator">/</span>
        <a href="/panier.php" class="breadcrumb-item">Panier</a>
        <span class="breadcrumb-separator">/</span>
        <span class="text-gray-900 font-medium">Checkout</span>
    </nav>

    <h1 class="text-3xl font-bold text-gray-900 mb-8">Finaliser la commande</h1>

    <form id="checkoutForm" method="POST" class="flex flex-col lg:flex-row gap-8">
        <!-- Checkout Form -->
        <div class="flex-1">
            <!-- Customer Info -->
            <div class="card p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Informations de livraison</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Nom complet *</label>
                        <input type="text" name="customer_name" class="form-input" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Téléphone *</label>
                        <input type="tel" name="customer_phone" class="form-input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Adresse *</label>
                    <input type="text" name="customer_address" class="form-input" value="<?= htmlspecialchars($user['address'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Ville *</label>
                    <input type="text" name="customer_city" class="form-input" value="<?= htmlspecialchars($user['city'] ?? 'Kindu') ?>" required>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Articles commandés</h2>
                <div id="checkoutItems">
                    <!-- Items loaded via JavaScript -->
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:w-96">
            <div class="card p-6 sticky" style="top: 100px;">
                <h2 class="text-xl font-semibold mb-4">Récapitulatif</h2>
                
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sous-total</span>
                        <span class="font-semibold" id="checkoutSubtotal">$0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Livraison</span>
                        <span class="font-semibold text-green-600">Gratuite</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between">
                        <span class="font-semibold">Total</span>
                        <span class="font-bold text-xl text-blue-600" id="checkoutTotal">$0.00</span>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-blue-900 mb-2">Paiement Mobile Money</h3>
                    <p class="text-sm text-blue-700">Envoyez le montant total au numéro:</p>
                    <p class="text-lg font-bold text-blue-900"><?= getSetting('payment_phone') ?? '+243 000 000 000' ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label">Numéro de transaction *</label>
                    <input type="text" name="transaction_number" class="form-input" placeholder="Ex: M1234567890" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Numéro utilisé pour le paiement *</label>
                    <input type="tel" name="payment_phone" class="form-input" placeholder="+243 ..." required>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    Confirmer la commande
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const checkoutItems = document.getElementById('checkoutItems');
    
    if (cart.length === 0) {
        checkoutItems.innerHTML = '<p class="text-gray-500">Votre panier est vide</p>';
        return;
    }
    
    let subtotal = 0;
    
    checkoutItems.innerHTML = cart.map(item => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        
        return `
            <div class="flex gap-4 py-3 border-b">
                <img src="${item.image || 'https://via.placeholder.com/60x60?text=P'}" alt="${item.name}" class="w-16 h-16 object-cover rounded">
                <div class="flex-1">
                    <h4 class="font-medium">${item.name}</h4>
                    <p class="text-sm text-gray-500">Qty: ${item.quantity} × ${item.price.toFixed(2)} $</p>
                </div>
                <p class="font-semibold">${itemTotal.toFixed(2)} $</p>
            </div>
        `;
    }).join('');
    
    document.getElementById('checkoutSubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('checkoutTotal').textContent = '$' + subtotal.toFixed(2);
});

// Handle form submission
document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    if (cart.length === 0) {
        alert('Votre panier est vide');
        return;
    }
    
    const formData = new FormData(this);
    const data = {
        customer_name: formData.get('customer_name'),
        customer_phone: formData.get('customer_phone'),
        customer_address: formData.get('customer_address'),
        customer_city: formData.get('customer_city'),
        transaction_number: formData.get('transaction_number'),
        payment_phone: formData.get('payment_phone'),
        items: cart
    };
    
    try {
        const response = await fetch('/api/orders/create/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            localStorage.removeItem('cart');
            alert('Commande créée avec succès! ID: #' + result.order_id);
            window.location.href = '/client/index.php?order=' + result.order_id + '&success=1';
        } else {
            alert(result.error || 'Erreur lors de la commande');
        }
    } catch (error) {
        alert('Erreur de connexion');
    }
});
</script>

<?php include 'components/footer.php'; ?>
