<?php
require_once 'config/db.php';
require_once 'config/functions.php';

// Initialize database
initDatabase();

$pageTitle = 'HOROZON ALBASERVICE - Votre boutique en ligne';
$popularProducts = getPopularProducts();
$categories = getCategories();

// Services data
$services = [
    [
        'name' => 'Chaussures',
        'description' => 'Chaussures de qualité pour hommes et femmes',
        'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop',
        'icon' => '👞'
    ],
    [
        'name' => 'Vêtements',
        'description' => 'Vêtements élégante et modernes',
        'image' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&h=400&fit=crop',
        'icon' => '👔'
    ],
    [
        'name' => 'Sacs',
        'description' => 'Sacs à main, sacs à dos et accessories',
        'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=400&h=400&fit=crop',
        'icon' => '👜'
    ],
    [
        'name' => 'Accessoires',
        'description' => 'Montres, bijoux et autres accessories',
        'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
        'icon' => '⌚'
    ]
];

include 'components/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg">
        <div class="hero-blob hero-blob-1"></div>
        <div class="hero-blob hero-blob-2"></div>
        <div class="hero-blob hero-blob-3"></div>
    </div>
    
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="hero-badge-dot"></span>
                <span class="text-sm">Meilleure boutique en ligne à Kindu</span>
            </div>
            
            <!-- Logo en cercle -->
            <div class="flex justify-center mb-6">
                <div class="w-32 h-32 rounded-full bg-white/10 backdrop-blur-sm border-4 border-white/30 flex items-center justify-center shadow-2xl">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-4xl shadow-lg">
                        H
                    </div>
                </div>
            </div>
            
            <h1 class="hero-title">HOROZON ALBASERVICE</h1>
            <p class="hero-subtitle">
                Votre destination pour des produits de qualité à Kindu, Maniema, RDC
            </p>
            <div class="hero-buttons">
                <a href="produits.php" class="btn btn-primary btn-lg">
                    Découvrir nos produits
                </a>
                <a href="register.php" class="btn btn-outline btn-lg" style="border-color: rgba(255,255,255,0.3); color: white;">
                    Créer un compte
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-16 bg-gray-50">
    <div class="container">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Nos Catégories</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Découvrez notre large gamme de produits de qualité
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($services as $service): ?>
                <a href="produits.php" class="block">
                    <div class="product-card">
                        <div class="relative">
                            <img src="<?= $service['image'] ?>" alt="<?= $service['name'] ?>" class="product-card-image" style="height: 200px; object-fit: cover;">
                            <div class="absolute top-4 left-4 w-12 h-12 bg-white/90 rounded-full flex items-center justify-center text-2xl">
                                <?= $service['icon'] ?>
                            </div>
                        </div>
                        <div class="product-card-body">
                            <h3 class="product-card-title text-lg"><?= $service['name'] ?></h3>
                            <p class="text-sm text-gray-500"><?= $service['description'] ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Popular Products -->
<section class="py-16">
    <div class="container">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Produits Populaires</h2>
                <p class="text-gray-600 mt-1">Les produits les plus appréciés par nos clients</p>
            </div>
            <a href="produits.php" class="btn btn-outline">Voir tout</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($popularProducts as $product): ?>
                <div class="product-card">
                    <div class="relative">
                        <?php if ($product['is_promotion']): ?>
                            <span class="product-card-badge badge-promotion">Promotion</span>
                        <?php elseif ($product['is_popular']): ?>
                            <span class="product-card-badge badge-popular">Populaire</span>
                        <?php endif; ?>
                        <img src="<?= $product['image'] ?? 'https://via.placeholder.com/300x200?text=Produit' ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             class="product-card-image">
                    </div>
                    <div class="product-card-body">
                        <h3 class="product-card-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="text-sm text-gray-500 mb-2"><?= htmlspecialchars($product['description'] ?? '') ?></p>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="product-card-price"><?= number_format($product['price'], 2) ?> $</span>
                                <?php if ($product['original_price']): ?>
                                    <span class="product-card-original-price"><?= number_format($product['original_price'], 2) ?> $</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button onclick="addToCart(<?= $product['id'] ?>, '<?= addslashes($product['name']) ?>, <?= $product['price'] ?>, '<?= $product['image'] ?? '' ?>')" 
                                class="btn btn-primary w-full mt-3">
                            Ajouter au panier
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-16 bg-gray-50">
    <div class="container">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Pourquoi Nous Choisir?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Nous nous engageons à vous offrir la meilleure expérience d'achat
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Qualité Garantie</h3>
                <p class="text-gray-600">Tous nos produits sont sélectionnés avec soin pour vous garantir la meilleure qualité</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Livraison Rapide</h3>
                <p class="text-gray-600">Livraison rapide et fiable dans toute la ville de Kindu</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Support 24/7</h3>
                <p class="text-gray-600">Notre équipe est disponible à tout moment pour vous aider</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-blue-800">
    <div class="container text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Prêt à Commander?</h2>
        <p class="text-blue-100 mb-8 max-w-2xl mx-auto">
            milliers de clients satisf Rejoignez desaits et passez votre première commande dès maintenant
        </p>
        <a href="produits.php" class="btn btn-white btn-lg" style="background: white; color: var(--primary);">
            Commander maintenant
        </a>
    </div>
</section>

<!-- Localisation Map Section -->
<section class="py-16 bg-gray-50">
    <div class="container">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Notre Localisation</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Venez nous rendre visite dans notre boutique à Kindu
            </p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">HOROZON ALBASERVICE</h3>
                        <p class="text-gray-600">Votre boutique de confiance</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-900">Adresse</p>
                            <p class="text-gray-600">Marché Maman Yemo, Centre Ville Birere</p>
                            <p class="text-gray-600">Kindu, Province du Maniema, RDC</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-900">Téléphone</p>
                            <p class="text-gray-600">+243 000 000 000</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-purple-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-900">Horaires d'ouverture</p>
                            <p class="text-gray-600">Lundi - Samedi: 8h00 - 20h00</p>
                            <p class="text-gray-600">Dimanche: 9h00 - 17h00</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="contact.php" class="btn btn-primary w-full text-center">
                        Nous contacter
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden h-96 lg:h-auto">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15838.557894109!2d25.8964!3d-2.9437!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sKindu%2C+Maniema%2C+DRC!5e0!3m2!1sfr!2scd!4v1640000000000!5m2!1sfr!2scd"
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>
