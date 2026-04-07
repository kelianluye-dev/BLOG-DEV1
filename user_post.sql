/* ========================================================= */
/*  UTILISATEURS DE TEST (mot de passe : test123)            */
/* ========================================================= */

INSERT INTO users (username, email, password, role) VALUES
(
    'admin',
    'admin@blog.fr',
    '$2y$10$jAF5gKVlhw8sATfFeN.YqODEyZWbSTc49XLQIqWxcF8ava1NTWHhu',
    'admin'
),
(
    'alice',
    'alice@blog.fr',
    '$2y$10$jAF5gKVlhw8sATfFeN.YqODEyZWbSTc49XLQIqWxcF8ava1NTWHhu',
    'user'
),
(
    'bob',
    'bob@blog.fr',
    '$2y$10$jAF5gKVlhw8sATfFeN.YqODEyZWbSTc49XLQIqWxcF8ava1NTWHhu',
    'user'
);

/* ========================================================= */
/*  POSTS DE TEST — Développement & Cybersécurité            */
/* ========================================================= */

INSERT INTO posts (user_id, title, content, category, technology) VALUES

(
    1,
    'Pourquoi React domine le développement frontend en 2025',
    'React reste la bibliothèque frontend la plus utilisée au monde. Sa philosophie basée sur les composants réutilisables, son écosystème riche et l adoption massive par les entreprises en font un choix incontournable. Avec l arrivée des Server Components et des améliorations de React 19, le framework continue d évoluer pour répondre aux besoins modernes des applications web. Si vous débutez dans le développement frontend, React est une compétence essentielle à maîtriser.',
    'developpement',
    'React'
),

(
    2,
    'Docker : conteneurisez vos applications comme un pro',
    'Docker a révolutionné la façon dont on déploie les applications. En encapsulant votre application et ses dépendances dans un conteneur, vous garantissez que votre code fonctionne de la même façon en local, en staging et en production. Les concepts clés à maîtriser sont les images, les conteneurs, le Dockerfile et Docker Compose. Une fois ces bases acquises, vous pouvez déployer n importe quelle application en quelques secondes, peu importe l environnement.',
    'developpement',
    'Docker'
),

(
    3,
    'Les attaques XSS : comprendre et se protéger',
    'Le Cross-Site Scripting (XSS) est l une des vulnérabilités web les plus répandues selon l OWASP Top 10. Elle permet à un attaquant d injecter du code JavaScript malveillant dans une page web visitée par d autres utilisateurs. Il existe trois types d XSS : réfléchi, stocké et basé sur le DOM. Pour s en protéger, il faut systématiquement échapper les données affichées avec htmlspecialchars() en PHP, utiliser une Content Security Policy (CSP) et valider toutes les entrées utilisateur côté serveur.',
    'cybersecurite',
    NULL
),

(
    1,
    'Tailwind CSS vs Bootstrap : lequel choisir en 2025 ?',
    'Le débat entre Tailwind CSS et Bootstrap fait rage dans la communauté frontend. Bootstrap offre des composants prêts à l emploi et une courbe d apprentissage douce, idéal pour prototyper rapidement. Tailwind adopte une approche utility-first qui donne un contrôle total sur le design sans quitter le HTML. En 2025, Tailwind a clairement pris l avantage en termes de popularité grâce à sa flexibilité et son intégration native avec les frameworks modernes comme Next.js et Vue.js.',
    'developpement',
    'Tailwind CSS'
),

(
    2,
    'SQL Injection : la faille qui ne pardonne pas',
    'L injection SQL reste l une des attaques les plus dévastatrices du web. En manipulant les requêtes SQL via les champs de formulaire, un attaquant peut lire, modifier ou supprimer toute la base de données, voire prendre le contrôle du serveur. La protection est simple mais doit être systématique : utiliser des requêtes préparées avec des paramètres liés (PDO en PHP, prepared statements en Java). Ne jamais concaténer directement des données utilisateur dans une requête SQL. Un seul oubli peut suffire à compromettre toute une application.',
    'cybersecurite',
    NULL
),

(
    3,
    'Introduction à TypeScript : pourquoi adopter le typage statique',
    'TypeScript est devenu le standard de facto pour les projets JavaScript sérieux. En ajoutant un système de types statiques à JavaScript, il permet de détecter les erreurs à la compilation plutôt qu à l exécution, améliore l autocomplétion dans les IDE et rend le code plus lisible et maintenable. Tous les grands frameworks (Angular, Next.js, NestJS) sont écrits en TypeScript. Si vous travaillez sur un projet en équipe ou à long terme, passer à TypeScript est un investissement qui vaut vraiment le coup.',
    'developpement',
    'TypeScript'
);
