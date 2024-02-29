<?php
session_start(); // Démarre la session

// Vérifie si l'utilisateur est connecté en tant que client
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header("Location: connexion.php");
    exit();
}

// Vérifie si l'ID client est présent dans l'URL
if (!isset($_GET['idC'])) {
    // Redirige vers une page d'erreur ou une autre page appropriée
    header("Location: erreur.php");
    exit();
}

// Récupère l'ID du client depuis l'URL
$id_client = $_GET['idC'];

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Traitement de la recherche de l'intervention
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Récupération de la description de l'intervention souhaitée
        $description = $_POST["description"];

        try {
            // Requête pour récupérer les détails de l'intervention à partir de la description et de l'ID du client
            $stmtIntervention = $pdo->prepare('SELECT ID_intervention, description FROM Intervention WHERE description LIKE :description AND ID_client = :id_client');
            $stmtIntervention->execute(['description' => "%$description%", 'id_client' => $id_client]);
            $interventions = $stmtIntervention->fetchAll();
        } catch (PDOException $e) {
            die('Erreur lors de la récupération des détails de l\'intervention : ' . $e->getMessage());
        }
    }

    // Affichage des détails de l'intervention sélectionnée
    if (isset($_GET['id'])) {
        $id_intervention = $_GET['id'];
        // Récupérer l'ID de l'intervenant associé à l'intervention
        $stmtIntervenant = $pdo->prepare('SELECT ID_intervenant FROM Intervention WHERE ID_intervention = :id_intervention');
        $stmtIntervenant->execute(['id_intervention' => $id_intervention]);
        $id_intervenant = $stmtIntervenant->fetch(PDO::FETCH_ASSOC)['ID_intervenant'];

        // Récupérer le nom d'utilisateur de l'intervenant
        $stmtNomUtilisateur = $pdo->prepare('SELECT nom_utilisateur FROM Intervenant WHERE ID_intervenant = :id_intervenant');
        $stmtNomUtilisateur->execute(['id_intervenant' => $id_intervenant]);
        $nom_utilisateur_intervenant = $stmtNomUtilisateur->fetch(PDO::FETCH_ASSOC)['nom_utilisateur'];


        // Requête pour récupérer les détails de l'intervention sélectionnée
        $stmtIntervention = $pdo->prepare('SELECT * FROM Intervention WHERE ID_intervention = :id_intervention');
        $stmtIntervention->execute(['id_intervention' => $id_intervention]);
        $intervention = $stmtIntervention->fetch(PDO::FETCH_ASSOC);

        // Requête pour récupérer les commentaires associés à l'intervention
        $stmtCommentaires = $pdo->prepare('SELECT * FROM Commentaire WHERE ID_intervention = :id_intervention');
        $stmtCommentaires->execute(['id_intervention' => $id_intervention]);
        $commentaires = $stmtCommentaires->fetchAll(PDO::FETCH_ASSOC);

// Affichage du tableau de bord du client


echo "<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Recherche et Détails de l'intervention</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css' rel='stylesheet'>
<link href='public/style.css' rel='stylesheet'>
<link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>

</head>
<nav class='p-4 mb-8'>
<div class='mx-auto flex justify-between items-center'>
    <h2 class='text-2xl font-bold'>Client Dashboard</h2>
    
    <a href='client.php?idC=$id_client' class='delete bg-red-500'><i class='fas fa-arrow-rotate-left'></i></a>

</div>
</nav>";


echo"  
<div class='container mx-auto p-4 '>


<h1 class='text-3xl font-bold mb-4'>Détails de l'intervention : </h1> ";

echo "<p>Description : " . htmlspecialchars($intervention['description']) . "</p>";
echo "<p>Intervenant : " . htmlspecialchars($nom_utilisateur_intervenant) . "</p>"; // Utiliser le nom d'utilisateur de l'intervenant récupéré précédemment
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
    echo "<p>Aucun commentaire pour cette intervention.</p>";
}




echo "<div class='fixed bottom-0 w-full bg-gray-100 p-2'>";
echo "<form class='flex items-center' method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id_intervention . "&idC=" . htmlspecialchars($id_client) . "'>";
echo "<input type='hidden' name='id_intervention' value='$id_intervention'>";
echo "<textarea name='contenu' rows='1' cols='30' required></textarea>";

// Récupérer l'ID utilisateur à partir de la table Client
$stmtClientId = $pdo->prepare('SELECT ID_utilisateur FROM Client WHERE ID_client = :id_client');
$stmtClientId->execute(['id_client' => $id_client]);
$result = $stmtClientId->fetch(PDO::FETCH_ASSOC);
$id_utilisateur = $result['ID_utilisateur'];

echo "<input type='hidden' name='id_utilisateur' value='$id_utilisateur'>";

echo "<button class=' button text-white rounded  ml-2' type='submit' >Ajouter</button>";
echo "</form>";
echo "</div>";
echo"</div>";

        // Traitement de l'ajout de commentaire
// Traitement de l'ajout de commentaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['contenu'])) {
        $contenu = $_POST['contenu'];
        $id_utilisateur = $_POST['id_utilisateur'];
        // Insérer le commentaire dans la base de données (sans inclure la date et l'heure)
        $stmtInsertCommentaire = $pdo->prepare('INSERT INTO Commentaire (ID_intervention, ID_utilisateur, contenu, date_heure) VALUES (:id_intervention, :id_utilisateur, :contenu, NOW())');
        $stmtInsertCommentaire->execute(['id_intervention' => $id_intervention, 'id_utilisateur' => $id_utilisateur, 'contenu' => $contenu]);
        // Rafraîchit la page pour afficher le nouveau commentaire
        header("Refresh:0");
    }
}

    }
} catch(PDOException $e) {
    die("<p class='text-red-500 font-bold'>Erreur lors de la récupération des données : " . $e->getMessage() . "</p>");
}
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
<?php if (!isset($_GET['id'])): ?>

<nav class="p-4 mb-8">
    <div class="mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Client Dashboard</h2>
        
        <a href="client.php?idC=<?php echo htmlspecialchars($id_client); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i></a>

    </div>
</nav>

<body>

   


    <div class="container mx-auto p-4 ">


    <h1 class="text-3xl font-bold mb-4">Recherche </h1>
        <!-- Formulaire de recherche de l'intervention -->
        <form method="post" action="" class="mb-4">
            <div class="w-full">
                <!-- Champ caché pour l'ID admin -->
                <input type="hidden" name="idC" value="<?php echo isset($_GET['idC']) ? htmlspecialchars($_GET['idC']) : ''; ?>">
                <label for="description" class="block mb-2">Description de l'intervention :</label>
                <input type="text" id="description" name="description" required class="block w-full rounded border-gray-400 border px-4 py-2 mb-2">
                <button type="submit" name="search" class="button rounded"> <i class="fas fa-search"></i></button>

            </div>
        </form>

        
           
    <?php endif; ?>

    <!-- Affichage des résultats de la recherche -->
    <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_GET['id'])): ?>
        <?php if ($interventions): ?>
            <h1 class='text-2xl font-bold mt-8 mb-4'>Résultats de la recherche :</h1>
            <ul>
            <div>
                <?php foreach ($interventions as $intervention): ?>
                    <li class='p-4'>
                        
                        <?php echo htmlspecialchars($intervention['description']); ?> 
                        <a href='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $intervention['ID_intervention'] .  "&idC=" . htmlspecialchars($id_client); ?>' class='button'><i class='fas fa-eye'></i></a>
                    </li>
               
                <?php endforeach; ?>
            </ul>
            </div>
        <?php else: ?>
            <p class='text-red-500'>Aucune intervention trouvée avec la description "<?php echo $description; ?>"</p>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>


<style>
    .fixed-bottom {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
    }
</style>



