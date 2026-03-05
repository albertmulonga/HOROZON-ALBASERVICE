<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pageTitle = 'Inscription - HOROZON ALBASERVICE';
$error = '';
$success = '';

// Redirect if already logged in
if (isLoggedIn()) {
    $user = getCurrentUser();
    redirectToDashboard($user['role']);
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $captchaAnswer = $_POST['captcha_answer'] ?? '';
    $captchaReal = $_POST['captcha_real'] ?? '';
    $profileImage = null;
    
    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['profile_image']['type'];
        $fileSize = $_FILES['profile_image']['size'];
        
        if (in_array($fileType, $allowedTypes) && $fileSize <= 2 * 1024 * 1024) {
            $uploadDir = 'uploads/profile/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $newFilename = uniqid('profile_') . '.' . $extension;
            $targetPath = $uploadDir . $newFilename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                $profileImage = $targetPath;
            }
        }
    }
    
    // Verify captcha
    if ($captchaAnswer !== $captchaReal) {
        $error = 'Réponse incorrecte. Veuillez résoudre le problème mathématique.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } else {
        $result = registerUser($name, $email, $phone, $password, $profileImage);
        
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            $success = 'Compte créé avec succès! Vous pouvez maintenant vous connecter.';
        }
    }
}

include 'components/header.php';
?>

<div class="auth-container">
    <!-- Visual Side -->
    <div class="auth-visual">
        <div class="auth-visual-bg"></div>
        <div class="auth-visual-content">
            <div class="text-center mb-8">
                <div class="w-24 h-24 mx-auto mb-6 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold mb-4">Créer un compte</h2>
                <p class="text-xl text-blue-200">Rejoignez HOROZON ALBASERVICE</p>
            </div>
            
            <div class="grid grid-cols-2 gap-6 mt-8">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <svg class="w-8 h-8 mx-auto mb-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p class="text-center text-white font-medium">Produits de qualité</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <svg class="w-8 h-8 mx-auto mb-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-center text-white font-medium">Livraison rapide</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <svg class="w-8 h-8 mx-auto mb-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <p class="text-center text-white font-medium">Compte sécurisé</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <svg class="w-8 h-8 mx-auto mb-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-center text-white font-medium">Commandes faciles</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Side -->
    <div class="auth-form-container">
        <form method="POST" class="auth-form" id="registerForm">
            <div class="auth-logo">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                    H
                </div>
                <span class="text-2xl font-bold text-gray-900">Créer un compte</span>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label" for="name">Nom complet <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" class="form-input" placeholder="Votre nom complet" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Téléphone</label>
                <input type="tel" id="phone" name="phone" class="form-input" placeholder="+243..." value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Photo de profil</label>
                <div class="profile-image-upload">
                    <div class="image-preview-container" id="imagePreviewContainer">
                        <div class="image-preview-placeholder" id="imagePreviewPlaceholder">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Cliquez pour ajouter une photo</span>
                        </div>
                        <img id="imagePreview" class="image-preview" src="" alt="Aperçu" style="display: none;">
                    </div>
                    <input type="file" id="profile_image" name="profile_image" class="profile-image-input" accept="image/*" onchange="previewProfileImage(this)">
                </div>
                <p class="text-sm text-gray-500 mt-1">Formats: JPG, PNG, GIF, WebP (max 2MB)</p>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Mot de passe <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                    <button type="button" onclick="togglePassword('password', 'togglePasswordBtn')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" id="togglePasswordBtn" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label class="form-label">Vérification <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2">
                    <span id="captchaQuestion" class="text-lg font-semibold text-gray-700">
                        <?php $n1 = rand(1,9); $n2 = rand(1,9); echo $n1 . ' + ' . $n2 . ' = ?'; ?>
                    </span>
                    <input type="hidden" name="captcha_real" value="<?= $n1 + $n2 ?>">
                    <input type="number" name="captcha_answer" class="form-input" style="width: 80px;" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full" style="margin-top: 1rem;">
                Créer mon compte
            </button>

            <p class="text-center mt-6 text-gray-600">
                Déjà un compte? 
                <a href="login.php" class="text-blue-600 font-semibold hover:underline">Se connecter</a>
            </p>
        </form>
    </div>
</div>

<script>
function togglePassword(inputId, btnId) {
    const input = document.getElementById(inputId);
    const btn = document.getElementById(btnId);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

function previewProfileImage(input) {
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('imagePreviewPlaceholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Make image preview clickable
document.getElementById('imagePreviewContainer').addEventListener('click', function() {
    document.getElementById('profile_image').click();
});
</script>

<?php include 'components/footer.php'; ?>
