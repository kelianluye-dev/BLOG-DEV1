<?php require_once 'view/partial/header.php'; ?>

<main class="techno-main">

    <h2>Technologies Web</h2>
    <p class="techno-subtitle">Répertoire complet des technologies du développement web</p>

    <!-- RECHERCHE + FILTRE -->
    <div class="techno-controls">
        <input
            type="text"
            id="search-input"
            placeholder="Rechercher une technologie..."
            oninput="filterTechnologies()"
            autocomplete="off"
        >

        <div class="techno-filters" id="category-filters">
            <button class="techno-filter-btn active" onclick="filterByCategory('all', this)">Tout</button>
            <button class="techno-filter-btn" onclick="filterByCategory('Langage', this)">Langages</button>
            <button class="techno-filter-btn" onclick="filterByCategory('Frontend', this)">Frontend</button>
            <button class="techno-filter-btn" onclick="filterByCategory('CSS', this)">CSS</button>
            <button class="techno-filter-btn" onclick="filterByCategory('Backend', this)">Backend</button>
            <button class="techno-filter-btn" onclick="filterByCategory('Base de données', this)">BDD</button>
            <button class="techno-filter-btn" onclick="filterByCategory('DevOps', this)">DevOps</button>
            <button class="techno-filter-btn" onclick="filterByCategory('Outils', this)">Outils</button>
        </div>
    </div>

    <!-- COMPTEUR -->
    <p class="techno-count" id="techno-count"></p>

    <!-- GRILLE -->
    <div class="techno-grid" id="techno-grid">
        <?php foreach ($technologies as $techno): ?>
        <a
            class="techno-card"
            href="index.php?action=showTechDiscussion&slug=<?php echo urlencode(strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $techno['name']), '-'))); ?>"
            data-name="<?php echo strtolower(htmlspecialchars($techno['name'])); ?>"
            data-category="<?php echo htmlspecialchars($techno['category']); ?>"
            data-description="<?php echo strtolower(htmlspecialchars($techno['description'] ?? '')); ?>"
        >
            <div class="techno-card-header">
                <span class="techno-name"><?php echo htmlspecialchars($techno['name']); ?></span>
                <span class="techno-cat-badge cat-<?php echo strtolower(str_replace(' ', '-', $techno['category'])); ?>">
                    <?php echo htmlspecialchars($techno['category']); ?>
                </span>
            </div>
            <?php if (!empty($techno['description'])): ?>
                <p class="techno-desc"><?php echo htmlspecialchars($techno['description']); ?></p>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>

    <p class="empty-message" id="no-results" style="display:none;">Aucune technologie trouvée.</p>

</main>

<script>
let activeCategory = 'all';

function filterTechnologies() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const cards = document.querySelectorAll('.techno-card');
    let count = 0;

    cards.forEach(card => {
        const name = card.dataset.name;
        const cat  = card.dataset.category;
        const desc = card.dataset.description;

        const matchSearch = search === '' || name.includes(search) || desc.includes(search);
        const matchCat    = activeCategory === 'all' || cat === activeCategory;

        if (matchSearch && matchCat) {
            card.style.display = 'flex';
            count++;
        } else {
            card.style.display = 'none';
        }
    });

    document.getElementById('techno-count').textContent = count + ' technologie' + (count > 1 ? 's' : '');
    document.getElementById('no-results').style.display = count === 0 ? 'block' : 'none';
}

function filterByCategory(category, btn) {
    activeCategory = category;
    document.querySelectorAll('.techno-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filterTechnologies();
}

// Compteur initial
window.addEventListener('DOMContentLoaded', () => {
    const total = document.querySelectorAll('.techno-card').length;
    document.getElementById('techno-count').textContent = total + ' technologies';
});
</script>

<?php require_once 'view/partial/footer.php'; ?>
