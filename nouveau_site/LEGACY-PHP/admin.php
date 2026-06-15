<?php
session_start();
require_once 'connect.php';

// Protection : uniquement admin (session 'admin' définie lors connexion)
if (empty($_SESSION['admin'])) {
    header('Location: connexion.php');
    exit;
}

// Récupérer la liste des projets pour le tableau
$projets = $bdd->query("SELECT * FROM projets ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Portfolio</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="admin-main">
    <h1 class="admin-title">Admin</h1>

    <!-- Formulaire d'ajout : envoie vers ajout_projet.php -->
    <form class="admin-form" method="post" action="ajout_projet.php" enctype="multipart/form-data">
        <div class="admin-row">
            <div class="admin-group">
                <label for="title">Titre :</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="admin-group">
                <label for="year">Année :</label>
                <select id="year" name="year" required>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                </select>
            </div>
        </div>

        <div class="admin-row">
            <div class="admin-group">
                <label for="domain">Domaine :</label>
                <select id="domain" name="domain" required>
                    <option value="Communication">Communication</option>
                    <option value="Audiovisuel">Audiovisuel</option>
                    <option value="Developpement Web">Developpement Web</option>
                </select>
            </div>
            <div class="admin-group">
                <label for="type">Type :</label>
                <select id="type" name="type" required>
                    <option value="Académique">Académique</option>
                    <option value="Professionnel">Professionnel</option>
                </select>
            </div>
            <div class="admin-group">
                <label for="skills">Compétences (au moins 1) :</label>
                <input type="text" id="skills" name="skills[]" placeholder="Compétence 1" required>
                <input type="text" name="skills[]" placeholder="Compétence 2">
                <input type="text" name="skills[]" placeholder="Compétence 3">
            </div>
        </div>

        <div class="admin-group admin-description">
            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="3" required></textarea>
        </div>

        <div class="admin-group admin-description">
            <label for="apprentissages">Apprentissages critiques (optionnel) :</label>
            <textarea id="apprentissages" name="apprentissages" rows="3" placeholder="Ce que vous avez appris" ></textarea>
        </div>

        <div class="admin-row">
            <div class="admin-group">
                <label for="link">Lien externe (optionnel) :</label>
                <input type="text" id="link" name="link">
            </div>
            <div class="admin-group">
                <label for="image">Ajouter des images (1ère image sera l'image principale) :</label>
                <input type="file" id="image" name="image[]" accept="image/*" multiple>
            </div>
        </div>

        <div class="admin-group">
            <label for="logiciels">Logiciels utilisés (optionnel) :</label>
            <input type="text" id="logiciels" name="logiciels[]" placeholder="Logiciel 1">
            <input type="text" name="logiciels[]" placeholder="Logiciel 2">
            <input type="text" name="logiciels[]" placeholder="Logiciel 3">
        </div>

        <button type="submit" class="btn-admin">Ajouter le projet</button>
    </form>

    <!-- Tableau des projets ajoutés -->
    <section class="admin-table-section">
        <h2 class="admin-table-title">Projets enregistrés</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Année</th>
                    <th>Domaine</th>
                    <th>Compétences</th>
                    <th>Logiciels</th>
                    <th>Apprentissages</th>
                    <th>Lien</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projets as $projet): 
                    $stmt = $bdd->prepare("SELECT skill FROM competences WHERE projet_id = ?");
                    $stmt->execute([$projet['id']]);
                    $skillsArr = $stmt->fetchAll(PDO::FETCH_COLUMN);
                ?>
                <tr>
                    <td><?= htmlspecialchars($projet['title']) ?></td>
                    <td><?= htmlspecialchars($projet['year']) ?></td>
                    <td><?= htmlspecialchars($projet['domain']) ?></td>
                    <td>
                        <?php foreach ($skillsArr as $skill): ?>
                            <span class="skill-box"><?= htmlspecialchars($skill) ?></span>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php if (!empty($projet['logiciels'])): ?>
                            <?php foreach (explode(',', $projet['logiciels']) as $l): ?>
                                <span class="skill-box"><?= htmlspecialchars(trim($l)) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= nl2br(htmlspecialchars(
                            strlen($projet['apprentissages'] ?? '') > 120 ? substr($projet['apprentissages'],0,120) . '...' : ($projet['apprentissages'] ?? '')
                        )) ?>
                    </td>
                    <td>
                        <?php if (!empty($projet['link'])): ?>
                            <a href="<?= htmlspecialchars($projet['link']) ?>" target="_blank">Lien</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($projet['image'])): ?>
                            <img src="<?= htmlspecialchars($projet['image']) ?>" alt="" width="60" height="40">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="modifier_projet.php?id=<?= $projet['id'] ?>" class="btn-details">Modifier</a>
                        <a href="supprimer_projet.php?id=<?= $projet['id'] ?>" class="btn-link" onclick="return confirm('Supprimer ce projet ?');">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

</main>

<?php include 'footer.php'; ?>
</body>
</html>