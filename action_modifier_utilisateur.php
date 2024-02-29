<?php
$id_admin = isset($_GET['idA']) ? $_GET['idA'] : null;

// Traitement de la modification de l'utilisateur
if (isset($_POST['ID_utilisateur']) && isset($_POST['nouveau_role'])) {
    // Récupération des données du formulaire
    $id_utilisateur = $_POST['ID_utilisateur'];
    $nouveau_role = $_POST['nouveau_role'];

    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Démarre une transaction
    $pdo->beginTransaction();

    try {

        // Requête de mise à jour du rôle de l'utilisateur dans la table Utilisateur
        $stmt = $pdo->prepare("UPDATE Utilisateur SET role = ? WHERE ID_utilisateur = ?");
        $stmt->execute([$nouveau_role, $id_utilisateur]);

        // Identifier la table correspondante en fonction du nouveau rôle
        switch ($nouveau_role) {
            case 'client':
                // Insérer les données dans la table Client
                $stmt_insert_client = $pdo->prepare("INSERT INTO Client (ID_utilisateur, nom_utilisateur, prenom, nom) SELECT ID_utilisateur, nom_utilisateur, prenom, nom FROM Utilisateur WHERE ID_utilisateur = ?");
                $stmt_insert_client->execute([$id_utilisateur]);

                // Supprimer les données de la table Intervenant associées à cet utilisateur
                $stmt_delete_intervenant = $pdo->prepare("DELETE FROM Intervenant WHERE ID_utilisateur = ?");
                $stmt_delete_intervenant->execute([$id_utilisateur]);

                // Supprimer les données de la table Standardiste associées à cet utilisateur
                $stmt_delete_standardiste = $pdo->prepare("DELETE FROM Standardiste WHERE ID_utilisateur = ?");
                $stmt_delete_standardiste->execute([$id_utilisateur]);
                break;
            case 'standardiste':
                // Insérer les données dans la table Standardiste
                $stmt_insert_standardiste = $pdo->prepare("INSERT INTO Standardiste (ID_utilisateur, nom_utilisateur, prenom, nom) SELECT ID_utilisateur, nom_utilisateur, prenom, nom FROM Utilisateur WHERE ID_utilisateur = ?");
                $stmt_insert_standardiste->execute([$id_utilisateur]);

                // Supprimer les données de la table Client associées à cet utilisateur
                $stmt_delete_client = $pdo->prepare("DELETE FROM Client WHERE ID_utilisateur = ?");
                $stmt_delete_client->execute([$id_utilisateur]);

                // Supprimer les données de la table Intervenant associées à cet utilisateur
                $stmt_delete_intervenant = $pdo->prepare("DELETE FROM Intervenant WHERE ID_utilisateur = ?");
                $stmt_delete_intervenant->execute([$id_utilisateur]);
                break;
            case 'intervenant':
                // Insérer les données dans la table Intervenant
                $stmt_insert_intervenant = $pdo->prepare("INSERT INTO Intervenant (ID_utilisateur, nom_utilisateur, prenom, nom) SELECT ID_utilisateur, nom_utilisateur, prenom, nom FROM Utilisateur WHERE ID_utilisateur = ?");
                $stmt_insert_intervenant->execute([$id_utilisateur]);

                // Supprimer les données de la table Client associées à cet utilisateur
                $stmt_delete_client = $pdo->prepare("DELETE FROM Client WHERE ID_utilisateur = ?");
                $stmt_delete_client->execute([$id_utilisateur]);

                // Supprimer les données de la table Standardiste associées à cet utilisateur
                $stmt_delete_standardiste = $pdo->prepare("DELETE FROM Standardiste WHERE ID_utilisateur = ?");
                $stmt_delete_standardiste->execute([$id_utilisateur]);
                break;
            default:
                // Gérer le cas où le nouveau rôle est invalide
                break;
        }

        // Valide la transaction
        $pdo->commit();

        // Redirection vers une page de confirmation ou de gestion des utilisateurs
        header("Location: gestion_utilisateurs.php?idA=" . htmlspecialchars($id_admin));
        exit();
    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction et afficher un message d'erreur
        $pdo->rollBack();
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Erreur : Aucune donnée reçue pour la modification de l'utilisateur.";
}
