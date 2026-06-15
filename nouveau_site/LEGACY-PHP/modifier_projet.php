<?php
session_start();
require_once 'connect.php';

// Protection admin
if (empty($_SESSION['admin'])) {
    header('Location: connexion.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$id = intval($_GET['id']);

// Traitement POST (mise à jour)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $domain = trim($_POST['domain'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $apprentissages = trim($_POST['apprentissages'] ?? '');
    $logicielsArr = array_map('trim', $_POST['logiciels'] ?? []);
    $logicielsArr = array_filter($logicielsArr, fn($v) => $v !== '');
    $logicielsCsv = $logicielsArr ? implode(',', $logicielsArr) : null;

    // Upload nouvelle image optionnelle
    $imagePath = null;
    if (!empty($_FILES['image']['name'][0])) {
        if (!is_dir('uploads')) mkdir('uploads', 0755, true);
        $originalName = basename($_FILES['image']['name'][0]);
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = uniqid('p_') . '.' . $ext;
        $dest = __DIR__ . '/uploads/' . $safeName;
        if (move_uploaded_file($_FILES['image']['tmp_name'][0], $dest)) {
            $imagePath = 'uploads/' . $safeName;
        }
    }

    if ($imagePath) {
        $stmt = $bdd->prepare("UPDATE projets SET title = ?, year = ?, domain = ?, description = ?, link = ?, image = ?, apprentissages = ?, logiciels = ? WHERE id = ?");
        $stmt->execute([$title, $year, $domain, $description, $link, $imagePath, $apprentissages ?: null, $logicielsCsv, $id]);
    } else {
        $stmt = $bdd->prepare("UPDATE projets SET title = ?, year = ?, domain = ?, description = ?, link = ?, apprentissages = ?, logiciels = ? WHERE id = ?");
        $stmt->execute([$title, $year, $domain, $description, $link, $apprentissages ?: null, $logicielsCsv, $id]);
    }

    // Remplacer compétences : suppression puis réinsertion simple
    $bdd->prepare("DELETE FROM competences WHERE projet_id = ?")->execute([$id]);
    foreach ($_POST['skills'] ?? [] as $skill) {
        $s = trim($skill);
        if ($s !== '') {
            $bdd->prepare("INSERT INTO competences (projet_id, skill) VALUES (?, ?)")->execute([$id, $s]);
        }
    }

    header('Location: admin.php');
    exit;
}

// Récupérer projet et compétences pour préremplir
$stmt = $bdd->prepare("SELECT * FROM projets WHERE id = ?");
$stmt->execute([$id]);
$projet = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$projet) {
    header('Location: admin.php');
    exit;
}

$skillsStmt = $bdd->prepare("SELECT skill FROM competences WHERE projet_id = ?");
$skillsStmt->execute([$id]);
$skills = $skillsStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le projet</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="admin-main">
    <h1 class="admin-title">Modifier le projet</h1>

    <form class="admin-form" method="post" enctype="multipart/form-data">
        <div class="admin-row">
            <div class="admin-group">
                <label for="title">Titre :</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($projet['title']) ?>" required>
            </div>
            <div class="admin-group">
                <label for="year">Année :</label>
                <input type="text" id="year" name="year" value="<?= htmlspecialchars($projet['year']) ?>" required>
            </div>
        </div>

        <div class="admin-row">
            <div class="admin-group">
                <label for="domain">Domaine :</label>
                <input type="text" id="domain" name="domain" value="<?= htmlspecialchars($projet['domain']) ?>" required>
            </div>
            <div class="admin-group">
                <label>Compétences :</label>
                <?php
                // Affiche jusqu'à 6 inputs (préremplis avec les compétences existantes)
                $maxInputs = 6;
                for ($i = 0; $i < $maxInputs; $i++):
                    $val = $skills[$i] ?? '';
                ?>
                    <input type="text" name="skills[]" value="<?= htmlspecialchars($val) ?>" placeholder="Compétence <?= $i+1 ?>">
                <?php endfor; ?>
            </div>
        </div>

        <div class="admin-group admin-description">
            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($projet['description']) ?></textarea>
        </div>

        <div class="admin-group admin-description">
            <label for="apprentissages">Apprentissages critiques (optionnel) :</label>
            <textarea id="apprentissages" name="apprentissages" rows="3" placeholder="Ce que vous avez appris"><?= htmlspecialchars($projet['apprentissages'] ?? '') ?></textarea>
        </div>

        <div class="admin-row">
            <div class="admin-group">
                <label for="link">Lien :</label>
                <input type="text" id="link" name="link" value="<?= htmlspecialchars($projet['link']) ?>">
            </div>
            <div class="admin-group">
                <label for="image">Modifier l'image (optionnel) :</label>
                <input type="file" id="image" name="image[]" accept="image/*">
                <?php if (!empty($projet['image'])): ?>
                    <p>Actuelle : <img src="<?= htmlspecialchars($projet['image']) ?>" alt="" style="width:120px;display:block;margin-top:8px;border-radius:8px"></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-group">
            <label for="logiciels">Logiciels utilisés (optionnel) :</label>
            <?php
            $existingLogiciels = [];
            if (!empty($projet['logiciels'])) $existingLogiciels = array_map('trim', explode(',', $projet['logiciels']));
            $maxLogiciels = 4;
            for ($i = 0; $i < $maxLogiciels; $i++):
                $val = $existingLogiciels[$i] ?? '';
            ?>
                <input type="text" id="logiciel_<?= $i ?>" name="logiciels[]" value="<?= htmlspecialchars($val) ?>" placeholder="Logiciel <?= $i+1 ?>">
            <?php endfor; ?>
        </div>

        <button type="submit" class="btn-admin">Enregistrer</button>
        <a href="admin.php" class="btn-link" style="margin-left:12px;">Annuler</a>
    </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>