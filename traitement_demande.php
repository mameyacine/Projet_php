<?php
// Gestion des erreurs et début de session
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Vérifie si l'ID de l'administrateur est passé dans l'URL
if (!isset($_GET['idA'])) {
    echo "ID administrateur non spécifié dans l'URL.";
    exit();
}

$id_admin = $_GET['idA'];

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connexion à la base de données (à adapter selon votre configuration)
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des données du formulaire
    $description = $_POST["description"];
    $statut = $_POST["statut"];
    $degre_urgence = $_POST["urgence"];
    $intervenant_username = $_POST["intervenant"];
    $date_heure = $_POST["date_heure"];
    $id_client = $_POST["id_client"];

    try {
        // Récupérer l'ID de l'intervenant
        $stmt_intervenant = $pdo->prepare("SELECT ID_intervenant FROM Intervenant WHERE nom_utilisateur = ?");
        $stmt_intervenant->execute([$intervenant_username]);
        $intervenant_row = $stmt_intervenant->fetch(PDO::FETCH_ASSOC);
        $intervenant_id = $intervenant_row['ID_intervenant'];

        // Préparer la requête d'insertion d'intervention
        $stmt_insert = $pdo->prepare("INSERT INTO Intervention (description, statut, degre_urgence, ID_client, ID_intervenant, date_heure) VALUES (?, ?, ?, ?, ?, ?)");
        
        // Exécuter la requête
        $stmt_insert->execute([$description, $statut, $degre_urgence, $id_client, $intervenant_id, $date_heure]);

        // Redirection vers une page de confirmation ou autre
        header("Location: admin.php?idA=" . htmlspecialchars($id_admin));
        exit();
    } catch(PDOException $e) {
        // En cas d'erreur, afficher un message d'erreur
        echo "Erreur lors de la création de l'intervention : " . $e->getMessage();
    }
} else {
    // Si le formulaire n'a pas été soumis, rediriger vers une page d'erreur ou autre
    header("Location: erreur.php");
    exit();
}
?>
