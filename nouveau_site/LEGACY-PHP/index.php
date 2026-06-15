<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clément Hanot — Portfolio audiovisuel &amp; création</title>
    <meta name="description" content="Portfolio de Clément Hanot, étudiant MMI à Toulon. Académique &amp; professionnel : audiovisuel, photo, communication, web.">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>

<!-- ========== HERO PLEIN ÉCRAN ========== -->
<section class="hero">
    <div class="hero-bg">
        <img src="img/image_pro.jpg" alt="" class="hero-img-left">
    </div>
    <div class="hero-overlay"></div>

    <div class="hero-content">
        <div class="hero-eyebrow" data-reveal="fade">Portfolio · 2024 — 2026</div>

        <h1 data-reveal>
            Clément <span class="accent">Hanot</span>
        </h1>

        <p class="hero-subtitle" data-reveal style="--delay:.15s;">
            Étudiant MMI à Toulon — Audiovisuel, photographie, communication &amp; web.
        </p>

        <!-- Boutons Académique / Professionnel : sélection principale -->
        <div class="filter-buttons" role="tablist" aria-label="Filtrer les projets par type" data-reveal style="--delay:.3s;">
            <button class="filter-btn" data-type="Académique">Académique</button>
            <button class="filter-btn active" data-type="Professionnel">Professionnel</button>
        </div>
    </div>

    <div class="hero-scroll-hint">Scroll</div>
</section>

<!-- ========== HEADER PROJETS ========== -->
<?php
require_once __DIR__ . '/connect.php';
$projects = $bdd->query("SELECT * FROM projets ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$projectsCount = count($projects);

/* Pré-calcul des compteurs par domaine et par année (pour la sidebar) */
$countByDomain = [];
$countByYear = [];
foreach ($projects as $p) {
    if (!empty($p['domain'])) {
        $countByDomain[$p['domain']] = ($countByDomain[$p['domain']] ?? 0) + 1;
    }
    if (!empty($p['year'])) {
        $countByYear[$p['year']] = ($countByYear[$p['year']] ?? 0) + 1;
    }
}
krsort($countByYear);
?>

<section class="projects-section" id="projects">
    <header class="projects-header" data-reveal>
        <div>
            <div class="label">— Sélection</div>
            <h2>Projets <em>récents</em></h2>
        </div>
        <div class="projects-count">
            (<?= str_pad($projectsCount, 2, '0', STR_PAD_LEFT) ?> projets)
        </div>
    </header>

    <!-- ========== LAYOUT : SIDEBAR + GRILLE ========== -->
    <div class="projects-layout">
        <!-- Sidebar gauche style e-commerce -->
        <aside class="filters-sidebar" aria-label="Filtres">
            <!-- Domaines (en texte, plus d'icônes) -->
            <div class="filters-group">
                <h3>Domaine</h3>
                <ul class="filters-list">
                    <li>
                        <a href="#" class="filter-link domain-btn active" data-domain="">
                            <span>Tous</span>
                            <span class="count"><?= $projectsCount ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="filter-link domain-btn" data-domain="Communication">
                            <span>Communication</span>
                            <span class="count"><?= $countByDomain['Communication'] ?? 0 ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="filter-link domain-btn" data-domain="Audiovisuel">
                            <span>Audiovisuel</span>
                            <span class="count"><?= $countByDomain['Audiovisuel'] ?? 0 ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="filter-link domain-btn" data-domain="Developpement Web">
                            <span>Développement Web</span>
                            <span class="count"><?= $countByDomain['Developpement Web'] ?? 0 ?></span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Année -->
            <div class="filters-group">
                <h3>Année</h3>
                <ul class="filters-list">
                    <li>
                        <a href="#" class="filter-link year-btn active" data-year="">
                            <span>Toutes</span>
                            <span class="count"><?= $projectsCount ?></span>
                        </a>
                    </li>
                    <?php foreach ($countByYear as $year => $n): ?>
                    <li>
                        <a href="#" class="filter-link year-btn" data-year="<?= htmlspecialchars($year) ?>">
                            <span><?= htmlspecialchars($year) ?></span>
                            <span class="count"><?= $n ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Compétence -->
            <div class="filters-group">
                <h3>Compétence</h3>
                <ul class="filters-list">
                    <li><label class="filter-link"><span><input type="checkbox" class="skill-check" value="colorimetrie" style="margin-right:8px;"> colorimétrie</span></label></li>
                    <li><label class="filter-link"><span><input type="checkbox" class="skill-check" value="cadrage" style="margin-right:8px;"> cadrage</span></label></li>
                    <li><label class="filter-link"><span><input type="checkbox" class="skill-check" value="montage" style="margin-right:8px;"> montage</span></label></li>
                    <li><label class="filter-link"><span><input type="checkbox" class="skill-check" value="photographie" style="margin-right:8px;"> photographie</span></label></li>
                </ul>
            </div>

            <!-- Pôles -->
            <div class="filters-group">
                <h3>Pôles</h3>
                <ul class="filters-list">
                    <li><label class="filter-link"><span><input type="checkbox" class="pole-check" value="pole1" style="margin-right:8px;"> Pôle 1</span></label></li>
                    <li><label class="filter-link"><span><input type="checkbox" class="pole-check" value="pole2" style="margin-right:8px;"> Pôle 2</span></label></li>
                </ul>
            </div>

            <a href="#" class="filter-reset" id="filterReset">↺ Réinitialiser</a>
        </aside>

        <!-- ========== GRILLE PROJETS (carrés collés) ========== -->
        <section class="projects">
            <div class="projects-grid">
                <?php foreach ($projects as $i => $project): ?>
                <?php
                    $skillsStmt = $bdd->prepare("SELECT skill FROM competences WHERE projet_id = ?");
                    $skillsStmt->execute([$project['id']]);
                    $skillsArr = array_map(function($r){ return $r['skill']; }, $skillsStmt->fetchAll(PDO::FETCH_ASSOC));
                    $dataSkills = htmlspecialchars(implode(',', $skillsArr));
                ?>
                <article
                    class="project-card"
                    style="background-image: url('<?= htmlspecialchars($project['image']) ?>');"
                    data-type="<?= htmlspecialchars($project['type'] ?? '') ?>"
                    data-year="<?= htmlspecialchars($project['year'] ?? '') ?>"
                    data-skills="<?= $dataSkills ?>"
                    data-domain="<?= htmlspecialchars($project['domain'] ?? '') ?>"
                    data-href="<?= 'projet.php?id=' . $project['id'] ?>"
                >
                    <div class="project-meta">
                        <span class="project-type"><?= htmlspecialchars($project['type'] ?? $project['year']) ?></span>
                        <span class="project-domain"><?= htmlspecialchars($project['domain']) ?></span>
                    </div>
                    <h3 class="project-title"><?= htmlspecialchars($project['title']) ?></h3>
                    <div class="project-skills">
                        <?php foreach ($skillsArr as $skill): ?>
                            <span class="skill-box"><?= htmlspecialchars($skill) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="project-desc"><?= htmlspecialchars($project['description']) ?></div>
                    <?php if (!empty($project['link'])): ?>
                        <a href="<?= htmlspecialchars($project['link']) ?>" target="_blank" class="project-link">Voir</a>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</section>

<!-- ========== MARQUEE DÉCORATIF ========== -->
<div class="marquee" aria-hidden="true">
    <div class="marquee-track">
        <span>Audiovisuel</span><span>Photographie</span><span>Cadrage</span><span>Colorimétrie</span><span>Montage</span><span>Direction</span><span>Récit</span><span>Lumière</span>
        <span>Audiovisuel</span><span>Photographie</span><span>Cadrage</span><span>Colorimétrie</span><span>Montage</span><span>Direction</span><span>Récit</span><span>Lumière</span>
    </div>
</div>

<!-- ========== STATISTIQUES ========== -->
<section class="stats-section">
    <div class="stats-inner">
        <div class="stat" data-reveal>
            <div class="stat-number" data-count="<?= $projectsCount ?>"><?= $projectsCount ?><span class="plus">+</span></div>
            <div class="stat-label">Projets<br>réalisés</div>
        </div>
        <div class="stat" data-reveal style="--delay:.1s;">
            <div class="stat-number" data-count="3">3</div>
            <div class="stat-label">Années<br>de pratique</div>
        </div>
        <div class="stat" data-reveal style="--delay:.2s;">
            <div class="stat-number" data-count="8">8<span class="plus">+</span></div>
            <div class="stat-label">Logiciels<br>maîtrisés</div>
        </div>
        <div class="stat" data-reveal style="--delay:.3s;">
            <div class="stat-number"><em style="font-style:italic;color:var(--rouge);">∞</em></div>
            <div class="stat-label">Idées<br>en chantier</div>
        </div>
    </div>
</section>

<!-- ========== TIMELINE PARCOURS ========== -->
<section class="timeline-section">
    <div class="timeline-inner">
        <header class="timeline-header" data-reveal>
            <div class="label">— Parcours</div>
            <h2>Étapes <em>clés</em></h2>
        </header>

        <div class="timeline">
            <article class="timeline-item" data-reveal="left">
                <div class="timeline-year">2026</div>
                <div class="timeline-title">
                    Recherche de stage
                    <small>Audiovisuel · communication</small>
                </div>
                <p class="timeline-desc">
                    À la recherche d'une expérience professionnelle dans la production audiovisuelle,
                    la communication visuelle ou la création de contenu. Disponible immédiatement.
                </p>
            </article>

            <article class="timeline-item" data-reveal="left" style="--delay:.1s;">
                <div class="timeline-year">2025</div>
                <div class="timeline-title">
                    DUT MMI — Année 2
                    <small>Université de Toulon</small>
                </div>
                <p class="timeline-desc">
                    Approfondissement des techniques de production audiovisuelle, montage, motion design
                    et développement web. Réalisation de projets transversaux et professionnels.
                </p>
            </article>

            <article class="timeline-item" data-reveal="left" style="--delay:.2s;">
                <div class="timeline-year">2024</div>
                <div class="timeline-title">
                    DUT MMI — Année 1
                    <small>Métiers du Multimédia &amp; de l'Internet</small>
                </div>
                <p class="timeline-desc">
                    Initiation aux fondamentaux : photographie, cadrage, écriture audiovisuelle,
                    web design, UX et culture numérique. Premiers projets publiés.
                </p>
            </article>

            <article class="timeline-item" data-reveal="left" style="--delay:.3s;">
                <div class="timeline-year">2023</div>
                <div class="timeline-title">
                    Baccalauréat
                    <small>Île-de-France → Var</small>
                </div>
                <p class="timeline-desc">
                    Obtention du baccalauréat, puis installation dans le Sud pour entamer
                    un parcours dédié à la création numérique et à l'image.
                </p>
            </article>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const typeBtns = document.querySelectorAll('.filter-buttons .filter-btn');
    const domainBtns = document.querySelectorAll('.domain-btn');
    const yearBtns = document.querySelectorAll('.year-btn');
    const skillChecks = document.querySelectorAll('.skill-check');
    const poleChecks = document.querySelectorAll('.pole-check');
    const cards = document.querySelectorAll('.project-card');
    const resetBtn = document.getElementById('filterReset');

    let current = 'Professionnel';
    let domain = '';
    let yearFilter = '';
    let competenceFilter = [];
    let poleFilter = [];

    function setActive(buttons, attr, value){
        buttons.forEach(b => b.classList.toggle('active', (b.dataset[attr] || '') === value));
    }

    function applyFilter(){
        cards.forEach(c => {
            const t = (c.dataset.type || '').trim();
            const d = (c.dataset.domain || '').trim();
            const y = (c.dataset.year || '').trim();
            const s = ((c.dataset.skills || '')).toLowerCase();

            const typeMatch = (t === current);
            const domainMatch = (!domain) || (d === domain);
            const yearMatch = (!yearFilter) || (y === yearFilter);
            const competenceMatch = (competenceFilter.length === 0) || competenceFilter.some(cs => s.indexOf(cs) !== -1);

            c.style.display = (typeMatch && domainMatch && yearMatch && competenceMatch) ? '' : 'none';
        });
        typeBtns.forEach(b => b.classList.toggle('active', b.dataset.type === current));
        setActive(domainBtns, 'domain', domain);
        setActive(yearBtns, 'year', yearFilter);
    }

    applyFilter();

    typeBtns.forEach(b => b.addEventListener('click', function(){
        current = this.dataset.type;
        applyFilter();
    }));

    domainBtns.forEach(db => db.addEventListener('click', function(e){
        e.preventDefault();
        domain = this.dataset.domain || '';
        applyFilter();
    }));

    yearBtns.forEach(yb => yb.addEventListener('click', function(e){
        e.preventDefault();
        yearFilter = this.dataset.year || '';
        applyFilter();
    }));

    skillChecks.forEach(ch => ch.addEventListener('change', function(){
        competenceFilter = Array.from(skillChecks).filter(i => i.checked).map(i => i.value.toLowerCase());
        applyFilter();
    }));

    poleChecks.forEach(ch => ch.addEventListener('change', function(){
        poleFilter = Array.from(poleChecks).filter(i => i.checked).map(i => i.value.toLowerCase());
        applyFilter();
    }));

    if (resetBtn) {
        resetBtn.addEventListener('click', function(e){
            e.preventDefault();
            domain = '';
            yearFilter = '';
            competenceFilter = [];
            poleFilter = [];
            skillChecks.forEach(c => c.checked = false);
            poleChecks.forEach(c => c.checked = false);
            applyFilter();
        });
    }

    // Click-through sur les cartes avec transition de page
    document.querySelectorAll('.project-card').forEach(card => {
        card.addEventListener('click', function(e){
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('input') || e.target.closest('label')) return;
            const href = this.dataset.href;
            if (!href) return;
            const trans = document.querySelector('.page-transition');
            if (trans) {
                trans.classList.add('active');
                setTimeout(()=>{ window.location.href = href; }, 450);
            } else {
                window.location.href = href;
            }
        });
    });
});
</script>
</body>
</html>
