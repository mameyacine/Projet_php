<?php
$id_admin = isset($_GET['idA']) ? $_GET['idA'] : null;



// Vérifier si l'ID de l'utilisateur est défini
if (isset($_POST['ID_utilisateur'])) {
    // Récupérer l'ID de l'utilisateur à supprimer
    $id_utilisateur = $_POST['ID_utilisateur'];

    try {
        // Connexion à la base de données
        $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Début de la transaction
        $pdo->beginTransaction();
        // Supprimer les commentaires associés aux interventions des clients
        $stmt_delete_commentaires= $pdo->prepare("DELETE FROM Commentaire WHERE ID_utilisateur = ?"); 
        $stmt_delete_commentaires->execute([$id_utilisateur]);



        // Supprimer les interventions associées à l'utilisateur
        $stmt_delete_interventions_clients = $pdo->prepare("DELETE FROM Intervention WHERE ID_client IN (SELECT ID_client FROM Client WHERE ID_utilisateur = ?)");
        $stmt_delete_interventions_clients->execute([$id_utilisateur]);


        // Supprimer les interventions associées à l'utilisateur


        $stmt_delete_interventions_standardistes = $pdo->prepare("DELETE FROM Intervention WHERE ID_standardiste IN (SELECT ID_standardiste FROM Standardiste WHERE ID_utilisateur = ?)");
        $stmt_delete_interventions_standardistes->execute([$id_utilisateur]);



        // Supprimer les interventions associées à l'utilisateur
        $stmt_delete_interventions_intervenants = $pdo->prepare("DELETE FROM Intervention WHERE ID_intervenant IN (SELECT ID_intervenant FROM Intervenant WHERE ID_utilisateur = ?)");
        $stmt_delete_interventions_intervenants->execute([$id_utilisateur]);


        // Supprimer les références dans les autres tables
        $stmt_delete_client = $pdo->prepare("DELETE FROM Client WHERE ID_utilisateur = ?");
        $stmt_delete_standardiste = $pdo->prepare("DELETE FROM Standardiste WHERE ID_utilisateur = ?");
        $stmt_delete_intervenant = $pdo->prepare("DELETE FROM Intervenant WHERE ID_utilisateur = ?");
        $stmt_delete_utilisateur = $pdo->prepare("DELETE FROM Utilisateur WHERE ID_utilisateur = ?");

        // Exécuter les requêtes
        $stmt_delete_client->execute([$id_utilisateur]);
        $stmt_delete_standardiste->execute([$id_utilisateur]);
        $stmt_delete_intervenant->execute([$id_utilisateur]);
        $stmt_delete_utilisateur->execute([$id_utilisateur]);

        // Valider la transaction
        $pdo->commit();

        // Redirection avec un message de confirmation
        header("Location: gestion_utilisateurs.php?idA=" . htmlspecialchars($id_admin));

        exit();
    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction et afficher un message d'erreur
        $pdo->rollBack();
        echo "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage();
    }
} else {
    echo "Aucun ID utilisateur spécifié pour la suppression.";
}
