/* ========================================================= */
/*  BASE DE DONNÉES : BLOG MVC                               */
/* ========================================================= */

/*
   Suppression de la base si elle existe déjà.
   ATTENTION : supprime toutes les données.
*/
DROP DATABASE IF EXISTS blog_mvc;

/*
   Création de la base de données.
   utf8mb4 = gestion complète des caractères (accents, emojis, etc.)
*/
CREATE DATABASE blog_mvc
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

/*
   On sélectionne la base
*/
USE blog_mvc;

/* ========================================================= */
/*  TABLE : users                                            */
/* ========================================================= */

/*
   Cette table contient les utilisateurs du site.
*/
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,

    /* Nom d'utilisateur */
    username VARCHAR(50) NOT NULL UNIQUE,

    /* Email */
    email VARCHAR(100) NOT NULL UNIQUE,

    /*
       Mot de passe haché (généré en PHP avec password_hash())
       On prévoit 255 caractères pour être large.
    */
    password VARCHAR(255) NOT NULL,

    /* Rôle utilisateur */
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',

    /* Photo de profil */
    avatar VARCHAR(255) DEFAULT NULL,

    /* Token "se souvenir de moi" */
    remember_token VARCHAR(64) DEFAULT NULL,
    remember_expires DATETIME DEFAULT NULL,

    /* Date de création */
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

/* ========================================================= */
/*  TABLE : posts                                            */
/* ========================================================= */

/*
   Cette table contient les articles / posts.
*/
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,

    /* Auteur du post */
    user_id INT NOT NULL,

    /* Titre */
    title VARCHAR(150) NOT NULL,

    /* Contenu */
    content TEXT NOT NULL,

    /* Chemin vers image */
    image_path VARCHAR(255) DEFAULT NULL,

    /* Catégorie du post */
    category ENUM('developpement', 'cybersecurite') NOT NULL DEFAULT 'developpement',

    /* Technologie associée (développement uniquement) */
    technology VARCHAR(100) DEFAULT NULL,

    /* Compteur de vues */
    views INT NOT NULL DEFAULT 0,

    /* Dates */
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    /* Clé étrangère */
    CONSTRAINT fk_posts_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

/* ========================================================= */
/*  TABLE : comments                                         */
/* ========================================================= */

/*
   Table des commentaires
*/
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,

    /* Post concerné */
    post_id INT NOT NULL,

    /* Auteur du commentaire */
    user_id INT NOT NULL,

    /* Contenu */
    content TEXT NOT NULL,

    /* Date */
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    /* Relations */
    CONSTRAINT fk_comments_post
        FOREIGN KEY (post_id)
        REFERENCES posts(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_comments_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

/* ========================================================= */
/*  TABLE : likes                                            */
/* ========================================================= */

/*
   Table des likes
*/
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,

    /* Post liké */
    post_id INT NOT NULL,

    /* Utilisateur */
    user_id INT NOT NULL,

    /* Date */
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    /* Relations */
    CONSTRAINT fk_likes_post
        FOREIGN KEY (post_id)
        REFERENCES posts(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_likes_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    /*
       Empêche un utilisateur de liker plusieurs fois le même post
    */
    CONSTRAINT unique_like UNIQUE (user_id, post_id)
) ENGINE=InnoDB;

/* ========================================================= */
/*  TABLE : tags                                             */
/* ========================================================= */
CREATE TABLE tags (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)  NOT NULL UNIQUE,
    slug VARCHAR(50)  NOT NULL UNIQUE
) ENGINE=InnoDB;

/* ========================================================= */
/*  TABLE : post_tags  (many-to-many posts <-> tags)         */
/* ========================================================= */
CREATE TABLE post_tags (
    post_id INT NOT NULL,
    tag_id  INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id)  REFERENCES tags(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

/* ========================================================= */
/*  TABLE : bookmarks                                        */
/* ========================================================= */
CREATE TABLE bookmarks (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    post_id    INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bookmark (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

/* ========================================================= */
/*  TABLE : notifications                                    */
/* ========================================================= */
CREATE TABLE notifications (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    actor_id   INT NOT NULL,
    post_id    INT NOT NULL,
    type       ENUM('comment','like') NOT NULL,
    is_read    TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id)  REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

/* ========================================================= */
/*  MIGRATIONS — exécuter si la base existe déjà             */
/* ========================================================= */
-- ALTER TABLE posts ADD COLUMN views INT NOT NULL DEFAULT 0;
-- ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE users ADD COLUMN remember_token VARCHAR(64) DEFAULT NULL;
-- ALTER TABLE users ADD COLUMN remember_expires DATETIME DEFAULT NULL;
-- CREATE TABLE tags (...) — voir ci-dessus
-- CREATE TABLE post_tags (...) — voir ci-dessus
-- CREATE TABLE bookmarks (...) — voir ci-dessus
-- CREATE TABLE notifications (...) — voir ci-dessus