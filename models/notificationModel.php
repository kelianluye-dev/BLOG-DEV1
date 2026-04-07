<?php
// Gestion des notifications (like / commentaire sur un post)

// Créer une notification — on ne notifie pas quelqu'un de ses propres actions
function createNotification($pdo, $user_id, $actor_id, $post_id, $type)
{
    if ($user_id === $actor_id) return;

    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, actor_id, post_id, type)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $actor_id, $post_id, $type]);
}

// Nb de notifications non lues (pour le badge dans le header)
function getUnreadNotificationsCount($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    return (int) $stmt->fetchColumn();
}

// Toutes les notifications d'un utilisateur (30 max)
function getUserNotifications($pdo, $user_id)
{
    $sql = "
        SELECT
            notifications.id,
            notifications.type,
            notifications.is_read,
            notifications.created_at,
            users.username AS actor_name,
            users.avatar   AS actor_avatar,
            posts.title    AS post_title,
            posts.id       AS post_id
        FROM notifications
        INNER JOIN users ON users.id = notifications.actor_id
        INNER JOIN posts ON posts.id = notifications.post_id
        WHERE notifications.user_id = ?
        ORDER BY notifications.created_at DESC
        LIMIT 30
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Marquer toutes les notifications comme lues quand on visite le profil
function markAllNotificationsRead($pdo, $user_id)
{
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$user_id]);
}
?>
