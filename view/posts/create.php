<?php require_once 'view/partial/header.php'; ?>

<!-- EasyMDE : éditeur Markdown -->
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">

<main>
    <h2>Créer un post</h2>

    <?php if (isset($errorMessage) && $errorMessage != ''): ?>
        <div class="error-message"><p><?php echo htmlspecialchars($errorMessage); ?></p></div>
    <?php endif; ?>

    <form action="index.php" method="POST" enctype="multipart/form-data">

        <label for="category">Catégorie</label>
        <select id="category" name="category" required onchange="toggleTechno(this.value)">
            <option value="developpement">Développement</option>
            <option value="cybersecurite">Cybersécurité</option>
        </select>

        <div id="techno-block">
            <label for="technology">Technologie</label>
            <?php $selectedTech = null; require 'view/partial/techno-select.php'; ?>
        </div>

        <label for="title">Titre</label>
        <input type="text" id="title" name="title" placeholder="Titre du post" required>

        <label for="content">Contenu (Markdown supporté)</label>
        <textarea id="content" name="content" rows="8" placeholder="Écris ton post..."></textarea>

        <label for="tags">Tags (séparés par des virgules)</label>
        <input type="text" id="tags" name="tags" placeholder="ex : php, sécurité, api">

        <label for="image">Image (optionnelle — JPG, PNG, WEBP, 2 Mo max)</label>
        <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">

        <button type="submit" name="action" value="createPost">Publier</button>
    </form>
</main>

<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script>
new EasyMDE({
    element: document.getElementById('content'),
    spellChecker: false,
    status: false,
    toolbar: ['bold','italic','heading','|','quote','unordered-list','ordered-list','|','link','image','|','preview']
});
function toggleTechno(value) {
    document.getElementById('techno-block').style.display = value === 'developpement' ? 'block' : 'none';
}
toggleTechno(document.getElementById('category').value);
</script>

<?php require_once 'view/partial/footer.php'; ?>
