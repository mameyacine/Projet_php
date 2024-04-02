<?php
// Affichage du formulaire
$id_admin = isset($_GET['idA']) ? $_GET['idA'] : null;

// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier si le formulaire de modification est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    // Récupérer les valeurs du formulaire
    $id_intervention = $_POST["id_intervention"];
    $new_description = $_POST["description"];
    $new_date_heure = $_POST["date_heure"];
    $new_intervenant = $_POST["intervenant"];
    $new_statut = $_POST["statut"];
    $new_degre_urgence = $_POST["degre_urgence"];

    try {
        $stmt_check_intervention = $pdo->prepare("SELECT * FROM Intervention WHERE ID_intervenant = ? AND date_heure = ? AND ID_intervention != ?");
        $stmt_check_intervention->execute([$new_intervenant, $new_date_heure, $id_intervention]);
        $intervention_existante = $stmt_check_intervention->fetch(PDO::FETCH_ASSOC);

        if ($intervention_existante) {
            throw new Exception("L'intervenant a déjà une intervention à cette date et heure.");
        }
        // Préparer la requête SQL pour la mise à jour de l'intervention
        $stmt = $pdo->prepare("UPDATE Intervention SET description = ?, date_heure = ?, ID_intervenant = ?, statut = ?, degre_urgence = ? WHERE ID_intervention = ?");

        // Exécuter la requête avec les valeurs appropriées
        $stmt->execute([$new_description, $new_date_heure, $new_intervenant, $new_statut, $new_degre_urgence, $id_intervention]);

        // Redirection vers une page de confirmation
        header("Location: admin.php?idA=$id_admin");
        exit();
    } catch(PDOException $e) {
        echo "Erreur lors de la modification de l'intervention : " . $e->getMessage();
    } catch (Exception $e) {
        echo "Erreur lors de la modification de l'intervention : " . $e->getMessage();
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une intervention</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"></head>

</head>  

<nav class="  p-4 mb-8">
    <div class=" mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Admin Dashboard</h2>
        <div>

            <a href="gestion_utilisateurs.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button">Gestion des utilisateurs</a>
            <a href="admin.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i> </a>
        </div>
    </div>
</nav>

<body>


<!-- Contenu de la page -->
<div class="container mx-auto p-2 ">
    <?php if (!isset($_GET['id']) || !isset($_GET['idA'])): ?>
        <h1 class="text-3xl font-bold mb-4 text-center">Rechercher une intervention</h1>

    <!-- Formulaire de recherche -->
    <form action="" method="post" class="mb-4">
            <input type="hidden" name="idA" value="<?php echo htmlspecialchars($id_admin); ?>">
                <label class="block form text-sm font-bold " for="description">Description :</label>
                <input type="text" name="description" class="w-5/6 px-3 py-2  border rounded-lg focus:outline-none m-2" placeholder="Entrez la description de l'intervention">
            <button type="submit" class="button rounded" name="search"><i class="fas fa-search"></i></button>
    </form>


    <?php endif; ?>

    <?php
    // Vérifier si le formulaire de recherche est soumis
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
        // Récupération de la description
        $description = $_POST['description'];

        // Traitement de la recherche d'intervention par description
        $stmt = $pdo->prepare("SELECT * FROM Intervention WHERE description LIKE ? AND (statut = 'en attente' OR statut = 'en cours') ");
        $stmt->execute(["%$description%"]);
        $interventions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Affichage des résultats de la recherche
        if (count($interventions) > 0) {
            echo "<h1 class='text-2xl font-bold mt-8 mb-4'>Résultats de la recherche :</h1>";
            
            echo "<ul>";
            echo "<div class =''>";
            foreach ($interventions as $intervention) {
                echo "<li class='p-4'>" . htmlspecialchars($intervention['description']) . "  <a class='button' href='modifier_intervention.php?id=" . htmlspecialchars($intervention['ID_intervention']) . "&idA=" . htmlspecialchars($id_admin) . "'><i class='fas fa-edit'></i> </a> <a class='delete bg-red-500 text-white 'href='supprimer_intervention.php?id=" . htmlspecialchars($intervention['ID_intervention']) . "&idA=" . htmlspecialchars($id_admin) .  "'><i class='fas fa-trash'></i> </a></li>";
            }
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<p class='text-red-500'>Aucune intervention trouvée avec cette description.</p>";
        }
    }

    // Vérifier si l'ID de l'intervention est présent dans l'URL
    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
        // Récupérer l'ID de l'intervention
        $id_intervention = $_GET['id'];

        // Récupérer les informations sur l'intervention depuis la base de données
        $stmt = $pdo->prepare("SELECT * FROM Intervention WHERE ID_intervention = ?");
        $stmt->execute([$id_intervention]);
        $intervention = $stmt->fetch(PDO::FETCH_ASSOC);

        // Requête pour récupérer les intervenants
        $stmt_intervenants = $pdo->query("SELECT * FROM Intervenant");
    
    ?>
    <!-- Formulaire de modification -->
            <h1 class="text-3xl font-bold mb-4 text-center">Modifier une intervention</h1>

    <div class=" mx-auto flex justify-center">
    <form action="" method="post" class="form">
        <input type="hidden" name="id_intervention" value="<?php echo $intervention['ID_intervention']; ?>">
        <div class="mb-4">
            <label class="block  text-sm font-bold mb-2" for="description">Description :</label>
            <input type="text" name="description" value="<?php echo htmlspecialchars($intervention['description']); ?>" class="w-full px-3 py-2  border rounded-lg focus:outline-none">
        </div>
        <div class="mb-4">
            <label class="block  text-sm font-bold mb-2" for="date_heure">Date et heure :</label>
            <input type="datetime-local" id="date_heure" name="date_heure" value="<?php echo date('Y-m-d\TH:i', strtotime($intervention['date_heure'])); ?>" class="w-full px-3 py-2  border rounded-lg focus:outline-none">
        </div>
        <div class="mb-4">
    <label class="block  text-sm font-bold mb-2" for="intervenant">Intervenant :</label>
    <select name="intervenant" class="w-full px-3 py-2  border rounded-lg focus:outline-none">
        <?php while ($row = $stmt_intervenants->fetch(PDO::FETCH_ASSOC)) : ?>
            <option value="<?php echo htmlspecialchars($row['ID_intervenant']); ?>" <?php if ($intervention['ID_intervenant'] === $row['ID_intervenant']) echo 'selected'; ?>><?php echo htmlspecialchars($row['nom_utilisateur']); ?></option>
        <?php endwhile; ?>
    </select>
</div>

        <div class="mb-4">
            <label class="block  text-sm font-bold mb-2" for="statut">Statut :</label>
            <select name="statut" class="w-full px-3 py-2  border rounded-lg focus:outline-none">
                <option value="En attente" <?php if ($intervention['statut'] === 'En attente') echo 'selected'; ?>>En attente</option>
                <option value="En cours" <?php if ($intervention['statut'] === 'En cours') echo 'selected'; ?>>En cours</option>
                <option value="Terminé" <?php if ($intervention['statut'] === 'Terminé') echo 'selected'; ?>>Terminé</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block  text-sm font-bold mb-2" for="degre_urgence">Degré d'urgence :</label>
            <select name="degre_urgence" class="w-full px-3 py-2  border rounded-lg focus:outline-none">
                <option value="Faible" <?php if ($intervention['degre_urgence'] === 'Faible') echo 'selected'; ?>>Faible</option>
                <option value="Moyen" <?php if ($intervention['degre_urgence'] === 'Moyen') echo 'selected'; ?>>Moyen</option>
                <option value="Élevé" <?php if ($intervention['degre_urgence'] === 'Élevé') echo 'selected'; ?>>Élevé</option>
            </select>
        </div>
        <input type="submit" name="update" value="Mettre à jour" class="button text-white font-bold py-2 px-4 rounded">
    </form>

        </div>
<?php
}
?>

</div>
</body>
</html>
