<?php
// Liste des technos groupées pour le select
// Variable attendue : $selectedTech (peut être null)
$techGroups = [
    'Langages'       => ['HTML','CSS','JavaScript','TypeScript','PHP','Python','Ruby','Go','Rust','Java','C#','Kotlin','Swift'],
    'Frontend'       => ['React','Vue.js','Angular','Svelte','Next.js','Nuxt.js','Astro','Remix','Solid.js','Alpine.js','HTMX'],
    'CSS / Design'   => ['Tailwind CSS','Bootstrap','Sass / SCSS','CSS Modules','Styled Components'],
    'Backend'        => ['Node.js','Express.js','Laravel','Symfony','Django','Flask','FastAPI','Ruby on Rails','Spring Boot','ASP.NET','NestJS','Hono'],
    'Base de données'=> ['MySQL','PostgreSQL','MongoDB','SQLite','Redis','Supabase','Firebase','PlanetScale'],
    'DevOps / Infra' => ['Docker','Kubernetes','Git / GitHub','CI/CD','AWS','Vercel','Netlify','Linux','Nginx','Apache'],
    'Outils'         => ['Vite','Webpack','Bun','Deno','GraphQL','REST API','WebSocket','PWA'],
];
?>
<select id="technology" name="technology">
    <option value="">— Aucune / Général —</option>
    <?php foreach ($techGroups as $group => $techs): ?>
    <optgroup label="<?php echo htmlspecialchars($group); ?>">
        <?php foreach ($techs as $tech): ?>
        <option <?php echo (isset($selectedTech) && $selectedTech === $tech) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($tech); ?>
        </option>
        <?php endforeach; ?>
    </optgroup>
    <?php endforeach; ?>
</select>
