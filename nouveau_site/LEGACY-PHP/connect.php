<?php
// connect.php - connexion adaptable local / production
$host = 'localhost';

// détecte si on est en local (localhost / 127.0.0.1)
$isLocal = (isset($_SERVER['HTTP_HOST']) &&
           (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false))
           || (php_sapi_name() === 'cli' && empty($_SERVER['REMOTE_ADDR']));

// CONFIG LOCALE (XAMPP)
if ($isLocal) {
    $dbname = 'webclementpro';   // <- change si tu veux le nom exact de ta DB locale
    $dbuser = 'root';
    $dbpass = '';
} else {
    // CONFIG PRO (Hostinger) - conserve tes identifiants actuels
    $dbname = 'u882448061_webclementpro';
    $dbuser = 'u882448061_Mitoychi';
    $dbpass = 'Mitoychi@0104';
}

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $bdd = new PDO($dsn, $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // message utile en dev : change ou supprime en prod
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}