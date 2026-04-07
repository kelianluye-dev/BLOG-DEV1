<?php require_once 'view/partial/header.php'; ?>

<!-- marked.js pour le rendu Markdown -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<main>
    <!-- FILTRES PAR CATÉGORIE -->
    <div class="category-filters">
        <a href="index.php" class="filter-btn <?php echo ($categoryFilter === null && !isset($_GET['tag'])) ? 'active' : ''; ?>">Tous</a>
        <a href="index.php?category=developpement" class="filter-btn filter-dev <?php echo ($categoryFilter === 'developpement') ? 'active' : ''; ?>">Développement</a>
        <a href="index.php?category=cybersecurite"  class="filter-btn filter-secu <?php echo ($categoryFilter === 'cybersecurite')  ? 'active' : ''; ?>">Cybersécurité</a>
    </div>

    <!-- BARRE DE RECHERCHE -->
    <form action="index.php" method="GET" class="search-form">
        <?php if ($categoryFilter): ?>
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
        <?php endif; ?>
        <input type="text" name="search" placeholder="Rechercher un post..."
               value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">
        <button type="submit">Rechercher</button>
        <?php if ($searchQuery): ?>
            <a href="index.php<?php echo $categoryFilter ? '?category='.htmlspecialchars($categoryFilter) : ''; ?>" class="search-clear">✕</a>
        <?php endif; ?>
    </form>

    <?php if ($searchQuery): ?>
        <p class="search-results-info">
            <?php echo $totalPosts; ?> résultat<?php echo $totalPosts > 1 ? 's' : ''; ?> pour "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
        </p>
    <?php endif; ?>

    <h2>
        <?php
        if (isset($_GET['tag'])) echo 'Tag : #' . htmlspecialchars($_GET['tag']);
        elseif ($categoryFilter === 'developpement') echo 'Développement Web';
        elseif ($categoryFilter === 'cybersecurite')  echo 'Cybersécurité';
        else echo 'Toutes les publications';
        ?>
    </h2>

    <?php if (count($posts) > 0): ?>

        <?php foreach ($posts as $post): ?>
        <article>
            <div class="article-header">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
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
                &nbsp;·&nbsp; <?php echo getReadingTime($post['content']); ?> min
                &nbsp;·&nbsp; <?php echo $post['views']; ?> vue<?php echo $post['views'] > 1 ? 's' : ''; ?>
            </p>

            <!-- Rendu Markdown -->
            <div class="post-content" data-markdown="<?php echo htmlspecialchars($post['content']); ?>"></div>

            <?php if (!empty($post['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Image du post">
            <?php endif; ?>

            <div class="post-actions">
                <a href="index.php?action=showPost&id=<?php echo $post['id']; ?>" class="btn-detail">💬 <?php echo $post['comment_count']; ?></a>

                <!-- Like AJAX -->
                <?php if (isset($_SESSION['user'])): ?>
                <button class="btn-like" data-post-id="<?php echo $post['id']; ?>" onclick="toggleLike(this)">
                    ♥ <span class="like-count"><?php echo $post['like_count']; ?></span>
                </button>
                <?php else: ?>
                    <span class="like-count">♥ <?php echo $post['like_count']; ?></span>
                <?php endif; ?>

                <!-- Bookmark -->
                <?php if (isset($_SESSION['user'])): ?>
                <form method="POST" action="index.php" style="display:inline;">
                    <input type="hidden" name="action" value="toggleBookmark">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <input type="hidden" name="redirect" value="index.php<?php echo $categoryFilter ? '?category='.$categoryFilter : ''; ?>">
                    <button type="submit" class="btn-bookmark" title="Ajouter aux favoris">🔖</button>
                </form>
                <?php endif; ?>

                <!-- Modifier (auteur/admin) -->
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
        <?php endforeach; ?>

        <!-- PAGINATION -->
        <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="index.php?page=<?php echo $currentPage - 1 . ($categoryFilter ? '&category='.htmlspecialchars($categoryFilter) : '') . ($searchQuery ? '&search='.urlencode($searchQuery) : ''); ?>">← Précédent</a>
            <?php endif; ?>
            <span class="pagination-info">Page <?php echo $currentPage; ?> / <?php echo $totalPages; ?></span>
            <?php if ($currentPage < $totalPages): ?>
                <a href="index.php?page=<?php echo $currentPage + 1 . ($categoryFilter ? '&category='.htmlspecialchars($categoryFilter) : '') . ($searchQuery ? '&search='.urlencode($searchQuery) : ''); ?>">Suivant →</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

    <?php else: ?>
        <div class="empty-message">
            <?php if ($searchQuery): ?>
                <p>Aucun résultat pour "<?php echo htmlspecialchars($searchQuery); ?>".</p>
            <?php else: ?>
                <p>Aucune publication pour le moment.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<script>
/* Rendu Markdown pour tous les posts */
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
    } catch(e) {
        window.location = 'index.php?action=showLogin';
    }
}
</script>

<?php require_once 'view/partial/footer.php'; ?>
