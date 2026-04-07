<?php require_once 'view/partial/header.php'; ?>

<!-- EasyMDE : éditeur Markdown -->
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">

<main>
    <h2>Modifier le post</h2>

    <?php if (isset($errorMessage) && $errorMessage != ''): ?>
        <div class="error-message"><p><?php echo htmlspecialchars($errorMessage); ?></p></div>
    <?php endif; ?>

    <form action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">

        <label for="category">Catégorie</label>
        <select id="category" name="category" required onchange="toggleTechno(this.value)">
            <option value="developpement" <?php echo $post['category'] === 'developpement' ? 'selected' : ''; ?>>Développement</option>
            <option value="cybersecurite" <?php echo $post['category'] === 'cybersecurite' ? 'selected' : ''; ?>>Cybersécurité</option>
        </select>

        <div id="techno-block">
            <label for="technology">Technologie</label>
            <?php $selectedTech = $post['technology']; require 'view/partial/techno-select.php'; ?>
        </div>

        <label for="title">Titre</label>
        <input type="text" id="title" name="title"
               value="<?php echo htmlspecialchars($post['title']); ?>" required>

        <label for="content">Contenu (Markdown supporté)</label>
        <textarea id="content" name="content" rows="8"><?php echo htmlspecialchars($post['content']); ?></textarea>

        <label for="tags">Tags (séparés par des virgules)</label>
        <input type="text" id="tags" name="tags"
               value="<?php echo htmlspecialchars($tagsString ?? ''); ?>"
               placeholder="ex : php, sécurité, api">

        <?php if (!empty($post['image_path'])): ?>
            <p class="form-hint">Image actuelle :</p>
            <img src="<?php echo htmlspecialchars($post['image_path']); ?>"
                 alt="Image actuelle" style="max-height:180px; margin-bottom:12px; border-radius:12px;">
        <?php endif; ?>

        <label for="image">Nouvelle image (remplace l'actuelle — JPG, PNG, WEBP, 2 Mo max)</label>
        <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">

        <div style="display:flex; gap:12px; align-items:center;">
            <button type="submit" name="action" value="editPost">Enregistrer</button>
            <a href="index.php?action=showPost&id=<?php echo $post['id']; ?>" class="btn-cancel">Annuler</a>
        </div>
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
