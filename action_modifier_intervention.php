<?php

$id_admin = isset($_GET['idA']) ? $_GET['idA'] : null;
if (isset($_GET['id']) && isset($_POST["description"]) && isset($_POST['statut']) && isset($_POST['degre_urgence'])  && isset($_POST["ID_intervenant"]) ) {
    $id_intervention = $_GET['id'];
    $new_description = $_POST["description"];
    $new_statut = $_POST["statut"];
    $new_degre_urgence = $_POST["degre_urgence"];
    $new_intervenant = $_POST["ID_intervenant"];

    try {
        // Connexion à la base de données
        $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt_intervenant_id = $pdo->prepare("SELECT ID_intervenant FROM Intervenant WHERE nom_utilisateur = ?");
        $stmt_intervenant_id->execute([$new_intervenant]);
        $intervenant_id = $stmt_intervenant_id->fetchColumn();

        // Met à jour la description de l'intervention dans la table Intervention
        $stmt_update = $pdo->prepare("UPDATE Intervention SET description = ?, statut = ?, degre_urgence = ?, ID_intervenant = ? WHERE ID_intervention = ?");
            $stmt_update->execute([$new_description, $new_statut, $new_degre_urgence, $intervenant_id, $id_intervention]);

        // Redirection vers une page de confirmation
        header("Location: admin.php?idA=$id_admin");
        exit();
    } catch(PDOException $e) {
        echo "Erreur lors de la modification de l'intervention : " . $e->getMessage();
    }
} else {
    echo "Toutes les données nécessaires n'ont pas été fournies.";
}

