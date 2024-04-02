
   <?php
session_start(); // Démarre la session

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifie si l'utilisateur est connecté en tant que intervenant
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'intervenant') {
        header("Location: connexion.php");
        exit();
    }

    // Vérifie si l'ID intervenant est présent dans l'URL
    if (!isset($_GET['idINT'])) {
        // Redirige vers une page d'erreur ou une autre page appropriée
        header("Location: erreur.php");
        exit();
    }

    // Récupère l'ID du intervenant depuis l'URL
    $id_intervenant = $_GET['idINT'];

    // Traitement de la recherche de l'intervention
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["description"])) {
        $description = $_POST["description"];
        
        // Assurez-vous que $id_intervenant est correctement défini
        if(isset($id_intervenant)) {
            try {
                // Requête pour récupérer les détails de l'intervention à partir de la description et de l'ID du intervenant
                $stmtIntervention = $pdo->prepare("SELECT ID_intervention, description FROM Intervention WHERE description LIKE :description AND ID_intervenant = :id_intervenant AND (statut = 'en attente' OR statut = 'en cours') ");
                // Associez les paramètres à la requête préparée et exécutez-la
                $stmtIntervention->execute(['description' => "%$description%", 'id_intervenant' => $id_intervenant]);
                $interventions = $stmtIntervention->fetchAll();
            } catch (PDOException $e) {
                // Gérez les erreurs de requête
                die('Erreur lors de la récupération des détails de l\'intervention : ' . $e->getMessage());
            }
        } else {
            // Gérez le cas où $id_intervenant n'est pas défini
            die("Erreur : ID intervenant non défini.");
        }
    }

    // Traitement de l'ajout de commentaire
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['contenu'])) {
        $id_intervention = $_POST['id_intervention'];
        $contenu = $_POST['contenu'];
        $id_utilisateur = $_POST['id_utilisateur'];
        // Insérer le commentaire dans la base de données (sans inclure la date et l'heure)
        $stmtInsertCommentaire = $pdo->prepare('INSERT INTO Commentaire (ID_intervention, ID_utilisateur, contenu, date_heure) VALUES (:id_intervention, :id_utilisateur, :contenu, NOW())');
        $stmtInsertCommentaire->execute(['id_intervention' => $id_intervention, 'id_utilisateur' => $id_utilisateur, 'contenu' => $contenu]);
        // Rafraîchit la page pour afficher le nouveau commentaire
        header("Refresh:0");
    }

    // Affichage des détails de l'intervention sélectionnée
    if (isset($_GET['id'])) {
        $id_intervention = $_GET['id'];
        // Récupérer l'ID du client associé à l'intervention
        $stmtClient = $pdo->prepare('SELECT ID_client FROM Intervention WHERE ID_intervention = :id_intervention');
        $stmtClient->execute(['id_intervention' => $id_intervention]);
        $id_client = $stmtClient->fetch(PDO::FETCH_ASSOC)['ID_client'];

        // Récupérer le nom d'utilisateur du client
        $stmtNomUtilisateur = $pdo->prepare('SELECT nom_utilisateur FROM Client WHERE ID_client= :id_client');
        $stmtNomUtilisateur->execute(['id_client' => $id_client]);
        $nom_utilisateur_client = $stmtNomUtilisateur->fetch(PDO::FETCH_ASSOC)['nom_utilisateur'];


        // Requête pour récupérer les détails de l'intervention sélectionnée
        $stmtIntervention = $pdo->prepare('SELECT * FROM Intervention WHERE ID_intervention = :id_intervention');
        $stmtIntervention->execute(['id_intervention' => $id_intervention]);
        $intervention = $stmtIntervention->fetch(PDO::FETCH_ASSOC);

        // Requête pour récupérer les commentaires associés à l'intervention
        $stmtCommentaires = $pdo->prepare('SELECT * FROM Commentaire WHERE ID_intervention = :id_intervention');
        $stmtCommentaires->execute(['id_intervention' => $id_intervention]);
        $commentaires = $stmtCommentaires->fetchAll(PDO::FETCH_ASSOC);

        // Affichage du tableau de bord du intervenant
        echo "   <nav class='p-4 mb-8'>
        <div class='mx-auto flex justify-between items-center'>
            <h2 class='text-2xl font-bold'>Intervenant Dashboard</h2>
            <a class='button' href='intervenant.php?idINT=$id_intervenant'><i class='fas fa-arrow-rotate-left'></i></a>
            </div>
    </nav>";

        // Affichage des détails de l'intervention
        echo "<div class='container mx-auto p-4 '>

        <h1 class='text-3xl font-bold mb-4'>Détails de l'intervention :</h1>";
        echo "<p>Description : " . htmlspecialchars($intervention['description']) . "</p>";
        echo "<p>Client : " . htmlspecialchars($nom_utilisateur_client) . "</p>"; // Utiliser le nom d'utilisateur du client récupéré précédemment
        echo "<p>Date: " . (isset($intervention['date_heure']) ? htmlspecialchars(date("d-m-Y", strtotime($intervention['date_heure']))) : '') . "</p>";
        echo "<p>Heure: " . (isset($intervention['date_heure']) ? htmlspecialchars(date("H:i", strtotime($intervention['date_heure']))) : '') . "</p>";
        echo "<p>Statut : " . htmlspecialchars($intervention['statut']) . "</p>";
        echo "<p>Degré d'urgence : " . htmlspecialchars($intervention['degre_urgence']) . "</p>";

        // Affichage des commentaires associés à l'intervention
        if ($commentaires) {
            echo "<h1 class='text-xl font-bold my-4'>Commentaires :</h1>";
            echo "<div class='grid-container'>";
            foreach ($commentaires as $commentaire) {
                // Récupérer le nom d'utilisateur associé à l'ID utilisateur du commentaire
                $stmtNomUtilisateur = $pdo->prepare('SELECT nom_utilisateur FROM Utilisateur WHERE ID_utilisateur = ? ');
                $stmtNomUtilisateur->execute([$commentaire['ID_utilisateur']]);
                $nom_utilisateur = $stmtNomUtilisateur->fetch(PDO::FETCH_ASSOC)['nom_utilisateur'];


                $date_heure_format = date("d-m-Y H:i", strtotime($commentaire['date_heure']));


                // Afficher le commentaire avec le nom d'utilisateur, la date et l'heure, puis le contenu
                echo "<div class='commentaire'>";
                echo "<div class='mr-4 flex justify-between items-center'>";
                echo "<strong class='text-lg'>" . htmlspecialchars($nom_utilisateur) . "</strong><br>";
                echo "<small class='text-gray-500'>" . htmlspecialchars($date_heure_format) . "</small>";               
                echo "</div>";
                echo "<div class='flex-grow'>";
                echo htmlspecialchars($commentaire['contenu']);
                echo "</div>";
                echo "</div>";
            }
            echo "</ul>";
        } else {
            echo "<p class='font-bold'>Aucun commentaire pour cette intervention.</p>";
        }

        echo "<div class='fixed bottom-0 w-full bg-gray-100 p-2'>";
        echo "<form class='flex items-center' method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id_intervention . "&idINT=" . htmlspecialchars($id_intervenant) . "'>";
        echo "<input type='hidden' name='id_intervention' value='$id_intervention'>";
        echo "<textarea name='contenu' rows='3' cols='30' required></textarea>";

        // Récupérer l'ID utilisateur à partir de la table Utilisateur
        $stmtintervenantId = $pdo->prepare('SELECT ID_utilisateur FROM intervenant WHERE ID_intervenant = :id_intervenant');
        $stmtintervenantId->execute(['id_intervenant' => $id_intervenant]);
        $result = $stmtintervenantId->fetch(PDO::FETCH_ASSOC);
        $id_utilisateur = $result['ID_utilisateur'];

        echo "<input type='hidden' name='id_utilisateur' value='$id_utilisateur'>";

        echo "<button class=' button text-white rounded  ml-2' type='submit' >Ajouter</button>";
        echo "</form>";
        echo "</div>";

    } else { // Si l'ID de l'intervention n'est pas défini
        // Affichage du formulaire de recherche de l'intervention
            echo "   <nav class='p-4 mb-8'>
        <div class='mx-auto flex justify-between items-center'>
            <h2 class='text-2xl font-bold'>Intervenant Dashboard</h2>
            <a class='button' href='intervenant.php?idINT=$id_intervenant'><i class='fas fa-arrow-rotate-left'></i></a>
            </div>
    </nav>";


                
        echo "    <div class='container mx-auto p-4'>

        <h1 class='text-3xl font-bold '>Recherche </h1>";
        echo "<form method='post' action='' class='mb-4'>
                <input type='hidden' name='idINT' value='" . (isset($_GET['idINT']) ? htmlspecialchars($_GET['idINT']) : '') . "'>
                <div class='w-full'>
                    <label for='description' class='block form text-sm font-bold '>Description de l'intervention :</label>
                    <input type='text' id='description' name='description' required class='w-5/6 px-3 py-2 border rounded-lg focus:outline-none '>
                    <button type='submit' class='button rounded' name='search'><i class='fas fa-search'></i></button>
                </div>
            </form>";

        // Affichage des résultats de la recherche
        if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_GET['id'])) {
            if ($interventions) {
                echo "<h1 class='text-xl font-bold mb-4'>Résultats de la recherche :</h1>";
                echo "<ul>
                <div>";
                foreach ($interventions as $intervention) {
                    echo "<li class='p-4'>
                    " . htmlspecialchars($intervention['description']) . " 
                    <a href='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $intervention['ID_intervention'] . "&idINT=" . htmlspecialchars($id_intervenant) . "' class='button'><i class='fas fa-eye'></i></a>
                </li>";

                
            
                }
                echo "</ul>
                </div>";
            } else {
                echo "<p class='text-red-500'>Aucune intervention trouvée avec la description \"" . $description . "\"</p>";
            }
        }
    }
} catch (PDOException $e) {
    die("<p class='text-red-500 font-bold'>Erreur lors de la récupération des données : " . $e->getMessage() . "</p>");
}
echo "</div>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commentaires</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>