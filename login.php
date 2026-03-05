<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pageTitle = 'Connexion - HOROZON ALBASERVICE';
$error = '';

// Redirect if already logged in
if (isLoggedIn()) {
    $user = getCurrentUser();
    redirectToDashboard($user['role']);
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $captchaAnswer = $_POST['captcha_answer'] ?? '';
    $captchaReal = $_POST['captcha_real'] ?? '';
    
    // Verify captcha
    if ($captchaAnswer !== $captchaReal) {
        $error = 'Réponse incorrecte. Veuillez résoudre le problème mathématique.';
    } else {
        $result = loginUser($email, $password);
        
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            // Redirect based on role
            redirectToDashboard($result['user']['role']);
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold mb-4">HOROZON ALBASERVICE</h2>
                <p class="text-xl text-blue-200">Votre boutique de confiance</p>
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
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <svg class="w-8 h-8 mx-auto mb-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <p class="text-center text-white font-medium">Support 24/7</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Side -->
    <div class="auth-form-container">
        <form method="POST" class="auth-form" id="loginForm">
            <div class="auth-logo">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                    H
                </div>
                <span class="text-2xl font-bold text-gray-900">Connexion</span>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Mot de passe</label>
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
                <label class="form-label">Vérification</label>
                <div class="flex items-center gap-2">
                    <span id="captchaQuestion" class="text-lg font-semibold text-gray-700">
                        <?php $n1 = rand(1,9); $n2 = rand(1,9); echo $n1 . ' + ' . $n2 . ' = ?'; ?>
                    </span>
                    <input type="hidden" name="captcha_real" value="<?= $n1 + $n2 ?>">
                    <input type="number" name="captcha_answer" class="form-input" style="width: 80px;" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full" style="margin-top: 1rem;">
                Se connecter
            </button>

            <p class="text-center mt-6 text-gray-600">
                Pas encore de compte? 
                <a href="/register.php" class="text-blue-600 font-semibold hover:underline">Créer un compte</a>
            </p>

            <div class="mt-6 p-4 bg-gray-100 rounded-lg">
                <p class="text-sm text-gray-600 font-medium mb-2">Compte admin par défaut:</p>
                <p class="text-sm text-gray-500">Email: <strong>vente@gmail.com</strong></p>
                <p class="text-sm text-gray-500">Mot de passe: <strong>admin.com</strong></p>
            </div>
        </form>
    </div>
</div>

<?php include 'components/footer.php'; ?>
