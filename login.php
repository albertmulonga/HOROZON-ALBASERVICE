<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pageTitle = 'Connexion - HORIZON ALBASERVICE';
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

<div class="login-page-wrapper">
    <!-- Left Side - Form -->
    <div class="login-form-section">
        <div class="login-form-container">
            <form method="POST" class="login-form-modern" id="loginForm">
                <div class="auth-logo-modern">
                    <div class="logo-icon-modern">
                        H
                    </div>
                    <span class="brand-name">HORIZON ALBASERVICE</span>
                </div>
                
                <h1 class="form-title">Bienvenue</h1>
                <p class="form-subtitle">Connectez-vous pour accéder à votre compte</p>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-icon-wrapper">
                        <svg class="input-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <input type="email" id="email" name="email" class="form-input-modern with-icon" placeholder="votre@email.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Mot de passe</label>
                    <div class="input-icon-wrapper">
                        <svg class="input-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input type="password" id="password" name="password" class="form-input-modern with-icon" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Vérification</label>
                    <div class="captcha-wrapper">
                        <span id="captchaQuestion" class="captcha-display">
                            <?php $n1 = rand(1,9); $n2 = rand(1,9); echo $n1 . ' + ' . $n2 . ' = ?'; ?>
                        </span>
                        <input type="hidden" name="captcha_real" value="<?= $n1 + $n2 ?>">
                        <input type="number" name="captcha_answer" class="captcha-input" placeholder="?" required>
                    </div>
                </div>

                <button type="submit" class="btn-login-modern">
                    Se connecter
                    <svg class="btn-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>

                <p class="register-link">
                    Pas encore de compte? 
                    <a href="register.php">Créer un compte</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Right Side - Video/Visual -->
    <div class="login-visual-section">
        <video autoplay muted loop playsinline class="login-bg-video">
            <source src="https://videos.pexels.com/video-files/3195394/3195394-uhd_2560_1440_25fps.mp4" type="video/mp4">
        </video>
        <div class="login-video-overlay"></div>
        <div class="login-visual-content">
            <div class="brand-showcase">
                <div class="brand-logo-showcase">
                    <span class="logo-text">H</span>
                </div>
                <h2 class="brand-title">HORIZON<br>ALBASERVICE</h2>
                <p class="brand-tagline">Votre boutique de confiance</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-item-modern">
                    <div class="feature-icon-modern">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <span>Produits de qualité</span>
                </div>
                <div class="feature-item-modern">
                    <div class="feature-icon-modern">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span>Livraison rapide</span>
                </div>
                <div class="feature-item-modern">
                    <div class="feature-icon-modern">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span>Paiement sécurisé</span>
                </div>
                <div class="feature-item-modern">
                    <div class="feature-icon-modern">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span>Support 24/7</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.login-page-wrapper {
    display: flex;
    min-height: calc(100vh - 80px);
}

.login-form-section {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
}

.login-form-container {
    width: 100%;
    max-width: 440px;
}

.login-form-modern {
    background: white;
    padding: 2.5rem;
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
}

.auth-logo-modern {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 2rem;
}

.logo-icon-modern {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 24px;
}

.brand-name {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
}

.form-title {
    font-size: 28px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
}

.form-subtitle {
    color: #64748b;
    margin-bottom: 2rem;
}

.form-input-modern {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #f8fafc;
}

.form-input-modern:focus {
    outline: none;
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.input-icon-wrapper {
    position: relative;
}

.input-icon-svg {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    color: #94a3b8;
}

.form-input-modern.with-icon {
    padding-left: 48px;
}

.captcha-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.captcha-display {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 16px;
    color: #334155;
}

.captcha-input {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #f8fafc;
}

.captcha-input:focus {
    outline: none;
    border-color: #3b82f6;
    background: white;
}

.btn-login-modern {
    width: 100%;
    padding: 16px 24px;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    margin-top: 1.5rem;
}

.btn-login-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);
}

.btn-arrow {
    width: 20px;
    height: 20px;
}

.register-link {
    text-align: center;
    margin-top: 1.5rem;
    color: #64748b;
}

.register-link a {
    color: #2563eb;
    font-weight: 600;
    text-decoration: none;
}

.register-link a:hover {
    text-decoration: underline;
}

/* Visual Section */
.login-visual-section {
    flex: 1;
    position: relative;
    overflow: hidden;
    display: none;
}

@media (min-width: 1024px) {
    .login-visual-section {
        display: block;
    }
}

.login-bg-video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.login-video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(30, 58, 138, 0.9) 0%, rgba(30, 64, 175, 0.85) 50%, rgba(29, 78, 216, 0.8) 100%);
}

.login-visual-content {
    position: relative;
    z-index: 10;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 3rem;
    text-align: center;
}

.brand-showcase {
    margin-bottom: 3rem;
}

.brand-logo-showcase {
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    backdrop-filter: blur(10px);
}

.logo-text {
    font-size: 48px;
    font-weight: 800;
    color: white;
}

.brand-title {
    font-size: 42px;
    font-weight: 800;
    color: white;
    line-height: 1.2;
    margin-bottom: 12px;
}

.brand-tagline {
    font-size: 20px;
    color: rgba(255, 255, 255, 0.8);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    max-width: 400px;
}

.feature-item-modern {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 20px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: white;
    font-weight: 500;
}

.feature-icon-modern {
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature-icon-modern svg {
    width: 22px;
    height: 22px;
}

@media (max-width: 768px) {
    .login-form-section {
        padding: 1.5rem;
    }
    
    .login-form-modern {
        padding: 1.5rem;
    }
    
    .brand-title {
        font-size: 32px;
    }
}
</style>

<?php include 'components/footer.php'; ?>
