<?php
// Détection de la page courante pour le menu actif
$current = basename($_SERVER['PHP_SELF']);
$onDark = in_array($current, ['moi.php', 'connexion.php', 'admin.php', 'projet.php']);
?>
<header class="header <?= $onDark ? 'on-dark' : '' ?>">
    <div class="navbar">
        <a href="index.php" class="btn-profile">Clément&nbsp;Hanot<span style="color:var(--accent);">.</span></a>
        <nav class="nav-links">
            <a href="index.php#projects"<?= $current === 'index.php' ? ' class="is-active"' : '' ?>>Projets</a>
            <a href="moi.php"<?= $current === 'moi.php' ? ' class="is-active"' : '' ?>>Profil</a>
            <a href="index.php#contact">Contact</a>
            <a href="connexion.php" class="nav-cta">Espace privé</a>
        </nav>
    </div>
</header>
<!-- Curseur custom + overlay de transition -->
<div class="cursor-ring" aria-hidden="true"></div>
<div class="cursor-dot" aria-hidden="true"></div>
<div class="page-transition" aria-hidden="true"></div>
