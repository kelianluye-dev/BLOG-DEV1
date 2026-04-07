<?php
// Point d'entrée du site
// On démarre la session avant tout le reste
session_start();

// On charge le contrôleur qui va gérer toute la logique
require_once 'controllers/postControllers.php';
?>
