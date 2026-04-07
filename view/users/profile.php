<?php require_once 'view/partial/header.php'; ?>

<main>

    <!-- PROFIL HEADER -->
    <div class="profile-card">
        <div class="profile-avatar-wrap">
            <?php if (!empty($profileUser['avatar'])): ?>
                <img src="<?php echo htmlspecialchars($profileUser['avatar']); ?>" class="profile-avatar-img" alt="Avatar">
            <?php else: ?>
                <div class="profile-avatar-placeholder"><?php echo strtoupper(mb_substr($profileUser['username'], 0, 1)); ?></div>
            <?php endif; ?>
        </div>
        <div class="profile-info">
            <h2><?php echo htmlspecialchars($profileUser['username']); ?></h2>
            <p class="profile-email"><?php echo htmlspecialchars($profileUser['email']); ?></p>
            <p class="meta">Membre depuis <?php echo getRelativeDate($profileUser['created_at']); ?></p>
            <?php if ($profileUser['role'] === 'admin'): ?>
                <span class="badge badge-admin">Admin</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-value"><?php echo $profileStats['post_count']; ?></span>
            <span class="stat-label">Posts</span>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $profileStats['likes_received']; ?></span>
            <span class="stat-label">Likes reçus</span>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $profileStats['comment_count']; ?></span>
            <span class="stat-label">Commentaires</span>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $profileStats['bookmark_count']; ?></span>
            <span class="stat-label">Favoris</span>
        </div>
    </div>

    <!-- MESSAGES -->
    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            <?php if ($_GET['success'] === 'password'): ?>
                <p>Mot de passe modifié avec succès.</p>
            <?php elseif ($_GET['success'] === 'avatar'): ?>
                <p>Avatar mis à jour avec succès.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="error-message"><p><?php echo htmlspecialchars($_GET['error']); ?></p></div>
    <?php endif; ?>

    <!-- PARAMÈTRES -->
    <div class="profile-settings">

        <div class="profile-section">
            <h3>Changer l'avatar</h3>
            <form action="index.php" method="POST" enctype="multipart/form-data" class="settings-form">
                <input type="file" id="avatarInput" name="avatar" accept=".jpg,.jpeg,.png,.webp" required style="display:none;">
                <label for="avatarInput" class="avatar-upload-label" id="avatarLabel">
                    <span class="avatar-upload-icon">🖼</span>
                    <span class="avatar-upload-text">Choisir une photo</span>
                </label>
                <button type="submit" name="action" value="updateAvatar">Mettre à jour</button>
            </form>
            <script>
                document.getElementById('avatarInput').addEventListener('change', function() {
                    var name = this.files[0] ? this.files[0].name : 'Choisir une photo';
                    document.querySelector('.avatar-upload-text').textContent = name;
                });
            </script>
        </div>

        <div class="profile-section">
            <h3>Changer le mot de passe</h3>
            <form action="index.php" method="POST" class="settings-form">
                <input type="password" name="current_password" placeholder="Mot de passe actuel" required>
                <input type="password" name="new_password"     placeholder="Nouveau mot de passe" required>
                <input type="password" name="confirm_password" placeholder="Confirmer" required>
                <button type="submit" name="action" value="updatePassword">Modifier</button>
            </form>
        </div>

    </div>

    <!-- NOTIFICATIONS -->
    <?php if (count($notifications) > 0): ?>
    <h3 class="section-title">Notifications récentes</h3>
    <div class="notifications-list">
        <?php foreach ($notifications as $notif): ?>
        <div class="notif-item <?php echo $notif['is_read'] ? '' : 'notif-unread'; ?>">
            <div class="notif-avatar">
                <?php if (!empty($notif['actor_avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($notif['actor_avatar']); ?>" alt="">
                <?php else: ?>
                    <div class="notif-avatar-placeholder"><?php echo strtoupper(mb_substr($notif['actor_name'], 0, 1)); ?></div>
                <?php endif; ?>
            </div>
            <div class="notif-body">
                <p>
                    <strong><?php echo htmlspecialchars($notif['actor_name']); ?></strong>
                    <?php echo $notif['type'] === 'comment' ? 'a commenté' : 'a aimé'; ?>
                    votre post "<a href="index.php?action=showPost&id=<?php echo $notif['post_id']; ?>"><?php echo htmlspecialchars($notif['post_title']); ?></a>"
                </p>
                <span class="notif-date"><?php echo getRelativeDate($notif['created_at']); ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- FAVORIS -->
    <?php if (count($bookmarks) > 0): ?>
    <h3 class="section-title">Mes favoris (<?php echo count($bookmarks); ?>)</h3>
    <?php foreach ($bookmarks as $post): ?>
    <article>
        <div class="article-header">
            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
            <span class="badge <?php echo $post['category'] === 'developpement' ? 'badge-dev' : 'badge-secu'; ?>">
                <?php echo $post['category'] === 'developpement' ? 'Développement' : 'Cybersécurité'; ?>
            </span>
        </div>
        <p class="meta">Par <strong><?php echo htmlspecialchars($post['username']); ?></strong> — <?php echo getRelativeDate($post['created_at']); ?></p>
        <p><?php echo nl2br(htmlspecialchars(mb_substr($post['content'], 0, 120))); ?>…</p>
        <div class="post-actions">
            <a href="index.php?action=showPost&id=<?php echo $post['id']; ?>" class="btn-detail">Voir</a>
            <form method="POST" action="index.php" style="display:inline;">
                <input type="hidden" name="action" value="toggleBookmark">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="hidden" name="redirect" value="index.php?action=showProfile">
                <button type="submit" class="btn-bookmark bookmarked">🔖 Retirer</button>
            </form>
        </div>
    </article>
    <?php endforeach; ?>
    <?php endif; ?>

    <!-- MES POSTS -->
    <h3 class="section-title">Mes publications (<?php echo $profileStats['post_count']; ?>)</h3>

    <?php if (count($profilePosts) > 0): ?>
        <?php foreach ($profilePosts as $post): ?>
        <article>
            <div class="article-header">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <span class="badge <?php echo $post['category'] === 'developpement' ? 'badge-dev' : 'badge-secu'; ?>">
                    <?php echo $post['category'] === 'developpement' ? 'Développement' : 'Cybersécurité'; ?>
                </span>
            </div>
            <p class="meta">
                <?php echo getRelativeDate($post['created_at']); ?>
                &nbsp;·&nbsp; ♥ <?php echo $post['like_count']; ?>
                &nbsp;·&nbsp; <?php echo $post['views']; ?> vue<?php echo $post['views'] > 1 ? 's' : ''; ?>
            </p>
            <p><?php echo nl2br(htmlspecialchars(mb_substr($post['content'], 0, 120))); ?>…</p>
            <div class="post-actions">
                <a href="index.php?action=showPost&id=<?php echo $post['id']; ?>" class="btn-detail">Voir</a>
                <a href="index.php?action=showEditPost&id=<?php echo $post['id']; ?>" class="btn-edit">Modifier</a>
                <form method="POST" action="index.php" style="display:inline;" onsubmit="return confirm('Supprimer ce post ?');">
                    <input type="hidden" name="action" value="deletePost">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit" class="btn-delete">Supprimer</button>
                </form>
            </div>
        </article>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-message"><p>Aucune publication pour le moment.</p></div>
    <?php endif; ?>

</main>

<?php require_once 'view/partial/footer.php'; ?>
