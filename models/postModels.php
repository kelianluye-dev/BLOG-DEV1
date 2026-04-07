<?php
// Modèle posts : toutes les fonctions qui touchent à la table posts

// Insérer un nouveau post en base
function createPost($pdo, $title, $content, $image_path, $user_id, $category, $technology)
{
    $sql = "
        INSERT INTO posts (title, content, image_path, user_id, category, technology)
        VALUES (?, ?, ?, ?, ?, ?)
    ";
    $statement = $pdo->prepare($sql);
    $statement->execute([$title, $content, $image_path, $user_id, $category, $technology]);
}

// Récupérer un post par son id avec le nom de l'auteur et le nb de likes
function getPostById($pdo, $id)
{
    $sql = "
        SELECT
            posts.id,
            posts.title,
            posts.content,
            posts.image_path,
            posts.created_at,
            posts.user_id,
            posts.category,
            posts.technology,
            posts.views,
            users.username,
            COUNT(likes.id) AS like_count
        FROM posts
        INNER JOIN users ON posts.user_id = users.id
        LEFT JOIN likes ON likes.post_id = posts.id
        WHERE posts.id = ?
        GROUP BY posts.id
    ";
    $statement = $pdo->prepare($sql);
    $statement->execute([$id]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}

// Récupérer tous les posts avec filtres optionnels (catégorie, recherche)
function getAllPosts($pdo, $category = null, $search = null, $limit = 10, $offset = 0)
{
    $conditions = [];
    $params     = [];

    if ($category !== null) {
        $conditions[] = "posts.category = ?";
        $params[]     = $category;
    }

    if ($search !== null) {
        $conditions[] = "(posts.title LIKE ? OR posts.content LIKE ?)";
        $params[]     = "%" . $search . "%";
        $params[]     = "%" . $search . "%";
    }

    $where  = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

    // Cast en int pour éviter les injections dans LIMIT/OFFSET
    $limit  = (int) $limit;
    $offset = (int) $offset;

    $sql = "
        SELECT
            posts.id,
            posts.title,
            posts.content,
            posts.image_path,
            posts.created_at,
            posts.user_id,
            posts.category,
            posts.technology,
            posts.views,
            users.username,
            COUNT(DISTINCT likes.id)    AS like_count,
            COUNT(DISTINCT comments.id) AS comment_count
        FROM posts
        INNER JOIN users    ON posts.user_id = users.id
        LEFT JOIN likes     ON likes.post_id = posts.id
        LEFT JOIN comments  ON comments.post_id = posts.id
        $where
        GROUP BY posts.id
        ORDER BY posts.created_at DESC
        LIMIT $limit OFFSET $offset
    ";

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

// Compter les posts pour la pagination
function countPosts($pdo, $category = null, $search = null)
{
    $conditions = [];
    $params     = [];

    if ($category !== null) {
        $conditions[] = "category = ?";
        $params[]     = $category;
    }

    if ($search !== null) {
        $conditions[] = "(title LIKE ? OR content LIKE ?)";
        $params[]     = "%" . $search . "%";
        $params[]     = "%" . $search . "%";
    }

    $where = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

    $sql       = "SELECT COUNT(*) FROM posts $where";
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    return (int) $statement->fetchColumn();
}

// Incrémenter le compteur de vues quand on consulte un post
function incrementPostViews($pdo, $post_id)
{
    $sql       = "UPDATE posts SET views = views + 1 WHERE id = ?";
    $statement = $pdo->prepare($sql);
    $statement->execute([$post_id]);
}

// Estimation du temps de lecture (~200 mots par minute)
function getReadingTime($content)
{
    $wordCount = str_word_count(strip_tags($content));
    return max(1, (int) ceil($wordCount / 200));
}

// Afficher la date de façon relative ("il y a 2h")
function getRelativeDate($datetime)
{
    $diff = time() - strtotime($datetime);

    if ($diff < 60)      return "il y a quelques secondes";
    if ($diff < 3600)    return "il y a " . floor($diff / 60) . " min";
    if ($diff < 86400)   return "il y a " . floor($diff / 3600) . " h";

    $days = floor($diff / 86400);
    if ($diff < 604800)  return "il y a " . $days . " jour" . ($days > 1 ? "s" : "");

    $weeks = floor($diff / 604800);
    if ($diff < 2592000) return "il y a " . $weeks . " semaine" . ($weeks > 1 ? "s" : "");

    $months = floor($diff / 2592000);
    if ($diff < 31536000) return "il y a " . $months . " mois";

    $years = floor($diff / 31536000);
    return "il y a " . $years . " an" . ($years > 1 ? "s" : "");
}

// Supprimer un post — un admin peut tout supprimer, un user seulement le sien
function deletePost($pdo, $post_id, $user_id, $role)
{
    if ($role === 'admin') {
        $sql = "DELETE FROM posts WHERE id = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute([$post_id]);
    } else {
        $sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute([$post_id, $user_id]);
    }
}

// Récupérer les commentaires d'un post avec le nom des auteurs
function getCommentsByPostId($pdo, $post_id)
{
    $sql = "
        SELECT
            comments.id,
            comments.content,
            comments.created_at,
            comments.user_id,
            users.username
        FROM comments
        INNER JOIN users ON comments.user_id = users.id
        WHERE comments.post_id = ?
        ORDER BY comments.created_at ASC
    ";
    $statement = $pdo->prepare($sql);
    $statement->execute([$post_id]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

// Ajouter un commentaire
function addComment($pdo, $post_id, $user_id, $content)
{
    $sql = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
    $statement = $pdo->prepare($sql);
    $statement->execute([$post_id, $user_id, $content]);
}

// Vérifier si l'utilisateur a déjà liké ce post
function hasUserLiked($pdo, $post_id, $user_id)
{
    $sql = "SELECT id FROM likes WHERE post_id = ? AND user_id = ?";
    $statement = $pdo->prepare($sql);
    $statement->execute([$post_id, $user_id]);
    return $statement->fetch(PDO::FETCH_ASSOC) !== false;
}

// Ajouter ou retirer un like
function toggleLike($pdo, $post_id, $user_id)
{
    if (hasUserLiked($pdo, $post_id, $user_id)) {
        $sql = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute([$post_id, $user_id]);
    } else {
        $sql = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
        $statement = $pdo->prepare($sql);
        $statement->execute([$post_id, $user_id]);
    }
}

// Retourner le nb de likes (utilisé pour la réponse AJAX)
function getLikeCount($pdo, $post_id)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
    $stmt->execute([$post_id]);
    return (int) $stmt->fetchColumn();
}

// Mettre à jour un post (si nouvelle image fournie, on la remplace)
function updatePost($pdo, $post_id, $title, $content, $image_path, $category, $technology)
{
    if ($image_path !== null) {
        $sql  = "UPDATE posts SET title=?, content=?, image_path=?, category=?, technology=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $content, $image_path, $category, $technology, $post_id]);
    } else {
        $sql  = "UPDATE posts SET title=?, content=?, category=?, technology=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $content, $category, $technology, $post_id]);
    }
}

// Supprimer un commentaire (seulement si c'est le sien)
function deleteComment($pdo, $comment_id, $user_id)
{
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt->execute([$comment_id, $user_id]);
}

// Les 20 derniers posts pour le flux RSS
function getRecentPosts($pdo, $limit = 20)
{
    $limit = (int) $limit;
    $sql = "
        SELECT posts.id, posts.title, posts.content, posts.created_at,
               posts.category, users.username
        FROM posts
        INNER JOIN users ON posts.user_id = users.id
        ORDER BY posts.created_at DESC
        LIMIT $limit
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
?>
