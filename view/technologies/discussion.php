<?php require_once 'view/partial/header.php'; ?>

<main>
    <a href="index.php?action=showTechnologies" class="btn-back">← Technologies</a>

    <div class="tech-discussion-header">
        <div>
            <h2><?php echo htmlspecialchars($techno['name']); ?></h2>
            <?php if (!empty($techno['description'])): ?>
                <p class="techno-desc" style="margin-top:4px;"><?php echo htmlspecialchars($techno['description']); ?></p>
            <?php endif; ?>
        </div>
        <span class="techno-cat-badge cat-<?php echo strtolower(str_replace(' ', '-', $techno['category'])); ?>">
            <?php echo htmlspecialchars($techno['category']); ?>
        </span>
    </div>

    <!-- FIL DE DISCUSSION -->
    <section class="comments-section" style="margin-top:16px;">
        <h3>Discussion · <?php echo count($messages); ?> message<?php echo count($messages) > 1 ? 's' : ''; ?></h3>

        <?php if (count($messages) > 0): ?>
            <?php foreach ($messages as $msg): ?>
            <div class="comment">
                <div class="comment-header">
                    <p class="meta">
                        <?php if (!empty($msg['avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($msg['avatar']); ?>" style="width:20px;height:20px;border-radius:50%;object-fit:cover;vertical-align:middle;margin-right:6px;">
                        <?php endif; ?>
                        <strong><?php echo htmlspecialchars($msg['username']); ?></strong>
                        — <?php echo getRelativeDate($msg['created_at']); ?>
                    </p>
                    <?php if (isset($_SESSION['user']) && $msg['user_id'] == $_SESSION['user']['id']): ?>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action"  value="deleteTechMessage">
                        <input type="hidden" name="id"      value="<?php echo $msg['id']; ?>">
                        <input type="hidden" name="tech_slug" value="<?php echo htmlspecialchars($techSlug); ?>">
                        <button type="submit" class="btn-delete-comment" onclick="return confirm('Supprimer ce message ?')">Supprimer</button>
                    </form>
                    <?php endif; ?>
                </div>
                <p><?php echo nl2br(htmlspecialchars($msg['content'])); ?></p>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-message" style="padding:20px;">Sois le premier à écrire dans ce fil !</p>
        <?php endif; ?>

        <?php if (isset($_SESSION['user'])): ?>
        <form method="POST" action="index.php" class="comment-form">
            <input type="hidden" name="action"    value="addTechMessage">
            <input type="hidden" name="tech_slug" value="<?php echo htmlspecialchars($techSlug); ?>">
            <label for="content">Ajouter un message</label>
            <textarea name="content" id="content" rows="3" placeholder="Partage ton expérience avec <?php echo htmlspecialchars($techno['name']); ?>..." required></textarea>
            <button type="submit">Publier</button>
        </form>
        <?php else: ?>
            <p style="padding:16px 20px; font-size:14px; color:var(--text-muted);">
                <a href="index.php?action=showLogin" style="color:var(--accent);">Connecte-toi</a> pour participer à la discussion.
            </p>
        <?php endif; ?>
    </section>
</main>

<?php require_once 'view/partial/footer.php'; ?>
