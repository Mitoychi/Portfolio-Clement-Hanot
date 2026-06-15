<?php
// nouveau_site/projet.php
require_once __DIR__ . '/connect.php';
session_start();

// Récupérer l'id depuis l'URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo "ID de projet invalide.";
    exit;
}

// Récupérer projet
$stmt = $bdd->prepare("SELECT * FROM projets WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$project) {
    http_response_code(404);
    echo "Projet introuvable.";
    exit;
}

// Récupérer compétences
$skillsStmt = $bdd->prepare("SELECT skill FROM competences WHERE projet_id = ?");
$skillsStmt->execute([$id]);
$skills = $skillsStmt->fetchAll(PDO::FETCH_COLUMN);

// Préparer l'URL de l'image (champ `image` dans la BDD doit être un chemin web relatif, ex: 'uploads/xxx.jpg')
$imageUrl = !empty($project['image']) ? $project['image'] : 'img/placeholder-hero.jpg';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= htmlspecialchars($project['title']) ?> — Portfolio</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>

<!-- Hero : première image en fond avec masque sombre -->
<section class="project-hero" style="background-image: url('<?= htmlspecialchars($imageUrl) ?>');">
    <div class="hero-overlay"></div>
    <div class="hero-inner">
        <div class="eyebrow" data-reveal>
            <?= htmlspecialchars($project['type'] ?? 'Projet') ?> · <?= htmlspecialchars($project['year'] ?? '') ?>
        </div>
        <h1 class="project-title" data-reveal style="--delay:.1s;"><?= htmlspecialchars($project['title']) ?></h1>
        <div class="project-subtags" data-reveal style="--delay:.2s;">
            <?php if (!empty($project['domain'])): ?>
                <span class="tag"><?= htmlspecialchars($project['domain']) ?></span>
            <?php endif; ?>
            <?php foreach ($skills as $s): ?>
                <span class="tag"><?= htmlspecialchars($s) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- suite de la page (description / galerie...) à remplir plus bas -->
<main class="project-main">
    <div class="container">
        <a href="index.php#projects" class="project-back" data-reveal>← Tous les projets</a>
        <div class="project-meta" data-reveal>
            <span class="project-year"><?= htmlspecialchars($project['year'] ?? '') ?></span>
            <?php if (!empty($project['type'])): ?>
                <span class="project-type-meta">· <?= htmlspecialchars($project['type']) ?></span>
            <?php endif; ?>
            <?php if (!empty($project['domain'])): ?>
                <span class="project-domain-meta">· <?= htmlspecialchars($project['domain']) ?></span>
            <?php endif; ?>
        </div>
        <?php
        // apprentissages: champ libre dans projets (optionnel)
        $apprentissages = !empty($project['apprentissages']) ? $project['apprentissages'] : '';

        // logiciels: priorité au champ `logiciels` (csv) dans la table projets, sinon tentative de détection depuis les compétences
        $logiciels = [];
        if (!empty($project['logiciels'])) {
            // stocké comme CSV
            $logiciels = array_filter(array_map('trim', explode(',', $project['logiciels'])));
        } else {
            // détecter quelques logiciels connus dans les compétences
            $known = ['davinci', 'lightroom', 'premiere', 'after effects', 'photoshop'];
            foreach ($skills as $s) {
                $low = strtolower($s);
                foreach ($known as $k) {
                    if (strpos($low, $k) !== false) {
                        // format propre
                        $logiciels[] = $s;
                        break;
                    }
                }
            }
            $logiciels = array_values(array_unique($logiciels));
        }
        ?>

        <div class="project-grid">
            <div class="project-left" data-reveal>
                <div class="box description-box">
                    <h3>— Description</h3>
                    <div class="box-content">
                        <?= nl2br(htmlspecialchars($project['description'] ?? '')) ?>
                    </div>
                </div>
            </div>

            <aside class="project-right" data-reveal="right" style="--delay:.15s;">
                <div class="box softwares-box">
                    <h4>— Logiciels</h4>
                    <div class="box-content">
                        <?php if (!empty($logiciels)): ?>
                            <ul class="software-list">
                                <?php foreach ($logiciels as $soft): ?>
                                    <li><?= htmlspecialchars($soft) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="box-content">Aucun logiciel renseigné.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="box apprentissage-box">
                    <h4>— Apprentissage critique</h4>
                    <div class="box-content">
                        <?php if (!empty($apprentissages)): ?>
                            <?= nl2br(htmlspecialchars($apprentissages)) ?>
                        <?php else: ?>
                            <div>Aucun apprentissage renseigné.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- small aside links removed (kept full-width link below description) -->
            </aside>
        </div>

        <?php if (!empty($project['link'])): ?>
        <div class="project-bottom-links">
            <div class="box links-full">
                <div class="box-content">
                    <strong>Liens :</strong>
                    <a href="<?= htmlspecialchars($project['link']) ?>" target="_blank"><?= htmlspecialchars($project['link']) ?></a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>