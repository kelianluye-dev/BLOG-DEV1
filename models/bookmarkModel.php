<?php
// Gestion des favoris (bookmarks)

// Ajouter ou retirer un favori selon l'état actuel
function toggleBookmark($pdo, $user_id, $post_id)
{
    if (hasUserBookmarked($pdo, $user_id, $post_id)) {
        $stmt = $pdo->prepare("DELETE FROM bookmarks WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$user_id, $post_id]);
        return false;
    } else {
        $stmt = $pdo->prepare("INSERT INTO bookmarks (user_id, post_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $post_id]);
        return true;
    }
}

// Vérifie si l'utilisateur a déjà mis ce post en favori
function hasUserBookmarked($pdo, $user_id, $post_id)
{
    $stmt = $pdo->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    return $stmt->fetch() !== false;
}

// Récupérer tous les favoris d'un utilisateur
function getUserBookmarks($pdo, $user_id)
{
    $sql = "
        SELECT
            posts.id, posts.title, posts.content, posts.image_path,
            posts.created_at, posts.category, posts.technology,
            posts.views, users.username, COUNT(likes.id) AS like_count,
            bookmarks.created_at AS bookmarked_at
        FROM bookmarks
        INNER JOIN posts ON bookmarks.post_id = posts.id
        INNER JOIN users ON posts.user_id = users.id
        LEFT JOIN likes ON likes.post_id = posts.id
        WHERE bookmarks.user_id = ?
        GROUP BY posts.id, bookmarks.created_at
        ORDER BY bookmarks.created_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
