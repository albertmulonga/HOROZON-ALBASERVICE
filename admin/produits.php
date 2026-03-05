<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$pageTitle = 'Gestion des produits - Admin';

// Require admin role
$user = requireRole(['admin']);

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = floatval($_POST['price'] ?? 0);
            $categoryId = intval($_POST['category_id'] ?? 0);
            $stock = intval($_POST['stock'] ?? 0);
            $isPopular = isset($_POST['is_popular']) ? 1 : 0;
            $isPromotion = isset($_POST['is_promotion']) ? 1 : 0;
            $originalPrice = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : null;
            
            if (empty($name) || $price <= 0) {
                $message = 'Veuillez remplir tous les champs obligatoires';
                $messageType = 'error';
            } else {
                $result = createProduct($name, $description, $price, $categoryId, $stock, $isPopular, $isPromotion, $originalPrice);
                if (isset($result['success'])) {
                    $message = 'Produit créé avec succès';
                    $messageType = 'success';
                } else {
                    $message = $result['error'];
                    $messageType = 'error';
                }
            }
        } elseif ($_POST['action'] === 'update') {
            $id = intval($_POST['id'] ?? 0);
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = floatval($_POST['price'] ?? 0);
            $categoryId = intval($_POST['category_id'] ?? 0);
            $stock = intval($_POST['stock'] ?? 0);
            $isPopular = isset($_POST['is_popular']) ? 1 : 0;
            $isPromotion = isset($_POST['is_promotion']) ? 1 : 0;
            $originalPrice = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : null;
            
            $result = updateProduct($id, $name, $description, $price, $categoryId, $stock, $isPopular, $isPromotion, $originalPrice);
            if (isset($result['success'])) {
                $message = 'Produit mis à jour avec succès';
                $messageType = 'success';
            } else {
                $message = $result['error'];
                $messageType = 'error';
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            $result = deleteProduct($id);
            if (isset($result['success'])) {
                $message = 'Produit supprimé avec succès';
                $messageType = 'success';
            }
        }
    }
}

// Get products and categories
$products = getAllProducts();
$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="flex items-center justify-between" style="height: 4rem;">
                <div class="flex items-center gap-4">
                    <a href="/" class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-xl">H</div>
                        <span class="text-xl font-bold text-gray-900">HOROZON</span>
                    </a>
                </div>
                
                <nav class="hidden md:flex items-center gap-6">
                    <a href="/admin/index.php" class="header-nav-link">Dashboard</a>
                    <a href="/admin/produits.php" class="header-nav-link active">Produits</a>
                    <a href="/admin/utilisateurs.php" class="header-nav-link">Utilisateurs</a>
                </nav>
                
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600"><?= htmlspecialchars($user['name']) ?></span>
                    <a href="/logout.php" class="btn btn-secondary btn-sm">Déconnexion</a>
                </div>
            </div>
        </div>
    </header>

    <main class="min-h-screen bg-gray-50">
        <div class="container py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Gestion des produits</h1>
                    <p class="text-gray-600">Ajoutez, modifiez ou supprimez des produits</p>
                </div>
                <button onclick="openModal('addProductModal')" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nouveau produit
                </button>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Products Table -->
            <div class="card">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Stock</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>#<?= $product['id'] ?></td>
                                    <td>
                                        <img src="<?= $product['image'] ?? 'https://via.placeholder.com/50x50?text=P' ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                    </td>
                                    <td>
                                        <div class="font-semibold"><?= htmlspecialchars($product['name']) ?></div>
                                        <div class="text-sm text-gray-500 truncate" style="max-width: 200px;"><?= htmlspecialchars($product['description'] ?? '') ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($product['category_name'] ?? 'Aucune') ?></td>
                                    <td>
                                        <span class="font-semibold">$<?= number_format($product['price'], 2) ?></span>
                                        <?php if ($product['original_price']): ?>
                                            <span class="text-sm text-gray-400 line-through ml-2">$<?= number_format($product['original_price'], 2) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $product['stock'] ?></td>
                                    <td>
                                        <?php if ($product['is_popular']): ?>
                                            <span class="status-badge bg-yellow-100 text-yellow-800">Populaire</span>
                                        <?php endif; ?>
                                        <?php if ($product['is_promotion']): ?>
                                            <span class="status-badge bg-red-100 text-red-800">Promotion</span>
                                        <?php endif; ?>
                                        <?php if (!$product['is_active']): ?>
                                            <span class="status-badge bg-gray-100 text-gray-800">Inactif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button onclick="editProduct(<?= $product['id'] ?>, '<?= addslashes($product['name']) ?>', '<?= addslashes($product['description'] ?? '') ?>', <?= $product['price'] ?>, <?= $product['category_id'] ?>, <?= $product['stock'] ?>, <?= $product['is_popular'] ?>, <?= $product['is_promotion'] ?>, <?= $product['original_price'] ?? 'null' ?>)" class="btn btn-secondary btn-sm">Modifier</button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit?')">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($products) === 0): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-gray-500">Aucun produit</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Nouveau produit</h3>
                <button onclick="closeModal('addProductModal')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label class="form-label">Nom du produit *</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-textarea" rows="3"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Prix *</label>
                            <input type="number" name="price" step="0.01" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-input" value="0">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Catégorie</label>
                        <select name="category_id" class="form-select">
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Prix original (promotion)</label>
                            <input type="number" name="original_price" step="0.01" class="form-input">
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_popular" class="form-checkbox">
                            <span>Produit populaire</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_promotion" class="form-checkbox">
                            <span>En promotion</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('addProductModal')" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Modifier le produit</h3>
                <button onclick="closeModal('editProductModal')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="editId">
                    
                    <div class="form-group">
                        <label class="form-label">Nom du produit *</label>
                        <input type="text" name="name" id="editName" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="editDescription" class="form-textarea" rows="3"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Prix *</label>
                            <input type="number" name="price" id="editPrice" step="0.01" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" id="editStock" class="form-input">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Catégorie</label>
                        <select name="category_id" id="editCategoryId" class="form-select">
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Prix original (promotion)</label>
                        <input type="number" name="original_price" id="editOriginalPrice" step="0.01" class="form-input">
                    </div>
                    
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_popular" id="editIsPopular">
                            <span>Produit populaire</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_promotion" id="editIsPromotion">
                            <span>En promotion</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('editProductModal')" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/js/main.js"></script>
    <script>
        function editProduct(id, name, description, price, categoryId, stock, isPopular, isPromotion, originalPrice) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editDescription').value = description;
            document.getElementById('editPrice').value = price;
            document.getElementById('editCategoryId').value = categoryId;
            document.getElementById('editStock').value = stock;
            document.getElementById('editIsPopular').checked = isPopular == 1;
            document.getElementById('editIsPromotion').checked = isPromotion == 1;
            document.getElementById('editOriginalPrice').value = originalPrice || '';
            
            openModal('editProductModal');
        }
    </script>
</body>
</html>
