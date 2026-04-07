# devmah — Blog MVC en PHP

**devmah** est une plateforme de blog communautaire dédiée au développement web et à la cybersécurité. Le projet a été conçu et développé de zéro en PHP pur, sans aucun framework, en suivant rigoureusement l'architecture **MVC (Modèle - Vue - Contrôleur)**.

Ce projet a été réalisé dans le cadre du TP Blog en 1ère année de **BTS SIO option SLAM**.

---

## Ce que fait le projet

devmah permet à des utilisateurs de s'inscrire, publier des articles, interagir avec la communauté et découvrir des ressources autour de deux grandes thématiques : le **développement** et la **cybersécurité**.

### Fonctionnalités principales

**Authentification**
- Inscription avec validation des champs et vérification des doublons
- Connexion sécurisée avec hachage des mots de passe (`password_hash` / `password_verify`)
- Option "Se souvenir de moi" via cookie sécurisé (token hashé en SHA-256, durée 30 jours)
- Changement de mot de passe depuis le profil

**Articles**
- Création d'articles avec titre, contenu, catégorie (`développement` ou `cybersécurité`) et technologie associée
- Upload d'image de couverture (JPG, PNG, WEBP — max 2 Mo)
- Modification et suppression de ses propres articles
- Compteur de vues automatique à chaque consultation
- Système de tags : associer des mots-clés à un article, filtrer par tag

**Interactions**
- Système de likes (avec support AJAX — pas de rechargement de page)
- Commentaires sur les articles
- Bookmarks (sauvegarder un article en favori)
- Notifications : l'auteur est notifié quand quelqu'un like ou commente son article

**Profil utilisateur**
- Page de profil avec statistiques (nombre d'articles, likes reçus...)
- Liste des articles publiés et des bookmarks
- Changement d'avatar (upload d'image)

**Espace Technologies**
- Section dédiée aux technologies (PHP, JavaScript, Python, etc.)
- Salon de discussion par technologie : les utilisateurs peuvent échanger en temps réel dans un fil de messages propre à chaque techno

**Recherche & Navigation**
- Recherche plein texte sur les articles
- Filtre par catégorie
- Pagination (10 articles par page)

**Administration**
- Panneau admin réservé aux comptes avec le rôle `admin`
- Voir tous les utilisateurs inscrits
- Supprimer un compte utilisateur
- Changer le rôle d'un utilisateur (user / admin)
- Les admins peuvent supprimer n'importe quel article ou commentaire

**Flux RSS**
- Flux RSS disponible sur `/index.php?action=feed`
- Expose les 20 derniers articles au format RSS 2.0

---

## Stack technique

| Composant | Technologie |
|---|---|
| Langage backend | PHP 8 (sans framework) |
| Base de données | MySQL via PDO |
| Serveur local | XAMPP (Apache + MySQL) |
| Frontend | HTML5, CSS3, JavaScript vanilla |
| Architecture | MVC fait main |
| Sécurité | `password_hash`, tokens SHA-256, `htmlspecialchars`, PDO préparé |

---

## Architecture du projet

```
MVC/
├── index.php                      # Point d'entrée unique — routing via $_GET['action']
├── config/
│   └── database.php               # Connexion PDO à MySQL
├── controllers/
│   └── postControllers.php        # Toute la logique : routing, actions GET et POST
├── models/
│   ├── postModels.php             # CRUD articles, likes, commentaires, vues
│   ├── userModel.php              # Inscription, connexion, avatar, remember token
│   ├── bookmarkModel.php          # Ajout / suppression de favoris
│   ├── notificationModel.php      # Création et lecture des notifications
│   ├── tagModel.php               # Gestion des tags et association aux articles
│   ├── techDiscussionModel.php    # Messages dans les salons par technologie
│   └── technologiesModel.php      # Liste des technologies disponibles
├── view/
│   ├── posts/
│   │   ├── post.php               # Page d'accueil — liste des articles
│   │   ├── detail.php             # Page d'un article (commentaires, likes...)
│   │   ├── create.php             # Formulaire de création d'article
│   │   └── edit.php               # Formulaire de modification
│   ├── users/
│   │   ├── login.php              # Page de connexion
│   │   ├── register.php           # Page d'inscription
│   │   └── profile.php            # Page de profil utilisateur
│   ├── admin/
│   │   └── users.php              # Panneau d'administration
│   ├── technologies/
│   │   ├── index.php              # Liste des technologies
│   │   └── discussion.php         # Salon de discussion par technologie
│   └── partial/                   # Composants réutilisables (header, footer, nav...)
├── assets/
│   ├── css/                       # Feuilles de style
│   └── js/                        # Scripts JavaScript (likes AJAX, etc.)
├── uploads/                       # Images des articles
│   └── avatars/                   # Avatars des utilisateurs
├── blog.sql                       # Base de données complète
├── migration.sql                  # Scripts de mise à jour de la BDD
├── technologies.sql               # Données des technologies
└── user_post.sql                  # Table de relation utilisateurs / articles
```

---

## Installation

### Prérequis
- XAMPP installé avec Apache et MySQL actifs
- Un navigateur web

### Étapes

1. Cloner ou copier le projet dans `C:/xampp/htdocs/blog/`

2. Ouvrir **phpMyAdmin** : [http://localhost/phpmyadmin](http://localhost/phpmyadmin)

3. Créer une base de données nommée `blog_mvc`

4. Importer le fichier `blog.sql` dans cette base

5. Ouvrir le projet dans le navigateur : [http://localhost/blog/MVC/](http://localhost/blog/MVC/)

---

## Sécurité

- Les mots de passe sont hachés avec `password_hash()` (bcrypt)
- Le cookie "Se souvenir de moi" utilise un token aléatoire de 64 caractères hashé en SHA-256 côté base de données
- Toutes les données affichées passent par `htmlspecialchars()` pour éviter les failles XSS
- Les requêtes SQL utilisent des requêtes préparées PDO pour éviter les injections SQL
- Les uploads sont filtrés par extension et limités en taille (2 Mo max)
- Les actions sensibles (admin, édition, suppression) vérifient le rôle et l'identité de l'utilisateur

---

## Auteur

Projet réalisé en 1ère année **BTS SIO SLAM**.
