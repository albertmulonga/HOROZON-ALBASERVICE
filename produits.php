<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pageTitle = 'Produits - HOROZON ALBASERVICE';

// Initialize database
initDatabase();

// Get parameters
$categoryId = isset($_GET['category']) ? intval($_GET['category']) : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

// Get products and categories
$products = getProducts($categoryId, $search);
$categories = getCategories();

include 'components/header.php';
?>

<div class="container py-8">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-6">
        <a href="/" class="breadcrumb-item">Accueil</a>
        <span class="breadcrumb-separator">/</span>
        <span class="text-gray-900 font-medium">Produits</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar - Categories -->
        <aside-64 flex-s class="lg:whrink-0">
            <div class="card p-6 sticky" style="top: 100px;">
                <h3 class="text-lg font-semibold mb-4">Catégories</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="/produits.php" class="block py-2 px-3 rounded <?= !$categoryId ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-50' ?>">
                            Tous les produits
                        </a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="/produits.php?category=<?= $category['id'] ?>" class="block py-2 px-3 rounded <?= $categoryId === $category['id'] ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-50' ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Search & Filter Bar -->
            <div class="card p-4 mb-6">
                <form action="/produits.php" method="GET" class="flex gap-4">
                    <?php if ($categoryId): ?>
                        <input type="hidden" name="category" value="<?= $categoryId ?>">
                    <?php endif; ?>
                    <div class="flex-1">
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Rechercher un produit..." class="form-input">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Results count -->
            <p class="text-gray-600 mb-4"><?= count($products) ?> produit(s) trouvé(s)</p>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($products as $product): ?>
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
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <span class="product-card-price"><?= number_format($product['price'], 2) ?> $</span>
                                    <?php if ($product['original_price']): ?>
                                        <span class="product-card-original-price"><?= number_format($product['original_price'], 2) ?> $</span>
                                    <?php endif; ?>
                                </div>
                                <span class="text-sm text-gray-500">Stock: <?= $product['stock'] ?></span>
                            </div>
                            <button onclick="addToCart(<?= $product['id'] ?>, '<?= addslashes($product['name']) ?>', <?= $product['price'] ?>, '<?= $product['image'] ?? '' ?>')" 
                                    class="btn btn-primary w-full">
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($products) === 0): ?>
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">Aucun produit trouvé</p>
                    <a href="/produits.php" class="btn btn-primary mt-4">Voir tous les produits</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>
