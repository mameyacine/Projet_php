<?php

$id_admin = isset($_GET['idA']) ? $_GET['idA'] : null;
// Vérifie si la méthode de requête est POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Récupération des données du formulaire
    $description = $_POST["description"];
    $statut = $_POST["statut"];
    $degre_urgence = $_POST["degre_urgence"];
    $client_username = $_POST["client"];
    $intervenant_username = $_POST["intervenant"];
    $date_heure = $_POST["date_heure"];

    try {
        // Connexion à la base de données
        $pdo = new PDO("mysql:host=localhost;port=8080;dbname=projet_php", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer l'ID du client
        $stmt_client = $pdo->prepare("SELECT ID_client FROM Client WHERE nom_utilisateur = ?");
        $stmt_client->execute([$client_username]);
        $client_row = $stmt_client->fetch(PDO::FETCH_ASSOC);
        $client_id = $client_row['ID_client'];

        // Récupérer l'ID de l'intervenant
        $stmt_intervenant = $pdo->prepare("SELECT ID_intervenant FROM Intervenant WHERE nom_utilisateur = ?");
        $stmt_intervenant->execute([$intervenant_username]);
        $intervenant_row = $stmt_intervenant->fetch(PDO::FETCH_ASSOC);
        $intervenant_id = $intervenant_row['ID_intervenant'];

        // Préparer la requête d'insertion d'intervention
        $stmt_insert = $pdo->prepare("INSERT INTO Intervention (description, statut, degre_urgence, ID_client, ID_intervenant, date_heure) VALUES (?, ?, ?, ?, ?, ?)");
        
        // Exécuter la requête
        $stmt_insert->execute([$description, $statut, $degre_urgence, $client_id, $intervenant_id, $date_heure ]);

        // Redirection vers la page de toutes les interventions
        header("Location: admin.php?idA=" . htmlspecialchars($id_admin));
        exit();
    } catch(PDOException $e) {
        // En cas d'erreur, afficher un message d'erreur
        echo "Erreur lors de la création de l'intervention : " . $e->getMessage();
    }
} else {
    // Redirection si la méthode de requête n'est pas POST
    header("Location: creer_intervention.php?idA=" . htmlspecialchars($id_admin));
    exit();
}

