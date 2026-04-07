<?php
// Modèle utilisateurs : inscription, connexion, profil, admin, remember me

// Récupérer un utilisateur par son id (sans le mot de passe)
function getUserById($pdo, $id)
{
    $sql = "SELECT id, username, email, role, avatar, created_at FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Stats du profil : nb posts, likes reçus, commentaires, favoris
function getUserStats($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $postCount = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes INNER JOIN posts ON likes.post_id = posts.id WHERE posts.user_id = ?");
    $stmt->execute([$user_id]);
    $likesReceived = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $commentCount = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookmarks WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $bookmarkCount = (int) $stmt->fetchColumn();

    return [
        'post_count'     => $postCount,
        'likes_received' => $likesReceived,
        'comment_count'  => $commentCount,
        'bookmark_count' => $bookmarkCount,
    ];
}

// Changer le mot de passe (on le hache avant de l'enregistrer)
function updatePassword($pdo, $user_id, $new_password)
{
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt   = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashed, $user_id]);
}

// Mettre à jour l'avatar
function updateAvatar($pdo, $user_id, $avatar_path)
{
    $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->execute([$avatar_path, $user_id]);
}

// Tous les utilisateurs pour le panel admin
function getAllUsers($pdo)
{
    $sql = "
        SELECT
            users.id,
            users.username,
            users.email,
            users.role,
            users.avatar,
            users.created_at,
            COUNT(posts.id) AS post_count
        FROM users
        LEFT JOIN posts ON posts.user_id = users.id
        GROUP BY users.id
        ORDER BY users.created_at DESC
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Supprimer un utilisateur (admin)
function adminDeleteUser($pdo, $user_id)
{
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
}

// Changer le rôle d'un utilisateur
function updateUserRole($pdo, $user_id, $role)
{
    if (!in_array($role, ['user', 'admin'])) return;
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$role, $user_id]);
}

// Remember me — stocker le token hashé en base
function setRememberToken($pdo, $user_id, $token_hash, $expires)
{
    $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, remember_expires = ? WHERE id = ?");
    $stmt->execute([$token_hash, $expires, $user_id]);
}

// Retrouver un utilisateur via son token (si pas expiré)
function getUserByRememberToken($pdo, $token_hash)
{
    $stmt = $pdo->prepare("SELECT id, username, email, role, avatar FROM users WHERE remember_token = ? AND remember_expires > NOW()");
    $stmt->execute([$token_hash]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Supprimer le token lors de la déconnexion
function clearRememberToken($pdo, $user_id)
{
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL, remember_expires = NULL WHERE id = ?");
    $stmt->execute([$user_id]);
}

// Posts d'un utilisateur pour la page profil
function getPostsByUserId($pdo, $user_id)
{
    $sql = "
        SELECT
            posts.id,
            posts.title,
            posts.content,
            posts.created_at,
            posts.category,
            posts.technology,
            posts.views,
            COUNT(likes.id) AS like_count
        FROM posts
        LEFT JOIN likes ON likes.post_id = posts.id
        WHERE posts.user_id = ?
        GROUP BY posts.id
        ORDER BY posts.created_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer un utilisateur par email (pour la connexion et l'inscription)
function getUserByEmail($pdo, $email)
{
    $sql = "SELECT * FROM users WHERE email = ?";
    $statement = $pdo->prepare($sql);
    $statement->execute([$email]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}

// Créer un nouvel utilisateur (mot de passe haché)
function createUser($pdo, $username, $email, $password)
{
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $statement = $pdo->prepare($sql);
    $statement->execute([$username, $email, $hashedPassword]);
}
?>
