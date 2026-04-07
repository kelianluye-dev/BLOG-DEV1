<?php
// Modèle tags — gestion des tags (relation many-to-many avec les posts)

// Convertir un nom de tag en slug pour l'URL
function tagNameToSlug($name)
{
    $name = mb_strtolower(trim($name));
    $name = preg_replace('/[^a-z0-9\s-]/u', '', $name);
    $name = preg_replace('/[\s-]+/', '-', $name);
    return trim($name, '-');
}

// Récupérer un tag existant ou le créer s'il n'existe pas encore
function getOrCreateTag($pdo, $name)
{
    $name = mb_strtolower(trim($name));
    $slug = tagNameToSlug($name);

    if ($name === '' || $slug === '') return null;

    $stmt = $pdo->prepare("SELECT id FROM tags WHERE slug = ?");
    $stmt->execute([$slug]);
    $tag = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tag) return $tag['id'];

    $stmt = $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
    $stmt->execute([$name, $slug]);
    return (int) $pdo->lastInsertId();
}

// Synchroniser les tags d'un post : on supprime les anciens et on remet les nouveaux
function syncPostTags($pdo, $post_id, $tags_string)
{
    $stmt = $pdo->prepare("DELETE FROM post_tags WHERE post_id = ?");
    $stmt->execute([$post_id]);

    if (trim($tags_string) === '') return;

    $tag_names = array_unique(array_filter(array_map('trim', explode(',', $tags_string))));

    foreach ($tag_names as $name) {
        $tag_id = getOrCreateTag($pdo, $name);
        if ($tag_id) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$post_id, $tag_id]);
        }
    }
}

// Récupérer les tags d'un post
function getPostTags($pdo, $post_id)
{
    $sql = "
        SELECT tags.id, tags.name, tags.slug
        FROM tags
        INNER JOIN post_tags ON post_tags.tag_id = tags.id
        WHERE post_tags.post_id = ?
        ORDER BY tags.name ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Tous les tags avec leur nombre de posts (pour un nuage de tags par ex)
function getAllTagsWithCount($pdo)
{
    $sql = "
        SELECT tags.id, tags.name, tags.slug, COUNT(post_tags.post_id) AS post_count
        FROM tags
        LEFT JOIN post_tags ON post_tags.tag_id = tags.id
        GROUP BY tags.id
        ORDER BY post_count DESC, tags.name ASC
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Posts filtrés par tag avec pagination
function getPostsByTag($pdo, $tag_slug, $limit = 10, $offset = 0)
{
    $limit  = (int) $limit;
    $offset = (int) $offset;
    $sql = "
        SELECT
            posts.id, posts.title, posts.content, posts.image_path,
            posts.created_at, posts.user_id, posts.category, posts.technology,
            posts.views, users.username,
            COUNT(DISTINCT likes.id)    AS like_count,
            COUNT(DISTINCT comments.id) AS comment_count
        FROM posts
        INNER JOIN post_tags ON post_tags.post_id = posts.id
        INNER JOIN tags ON tags.id = post_tags.tag_id AND tags.slug = ?
        INNER JOIN users ON posts.user_id = users.id
        LEFT JOIN likes    ON likes.post_id    = posts.id
        LEFT JOIN comments ON comments.post_id = posts.id
        GROUP BY posts.id
        ORDER BY posts.created_at DESC
        LIMIT $limit OFFSET $offset
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tag_slug]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countPostsByTag($pdo, $tag_slug)
{
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT posts.id) FROM posts INNER JOIN post_tags ON post_tags.post_id = posts.id INNER JOIN tags ON tags.id = post_tags.tag_id AND tags.slug = ?");
    $stmt->execute([$tag_slug]);
    return (int) $stmt->fetchColumn();
}
?>
