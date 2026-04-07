<?php require_once 'view/partial/header.php'; ?>

<!-- marked.js pour le rendu Markdown -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<main>
    <a href="index.php" class="btn-back">← Retour</a>

    <!-- POST -->
    <article>
        <div class="article-header">
            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
            <span class="badge <?php echo $post['category'] === 'developpement' ? 'badge-dev' : 'badge-secu'; ?>">
                <?php echo $post['category'] === 'developpement' ? 'Développement' : 'Cybersécurité'; ?>
            </span>
            <?php if (!empty($post['technology'])): ?>
                <span class="badge badge-techno"><?php echo htmlspecialchars($post['technology']); ?></span>
            <?php endif; ?>
        </div>

        <p class="meta">
            Publié par <strong><?php echo htmlspecialchars($post['username']); ?></strong>
            — <?php echo getRelativeDate($post['created_at']); ?>
            &nbsp;·&nbsp; <?php echo getReadingTime($post['content']); ?> min de lecture
            &nbsp;·&nbsp; <?php echo $post['views']; ?> vue<?php echo $post['views'] > 1 ? 's' : ''; ?>
        </p>

        <!-- Tags -->
        <?php if (!empty($postTags)): ?>
        <div class="tags-list">
            <?php foreach ($postTags as $tag): ?>
                <a href="index.php?action=showTag&tag=<?php echo htmlspecialchars($tag['slug']); ?>" class="tag-link">#<?php echo htmlspecialchars($tag['name']); ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Rendu Markdown -->
        <div class="post-content" data-markdown="<?php echo htmlspecialchars($post['content']); ?>"></div>

        <?php if (!empty($post['image_path'])): ?>
            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Image du post">
        <?php endif; ?>

        <div class="post-actions">
            <!-- Like AJAX -->
            <?php if (isset($_SESSION['user'])): ?>
            <button class="btn-like <?php echo $userLiked ? 'liked' : ''; ?>"
                    data-post-id="<?php echo $post['id']; ?>" onclick="toggleLike(this)">
                ♥ <span class="like-count"><?php echo $post['like_count']; ?></span>
                <?php echo $userLiked ? 'Aimé' : 'Aimer'; ?>
            </button>
            <?php else: ?>
                <span class="like-count">♥ <?php echo $post['like_count']; ?></span>
            <?php endif; ?>

            <!-- Bookmark -->
            <?php if (isset($_SESSION['user'])): ?>
            <form method="POST" action="index.php" style="display:inline;">
                <input type="hidden" name="action" value="toggleBookmark">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="hidden" name="redirect" value="index.php?action=showPost&id=<?php echo $post['id']; ?>">
                <button type="submit" class="btn-bookmark <?php echo $userBookmarked ? 'bookmarked' : ''; ?>">
                    <?php echo $userBookmarked ? 'Sauvegardé' : 'Sauvegarder'; ?>
                </button>
            </form>
            <?php endif; ?>

            <!-- Modifier / Supprimer -->
            <?php if (isset($_SESSION['user']) && ($_SESSION['user']['id'] == $post['user_id'] || $_SESSION['user']['role'] === 'admin')): ?>
            <a href="index.php?action=showEditPost&id=<?php echo $post['id']; ?>" class="btn-edit">Modifier</a>
            <form method="POST" action="index.php" style="display:inline;" onsubmit="return confirm('Supprimer ce post ?');">
                <input type="hidden" name="action" value="deletePost">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <button type="submit" class="btn-delete">Supprimer</button>
            </form>
            <?php endif; ?>
        </div>
    </article>

    <!-- COMMENTAIRES -->
    <section class="comments-section">
        <h3>Commentaires (<?php echo count($comments); ?>)</h3>

        <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <div class="comment-header">
                    <p class="meta">
                        <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                        — <?php echo getRelativeDate($comment['created_at']); ?>
                    </p>
                    <?php if (isset($_SESSION['user']) && $comment['user_id'] == $_SESSION['user']['id']): ?>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action"     value="deleteComment">
                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                        <input type="hidden" name="post_id"    value="<?php echo $post['id']; ?>">
                        <button type="submit" class="btn-delete-comment" onclick="return confirm('Supprimer ce commentaire ?')">Supprimer</button>
                    </form>
                    <?php endif; ?>
                </div>
                <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-message">Aucun commentaire pour le moment.</p>
        <?php endif; ?>

        <?php if (isset($_SESSION['user'])): ?>
        <form method="POST" action="index.php" class="comment-form">
            <input type="hidden" name="action"  value="addComment">
            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
            <label for="content">Ajouter un commentaire</label>
            <textarea name="content" id="content" rows="3" placeholder="Votre commentaire..." required></textarea>
            <button type="submit">Publier</button>
        </form>
        <?php else: ?>
            <p><a href="index.php?action=showLogin">Connectez-vous</a> pour commenter.</p>
        <?php endif; ?>
    </section>
</main>

<script>
/* Rendu Markdown */
marked.setOptions({ breaks: true });
document.querySelectorAll('.post-content').forEach(function(el) {
    el.innerHTML = marked.parse(el.getAttribute('data-markdown'));
});

/* AJAX Like */
async function toggleLike(btn) {
    const postId = btn.dataset.postId;
    try {
        const res = await fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'action=toggleLike&post_id=' + postId
        });
        const data = await res.json();
        btn.querySelector('.like-count').textContent = data.like_count;
        btn.classList.toggle('liked', data.liked);
        btn.innerHTML = '♥ <span class="like-count">' + data.like_count + '</span> ' + (data.liked ? 'Aimé' : 'Aimer');
        btn.dataset.postId = postId;
        btn.setAttribute('onclick', 'toggleLike(this)');
    } catch(e) {}
}
</script>

<?php require_once 'view/partial/footer.php'; ?>
