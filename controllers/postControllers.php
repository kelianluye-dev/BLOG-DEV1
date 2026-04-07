<?php
// Contrôleur principal — reçoit toutes les requêtes et décide quoi faire

// Connexion BDD + chargement des modèles
require_once 'config/database.php';

require_once 'models/userModel.php';
require_once 'models/postModels.php';
require_once 'models/technologiesModel.php';
require_once 'models/tagModel.php';
require_once 'models/techDiscussionModel.php';
require_once 'models/bookmarkModel.php';
require_once 'models/notificationModel.php';

$errorMessage = '';

// Auto-login via cookie "Se souvenir de moi"
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
    $tokenHash = hash('sha256', $_COOKIE['remember_token']);
    $remembered = getUserByRememberToken($pdo, $tokenHash);
    if ($remembered) {
        $_SESSION['user'] = [
            'id'       => $remembered['id'],
            'username' => $remembered['username'],
            'email'    => $remembered['email'],
            'role'     => $remembered['role'],
            'avatar'   => $remembered['avatar'],
        ];
    }
}

// -------------------------------------------------------
// ACTIONS GET
// -------------------------------------------------------
if (isset($_GET['action'])) {

    if ($_GET['action'] == 'showLogin') {
        require_once 'view/users/login.php';
        exit();
    }

    else if ($_GET['action'] == 'showRegister') {
        require_once 'view/users/register.php';
        exit();
    }

    else if ($_GET['action'] == 'showCreate') {
        if (isset($_SESSION['user'])) {
            require_once 'view/posts/create.php';
            exit();
        } else {
            header('Location: index.php?action=showLogin');
            exit();
        }
    }

    else if ($_GET['action'] == 'logout') {
        // On efface le cookie remember me s'il existe
        if (isset($_COOKIE['remember_token'])) {
            if (isset($_SESSION['user'])) {
                clearRememberToken($pdo, $_SESSION['user']['id']);
            }
            setcookie('remember_token', '', time() - 3600, '/');
        }
        $_SESSION = [];
        session_destroy();
        header('Location: index.php');
        exit();
    }

    else if ($_GET['action'] == 'showTechnologies') {
        $technologies = getAllTechnologies($pdo);
        require_once 'view/technologies/index.php';
        exit();
    }

    else if ($_GET['action'] == 'showTechDiscussion') {
        $techSlug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
        $technologies = getAllTechnologies($pdo);
        $techno = null;
        foreach ($technologies as $t) {
            if (techSlug($t['name']) === $techSlug) { $techno = $t; break; }
        }
        if (!$techno) { header('Location: index.php?action=showTechnologies'); exit(); }
        $messages = getTechMessages($pdo, $techSlug);
        require_once 'view/technologies/discussion.php';
        exit();
    }

    else if ($_GET['action'] == 'showProfile') {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=showLogin');
            exit();
        }
        $profileUser  = getUserById($pdo, $_SESSION['user']['id']);
        $profileStats = getUserStats($pdo, $_SESSION['user']['id']);
        $profilePosts = getPostsByUserId($pdo, $_SESSION['user']['id']);
        $bookmarks    = getUserBookmarks($pdo, $_SESSION['user']['id']);
        $notifications = getUserNotifications($pdo, $_SESSION['user']['id']);
        markAllNotificationsRead($pdo, $_SESSION['user']['id']);
        require_once 'view/users/profile.php';
        exit();
    }

    else if ($_GET['action'] == 'showEditPost' && isset($_GET['id'])) {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=showLogin');
            exit();
        }
        $post = getPostById($pdo, (int) $_GET['id']);
        if (!$post || ($post['user_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] !== 'admin')) {
            header('Location: index.php');
            exit();
        }
        $postTags = getPostTags($pdo, $post['id']);
        $tagsString = implode(', ', array_column($postTags, 'name'));
        require_once 'view/posts/edit.php';
        exit();
    }

    else if ($_GET['action'] == 'showAdmin') {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php');
            exit();
        }
        $allUsers = getAllUsers($pdo);
        require_once 'view/admin/users.php';
        exit();
    }

    // Flux RSS
    else if ($_GET['action'] == 'feed') {
        $recentPosts = getRecentPosts($pdo, 20);
        header('Content-Type: application/rss+xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<rss version="2.0"><channel>';
        echo '<title>devmah</title>';
        echo '<link>http://' . htmlspecialchars($_SERVER['HTTP_HOST']) . '/blog1/MVC/index.php</link>';
        echo '<description>Blog sur le développement web et la cybersécurité</description>';
        foreach ($recentPosts as $p) {
            echo '<item>';
            echo '<title>' . htmlspecialchars($p['title']) . '</title>';
            echo '<link>http://' . htmlspecialchars($_SERVER['HTTP_HOST']) . '/blog1/MVC/index.php?action=showPost&id=' . $p['id'] . '</link>';
            echo '<description>' . htmlspecialchars(mb_substr(strip_tags($p['content']), 0, 300)) . '...</description>';
            echo '<author>' . htmlspecialchars($p['username']) . '</author>';
            echo '<pubDate>' . date(DATE_RSS, strtotime($p['created_at'])) . '</pubDate>';
            echo '</item>';
        }
        echo '</channel></rss>';
        exit();
    }

    else if ($_GET['action'] == 'showTag' && isset($_GET['tag'])) {
        $tag_slug    = htmlspecialchars($_GET['tag']);
        $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $totalPosts  = countPostsByTag($pdo, $tag_slug);
        $totalPages  = max(1, (int) ceil($totalPosts / 10));
        $currentPage = min($currentPage, $totalPages);
        $offset      = ($currentPage - 1) * 10;
        $posts       = getPostsByTag($pdo, $tag_slug, 10, $offset);
        $categoryFilter = null;
        $searchQuery    = null;
        require_once 'view/posts/post.php';
        exit();
    }

    else if ($_GET['action'] == 'showPost' && isset($_GET['id'])) {

        $post_id = (int) $_GET['id'];
        $post = getPostById($pdo, $post_id);

        if (!$post) {
            header('Location: index.php');
            exit();
        }

        // On compte une vue à chaque fois qu'on consulte le post
        incrementPostViews($pdo, $post_id);

        $comments = getCommentsByPostId($pdo, $post_id);
        $postTags = getPostTags($pdo, $post_id);

        $userLiked      = false;
        $userBookmarked = false;
        if (isset($_SESSION['user'])) {
            $userLiked      = hasUserLiked($pdo, $post_id, $_SESSION['user']['id']);
            $userBookmarked = hasUserBookmarked($pdo, $_SESSION['user']['id'], $post_id);
        }

        require_once 'view/posts/detail.php';
        exit();
    }
}

// -------------------------------------------------------
// ACTIONS POST (formulaires)
// -------------------------------------------------------
if (isset($_POST['action'])) {

    // Connexion
    if ($_POST['action'] == 'login') {

        $email    = trim($_POST['email']);
        $password = trim($_POST['password']);
        $remember = isset($_POST['remember']);

        $user = getUserByEmail($pdo, $email);

        if ($user) {
            if (password_verify($password, $user['password'])) {

                $_SESSION['user'] = [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'email'    => $user['email'],
                    'role'     => $user['role'],
                    'avatar'   => $user['avatar'] ?? null,
                ];

                // Si la case "se souvenir de moi" est cochée, on génère un token sécurisé
                if ($remember) {
                    $token     = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $token);
                    $expires   = date('Y-m-d H:i:s', time() + 30 * 24 * 3600);
                    setRememberToken($pdo, $user['id'], $tokenHash, $expires);
                    setcookie('remember_token', $token, time() + 30 * 24 * 3600, '/', '', false, true);
                }

                header('Location: index.php');
                exit();

            } else {
                $errorMessage = 'Mot de passe incorrect.';
                require_once 'view/users/login.php';
                exit();
            }
        } else {
            $errorMessage = 'Aucun utilisateur trouvé avec cet email.';
            require_once 'view/users/login.php';
            exit();
        }
    }

    // Inscription
    else if ($_POST['action'] == 'register') {

        $username         = trim($_POST['username']);
        $email            = trim($_POST['email']);
        $password         = trim($_POST['password']);
        $password_confirm = trim($_POST['password_confirm']);

        if ($username == '' || $email == '' || $password == '' || $password_confirm == '') {
            $errorMessage = 'Tous les champs sont obligatoires.';
            require_once 'view/users/register.php';
            exit();
        }

        if (strlen($password) < 6) {
            $errorMessage = 'Le mot de passe doit faire au moins 6 caractères.';
            require_once 'view/users/register.php';
            exit();
        }

        if ($password !== $password_confirm) {
            $errorMessage = 'Les mots de passe ne correspondent pas.';
            require_once 'view/users/register.php';
            exit();
        }

        // On vérifie qu'il n'y a pas déjà un compte avec cet email
        $existingUser = getUserByEmail($pdo, $email);

        if ($existingUser) {
            $errorMessage = 'Un compte existe déjà avec cette adresse email.';
            require_once 'view/users/register.php';
            exit();
        }

        createUser($pdo, $username, $email, $password);

        header('Location: index.php?action=showLogin&registered=1');
        exit();
    }

    // Créer un post
    else if ($_POST['action'] == 'createPost') {

        if (isset($_SESSION['user'])) {

            $title   = trim($_POST['title']);
            $content = trim($_POST['content']);
            $user_id = $_SESSION['user']['id'];
            $image_path = null;

            // Traitement de l'image si un fichier est envoyé
            if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {

                if ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
                    $errorMessage = 'Une erreur est survenue lors de l\'envoi du fichier.';
                    require_once 'view/posts/create.php';
                    exit();
                }

                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $errorMessage = 'Format non autorisé. Formats acceptés : JPG, JPEG, PNG, WEBP.';
                    require_once 'view/posts/create.php';
                    exit();
                }

                $maxSize = 2 * 1024 * 1024; // 2 Mo

                if ($_FILES['image']['size'] > $maxSize) {
                    $errorMessage = 'L\'image est trop lourde. Taille maximale autorisée : 2 Mo.';
                    require_once 'view/posts/create.php';
                    exit();
                }

                // Nom unique pour éviter les conflits
                $uniqueFilename = uniqid('img_', true) . '.' . $fileExtension;
                $uploadDir = 'uploads/';
                $destination = $uploadDir . $uniqueFilename;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $errorMessage = 'Impossible de sauvegarder l\'image. Vérifiez les permissions du dossier uploads/.';
                    require_once 'view/posts/create.php';
                    exit();
                }

                $image_path = $destination;
            }

            $allowed_categories = ['developpement', 'cybersecurite'];
            $category = isset($_POST['category']) && in_array($_POST['category'], $allowed_categories)
                ? $_POST['category']
                : 'developpement';

            $technology = null;
            if ($category === 'developpement' && isset($_POST['technology']) && trim($_POST['technology']) !== '') {
                $technology = trim($_POST['technology']);
            }

            if ($title != '' && $content != '') {

                createPost($pdo, $title, $content, $image_path, $user_id, $category, $technology);
                $newPostId = (int) $pdo->lastInsertId();

                if (isset($_POST['tags'])) {
                    syncPostTags($pdo, $newPostId, trim($_POST['tags']));
                }

                header('Location: index.php');
                exit();

            } else {
                $errorMessage = 'Le titre et le contenu sont obligatoires.';
                require_once 'view/posts/create.php';
                exit();
            }

        } else {
            header('Location: index.php?action=showLogin');
            exit();
        }
    }

    // Ajouter un commentaire
    else if ($_POST['action'] == 'addComment') {

        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=showLogin');
            exit();
        }

        $post_id = (int) $_POST['post_id'];
        $content = trim($_POST['content']);

        if ($content != '') {
            addComment($pdo, $post_id, $_SESSION['user']['id'], $content);

            // Notifier le propriétaire du post
            $post = getPostById($pdo, $post_id);
            if ($post) {
                createNotification($pdo, $post['user_id'], $_SESSION['user']['id'], $post_id, 'comment');
            }
        }

        header('Location: index.php?action=showPost&id=' . $post_id);
        exit();
    }

    // Like — supporte AJAX (retourne JSON) et formulaire classique
    else if ($_POST['action'] == 'toggleLike') {

        if (!isset($_SESSION['user'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'not_logged_in']);
                exit();
            }
            header('Location: index.php?action=showLogin');
            exit();
        }

        $post_id = (int) $_POST['post_id'];
        toggleLike($pdo, $post_id, $_SESSION['user']['id']);

        $post = getPostById($pdo, $post_id);
        if ($post && hasUserLiked($pdo, $post_id, $_SESSION['user']['id'])) {
            createNotification($pdo, $post['user_id'], $_SESSION['user']['id'], $post_id, 'like');
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'liked'      => hasUserLiked($pdo, $post_id, $_SESSION['user']['id']),
                'like_count' => getLikeCount($pdo, $post_id),
            ]);
            exit();
        }

        $redirect = (isset($_POST['redirect']) && strpos($_POST['redirect'], 'index.php') === 0)
            ? $_POST['redirect'] : 'index.php';
        header('Location: ' . $redirect);
        exit();
    }

    // Supprimer un post
    else if ($_POST['action'] == 'deletePost') {

        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=showLogin');
            exit();
        }

        $post_id = (int) $_POST['post_id'];
        deletePost($pdo, $post_id, $_SESSION['user']['id'], $_SESSION['user']['role']);

        header('Location: index.php');
        exit();
    }

    // Modifier un post
    else if ($_POST['action'] == 'editPost') {

        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=showLogin');
            exit();
        }

        $post_id = (int) $_POST['post_id'];
        $post    = getPostById($pdo, $post_id);

        if (!$post || ($post['user_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] !== 'admin')) {
            header('Location: index.php');
            exit();
        }

        $title    = trim($_POST['title']);
        $content  = trim($_POST['content']);
        $category = isset($_POST['category']) && in_array($_POST['category'], ['developpement', 'cybersecurite'])
            ? $_POST['category'] : 'developpement';
        $technology = null;
        if ($category === 'developpement' && isset($_POST['technology']) && trim($_POST['technology']) !== '') {
            $technology = trim($_POST['technology']);
        }

        if ($title == '' || $content == '') {
            $errorMessage = 'Le titre et le contenu sont obligatoires.';
            $postTags   = getPostTags($pdo, $post_id);
            $tagsString = implode(', ', array_column($postTags, 'name'));
            require_once 'view/posts/edit.php';
            exit();
        }

        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp']) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
                    $filename   = uniqid('img_', true) . '.' . $ext;
                    $dest       = 'uploads/' . $filename;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                        $image_path = $dest;
                    }
                }
            }
        }

        updatePost($pdo, $post_id, $title, $content, $image_path, $category, $technology);

        if (isset($_POST['tags'])) {
            syncPostTags($pdo, $post_id, trim($_POST['tags']));
        }

        header('Location: index.php?action=showPost&id=' . $post_id);
        exit();
    }

    // Supprimer un commentaire
    else if ($_POST['action'] == 'deleteComment') {

        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=showLogin');
            exit();
        }

        $comment_id = (int) $_POST['comment_id'];
        $post_id    = (int) $_POST['post_id'];
        deleteComment($pdo, $comment_id, $_SESSION['user']['id']);

        header('Location: index.php?action=showPost&id=' . $post_id);
        exit();
    }

    // Messages dans les discussions par techno
    else if ($_POST['action'] == 'addTechMessage') {
        if (!isset($_SESSION['user'])) { header('Location: index.php?action=showLogin'); exit(); }
        $techSlug = trim($_POST['tech_slug'] ?? '');
        $content  = trim($_POST['content']   ?? '');
        if ($techSlug !== '' && $content !== '') {
            addTechMessage($pdo, $techSlug, $_SESSION['user']['id'], $content);
        }
        header('Location: index.php?action=showTechDiscussion&slug=' . urlencode($techSlug));
        exit();
    }

    else if ($_POST['action'] == 'deleteTechMessage') {
        if (!isset($_SESSION['user'])) { header('Location: index.php?action=showLogin'); exit(); }
        $id       = (int) ($_POST['id']       ?? 0);
        $techSlug = trim($_POST['tech_slug']  ?? '');
        deleteTechMessage($pdo, $id, $_SESSION['user']['id']);
        header('Location: index.php?action=showTechDiscussion&slug=' . urlencode($techSlug));
        exit();
    }

    // Bookmark
    else if ($_POST['action'] == 'toggleBookmark') {

        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=showLogin');
            exit();
        }

        $post_id = (int) $_POST['post_id'];
        toggleBookmark($pdo, $_SESSION['user']['id'], $post_id);

        $redirect = (isset($_POST['redirect']) && strpos($_POST['redirect'], 'index.php') === 0)
            ? $_POST['redirect'] : 'index.php';
        header('Location: ' . $redirect);
        exit();
    }

    // Changer le mot de passe
    else if ($_POST['action'] == 'updatePassword') {

        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=showLogin');
            exit();
        }

        $current = trim($_POST['current_password']);
        $new     = trim($_POST['new_password']);
        $confirm = trim($_POST['confirm_password']);

        $userFull = getUserByEmail($pdo, $_SESSION['user']['email']);

        if (!password_verify($current, $userFull['password'])) {
            header('Location: index.php?action=showProfile&error=' . urlencode('Mot de passe actuel incorrect.'));
            exit();
        }
        if ($new !== $confirm) {
            header('Location: index.php?action=showProfile&error=' . urlencode('Les mots de passe ne correspondent pas.'));
            exit();
        }
        if (strlen($new) < 6) {
            header('Location: index.php?action=showProfile&error=' . urlencode('Le mot de passe doit faire au moins 6 caractères.'));
            exit();
        }

        updatePassword($pdo, $_SESSION['user']['id'], $new);
        header('Location: index.php?action=showProfile&success=password');
        exit();
    }

    // Changer l'avatar
    else if ($_POST['action'] == 'updateAvatar') {

        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=showLogin');
            exit();
        }

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] == UPLOAD_ERR_NO_FILE) {
            header('Location: index.php?action=showProfile');
            exit();
        }

        if ($_FILES['avatar']['error'] != UPLOAD_ERR_OK) {
            header('Location: index.php?action=showProfile&error=' . urlencode('Erreur lors de l\'envoi.'));
            exit();
        }

        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
            header('Location: index.php?action=showProfile&error=' . urlencode('Format non autorisé.'));
            exit();
        }
        if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            header('Location: index.php?action=showProfile&error=' . urlencode('Image trop lourde. Maximum 2 Mo.'));
            exit();
        }

        if (!is_dir('uploads/avatars')) {
            mkdir('uploads/avatars', 0755, true);
        }

        $filename = uniqid('avatar_', true) . '.' . $ext;
        $dest     = 'uploads/avatars/' . $filename;

        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
            header('Location: index.php?action=showProfile&error=' . urlencode('Impossible de sauvegarder l\'avatar.'));
            exit();
        }

        updateAvatar($pdo, $_SESSION['user']['id'], $dest);
        $_SESSION['user']['avatar'] = $dest;
        header('Location: index.php?action=showProfile&success=avatar');
        exit();
    }

    // Admin — supprimer un utilisateur
    else if ($_POST['action'] == 'adminDeleteUser') {

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php');
            exit();
        }

        $user_id = (int) $_POST['user_id'];
        if ($user_id !== $_SESSION['user']['id']) {
            adminDeleteUser($pdo, $user_id);
        }

        header('Location: index.php?action=showAdmin');
        exit();
    }

    // Admin — changer le rôle d'un utilisateur
    else if ($_POST['action'] == 'updateUserRole') {

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php');
            exit();
        }

        $user_id = (int) $_POST['user_id'];
        $role    = trim($_POST['role']);
        if ($user_id !== $_SESSION['user']['id']) {
            updateUserRole($pdo, $user_id, $role);
        }

        header('Location: index.php?action=showAdmin');
        exit();
    }
}

// -------------------------------------------------------
// AFFICHAGE PAR DÉFAUT : liste des posts
// -------------------------------------------------------

$categoryFilter = null;
if (isset($_GET['category']) && in_array($_GET['category'], ['developpement', 'cybersecurite'])) {
    $categoryFilter = $_GET['category'];
}

$searchQuery = isset($_GET['search']) && trim($_GET['search']) !== '' ? trim($_GET['search']) : null;

// Pagination : 10 posts par page
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$totalPosts  = countPosts($pdo, $categoryFilter, $searchQuery);
$totalPages  = max(1, (int) ceil($totalPosts / 10));
$currentPage = min($currentPage, $totalPages);
$offset      = ($currentPage - 1) * 10;

$posts = getAllPosts($pdo, $categoryFilter, $searchQuery, 10, $offset);

require_once 'view/posts/post.php';
?>
