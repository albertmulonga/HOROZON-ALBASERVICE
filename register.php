<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pageTitle = 'Inscription - HOROZON ALBASERVICE';
$error = '';

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
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $latitude = $_POST['latitude'] ?? '';
    $longitude = $_POST['longitude'] ?? '';
    $captchaAnswer = $_POST['captcha_answer'] ?? '';
    $captchaReal = $_POST['captcha_real'] ?? '';
    
    // Verify captcha
    if ($captchaAnswer !== $captchaReal) {
        $error = 'Réponse incorrecte. Veuillez résoudre le problème mathématique.';
    } elseif (empty($name) || empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs obligatoires';
    } elseif ($password !== $confirmPassword) {
        $error = 'Les mots de passe ne correspondent pas';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères';
    } else {
        // Handle profile image upload
        $profileImage = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = uniqid() . '_' . basename($_FILES['profile_image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                $profileImage = $targetPath;
            }
        }
        
        $result = createUser($name, $email, $phone, $password, 'client', $address, $city, $profileImage, $latitude, $longitude);
        
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            // Redirect to client dashboard
            header('Location: /client/index.php');
            exit;
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
            <div class="text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold mb-4">Rejoignez-nous</h2>
                <p class="text-xl text-blue-200">Créez votre compte et commencez vos achats</p>
            </div>
            
            <div class="mt-12 space-y-4 text-left">
                <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-white">Facile à utiliser</p>
                        <p class="text-sm text-blue-200">Navigation simple et rapide</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-white">Paiement sécurisé</p>
                        <p class="text-sm text-blue-200">Transactions sécurisées</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-white">Livraison rapide</p>
                        <p class="text-sm text-blue-200">Recevez vos commandes vite</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Side -->
    <div class="auth-form-container">
        <form method="POST" class="auth-form" id="registerForm" enctype="multipart/form-data">
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

            <!-- Profile Photo -->
            <div class="form-group">
                <label class="form-label">Photo de profil</label>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                        <img id="profilePreview" src="#" alt="Aperçu" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                        <svg id="profilePlaceholder" class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <input type="file" name="profile_image" accept="image/*" onchange="previewProfileImage(this)" class="text-sm">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="name">Nom complet *</label>
                <input type="text" id="name" name="name" class="form-input" placeholder="Votre nom" required value="<?= $_POST['name'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" required value="<?= $_POST['email'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Téléphone</label>
                <input type="tel" id="phone" name="phone" class="form-input" placeholder="+243 ..." value="<?= $_POST['phone'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Adresse</label>
                <input type="text" id="address" name="address" class="form-input" placeholder="Votre adresse" value="<?= $_POST['address'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="city">Ville</label>
                <input type="text" id="city" name="city" class="form-input" placeholder="Votre ville" value="<?= $_POST['city'] ?? '' ?>">
            </div>

            <!-- GPS Coordinates (hidden, auto-filled) -->
            <input type="hidden" id="latitude" name="latitude" value="<?= $_POST['latitude'] ?? '' ?>">
            <input type="hidden" id="longitude" name="longitude" value="<?= $_POST['longitude'] ?? '' ?>">

            <div class="form-group">
                <label class="form-label">Vérification</label>
                <div class="flex items-center gap-2">
                    <span id="captchaQuestion" class="text-lg font-semibold text-gray-700">
                        <?php $n1 = rand(1,9); $n2 = rand(1,9); echo $n1 . ' + ' . $n2 . ' = ?'; ?>
                    </span>
                    <input type="hidden" name="captcha_real" value="<?= $n1 + $n2 ?>">
                    <input type="number" name="captcha_answer" class="form-input" style="width: 80px;" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Mot de passe *</label>
                <div class="relative">
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                    <button type="button" id="togglePassword" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirmer le mot de passe *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary w-full" style="margin-top: 1rem;">
                Créer mon compte
            </button>

            <p class="text-center mt-6 text-gray-600">
                Déjà un compte? 
                <a href="/login.php" class="text-blue-600 font-semibold hover:underline">Se connecter</a>
            </p>
        </form>
    </div>
</div>

<?php include 'components/footer.php'; ?>

<script>
    // Auto-get GPS location on page load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            getUserLocation();
        }, 1000);
    });
</script>
