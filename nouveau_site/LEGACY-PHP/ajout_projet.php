<?php
session_start();
// fichier: ajout_projet.php
// Mode debug (désactiver en production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// include de la connexion BDD – chemin basé sur ce fichier
require_once __DIR__ . '/connect.php';

// Protection admin (utilise la même session que connexion.php)
if (empty($_SESSION['admin'])) {
    header('Location: connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

// Logging utile pour debug (écrase à chaque requête POST)
$logFile = __DIR__ . '/debug_ajout.log';
file_put_contents($logFile, "--- " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);
file_put_contents($logFile, "SESSION: " . print_r($_SESSION, true) . "\n", FILE_APPEND);
file_put_contents($logFile, "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents($logFile, "FILES: " . print_r($_FILES, true) . "\n", FILE_APPEND);

// Récupération et nettoyage
$title = trim($_POST['title'] ?? '');
$year = trim($_POST['year'] ?? '');
$domain = trim($_POST['domain'] ?? '');
$description = trim($_POST['description'] ?? '');
$link = trim($_POST['link'] ?? '');
// nouveau champ type (Académique|Professionnel)
$type = trim($_POST['type'] ?? '');
// apprentissages et logiciels
$apprentissages = trim($_POST['apprentissages'] ?? '');
$logicielsArr = array_map('trim', $_POST['logiciels'] ?? []);
$logicielsArr = array_filter($logicielsArr, fn($v) => $v !== '');
$logicielsCsv = $logicielsArr ? implode(',', $logicielsArr) : null;

// Validation minimale
if ($title === '' || $year === '' || $domain === '') {
    die('Les champs Titre, Année et Domaine sont requis.');
}

// Préparer dossier uploads (chemin absolu)
$uploadsDir = __DIR__ . '/uploads';
if (!is_dir($uploadsDir)) {
    if (!mkdir($uploadsDir, 0755, true)) {
        die('Impossible de créer le dossier uploads (vérifie les permissions).');
    }
}

// Upload : prendre la première image fournie
$imagePath = null;
if (!empty($_FILES['image']['name'][0])) {
    $tmp  = $_FILES['image']['tmp_name'][0];
    $name = basename($_FILES['image']['name'][0]);
    $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    // extensions autorisées
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allowed)) {
        die('Type de fichier non autorisé. Autorisés: ' . implode(', ', $allowed));
    }

    // vérifier erreur upload
    if ($_FILES['image']['error'][0] !== UPLOAD_ERR_OK) {
        die('Erreur lors de l\'upload (code: ' . $_FILES['image']['error'][0] . ')');
    }

    // nom sûr
    $safeName = uniqid('p_') . '.' . $ext;
    $destination = $uploadsDir . '/' . $safeName;

    if (!move_uploaded_file($tmp, $destination)) {
        die('Échec de l\'upload : move_uploaded_file a retourné false.');
    }

    // Chemin relatif côté web
    $imagePath = 'uploads/' . $safeName;
}

// Insertion en base (utilise $bdd fourni par connect.php)
try {
    $stmt = $bdd->prepare("INSERT INTO `projets` (`title`, `year`, `domain`, `type`, `description`, `link`, `image`, `apprentissages`, `logiciels`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // si $imagePath est null, envoyer explicitement null
    $params = [$title, $year, $domain, $type, $description, $link, $imagePath, $apprentissages ?: null, $logicielsCsv];
    $stmt->execute($params);
    $projectId = $bdd->lastInsertId();

    // compétences
    foreach ($_POST['skills'] ?? [] as $skill) {
        $s = trim($skill);
        if ($s !== '') {
            $ins = $bdd->prepare("INSERT INTO `competences` (`projet_id`, `skill`) VALUES (?, ?)");
            $ins->execute([$projectId, $s]);
        }
    }
    file_put_contents($logFile, "Insert OK, projectId=" . $projectId . "\n\n", FILE_APPEND);
    header('Location: admin.php');
    exit;
} catch (PDOException $e) {
    // log l'erreur puis l'affiche en dev
    file_put_contents($logFile, "SQL ERROR: " . $e->getMessage() . "\n\n", FILE_APPEND);
    die('Erreur SQL : ' . $e->getMessage());
}