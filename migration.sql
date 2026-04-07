-- =====================================================
-- MIGRATION — Exécuter dans phpMyAdmin sur blog_mvc
-- (ne supprime pas les données existantes)
-- =====================================================

USE blog_mvc;

-- Colonne vues sur les posts (Bloc 1)
ALTER TABLE posts ADD COLUMN IF NOT EXISTS views INT NOT NULL DEFAULT 0;

-- Colonnes utilisateurs (Bloc 2)
ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS remember_token VARCHAR(64) DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS remember_expires DATETIME DEFAULT NULL;

-- Table tags (Bloc 3)
CREATE TABLE IF NOT EXISTS tags (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Table post_tags (Bloc 3)
CREATE TABLE IF NOT EXISTS post_tags (
    post_id INT NOT NULL,
    tag_id  INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id)  REFERENCES tags(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table bookmarks (Bloc 3)
CREATE TABLE IF NOT EXISTS bookmarks (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    post_id    INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bookmark (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table discussions technologie (fil par techno)
CREATE TABLE IF NOT EXISTS tech_discussions (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    tech_slug  VARCHAR(100) NOT NULL,
    user_id    INT NOT NULL,
    content    TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_tech_slug (tech_slug)
) ENGINE=InnoDB;

-- Table notifications (Bloc 3)
CREATE TABLE IF NOT EXISTS notifications (
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
