<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pageTitle = 'Inscription - HOROZON ALBASERVICE';

// Redirect if already logged in
if (isLoggedIn()) {
    $user = getCurrentUser();
    redirectToDashboard($user['role']);
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold mb-4">Inscription restreinte</h2>
                <p class="text-xl text-blue-200">Contactez l'administrateur pour créer un compte</p>
            </div>
            
            <div class="mt-12 space-y-4 text-left">
                <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-white">Contactez-nous</p>
                        <p class="text-sm text-blue-200">Envoyez un email à l'administrateur</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-white">Support disponible</p>
                        <p class="text-sm text-blue-200">Nous répondons à vos questions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Side -->
    <div class="auth-form-container">
        <div class="auth-form">
            <div class="auth-logo">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                    H
                </div>
                <span class="text-2xl font-bold text-gray-900">Comment s'inscrire?</span>
            </div>

            <div class="alert alert-info" style="background: #eff6ff; border-color: #3b82f6; color: #1e40af; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Inscription par l'administrateur uniquement</p>
                        <p class="mt-1 text-sm">Pour des raisons de sécurité, la création de compte client est reservée uniquement à l'administrateur de la plateforme.</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Comment obtenir un compte?</h3>
                <ol class="space-y-3 text-gray-700">
                    <li class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">1</span>
                        <span>Contactez l'administrateur par téléphone ou email</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">2</span>
                        <span>L'administrateur créera votre compte</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">3</span>
                        <span>Vous recevrez vos identifiants de connexion</span>
                    </li>
                </ol>
            </div>

            <div class="bg-blue-50 rounded-xl p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <strong>Contact admin:</strong><br>
                    Email: <a href="mailto:vente@gmail.com" class="underline">vente@gmail.com</a>
                </p>
            </div>

            <a href="/login.php" class="btn btn-primary w-full text-center">
                Se connecter
            </a>

            <p class="text-center mt-6 text-gray-600">
                Déjà un compte? 
                <a href="/login.php" class="text-blue-600 font-semibold hover:underline">Se connecter</a>
            </p>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>
