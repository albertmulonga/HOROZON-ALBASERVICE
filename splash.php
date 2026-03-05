<?php
/**
 * Page d'animation (Splash Screen)
 * Affiche une animation de 18 secondes avant de rediriger vers l'accueil
 */

// Duration: 18 seconds
$splashDuration = 18;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOROZON ALBASERVICE - Chargement...</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body.splash-body {
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Background animé professionnel */
        .splash-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            z-index: -2;
        }
        
        /* Overlay avec effet de tissu/vêtements */
        .splash-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(ellipse at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            z-index: -1;
        }
        
        /* Particules flottantes (vêtements blancs) */
        .floating-clothes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        
        .cloth {
            position: absolute;
            opacity: 0.15;
            animation: floatCloth 15s infinite ease-in-out;
        }
        
        .cloth svg {
            width: 80px;
            height: 80px;
            fill: white;
        }
        
        @keyframes floatCloth {
            0%, 100% {
                transform: translateY(100vh) rotate(0deg) scale(0.5);
                opacity: 0;
            }
            10% {
                opacity: 0.15;
            }
            90% {
                opacity: 0.15;
            }
            100% {
                transform: translateY(-100px) rotate(360deg) scale(1);
                opacity: 0;
            }
        }
        
        /* Conteneur principal */
        .splash-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            text-align: center;
        }
        
        /* Logo principal animé */
        .splash-logo-container {
            position: relative;
            margin-bottom: 40px;
        }
        
        .splash-logo {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            font-weight: bold;
            color: white;
            box-shadow: 
                0 0 60px rgba(102, 126, 234, 0.5),
                0 0 100px rgba(118, 75, 162, 0.3),
                inset 0 0 30px rgba(255, 255, 255, 0.2);
            animation: pulseLogo 2s infinite ease-in-out;
            position: relative;
            z-index: 2;
        }
        
        @keyframes pulseLogo {
            0%, 100% {
                transform: scale(1);
                box-shadow: 
                    0 0 60px rgba(102, 126, 234, 0.5),
                    0 0 100px rgba(118, 75, 162, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 
                    0 0 80px rgba(102, 126, 234, 0.7),
                    0 0 120px rgba(118, 75, 162, 0.5);
            }
        }
        
        /* Anneau autour du logo */
        .splash-logo-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 220px;
            height: 220px;
            border: 3px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            animation: rotateRing 10s linear infinite;
        }
        
        .splash-logo-ring::before,
        .splash-logo-ring::after {
            content: '';
            position: absolute;
            width: 15px;
            height: 15px;
            background: #ffd700;
            border-radius: 50%;
            box-shadow: 0 0 20px #ffd700;
        }
        
        .splash-logo-ring::before {
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .splash-logo-ring::after {
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            background: #00ff88;
            box-shadow: 0 0 20px #00ff88;
        }
        
        @keyframes rotateRing {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        /* Titre principal */
        .splash-title {
            font-size: 48px;
            font-weight: 800;
            color: white;
            margin-bottom: 10px;
            text-shadow: 0 0 30px rgba(102, 126, 234, 0.5);
            animation: fadeInUp 1s ease-out;
        }
        
        .splash-title span {
            background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Sous-titre */
        .splash-subtitle {
            font-size: 20px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 40px;
            animation: fadeInUp 1s ease-out 0.3s both;
        }
        
        /* Publicités/Textes promotionnels */
        .splash-ads {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 800px;
            animation: fadeInUp 1s ease-out 0.6s both;
        }
        
        .splash-ad {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 15px 25px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .splash-ad:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-5px);
        }
        
        .splash-ad-icon {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .splash-ad strong {
            display: block;
            color: #ffd700;
            margin-bottom: 3px;
        }
        
        /* Barre de progression */
        .splash-progress-container {
            width: 300px;
            margin-top: 50px;
            animation: fadeInUp 1s ease-out 0.9s both;
        }
        
        .splash-progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
            overflow: hidden;
        }
        
        .splash-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #ffd700 100%);
            border-radius: 3px;
            animation: progressFill <?= $splashDuration ?>s linear forwards;
        }
        
        @keyframes progressFill {
            from { width: 0%; }
            to { width: 100%; }
        }
        
        .splash-timer {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            margin-top: 10px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .splash-logo {
                width: 140px;
                height: 140px;
                font-size: 60px;
            }
            
            .splash-logo-ring {
                width: 170px;
                height: 170px;
            }
            
            .splash-title {
                font-size: 32px;
            }
            
            .splash-subtitle {
                font-size: 16px;
            }
            
            .splash-ads {
                flex-direction: column;
                align-items: center;
            }
            
            .splash-ad {
                width: 100%;
                max-width: 280px;
            }
        }
    </style>
</head>
<body class="splash-body">
    <!-- Background -->
    <div class="splash-background"></div>
    <div class="splash-overlay"></div>
    
    <!-- Vetements flottants -->
    <div class="floating-clothes">
        <!-- T-shirt -->
        <div class="cloth" style="left: 10%; animation-delay: 0s;">
            <svg viewBox="0 0 24 24"><path d="M21.6 18.2L13 11.75v-.91c1.65-.49 2.8-2.17 2.43-4.05-.26-1.31-1.3-2.4-2.61-2.7C10.54 3.57 8.5 5.3 8.5 7.5h2c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5c0 .84-.69 1.52-1.53 1.5-.54-.01-.97.45-.97.99v1.76L2.4 18.2c-.91.77-.99 2.11-.16 2.93l4.14 4.14c.57.58 1.56.58 2.14 0l5.08-5.08c.2-.2.47-.31.75-.31s.55.11.75.31l5.08 5.08c.58.58 1.57.58 2.14 0l4.14-4.14c.83-.82.75-2.16-.16-2.93z"/></svg>
        </div>
        <!-- Robe -->
        <div class="cloth" style="left: 25%; animation-delay: 2s;">
            <svg viewBox="0 0 24 24"><path d="M12 2c-2.21 0-4 1.79-4 4v2H3v2h1v11c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10h1V8h-5V6c0-2.21-1.79-4-4-4zm0 2c1.1 0 2 .9 2 2v2H10V6c0-1.1.9-2 2-2z"/></svg>
        </div>
        <!-- Pantalon -->
        <div class="cloth" style="left: 40%; animation-delay: 4s;">
            <svg viewBox="0 0 24 24"><path d="M4 2h16v2H4V2zm1 4v4h2v12h2V10h6v12h2V10h2V6H3v2h2z"/></svg>
        </div>
        <!-- Chemise -->
        <div class="cloth" style="left: 55%; animation-delay: 1s;">
            <svg viewBox="0 0 24 24"><path d="M21 3H3v18h18V3zm-2 16H5V5h14v14zM7 7h4v4H7V7zm6 0h4v4h-4V7zm-6 6h4v4H7v-4zm6 0h4v4h-4v-4z"/></svg>
        </div>
        <!-- Sac -->
        <div class="cloth" style="left: 70%; animation-delay: 3s;">
            <svg viewBox="0 0 24 24"><path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/></svg>
        </div>
        <!-- Chaussure -->
        <div class="cloth" style="left: 85%; animation-delay: 5s;">
            <svg viewBox="0 0 24 24"><path d="M2 18.5c0-1.1.9-2 2-2h16c1.1 0 2 .9 2 2v3H2v-3zm2-4.5c0-.28.22-.5.5-.5h15c.28 0 .5.22.5.5v1.5H4v-1.5zm2-3.5c0-.28.22-.5.5-.5h13c.28 0 .5.22.5.5V8H6V6.5zm14.5-3c0-.83-.67-1.5-1.5-1.5H5c-.83 0-1.5.67-1.5 1.5v2h17V5z"/></svg>
        </div>
        <!--另一件T恤 -->
        <div class="cloth" style="left: 15%; animation-delay: 6s;">
            <svg viewBox="0 0 24 24"><path d="M21.6 18.2L13 11.75v-.91c1.65-.49 2.8-2.17 2.43-4.05-.26-1.31-1.3-2.4-2.61-2.7C10.54 3.57 8.5 5.3 8.5 7.5h2c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5c0 .84-.69 1.52-1.53 1.5-.54-.01-.97.45-.97.99v1.76L2.4 18.2c-.91.77-.99 2.11-.16 2.93l4.14 4.14c.57.58 1.56.58 2.14 0l5.08-5.08c.2-.2.47-.31.75-.31s.55.11.75.31l5.08 5.08c.58.58 1.57.58 2.14 0l4.14-4.14c.83-.82.75-2.16-.16-2.93z"/></svg>
        </div>
        <!--另一条裤子 -->
        <div class="cloth" style="left: 60%; animation-delay: 7s;">
            <svg viewBox="0 0 24 24"><path d="M4 2h16v2H4V2zm1 4v4h2v12h2V10h6v12h2V10h2V6H3v2h2z"/></svg>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="splash-container">
        <!-- Logo -->
        <div class="splash-logo-container">
            <div class="splash-logo-ring"></div>
            <div class="splash-logo">H</div>
        </div>
        
        <!-- Titre -->
        <h1 class="splash-title">HOROZON <span>ALBASERVICE</span></h1>
        <p class="splash-subtitle">Votre boutique en ligne de confiance à Kindu, Maniema</p>
        
        <!-- Publicités -->
        <div class="splash-ads">
            <div class="splash-ad">
                <div class="splash-ad-icon">👗</div>
                <strong>Nouveautés Mode</strong>
                Collection 2026 disponible
            </div>
            <div class="splash-ad">
                <div class="splash-ad-icon">🚚</div>
                <strong>Livraison GPS</strong>
                Suivi en temps réel
            </div>
            <div class="splash-ad">
                <div class="splash-ad-icon">📱</div>
                <strong>Paiement Mobile</strong>
                MTN Moov Airtel
            </div>
            <div class="splash-ad">
                <div class="splash-ad-icon">⭐</div>
                <strong>Qualité Garantie</strong>
                Produits authentiques
            </div>
        </div>
        
        <!-- Barre de progression -->
        <div class="splash-progress-container">
            <div class="splash-progress-bar">
                <div class="splash-progress-fill"></div>
            </div>
            <div class="splash-timer">Chargement en cours...</div>
        </div>
    </div>
    
    <!-- Redirection automatique -->
    <script>
        // Redirection après <?= $splashDuration ?> secondes
        setTimeout(function() {
            window.location.href = 'index.php';
        }, <?= $splashDuration * 1000 ?>);
        
        // Mise à jour du texte du timer
        let secondsLeft = <?= $splashDuration ?>;
        const timerElement = document.querySelector('.splash-timer');
        
        const timerInterval = setInterval(function() {
            secondsLeft--;
            if (secondsLeft > 0) {
                timerElement.textContent = 'Redirection dans ' + secondsLeft + ' secondes...';
            } else {
                clearInterval(timerInterval);
            }
        }, 1000);
    </script>
</body>
</html>
