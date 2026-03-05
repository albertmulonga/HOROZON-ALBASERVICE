<?php
/**
 * En-tête du site
 */
$user = getCurrentUser();
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
    $cartCount = array_sum(array_column($cart, 'quantity'));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'HORIZON ALBASERVICE' ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="favicon.ico">
    <script src="js/main.js" defer></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="flex items-center justify-between" style="height: 4rem;">
                <!-- Logo -->
                <a href="index.php" class="header-logo">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-600 to-blue-800 flex items-center justify-center text-white font-bold text-xl border-4 border-white shadow-lg">
                        H
                    </div>
                    <span class="header-logo-text hidden md:block">HORIZON ALBASERVICE</span>
                </a>

                <!-- Navigation -->
                <nav class="header-nav hidden md:flex">
                    <a href="index.php" class="header-nav-link <?= $_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php' ? 'active' : '' ?>">Accueil</a>
                    <a href="produits.php" class="header-nav-link <?= strpos($_SERVER['REQUEST_URI'], 'produits') !== false ? 'active' : '' ?>">Produits</a>
                    <a href="categories.php" class="header-nav-link <?= strpos($_SERVER['REQUEST_URI'], 'categories') !== false ? 'active' : '' ?>">Catégories</a>
                    <a href="promotions.php" class="header-nav-link <?= strpos($_SERVER['REQUEST_URI'], 'promotions') !== false ? 'active' : '' ?>">Promotions</a>
                    <a href="contact.php" class="header-nav-link <?= strpos($_SERVER['REQUEST_URI'], 'contact') !== false ? 'active' : '' ?>">Contact</a>
                </nav>

                <!-- Actions -->
                <div class="header-actions">
                    <!-- Cart -->
                    <a href="panier.php" class="header-cart">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <?php if ($cartCount > 0): ?>
                            <span class="header-cart-count"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>

                    <!-- User Menu -->
                    <?php if ($user): ?>
                        <div class="dropdown">
                            <button onclick="toggleDropdown()" class="flex items-center gap-2 cursor-pointer">
                                <?php if (!empty($user['profile_image'])): ?>
                                    <img src="<?= htmlspecialchars($user['profile_image']) ?>" 
                                         alt="<?= htmlspecialchars($user['name']) ?>" 
                                         class="w-8 h-8 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <svg class="w-4 h-4 text-white hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="userDropdown" class="dropdown-menu">
                                <div class="p-3 border-b">
                                    <p class="font-semibold"><?= htmlspecialchars($user['name']) ?></p>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                                </div>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <a href="admin/index.php" class="dropdown-item">Tableau de bord Admin</a>
                                    <a href="admin/produits.php" class="dropdown-item">Gérer les produits</a>
                                    <a href="admin/utilisateurs.php" class="dropdown-item">Gérer les utilisateurs</a>
                                    <a href="admin/commandes.php" class="dropdown-item">Gérer les commandes</a>
                                    <a href="admin/livreurs.php" class="dropdown-item">Gérer les livreurs</a>
                                <?php elseif ($user['role'] === 'livreur'): ?>
                                    <a href="livreur/index.php" class="dropdown-item">Tableau de bord Livreur</a>
                                <?php else: ?>
                                    <a href="client/index.php" class="dropdown-item">Mon compte</a>
                                <?php endif; ?>
                                <a href="profil.php" class="dropdown-item">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Mon profil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="logout.php" class="dropdown-item text-red-600">Déconnexion</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-white btn-sm">Connexion</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main>
