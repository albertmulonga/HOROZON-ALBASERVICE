# Active Context: E-commerce HIRIZON DE KINDU (PHP + MySQL)

## Current State

**Project Status**: ✅ Conversion complète vers PHP + MySQL terminée

La plateforme e-commerce HIRIZON DE KINDU est une plateforme de vente en ligne complète avec suivi GPS.

## Recently Completed

- [x] Conversion complète du projet Next.js vers PHP
- [x] Schéma de base de données MySQL créé (8 tables)
- [x] Pages principales: index, produits, catégories, contact
- [x] Authentification: login, register, logout
- [x] Tableaux de bord: admin, client, livreur
- [x] Gestion admin: produits, utilisateurs
- [x] Panier et checkout avec paiement Mobile Money
- [x] API PHP pour les commandes
- [x] Renommage en HIRIZON DE KINDU
- [x] Page Promotions
- [x] Gestion commandes admin avec validation paiement
- [x] Gestion livreurs
- [x] Suivi GPS en temps réel avec Google Maps
- [x] API de géolocalisation

## Current Structure (PHP)

| File/Directory | Purpose |
|----------------|---------|
| `config/db.php` | Configuration BDD MySQL + schéma |
| `config/functions.php` | Fonctions utilitaires PHP |
| `css/style.css` | Styles CSS complets |
| `js/main.js` | JavaScript client |
| `index.php` | Page d'accueil |
| `promotions.php` | Page des promotions |
| `login.php` | Page de connexion |
| `register.php` | Page d'inscription |
| `logout.php` | Déconnexion |
| `produits.php` | Liste des produits |
| `categories.php` | Catégories |
| `panier.php` | Panier |
| `checkout.php` | Paiement |
| `contact.php` | Contact |
| `admin/index.php` | Dashboard Admin |
| `admin/produits.php` | Gestion produits |
| `admin/utilisateurs.php` | Gestion utilisateurs |
| `admin/commandes.php` | Gestion commandes + validation paiement |
| `admin/livreurs.php` | Gestion livreurs |
| `client/index.php` | Dashboard Client |
| `client/commande.php` | Détails commande avec suivi GPS |
| `livreur/index.php` | Dashboard Livreur avec GPS |
| `api/orders/` | API commandes et géolocalisation |

## Session History

| Date | Changes |
|------|---------|
| 2026-03-05 | Conversion complète Next.js → PHP + MySQL |

## Pour tester

1. Importer la base de données MySQL (les tables sont créées automatiquement)
2. Configurer les paramètres de connexion MySQL dans `config/db.php`
3. Compte admin: `vente@gmail.com` / `admin.com`
