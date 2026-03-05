<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pageTitle = 'Catégories - HOROZON ALBASERVICE';

initDatabase();
$categories = getCategories();

include 'components/header.php';
?>

<div class="container py-8">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-6">
        <a href="index.php" class="breadcrumb-item">Accueil</a>
        <span class="breadcrumb-separator">/</span>
        <span class="text-gray-900 font-medium">Catégories</span>
    </nav>

    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Nos Catégories</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
            Explorez notre large gamme de produits par catégorie
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($categories as $category): ?>
            <a href="produits.php?category=<?= $category['id'] ?>" class="block">
                <div class="product-card">
                    <div class="relative">
                        <img src="<?= $category['image'] ?? 'https://via.placeholder.com/400x300?text=' . urlencode($category['name']) ?>" 
                             alt="<?= htmlspecialchars($category['name']) ?>" 
                             class="product-card-image"
                             style="height: 200px; object-fit: cover;">
                    </div>
                    <div class="product-card-body text-center">
                        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($category['name']) ?></h3>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($category['description'] ?? '') ?></p>
                        <span class="inline-block mt-3 text-blue-600 font-medium">Voir les produits →</span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'components/footer.php'; ?>
