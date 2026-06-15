<?php
session_start();
require_once 'connect.php';

// Protection : uniquement admin
if (empty($_SESSION['admin'])) {
    header('Location: connexion.php');
    exit;
}

// Vérification id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$id = intval($_GET['id']);

// Supprimer compétences associées (si contrainte cascade non présente) et le projet
$delSkills = $bdd->prepare("DELETE FROM competences WHERE projet_id = ?");
$delSkills->execute([$id]);

$del = $bdd->prepare("DELETE FROM projets WHERE id = ?");
$del->execute([$id]);

header('Location: admin.php');
exit;