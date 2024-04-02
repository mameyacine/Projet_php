<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirection si l'utilisateur n'est pas connecté en tant qu'intervenant
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'intervenant') {
    header("Location: connexion.php");
    exit();
}

// Gestion de la déconnexion
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: connexion.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$id_intervenant = isset($_GET['idINT']) ? $_GET['idINT'] : null;

if ($id_intervenant !== null) {
    $stmt_intervenant = $pdo->prepare("SELECT ID_intervenant FROM Intervenant WHERE ID_intervenant = ?");
    $stmt_intervenant->execute([$id_intervenant]);
    $id_intervenant_row = $stmt_intervenant->fetch(PDO::FETCH_ASSOC);

    if ($id_intervenant_row !== false) {
        // Gestion de la recherche
        $search_results = [];
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
            $search = isset($_POST['search']) ? $_POST['search'] : '';
            $stmt_search = $pdo->prepare("SELECT ID_intervention, description FROM Intervention WHERE ID_intervenant = ?  AND (statut = 'en attente' OR statut = 'en cours') AND description LIKE ?");
            $stmt_search->execute([$id_intervenant, "%$search%"]);
            $search_results = $stmt_search->fetchAll(PDO::FETCH_ASSOC);
        }

        // Affichage des interventions
        $interventions = [];
        if (isset($_GET['id_intervention'])) {
            // Si un ID d'intervention est spécifié, récupérer les détails de cette intervention
            $id_intervention = $_GET['id_intervention'];
            $stmt_intervention = $pdo->prepare("SELECT * FROM Intervention WHERE ID_intervenant = ? AND ID_intervention = ?");
            $stmt_intervention->execute([$id_intervenant, $id_intervention]);
            $intervention = $stmt_intervention->fetch(PDO::FETCH_ASSOC);
            // Afficher les détails de l'intervention pour la modification
            $description = $intervention['description'];
            $statut = $intervention['statut'];
            $degre_urgence = $intervention['degre_urgence'];

            // Traitement de la mise à jour de l'intervention
            if (isset($_POST['update'])) {
                $new_description = $_POST['description'];
                $new_statut = $_POST['statut'];
                $new_degre_urgence = $_POST['degre_urgence'];

                // Préparation et exécution de la requête de mise à jour
                $stmt_update = $pdo->prepare("UPDATE Intervention SET description = ?, statut = ?, degre_urgence = ? WHERE ID_intervention = ?");
                $stmt_update->execute([$new_description, $new_statut, $new_degre_urgence, $id_intervention]);

                // Redirection vers la même page pour actualiser les données après la mise à jour
                header("Location:intervenant.php?idINT=$id_intervenant&id_intervention=$id_intervention");
                exit();
            }
        } else {
            // Si aucun ID d'intervention n'est spécifié, afficher toutes les interventions de l'intervenant
            $stmt_interventions = $pdo->prepare("SELECT ID_intervention, description FROM Intervention WHERE ID_intervenant = ?");
            $stmt_interventions->execute([$id_intervenant]);
            $interventions = $stmt_interventions->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        $error_message = "Aucun intervenant correspondant trouvé.";
    }
} else {
    $error_message = "ID intervenant non spécifié.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Intervenant</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"></head>
<body>

<?php if (isset($error_message)) : ?>
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-4">Erreur</h1>
        <p class="text-red-500"><?php echo $error_message; ?></p>
    </div>
<?php else : ?>

<!-- Barre de navigation -->
<nav class="p-4 mb-8">
    <div class="mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Intervenant Dashboard</h2>
        <a href="intervenant.php?idINT=<?php echo htmlspecialchars($id_intervenant); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i> </a>

    </div>
</nav>

    <!-- Formulaire de recherche d'intervention -->
    <?php if (!isset($intervention)) : ?>
        <div class="container mx-auto p-2">
            <h1 class="text-3xl font-bold mb-4 text-center p-4">Rechercher une intervention</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?idINT=<?php echo htmlspecialchars($id_intervenant); ?>" method="post" class="mb-4">
                <input type="hidden" name="id_intervenant" value="<?php echo htmlspecialchars($id_intervenant); ?>">
                <label for="search" class="block form text-sm font-bold ">Recherche par description :</label>
                <input type="text" name="search" id="search" class="w-5/6 px-3 py-2 border rounded-lg focus:outline-none m-2" >
                <button type="submit" class="button rounded" name="search"><i class="fas fa-search"></i></button>
            </form>
         
            <!-- Affichage des résultats de recherche -->
            <?php if (!empty($search_results)) : ?>
                <h1 class="text-2xl font-bold mt-8 mb-4">Résultats de la recherche :</h1>
              

                <ul>
                    <div>
                    <?php foreach ($search_results as $result) : ?>
                        <li class="p-4"><?php echo htmlspecialchars($result['description']); ?>
                            <a class="button" href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id_intervention=<?php echo htmlspecialchars($result['ID_intervention']); ?>&idINT=<?php echo htmlspecialchars($id_intervenant); ?>"><i class="fas fa-edit"></i></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                    </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire de modification d'intervention -->
    <?php if (isset($intervention)) : ?>

        <h1 class="text-3xl font-bold mb-4 p-4 text-center">Modifier l'intervention</h1>

        <div class=" mx-auto flex justify-center">

        <form action="" method="post" class="form">

        <div class="mb-4">

            <input type="hidden"  name="id_intervention" value="<?php echo $intervention['ID_intervention']; ?>">
            <input type="text" name="description" value="<?php echo htmlspecialchars($description); ?>">
        </div>
        <div class="mb-4">
            <select name="statut">
                <option value="En attente" <?php if($statut == 'En attente') echo 'selected'; ?>>En attente</option>
                <option value="En cours" <?php if($statut == 'En cours') echo 'selected'; ?>>En cours</option>
                <option value="Terminé" <?php if($statut == 'Terminé') echo 'selected'; ?>>Terminé</option>
            </select>

        </div>
        <div class="mb-4">

            <select name="degre_urgence">
                <option value="Faible" <?php if($degre_urgence == 'Faible') echo 'selected'; ?>>Faible</option>
                <option value="Moyen" <?php if($degre_urgence == 'Moyen') echo 'selected'; ?>>Moyen</option>
                <option value="Élevé" <?php if($degre_urgence == 'Élevé') echo 'selected'; ?>>Élevé</option>
            </select>

        </div>

            <button type="submit" class="button" name="update">Mettre à jour</button>
        </form>
        </div>
    </div>
    <?php endif; ?>

<?php endif; ?>

</body>
</html>
