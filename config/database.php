<?php
// Connexion à la base de données avec PDO

$host     = 'localhost';
$dbname   = 'blog_mvc';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    // Mode exception pour voir les erreurs SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
?>
