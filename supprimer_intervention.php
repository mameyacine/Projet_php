<?php
$id_admin = isset($_GET['idA']) ? $_GET['idA'] : null;
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

// Gestion de la déconnexion
if (isset($_POST['logout'])) {
    session_unset(); // Supprime toutes les variables de session
    session_destroy(); // Détruit la session
    header("Location: connexion.php"); // Redirige vers la page de connexion après la déconnexion
    exit();
}
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {

    // Récupérer l'ID de l'intervention à supprimer
    $intervention_id = $_GET["id"];

    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour supprimer l'intervention
    $stmt = $pdo->prepare("DELETE FROM Intervention WHERE ID_intervention = ?");
    $stmt->execute([$intervention_id]);

    // Redirection vers le tableau de toutes les interventions
    header("Location: admin.php?idA=$id_admin");
    exit();
} else {
    // Redirection si l'ID de l'intervention n'est pas défini ou si la méthode de requête n'est pas GET
    header("Location: admin.php?idA=$id_admin");
    exit();
}
