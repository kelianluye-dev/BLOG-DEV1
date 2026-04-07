<?php
// Header commun à toutes les pages
// On récupère le nombre de notifs non lues pour afficher le badge

$unreadCount = 0;
if (isset($_SESSION['user'])) {
    $unreadCount = getUnreadNotificationsCount($pdo, $_SESSION['user']['id']);
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>devmah — Développement &amp; Cybersécurité</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Script avant le render pour éviter le flash du mode sombre -->
    <script>
        (function() {
            const t = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>
<body>

<header>
    <div class="header-content">
        <div class="header-title">
            <a href="index.php" style="text-decoration:none; color:inherit;"><h1>devmah</h1></a>
        </div>

        <nav>
            <a href="index.php">Accueil</a>
            <a href="index.php?action=showTechnologies">Technologies</a>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="index.php?action=showCreate">Créer un post</a>
                <a href="index.php?action=showProfile" class="nav-profile">
                    <?php if (!empty($_SESSION['user']['avatar'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['user']['avatar']); ?>" class="nav-avatar" alt="">
                    <?php endif; ?>
                    <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                    <?php if ($unreadCount > 0): ?>
                        <span class="notif-badge"><?php echo $unreadCount; ?></span>
                    <?php endif; ?>
                </a>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="index.php?action=showAdmin" class="nav-admin">Admin</a>
                <?php endif; ?>
                <a href="index.php?action=logout">Déconnexion</a>
            <?php else: ?>
                <a href="index.php?action=showLogin">Connexion</a>
                <a href="index.php?action=showRegister">Inscription</a>
            <?php endif; ?>
        </nav>

        <button class="theme-toggle" onclick="toggleTheme()" title="Mode sombre / clair" aria-label="Changer le thème">
            <span id="theme-icon">🌙</span>
        </button>
    </div>
</header>

<script>
function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme');
    const next    = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    document.getElementById('theme-icon').textContent = next === 'dark' ? '☀️' : '🌙';
}
document.addEventListener('DOMContentLoaded', function() {
    const t = document.documentElement.getAttribute('data-theme');
    document.getElementById('theme-icon').textContent = t === 'dark' ? '☀️' : '🌙';
});
</script>
