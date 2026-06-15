<?php
require __DIR__ . '/connect.php';
try {
    echo 'Connexion BDD OK<br>';
    $row = $bdd->query("SELECT COUNT(*) FROM admin")->fetchColumn();
    echo 'Nombre de comptes admin : ' . intval($row) . '<br>';
    $tables = $bdd->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo '<pre>Tables: ' . print_r($tables, true) . '</pre>';
} catch (Exception $e) {
    echo 'Erreur BDD : ' . $e->getMessage();
}