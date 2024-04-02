<?php
// Gestion des erreurs et début de session
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Vérification du rôle de l'utilisateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'standardiste') {
    header("Location: connexion.php");
    exit();
}

// Déconnexion de l'utilisateur
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: connexion.php");
    exit();
}

// Récupération de l'identifiant du standardiste depuis l'URL
$id_standardiste = isset($_GET['idST']) ? $_GET['idST'] : null;

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    exit();
}

// Traitement de la recherche d'intervention
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["search"])) {
    $description = $_POST["description"];
    
    // Assurez-vous que $id_standardiste est défini
    if (isset($id_standardiste)) {
        try {


    
            // Requête pour récupérer les détails de l'intervention
            $stmtIntervention = $pdo->prepare("SELECT ID_intervention, description FROM Intervention WHERE description LIKE :description AND ID_standardiste = :id_standardiste AND (statut = 'en attente' OR statut = 'en cours') ");
            $stmtIntervention->execute(['description' => "%$description%", 'id_standardiste' => $id_standardiste]);
            $interventions = $stmtIntervention->fetchAll();
        } catch (PDOException $e) {
            die('Erreur lors de la récupération des détails de l\'intervention : ' . $e->getMessage());
        }
    } else {
        die("Erreur : ID standardiste non défini.");
    }
}

// Traitement du formulaire de modification de l'intervention
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $id_intervention = $_POST["id_intervention"];
    $new_description = $_POST["description"];
    $new_date_heure = $_POST["date_heure"];
    $new_statut = $_POST["statut"];
    $new_degre_urgence = $_POST["degre_urgence"];

    try {
        
        // Vérifier si le standardiste a déjà une intervention à la même date et heure
        $stmt_check_intervention = $pdo->prepare("SELECT * FROM Intervention WHERE ID_standardiste = ? AND date_heure = ? AND ID_intervention != ?");
        $stmt_check_intervention->execute([$id_standardiste, $new_date_heure, $id_intervention]);
        $intervention_existante = $stmt_check_intervention->fetch(PDO::FETCH_ASSOC);

        if ($intervention_existante) {
            throw new Exception("Le standardiste a déjà une intervention à cette date et heure.");
        }

        // Préparation de la requête SQL pour la mise à jour de l'intervention
        $stmt = $pdo->prepare("UPDATE Intervention SET description = ?, date_heure = ?, statut = ?, degre_urgence = ? WHERE ID_intervention = ?");

        // Exécution de la requête avec les valeurs appropriées
        $stmt->execute([$new_description, $new_date_heure, $new_statut, $new_degre_urgence, $id_intervention]);

        // Redirection vers une page de confirmation
        header("Location: standardiste.php?idST=$id_standardiste");
        exit();
    } catch(PDOException $e) {
        echo "Erreur lors de la modification de l'intervention : " . $e->getMessage();
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}




if ($_SERVER["REQUEST_METHOD"] === "POST"  && isset($_POST["delete"])) {

    // Récupérer l'ID de l'intervention à supprimer
    $intervention_id = $_POST["id_intervention"];
    var_dump($intervention_id);

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



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une intervention</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="p-4 mb-8">
        <div class="mx-auto flex justify-between items-center">
            <h2 class="text-2xl font-bold">Standardiste Dashboard</h2>
            <div>
            <a href="voir_tout.php?idST=<?= htmlspecialchars($id_standardiste); ?>" class="button">Toutes les interventions</a>

                <a href="clients_tous.php?idST=<?= htmlspecialchars($id_standardiste); ?>" class="button">Tous les clients</a>

                <a href="standardiste.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i> </a>
            </div>
        </div>
    </nav>

    <!-- Contenu de la page -->
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4 text-center">Modifier une intervention</h1>

      <!-- Formulaire de recherche -->
        <?php if (!isset($_GET['edit']) || !isset($_GET['id'])): ?>
            <form action="" method="post" class="mb-4">
                <input type="hidden" name="search_action" value="search">
                <input type="hidden" name="idST" value="<?= htmlspecialchars($id_standardiste); ?>">
                <div class="w-full">
                    <label class="block form text-sm font-bold " for="description">Description :</label>
                    <input type="text" name="description" class="w-5/6 px-3 py-2 border rounded-lg focus:outline-none m-2"placeholder="Entrez la description de l'intervention">
                    <button type="submit" class="button rounded" name="search"><i class="fas fa-search"></i></button>
                </div>
            </form>
        <?php endif; ?>

        <!-- Affichage des résultats de la recherche -->
        <?php if(isset($interventions)): ?>
            <h1 class="text-2xl font-bold mt-8 mb-4">Résultats de la recherche :</h1>
           
            <?php foreach ($interventions as $intervention) : ?>
        <div class="flex items-center m-2">
            <p class="mr-2"><?php echo $intervention['description']; ?></p>
        
            <!-- Bouton Modifier -->
            <form action="" method="get" class="mr-2">
                <input type="hidden" name="id" value="<?php echo $intervention['ID_intervention']; ?>">
                <input type="hidden" name="idST" value="<?php echo $id_standardiste; ?>"> <!-- Ajout de idST dans le formulaire -->

                <button type="submit" name="edit" class="button"><i class="fas fa-edit"></i></button>
            </form>
                        
            <!-- Bouton Supprimer -->
            <form action="" method="post">
                <input type="hidden" name="id_intervention" value="<?php echo $intervention['ID_intervention']; ?>">
                <input type="hidden" name="idST" value="<?php echo $id_standardiste; ?>"> <!-- Ajout de idST dans le formulaire -->
                <button type="submit" name="delete" class="delete bg-red-500 text-white" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette intervention ?');"><i class="fas fa-trash"></i></button>
            </form>
        </div>
                <?php endforeach; ?>
           
        <?php endif; ?>


        <!-- Affichage du formulaire de modification -->
        <?php if (isset($_GET['edit']) && isset($_GET['id'])): ?>
            <?php
            $id_intervention = $_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM Intervention WHERE ID_intervention = ?");
            $stmt->execute([$id_intervention]);
            $intervention = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
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
        <?php endif; ?>

    </div>
</body>
</html>
