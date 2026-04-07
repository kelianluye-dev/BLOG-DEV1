<?php

function getAllTechnologies($pdo)
{
    $sql = "SELECT * FROM technologies ORDER BY category ASC, name ASC";
    $statement = $pdo->query($sql);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getTechnologiesByCategory($pdo, $category)
{
    $sql = "SELECT * FROM technologies WHERE category = ? ORDER BY name ASC";
    $statement = $pdo->prepare($sql);
    $statement->execute([$category]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function searchTechnologies($pdo, $search)
{
    $sql = "SELECT * FROM technologies WHERE name LIKE ? OR description LIKE ? ORDER BY category ASC, name ASC";
    $term = '%' . $search . '%';
    $statement = $pdo->prepare($sql);
    $statement->execute([$term, $term]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
?>
