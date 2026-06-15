<?php
session_start();
require __DIR__ . '/connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Remplis tous les champs.';
    } else {
        try {
            $stmt = $bdd->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $error = 'Utilisateur introuvable.';
            } else {
                $stored = $user['password'] ?? '';

                // cas normal : hash stocké
                if ($stored !== '' && password_verify($password, $stored)) {
                    session_regenerate_id(true);
                    $_SESSION['admin'] = true;
                    $_SESSION['admin_user'] = $user['username'];
                    header('Location: admin.php');
                    exit;
                }

                // cas import : mot de passe stocké en clair (migration automatique)
                if ($stored !== '' && $stored === $password) {
                    // migrate to hash
                    $newhash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $bdd->prepare("UPDATE admin SET password = ? WHERE id = ?");
                    $upd->execute([$newhash, $user['id']]);
                    session_regenerate_id(true);
                    $_SESSION['admin'] = true;
                    $_SESSION['admin_user'] = $user['username'];
                    header('Location: admin.php');
                    exit;
                }

                // else échec
                $error = 'Identifiants incorrects.';
            }
        } catch (Exception $e) {
            // en dev, afficher l'erreur — retire en production
            $error = 'Erreur serveur: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Connexion</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<main class="connexion-main">
    <h1 class="connexion-title">Espace <em>privé</em></h1>
    <?php if ($error): ?>
        <div class="connexion-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="connexion-form">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input id="username" name="username" type="text" required autocomplete="username">
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">
        </div>
        <button class="btn-connexion" type="submit">Se connecter →</button>
    </form>
</main>
<?php include 'footer.php'; ?>
</body>
</html>