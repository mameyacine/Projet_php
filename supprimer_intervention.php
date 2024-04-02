<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
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

    try {
        // Connexion à la base de données
        $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Suppression des commentaires
        $stmt_delete_commentaires = $pdo->prepare("DELETE FROM Commentaire WHERE ID_intervention = ?");
        $stmt_delete_commentaires->execute([$intervention_id]);
    
        // Suppression de l'intervention
        $stmt = $pdo->prepare("DELETE FROM Intervention WHERE ID_intervention = ?");
        $stmt->execute([$intervention_id]);
    
        // Redirection vers le tableau de toutes les interventions
        header("Location: admin.php?idA=$id_admin");
        exit();
    } catch (PDOException $e) {
        // Gestion des erreurs
        echo "Erreur : " . $e->getMessage();
    }
    

  
} 
