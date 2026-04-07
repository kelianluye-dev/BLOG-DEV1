<?php
// Modèle pour les discussions par technologie

// Convertir le nom d'une techno en slug URL
function techSlug($name) {
    return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
}

// Récupérer tous les messages d'une discussion (triés par date croissante)
function getTechMessages($pdo, $tech_slug) {
    $sql = "
        SELECT tech_discussions.id, tech_discussions.content, tech_discussions.created_at,
               tech_discussions.user_id, users.username, users.avatar
        FROM tech_discussions
        INNER JOIN users ON tech_discussions.user_id = users.id
        WHERE tech_discussions.tech_slug = ?
        ORDER BY tech_discussions.created_at ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tech_slug]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Poster un message dans une discussion
function addTechMessage($pdo, $tech_slug, $user_id, $content) {
    $stmt = $pdo->prepare("INSERT INTO tech_discussions (tech_slug, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$tech_slug, $user_id, $content]);
}

// Supprimer un message (seulement le sien)
function deleteTechMessage($pdo, $id, $user_id) {
    $stmt = $pdo->prepare("DELETE FROM tech_discussions WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
}

function countTechMessages($pdo, $tech_slug) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tech_discussions WHERE tech_slug = ?");
    $stmt->execute([$tech_slug]);
    return (int) $stmt->fetchColumn();
}
