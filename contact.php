<?php
require_once 'config/db.php';
require_once 'config/functions.php';

$pageTitle = 'Contact - HORIZON ALBASERVICE';

initDatabase();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle contact form (could be saved to database or sent via email)
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $messageText = $_POST['message'] ?? '';
    
    if (empty($name) || empty($email) || empty($messageText)) {
        $message = 'Veuillez remplir tous les champs obligatoires';
        $messageType = 'error';
    } else {
        $message = 'Merci pour votre message! Nous vous répondrons sous 24-48 heures.';
        $messageType = 'success';
    }
}

include 'components/header.php';
?>

<div class="container py-8">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-6">
        <a href="index.php" class="breadcrumb-item">Accueil</a>
        <span class="breadcrumb-separator">/</span>
        <span class="text-gray-900 font-medium">Contact</span>
    </nav>

    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Contactez-nous</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
            Vous avez des questions? N'hésitez pas à nous contacter. Notre équipe est disponible pour vous aider.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Contact Info -->
        <div class="card p-8">
            <h2 class="text-2xl font-bold mb-6">Informations de contact</h2>
            
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Adresse</h3>
                        <p class="text-gray-600"><?= getSetting('shop_address') ?? 'Kindu, Congo' ?></p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Téléphone</h3>
                        <p class="text-gray-600">+243 000 000 000</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Email</h3>
                        <p class="text-gray-600">contact@horozon.com</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Heures d'ouverture</h3>
                        <p class="text-gray-600">Lundi - Samedi: 8h00 - 20h00</p>
                        <p class="text-gray-600">Dimanche: 9h00 - 18h00</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="card p-8">
            <h2 class="text-2xl font-bold mb-6">Envoyez-nous un message</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?> mb-6">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label" for="name">Nom complet *</label>
                    <input type="text" id="name" name="name" class="form-input" required value="<?= $_POST['name'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email *</label>
                    <input type="email" id="email" name="email" class="form-input" required value="<?= $_POST['email'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="subject">Sujet</label>
                    <input type="text" id="subject" name="subject" class="form-input" value="<?= $_POST['subject'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="message">Message *</label>
                    <textarea id="message" name="message" rows="5" class="form-textarea" required><?= $_POST['message'] ?? '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    Envoyer le message
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>
