<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$pageTitle = 'Gestion des utilisateurs - Admin';

// Require admin role
$user = requireRole(['admin']);

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'client';
            $address = $_POST['address'] ?? '';
            $city = $_POST['city'] ?? '';
                $profileImage = isset($_FILES['profile_image']) ? $_FILES['profile_image'] : null;
                
                if (empty($name) || empty($email) || empty($password)) {
                    $message = 'Veuillez remplir tous les champs obligatoires';
                    $messageType = 'error';
                } else {
                    $result = createUserByAdmin($name, $email, $phone, $password, $role, $address, $city, $profileImage);
                if (isset($result['success'])) {
                    $message = 'Utilisateur créé avec succès';
                    $messageType = 'success';
                } else {
                    $message = $result['error'];
                    $messageType = 'error';
                }
            }
        } elseif ($_POST['action'] === 'toggle') {
            $id = intval($_POST['id'] ?? 0);
            $result = toggleUserStatus($id);
            if (isset($result['success'])) {
                $message = 'Statut mis à jour';
                $messageType = 'success';
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            $result = deleteUser($id);
            if (isset($result['success'])) {
                $message = 'Utilisateur supprimé';
                $messageType = 'success';
            }
        }
    }
}

// Get all users
$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="flex items-center justify-between" style="height: 4rem;">
                <div class="flex items-center gap-4">
                    <a href="../index.php" class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-xl">H</div>
                        <span class="text-xl font-bold text-gray-900">HORIZON</span>
                    </a>
                </div>
                
                <nav class="hidden md:flex items-center gap-6">
                    <a href="../index.php" class="header-nav-link">Dashboard</a>
                    <a href="../produits.php" class="header-nav-link">Produits</a>
                    <a href="../utilisateurs.php" class="header-nav-link active">Utilisateurs</a>
                </nav>
                
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600"><?= htmlspecialchars($user['name']) ?></span>
                    <a href="../logout.php" class="btn btn-secondary btn-sm">Déconnexion</a>
                </div>
            </div>
        </div>
    </header>

    <main class="min-h-screen bg-gray-50">
        <div class="container py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Gestion des utilisateurs</h1>
                    <p class="text-gray-600">Gérez les clients, livreurs et administrateurs</p>
                </div>
                <button onclick="openModal('addUserModal')" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nouvel utilisateur
                </button>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Users Table -->
            <div class="card">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td>#<?= $u['id'] ?></td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                                <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="font-semibold"><?= htmlspecialchars($u['name']) ?></div>
                                                <?php if ($u['city']): ?>
                                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($u['city']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
                                    <td>
                                        <?php 
                                        $roleColors = [
                                            'admin' => 'bg-purple-100 text-purple-800',
                                            'livreur' => 'bg-orange-100 text-orange-800',
                                            'client' => 'bg-blue-100 text-blue-800'
                                        ];
                                        $roleLabels = [
                                            'admin' => 'Administrateur',
                                            'livreur' => 'Livreur',
                                            'client' => 'Client'
                                        ];
                                        ?>
                                        <span class="status-badge <?= $roleColors[$u['role']] ?? 'bg-gray-100' ?>">
                                            <?= $roleLabels[$u['role']] ?? $u['role'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($u['is_active']): ?>
                                            <span class="status-badge bg-green-100 text-green-800">Actif</span>
                                        <?php else: ?>
                                            <span class="status-badge bg-red-100 text-red-800">Inactif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <?php if ($u['id'] != $user['id']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="toggle">
                                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="btn btn-sm <?= $u['is_active'] ? 'btn-warning' : 'btn-success' ?>">
                                                        <?= $u['is_active'] ? 'Désactiver' : 'Activer' ?>
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-sm text-gray-500">(Vous)</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($users) === 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-gray-500">Aucun utilisateur</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Nouvel utilisateur</h3>
                <button onclick="closeModal('addUserModal')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <!-- Photo de profil -->
                    <div class="form-group">
                        <label class="form-label">Photo de profil</label>
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <label class="btn btn-secondary btn-sm cursor-pointer">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Choisir une photo
                                <input type="file" name="profile_image" accept="image/*" class="hidden" onchange="previewImage(this, 'previewProfileImage')">
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Nom complet *</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" name="phone" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Mot de passe *</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Rôle *</label>
                        <select name="role" class="form-select" required>
                            <option value="client">Client</option>
                            <option value="livreur">Livreur</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Adresse</label>
                        <input type="text" name="address" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Ville</label>
                        <input type="text" name="city" class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('addUserModal')" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>
