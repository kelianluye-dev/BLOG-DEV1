<?php require_once 'view/partial/header.php'; ?>

<main>
    <h2>Créer un compte</h2>

    <?php if ($errorMessage != ''): ?>
        <div class="error-message">
            <p><?php echo htmlspecialchars($errorMessage); ?></p>
        </div>
    <?php endif; ?>

    <form action="index.php" method="POST">

        <div>
            <label for="username">Nom d'utilisateur :</label><br>
            <input type="text" id="username" name="username" required>
        </div>

        <br>

        <div>
            <label for="email">Email :</label><br>
            <input type="email" id="email" name="email" required>
        </div>

        <br>

        <div>
            <label for="password">Mot de passe :</label><br>
            <input type="password" id="password" name="password" required minlength="6">
        </div>

        <br>

        <div>
            <label for="password_confirm">Confirmer le mot de passe :</label><br>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>

        <br>

        <div>
            <button type="submit" name="action" value="register">S'inscrire</button>
        </div>

    </form>

    <p>Vous avez déjà un compte ? <a href="index.php?action=showLogin">Se connecter</a></p>
</main>

<?php require_once 'view/partial/footer.php'; ?>
