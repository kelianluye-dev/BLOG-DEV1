<?php require_once 'view/partial/header.php'; ?>

<main>
    <h2>Connexion</h2>

    <?php
    // Message de succès si l'utilisateur vient de s'inscrire
    if (isset($_GET['registered']) && $_GET['registered'] == '1') {
        ?>
        <div class="success-message">
            <p>Compte créé avec succès ! Vous pouvez maintenant vous connecter.</p>
        </div>
        <?php
    }
    ?>

    <?php if ($errorMessage != ''): ?>
        <div class="error-message">
            <p><?php echo htmlspecialchars($errorMessage); ?></p>
        </div>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <div>
            <label for="email">Email :</label><br>
            <input type="email" id="email" name="email" required>
        </div>

        <br>

        <div>
            <label for="password">Mot de passe :</label><br>
            <input type="password" id="password" name="password" required>
        </div>

        <br>

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
            <input type="checkbox" id="remember" name="remember" style="width:auto; margin:0;">
            <label for="remember" style="text-transform:none; font-size:14px; color:var(--text-secondary); font-weight:400;">Se souvenir de moi (30 jours)</label>
        </div>

        <div>
            <button type="submit" name="action" value="login">Se connecter</button>
        </div>
    </form>
</main>

<?php require_once 'view/partial/footer.php'; ?>
