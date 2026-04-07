-- =============================================
-- TABLE : technologies
-- =============================================

CREATE TABLE IF NOT EXISTS technologies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- =============================================
-- LANGAGES
-- =============================================
INSERT INTO technologies (name, category, description) VALUES
('HTML', 'Langage', 'Langage de balisage pour structurer les pages web'),
('CSS', 'Langage', 'Langage de style pour la mise en forme des pages web'),
('JavaScript', 'Langage', 'Langage de programmation incontournable du web'),
('TypeScript', 'Langage', 'Superset de JavaScript avec typage statique'),
('PHP', 'Langage', 'Langage backend très répandu pour le web dynamique'),
('Python', 'Langage', 'Langage polyvalent, populaire pour le backend et la data'),
('Ruby', 'Langage', 'Langage élégant, connu pour Ruby on Rails'),
('Go', 'Langage', 'Langage compilé de Google, performant et simple'),
('Rust', 'Langage', 'Langage système ultra-performant et sécurisé'),
('Java', 'Langage', 'Langage orienté objet très utilisé en entreprise'),
('C#', 'Langage', 'Langage Microsoft, utilisé avec ASP.NET'),
('Kotlin', 'Langage', 'Langage moderne pour Android et le backend JVM'),
('Swift', 'Langage', 'Langage Apple pour iOS, macOS et le web');

-- =============================================
-- FRONTEND
-- =============================================
INSERT INTO technologies (name, category, description) VALUES
('React', 'Frontend', 'Bibliothèque UI de Meta, composants réutilisables'),
('Vue.js', 'Frontend', 'Framework progressif, facile à apprendre'),
('Angular', 'Frontend', 'Framework complet de Google pour les SPA'),
('Svelte', 'Frontend', 'Compilateur frontend sans virtual DOM, très rapide'),
('Next.js', 'Frontend', 'Framework React pour le SSR, SSG et les API routes'),
('Nuxt.js', 'Frontend', 'Framework Vue.js pour le SSR et les applications universelles'),
('Astro', 'Frontend', 'Framework orienté performance, génère du HTML statique'),
('Remix', 'Frontend', 'Framework React full-stack centré sur les standards web'),
('Solid.js', 'Frontend', 'Framework réactif ultra-performant, syntaxe proche de React'),
('Alpine.js', 'Frontend', 'Framework léger pour ajouter de l\'interactivité sans build'),
('HTMX', 'Frontend', 'Accès aux fonctionnalités AJAX directement en HTML'),
('Lit', 'Frontend', 'Bibliothèque Google pour les Web Components'),
('Qwik', 'Frontend', 'Framework à résumabilité, chargement instantané');

-- =============================================
-- CSS / DESIGN
-- =============================================
INSERT INTO technologies (name, category, description) VALUES
('Tailwind CSS', 'CSS', 'Framework utility-first, styles directement en HTML'),
('Bootstrap', 'CSS', 'Framework CSS populaire avec composants prêts à l\'emploi'),
('Sass / SCSS', 'CSS', 'Préprocesseur CSS avec variables, mixins et nesting'),
('CSS Modules', 'CSS', 'Scoping local des styles par composant'),
('Styled Components', 'CSS', 'CSS-in-JS pour React, styles encapsulés'),
('Emotion', 'CSS', 'Bibliothèque CSS-in-JS performante'),
('UnoCSS', 'CSS', 'Moteur CSS atomique ultra-rapide'),
('Bulma', 'CSS', 'Framework CSS moderne basé sur Flexbox'),
('Pico CSS', 'CSS', 'Framework CSS minimal et sémantique');

-- =============================================
-- BACKEND
-- =============================================
INSERT INTO technologies (name, category, description) VALUES
('Node.js', 'Backend', 'Runtime JavaScript côté serveur basé sur V8'),
('Express.js', 'Backend', 'Framework Node.js minimaliste pour les API REST'),
('Laravel', 'Backend', 'Framework PHP élégant avec ORM, routing et templating'),
('Symfony', 'Backend', 'Framework PHP robuste et modulaire'),
('Django', 'Backend', 'Framework Python batteries incluses, très complet'),
('Flask', 'Backend', 'Micro-framework Python léger et flexible'),
('FastAPI', 'Backend', 'Framework Python moderne, async, auto-documentation'),
('Ruby on Rails', 'Backend', 'Framework Ruby convention over configuration'),
('Spring Boot', 'Backend', 'Framework Java pour les applications d\'entreprise'),
('ASP.NET', 'Backend', 'Framework Microsoft pour les apps web en C#'),
('NestJS', 'Backend', 'Framework Node.js structuré, inspiré d\'Angular'),
('Hono', 'Backend', 'Framework web ultraléger pour les edge runtimes'),
('Adonis.js', 'Backend', 'Framework Node.js full-stack inspiré de Laravel'),
('Elysia', 'Backend', 'Framework TypeScript ultra-rapide pour Bun');

-- =============================================
-- BASE DE DONNÉES
-- =============================================
INSERT INTO technologies (name, category, description) VALUES
('MySQL', 'Base de données', 'SGBD relationnel open-source le plus populaire'),
('PostgreSQL', 'Base de données', 'SGBD relationnel avancé, open-source et très puissant'),
('MongoDB', 'Base de données', 'Base de données NoSQL orientée documents JSON'),
('SQLite', 'Base de données', 'Base de données légère, embarquée dans les fichiers'),
('Redis', 'Base de données', 'Base de données clé-valeur en mémoire, ultra-rapide'),
('Supabase', 'Base de données', 'Alternative open-source à Firebase basée sur PostgreSQL'),
('Firebase', 'Base de données', 'BaaS Google, temps réel, authentification intégrée'),
('PlanetScale', 'Base de données', 'Base de données MySQL serverless et scalable'),
('Turso', 'Base de données', 'SQLite distribué pour les edge runtimes'),
('Prisma', 'Base de données', 'ORM TypeScript moderne avec migrations automatiques'),
('Drizzle', 'Base de données', 'ORM TypeScript léger et type-safe');

-- =============================================
-- DEVOPS / INFRA
-- =============================================
INSERT INTO technologies (name, category, description) VALUES
('Docker', 'DevOps', 'Conteneurisation d\'applications pour des déploiements reproductibles'),
('Kubernetes', 'DevOps', 'Orchestration de conteneurs à grande échelle'),
('Git', 'DevOps', 'Système de contrôle de version distribué'),
('GitHub Actions', 'DevOps', 'CI/CD intégré à GitHub, automatisation des workflows'),
('AWS', 'DevOps', 'Cloud d\'Amazon, leader du marché cloud'),
('Vercel', 'DevOps', 'Déploiement optimisé pour Next.js et le frontend'),
('Netlify', 'DevOps', 'Hébergement et CI/CD pour les sites statiques et Jamstack'),
('Linux', 'DevOps', 'Système d\'exploitation serveur incontournable'),
('Nginx', 'DevOps', 'Serveur web et reverse proxy haute performance'),
('Apache', 'DevOps', 'Serveur web open-source le plus historique'),
('Terraform', 'DevOps', 'Infrastructure as Code pour gérer le cloud'),
('Ansible', 'DevOps', 'Automatisation de la configuration des serveurs'),
('Cloudflare', 'DevOps', 'CDN, DNS, protection DDoS et edge computing');

-- =============================================
-- OUTILS
-- =============================================
INSERT INTO technologies (name, category, description) VALUES
('Vite', 'Outils', 'Bundler ultra-rapide pour le développement moderne'),
('Webpack', 'Outils', 'Bundler de modules JavaScript très configurable'),
('Bun', 'Outils', 'Runtime JavaScript tout-en-un ultra-rapide'),
('Deno', 'Outils', 'Runtime JavaScript sécurisé par Ryan Dahl'),
('GraphQL', 'Outils', 'Langage de requête pour les API, alternative à REST'),
('REST API', 'Outils', 'Architecture standard pour les API web'),
('WebSocket', 'Outils', 'Communication bidirectionnelle temps réel'),
('PWA', 'Outils', 'Progressive Web App, app web installable'),
('Storybook', 'Outils', 'Développement et documentation de composants UI'),
('Vitest', 'Outils', 'Framework de tests unitaires basé sur Vite'),
('Playwright', 'Outils', 'Tests end-to-end pour les applications web'),
('ESLint', 'Outils', 'Linter JavaScript pour détecter les erreurs de code'),
('Prettier', 'Outils', 'Formateur de code automatique et opinionné');
