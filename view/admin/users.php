<?php require_once 'view/partial/header.php'; ?>

<main style="max-width:900px;">
    <h2>Panel Admin — <?php echo count($allUsers); ?> utilisateurs</h2>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Posts</th>
                    <th>Inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allUsers as $u): ?>
                <tr>
                    <td>
                        <?php if (!empty($u['avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($u['avatar']); ?>" class="admin-avatar-img" alt="">
                        <?php else: ?>
                            <div class="admin-avatar-placeholder"><?php echo strtoupper(mb_substr($u['username'], 0, 1)); ?></div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                    <td style="font-size:13px; color:var(--text-muted);"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <span class="badge <?php echo $u['role'] === 'admin' ? 'badge-admin' : 'badge-user-role'; ?>">
                            <?php echo $u['role']; ?>
                        </span>
                    </td>
                    <td><?php echo $u['post_count']; ?></td>
                    <td style="font-size:13px; color:var(--text-muted);"><?php echo getRelativeDate($u['created_at']); ?></td>
                    <td>
                        <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                        <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">

                            <!-- Changer le rôle -->
                            <form method="POST" action="index.php" style="display:flex; gap:4px;">
                                <input type="hidden" name="action" value="updateUserRole">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <select name="role" style="padding:4px 8px; font-size:12px; margin:0; border-radius:6px;">
                                    <option value="user"  <?php echo $u['role'] === 'user'  ? 'selected' : ''; ?>>user</option>
                                    <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>admin</option>
                                </select>
                                <button type="submit" style="padding:4px 10px; font-size:12px; border-radius:6px;">OK</button>
                            </form>

                            <!-- Supprimer -->
                            <form method="POST" action="index.php" onsubmit="return confirm('Supprimer cet utilisateur et tous ses posts ?');">
                                <input type="hidden" name="action" value="adminDeleteUser">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <button type="submit" class="btn-delete" style="font-size:12px; padding:4px 10px;">Supprimer</button>
                            </form>
                        </div>
                        <?php else: ?>
                            <span style="font-size:12px; color:var(--text-muted);">Vous</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <p style="margin-top:16px; font-size:13px; color:var(--text-muted);">
        Flux RSS disponible : <a href="index.php?action=feed">index.php?action=feed</a>
    </p>
</main>

<?php require_once 'view/partial/footer.php'; ?>
