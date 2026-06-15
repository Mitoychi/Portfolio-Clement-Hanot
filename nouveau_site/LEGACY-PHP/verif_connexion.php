<?php
session_start();

function Connexion() {
    $hostname = 'localhost';
    $username = 'root';
    $password = '';
    $db = 'portfolio';
    $dsn = "mysql:host=$hostname;dbname=$db";
    try {
        $bdd = new PDO($dsn, $username, $password);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $bdd;
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}

$db = Connexion();

$login = $_POST['login'];
$password = $_POST['password'];

$requete = $db->prepare("SELECT * FROM prof WHERE Login = ? AND Password = ?");
$requete->execute(array($login, $password));
$prof = $requete->fetch();

if ($prof) {
    $_SESSION['prof_connecte'] = true;
    header("Location: index.php");
} else {
    echo "Identifiants incorrects. <a href='connexion.php'>Réessayer</a>";
}
?>
