# Active Context: E-commerce HOROZON ALBASERVICE (PHP + MySQL)

## Current State

**Project Status**: ✅ En cours de conversion vers PHP + MySQL

La plateforme e-commerce HOROZON ALBASERVICE est en cours de conversion de Next.js vers PHP avec MySQL.

## Recently Completed

- [x] Conversion complète du projet Next.js vers PHP
- [x] Schéma de base de données MySQL créé (8 tables)
- [x] Pages principales: index, produits, catégories, contact
- [x] Authentification: login, register, logout
- [x] Tableaux de bord: admin, client, livreur
- [x] Gestion admin: produits, utilisateurs
- [x] Panier et checkout avec paiement Mobile Money
- [x] API PHP pour les commandes
- [x] Suppression de l'ancien projet Next.js

## Current Structure (PHP)

| File/Directory | Purpose |
|----------------|---------|
| `config/db.php` | Configuration BDD MySQL + schéma |
| `config/functions.php` | Fonctions utilitaires PHP |
| `css/style.css` | Styles CSS complets |
| `js/main.js` | JavaScript client |
| `index.php` | Page d'accueil |
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
| `client/index.php` | Dashboard Client |
| `livreur/index.php` | Dashboard Livreur |

## Session History

| Date | Changes |
|------|---------|
| 2026-03-05 | Conversion complète Next.js → PHP + MySQL |

## Pour tester

1. Importer la base de données MySQL (les tables sont créées automatiquement)
2. Configurer les paramètres de connexion MySQL dans `config/db.php`
3. Compte admin: `vente@gmail.com` / `admin.com`
