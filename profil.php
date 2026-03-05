<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pageTitle = 'Mon Profil - HORIZON ALBASERVICE';

// Require login
$user = requireRole(['client', 'admin', 'livreur']);

$message = '';
$messageType = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $city = $_POST['city'] ?? '';
        
        if (empty($name) || empty($email)) {
            $message = 'Le nom et l\'email sont obligatoires';
            $messageType = 'error';
        } else {
            $result = updateUserProfile($user['id'], $name, $email, $phone, $address, $city);
            if (isset($result['success'])) {
                $message = 'Profil mis à jour avec succès';
                $messageType = 'success';
                $user = getCurrentUser(); // Refresh user data
            } else {
                $message = $result['error'];
                $messageType = 'error';
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $message = 'Veuillez remplir tous les champs';
            $messageType = 'error';
        } elseif ($newPassword !== $confirmPassword) {
            $message = 'Les nouveaux mots de passe ne correspondent pas';
            $messageType = 'error';
        } elseif (strlen($newPassword) < 6) {
            $message = 'Le mot de passe doit contenir au moins 6 caractères';
            $messageType = 'error';
        } else {
            $result = updateUserPassword($user['id'], $currentPassword, $newPassword);
            if (isset($result['success'])) {
                $message = 'Mot de passe mis à jour avec succès';
                $messageType = 'success';
            } else {
                $message = $result['error'];
                $messageType = 'error';
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update_photo') {
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $result = updateUserPhoto($user['id'], $_FILES['profile_image']);
            if (isset($result['success'])) {
                $message = 'Photo de profil mise à jour avec succès';
                $messageType = 'success';
                $user = getCurrentUser(); // Refresh user data
            } else {
                $message = $result['error'];
                $messageType = 'error';
            }
        } else {
            $message = 'Veuillez sélectionner une image';
            $messageType = 'error';
        }
    }
}

include 'components/header.php';
?>

<main class="min-h-screen bg-gray-50 py-8">
    <div class="container">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Mon Profil</h1>
                <p class="text-gray-600">Gérez vos informations personnelles</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?> mb-6">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Photo Section -->
                <div class="lg:col-span-1">
                    <div class="card">
                        <div class="text-center">
                            <h2 class="text-lg font-semibold mb-4">Photo de profil</h2>
                            
                            <?php if (!empty($user['profile_image'])): ?>
                                <img src="<?= htmlspecialchars($user['profile_image']) ?>" 
                                     alt="Photo de profil" 
                                     class="w-32 h-32 rounded-full object-cover mx-auto mb-4 border-4 border-white shadow-lg">
                            <?php else: ?>
                                <div class="w-32 h-32 rounded-full bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center text-white text-4xl font-bold mx-auto mb-4">
                                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data" class="mt-4">
                                <input type="hidden" name="action" value="update_photo">
                                <label class="btn btn-primary cursor-pointer">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Changer la photo
                                    <input type="file" name="profile_image" accept="image/*" class="hidden" onchange="this.form.submit()">
                                </label>
                            </form>
                            
                            <div class="mt-4 text-sm text-gray-500">
                                <p>Format: JPG, PNG ou GIF</p>
                                <p>Taille max: 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Info Section -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <div class="card">
                        <h2 class="text-lg font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Informations personnelles
                        </h2>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="form-label" for="name">Nom complet *</label>
                                    <input type="text" id="name" name="name" class="form-input" 
                                           value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="email">Email *</label>
                                    <input type="email" id="email" name="email" class="form-input" 
                                           value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone" class="form-input" 
                                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="city">Ville</label>
                                    <input type="text" id="city" name="city" class="form-input" 
                                           value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="form-group mt-4">
                                <label class="form-label" for="address">Adresse</label>
                                <textarea id="address" name="address" class="form-input" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary mt-4">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Enregistrer les modifications
                            </button>
                        </form>
                    </div>

                    <!-- Change Password -->
                    <div class="card">
                        <h2 class="text-lg font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            Changer le mot de passe
                        </h2>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="update_password">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="form-label" for="current_password">Mot de passe actuel</label>
                                    <input type="password" id="current_password" name="current_password" class="form-input" required>
                                </div>
                                
                                <div></div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="new_password">Nouveau mot de passe</label>
                                    <input type="password" id="new_password" name="new_password" class="form-input" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="confirm_password">Confirmer le mot de passe</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary mt-4">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Changer le mot de passe
                            </button>
                        </form>
                    </div>

                    <!-- Account Info -->
                    <div class="card">
                        <h2 class="text-lg font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informations du compte
                        </h2>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Type de compte</p>
                                <p class="font-semibold">
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="text-purple-600">Administrateur</span>
                                    <?php elseif ($user['role'] === 'livreur'): ?>
                                        <span class="text-orange-600">Livreur</span>
                                    <?php else: ?>
                                        <span class="text-blue-600">Client</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Statut</p>
                                <p class="font-semibold text-green-600">Actif</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Membre depuis</p>
                                <p class="font-semibold"><?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">ID Client</p>
                                <p class="font-semibold">#<?= $user['id'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>
