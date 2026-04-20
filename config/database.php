<?php                                                                                                                               $host     = getenv('MYSQL_ADDON_HOST')     ?: 'localhost';
  $dbname   = getenv('MYSQL_ADDON_DB')       ?: 'blog_mvc';                                                                           $username = getenv('MYSQL_ADDON_USER')     ?: 'root';
  $password = getenv('MYSQL_ADDON_PASSWORD') ?: '';

  try {
      $pdo = new PDO(
          "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
          $username,
          $password
      );
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
      die('Erreur de connexion : ' . $e->getMessage());
  }
  ?>
