<?php
/**
 * Pied de page du site
 */
$shopName = getSetting('shop_name') ?? 'HOROZON ALBASERVICE';
$shopAddress = getSetting('shop_address') ?? 'Kindu, Congo';
?>

    </main>

    <footer class="footer">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg flex items-center justify-center text-white font-bold">
                            H
                        </div>
                        <span class="text-xl font-bold text-white"><?= htmlspecialchars($shopName) ?></span>
                    </div>
                    <p class="text-gray-400">Votre boutique de confiance pour des produits de qualité à <?= htmlspecialchars($shopAddress) ?>.</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="footer-title">Liens rapides</h4>
                    <ul class="space-y-2">
                        <li><a href="/produits.php" class="footer-link">Produits</a></li>
                        <li><a href="/categories.php" class="footer-link">Catégories</a></li>
                        <li><a href="/contact.php" class="footer-link">Contact</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h4 class="footer-title">Catégories</h4>
                    <ul class="space-y-2">
                        <li><a href="/categories.php" class="footer-link">Vêtements</a></li>
                        <li><a href="/categories.php" class="footer-link">Sacs</a></li>
                        <li><a href="/categories.php" class="footer-link">Chaussures</a></li>
                        <li><a href="/categories.php" class="footer-link">Accessoires</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="footer-title">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <?= htmlspecialchars($shopAddress) ?>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            +243 000 000 000
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            contact@horozon.com
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($shopName) ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
