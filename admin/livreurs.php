<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$pageTitle = 'Gestion des Livreurs - HIRIZON DE KINDU';

// Require admin role
$user = requireRole(['admin']);

// Get all livreurs with their stats
$db = getDB();
$stmt = $db->query("SELECT u.*, 
    (SELECT COUNT(*) FROM orders WHERE delivery_person_id = u.id) as total_deliveries,
    (SELECT COUNT(*) FROM orders WHERE delivery_person_id = u.id AND status = 'livre') as completed_deliveries
    FROM users u WHERE u.role = 'livreur' ORDER BY u.created_at DESC");
$livreurs = $stmt->fetchAll();

// Handle create/update livreur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        if ($password) {
            $result = createUserByAdmin($name, $email, $phone, $password, 'livreur', $address, $city);
            if (isset($result['success'])) {
                echo '<script>alert("Livreur créé avec succès!");</script>';
            } else {
                echo '<script>alert("Erreur: ' . $result['error'] . '");</script>';
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $livreurId = (int)$_POST['livreur_id'];
        deleteUser($livreurId);
    }
    
    // Refresh livreurs list
    $stmt = $db->query("SELECT u.*, 
        (SELECT COUNT(*) FROM orders WHERE delivery_person_id = u.id) as total_deliveries,
        (SELECT COUNT(*) FROM orders WHERE delivery_person_id = u.id AND status = 'livre') as completed_deliveries
        FROM users u WHERE u.role = 'livreur' ORDER BY u.created_at DESC");
    $livreurs = $stmt->fetchAll();
}
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
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-xl">HK</div>
                        <span class="text-xl font-bold text-gray-900">HIRIZON</span>
                    </a>
                </div>
                
                <nav class="hidden md:flex items-center gap-6">
                    <a href="/admin/index.php" class="header-nav-link">Dashboard</a>
                    <a href="/admin/produits.php" class="header-nav-link">Produits</a>
                    <a href="/admin/utilisateurs.php" class="header-nav-link">Utilisateurs</a>
                    <a href="/admin/commandes.php" class="header-nav-link">Commandes</a>
                    <a href="/admin/livreurs.php" class="header-nav-link active">Livreurs</a>
                </nav>
                
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600"><?= htmlspecialchars($user['name']) ?></span>
                    <div class="dropdown">
                        <button onclick="toggleDropdown()" class="flex items-center gap-2 cursor-pointer">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            </div>
                        </button>
                        <div id="userDropdown" class="dropdown-menu">
                            <div class="p-3 border-b">
                                <p class="font-semibold"><?= htmlspecialchars($user['name']) ?></p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                            <a href="/admin/index.php" class="dropdown-item">Dashboard</a>
                            <a href="/admin/produits.php" class="dropdown-item">Produits</a>
                            <a href="/admin/utilisateurs.php" class="dropdown-item">Utilisateurs</a>
                            <a href="/admin/commandes.php" class="dropdown-item">Commandes</a>
                            <div class="dropdown-divider"></div>
                            <a href="/logout.php" class="dropdown-item text-red-600">Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="min-h-screen bg-gray-50">
        <!-- Page Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="container py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient">Gestion des Livreurs</h1>
                        <p class="text-gray-600 mt-1">Gérez les livreurs et leurs performances</p>
                    </div>
                    <button onclick="openModal()" class="btn btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter un livreur
                    </button>
                </div>
            </div>
        </div>

        <div class="container py-8">
            <!-- Livreurs Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($livreurs as $livreur): ?>
                    <div class="card">
                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                                    <?= strtoupper(substr($livreur['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold"><?= htmlspecialchars($livreur['name']) ?></h3>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($livreur['phone'] ?? 'Sans téléphone') ?></p>
                                    <span class="text-xs <?= $livreur['is_active'] ? 'text-green-600' : 'text-red-600' ?>">
                                        <?= $livreur['is_active'] ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-2xl font-bold text-blue-600"><?= $livreur['total_deliveries'] ?></p>
                                    <p class="text-xs text-gray-500">Total livraisons</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-2xl font-bold text-green-600"><?= $livreur['completed_deliveries'] ?></p>
                                    <p class="text-xs text-gray-500">Livrés</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <a href="/admin/livreur_detail.php?id=<?= $livreur['id'] ?>" class="btn btn-secondary btn-sm flex-1">Voir détails</a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="livreur_id" value="<?= $livreur['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livreur?')">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (count($livreurs) === 0): ?>
                    <div class="col-span-full text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun livreur</h3>
                        <p class="text-gray-600 mb-6">Ajoutez des livreurs pour les livraisons</p>
                        <button onclick="openModal()" class="btn btn-primary">Ajouter un livreur</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Add Livreur Modal -->
    <div id="addModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Ajouter un livreur</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nom complet *</label>
                        <input type="text" name="name" class="input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Téléphone *</label>
                        <input type="tel" name="phone" class="input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mot de passe *</label>
                        <input type="password" name="password" class="input" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Adresse</label>
                        <input type="text" name="address" class="input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ville</label>
                        <input type="text" name="city" class="input" value="Kindu">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/js/main.js"></script>
    <script>
        function openModal() {
            document.getElementById('addModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('addModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
