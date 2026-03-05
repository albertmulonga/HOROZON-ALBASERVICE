<?php
require_once 'config/db.php';
require_once 'config/functions.php';

// Initialize database
initDatabase();

$pageTitle = 'Promotions - HOROZON ALBASERVICE';

// Get promotions products
$db = getDB();
$stmt = $db->prepare("SELECT p.*, c.name as category_name 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE p.is_active = 1 AND p.is_promotion = 1 
                      ORDER BY p.created_at DESC");
$stmt->execute();
$promotions = $stmt->fetchAll();

include 'components/header.php';
?>

<!-- Hero Section -->
<section class="hero" style="min-height: 40vh;">
    <div class="hero-bg">
        <div class="hero-blob hero-blob-1"></div>
        <div class="hero-blob hero-blob-2"></div>
        <div class="hero-blob hero-blob-3"></div>
    </div>
    
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="hero-badge-dot"></span>
                <span class="text-sm">Offres Speciales</span>
            </div>
            <h1 class="hero-title">Nos Promotions</h1>
            <p class="hero-subtitle">
                Profitez de nos offres exceptionnelles sur une large gamme de produits
            </p>
        </div>
    </div>
</section>

<!-- Promotions Section -->
<section class="py-16">
    <div class="container">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Produits en Promotion</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Découvrez nos offres limitées et économisez sur vos achats
            </p>
        </div>
        
        <?php if (count($promotions) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($promotions as $product): ?>
                    <div class="product-card">
                        <div class="relative">
                            <span class="product-card-badge badge-promotion">-<?= round((1 - $product['price'] / $product['original_price']) * 100) ?>%</span>
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
                                    <span class="product-card-original-price"><?= number_format($product['original_price'], 2) ?> $</span>
                                </div>
                            </div>
                            <button onclick="addToCart(<?= $product['id'] ?>, '<?= addslashes($product['name']) ?>', <?= $product['price'] ?>, '<?= $product['image'] ?? '' ?>')" 
                                    class="btn btn-primary w-full mt-3">
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune promotion disponible</h3>
                <p class="text-gray-600 mb-6">Revenez plus tard pour découvrir nos offres</p>
                <a href="produits.php" class="btn btn-primary">Voir tous les produits</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-blue-800">
    <div class="container text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Vous avez des questions?</h2>
        <p class="text-blue-100 mb-8 max-w-2xl mx-auto">
            N'hésitez pas à nous contacter pour toute demande d'information
        </p>
        <a href="contact.php" class="btn btn-white btn-lg" style="background: white; color: var(--primary);">
            Nous contacter
        </a>
    </div>
</section>

<?php include 'components/footer.php'; ?>
