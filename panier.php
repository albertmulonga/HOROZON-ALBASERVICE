<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pageTitle = 'Panier - HOROZON ALBASERVICE';

include 'components/header.php';
?>

<div class="container py-8">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-6">
        <a href="/" class="breadcrumb-item">Accueil</a>
        <span class="breadcrumb-separator">/</span>
        <span class="text-gray-900 font-medium">Panier</span>
    </nav>

    <h1 class="text-3xl font-bold text-gray-900 mb-8">Mon Panier</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Cart Items -->
        <div class="flex-1">
            <div id="cartItems" class="space-y-4">
                <!-- Cart items will be loaded via JavaScript -->
            </div>
            
            <div id="emptyCart" class="text-center py-12" style="display: none;">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500 text-lg mb-4">Votre panier est vide</p>
                <a href="/produits.php" class="btn btn-primary">Découvrir nos produits</a>
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="lg:w-96">
            <div class="card p-6 sticky" style="top: 100px;">
                <h2 class="text-xl font-semibold mb-4">Récapitulatif</h2>
                
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sous-total</span>
                        <span class="font-semibold" id="cartSubtotal">$0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Livraison</span>
                        <span class="font-semibold">Gratuite</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between">
                        <span class="font-semibold">Total</span>
                        <span class="font-bold text-xl text-blue-600" id="cartTotal">$0.00</span>
                    </div>
                </div>

                <?php if (isLoggedIn()): ?>
                    <a href="/checkout.php" class="btn btn-primary w-full mb-3">
                        Passer la commande
                    </a>
                <?php else: ?>
                    <a href="/login.php" class="btn btn-primary w-full mb-3">
                        Se connecter pour commander
                    </a>
                    <p class="text-sm text-gray-500 text-center">Vous devez être connecté pour passer une commande</p>
                <?php endif; ?>

                <button onclick="clearCart()" class="btn btn-outline w-full mt-3">
                    Vider le panier
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Load cart items from localStorage
document.addEventListener('DOMContentLoaded', function() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const cartItemsContainer = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    
    if (cart.length === 0) {
        cartItemsContainer.style.display = 'none';
        emptyCart.style.display = 'block';
        return;
    }
    
    let subtotal = 0;
    
    cartItemsContainer.innerHTML = cart.map(item => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        
        return `
            <div class="card p-4 flex gap-4">
                <img src="${item.image || 'https://via.placeholder.com/100x100?text=P'}" alt="${item.name}" class="w-24 h-24 object-cover rounded-lg">
                <div class="flex-1">
                    <h3 class="font-semibold text-lg">${item.name}</h3>
                    <p class="text-gray-500">${item.price.toFixed(2)} $</p>
                    <div class="flex items-center gap-3 mt-2">
                        <button onclick="updateQuantity(${item.id}, -1)" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">-</button>
                        <span class="font-semibold">${item.quantity}</span>
                        <button onclick="updateQuantity(${item.id}, 1)" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">+</button>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-lg">${itemTotal.toFixed(2)} $</p>
                    <button onclick="removeFromCart(${item.id})" class="text-red-500 text-sm hover:underline mt-2">Supprimer</button>
                </div>
            </div>
        `;
    }).join('');
    
    document.getElementById('cartSubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('cartTotal').textContent = '$' + subtotal.toFixed(2);
});
</script>

<?php include 'components/footer.php'; ?>
