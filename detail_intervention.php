<?php
// Affichage du formulaire
$id_admin = isset($_GET['idA']) ? $_GET['idA'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche et Détails de l'intervention</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
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
    <div class="container mx-auto p-4 ">

    <h1 class="text-3xl font-bold mb-4">Recherche et Détails de l'intervention</h1>

    <!-- Formulaire de recherche de l'intervention -->
    <form method="post" action="" class="mb-8">
        <!-- Champ caché pour l'ID admin -->
        <input type="hidden" name="idA" value="<?php echo isset($_GET['idA']) ? htmlspecialchars($_GET['idA']) : ''; ?>">
        <label class="block form text-sm font-bold mb-2" for="description">Description de l'intervention :</label>
        <input type="text" id="description" name="description" required class='w-5/6 px-3 py-2 border rounded-lg focus:outline-none 'placeholder="Entrez la description de l'intervention">
        <button type="submit" value="Rechercher" class="button px-4 py-2 rounded "><i class="fas fa-search"></i></button>
    </form>

    <!-- PHP pour la recherche et l'affichage des détails -->
    <?php
    // Connexion à la base de données
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=projet_php', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }

    // Traitement de la recherche de l'intervention
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Récupération de la description de l'intervention souhaitée
        $description = $_POST["description"];

        try {
            // Requête pour récupérer les détails de l'intervention à partir de la description
            $stmtIntervention = $pdo->prepare('SELECT ID_intervention, description FROM Intervention WHERE description LIKE :description');
            $stmtIntervention->execute(['description' => "%$description%"]);
            $interventions = $stmtIntervention->fetchAll();
        } catch (PDOException $e) {
            die('Erreur lors de la récupération des détails de l\'intervention : ' . $e->getMessage());
        }

        // Affichage des résultats de la recherche
        if ($interventions) {
            echo "<h1 class='text-2xl font-bold mt-8 mb-4'>Résultats de la recherche :</h1>";
            echo "<ul>";
            echo "<div class =''>";
            foreach ($interventions as $intervention) {
                echo "<li class='p-4'>" . htmlspecialchars($intervention['description']) . "  <a href='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $intervention['ID_intervention'] .  "&idA=" . htmlspecialchars($id_admin) . "' class='button'><i class='fas fa-eye'></i></a></li>";
            }
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<p class='text-red-500'>Aucune intervention trouvée avec la description \"$description\"</p>";
        }
    }

   // Affichage des détails de l'intervention sélectionnée
    if (isset($_GET['id'])) {
        $id_intervention = $_GET['id'];

        try {
            // Requête pour récupérer les détails de l'intervention sélectionnée
            $stmtIntervention = $pdo->prepare('SELECT i.description, i.date_heure, c.num_rue, c.nom_rue, c.code_postal, c.ville, cl.nom_utilisateur AS client_nom, inv.nom_utilisateur AS intervenant_nom FROM Intervention i LEFT JOIN Client c ON i.ID_client = c.ID_client LEFT JOIN Intervenant inv ON i.ID_intervenant = inv.ID_intervenant LEFT JOIN Client cl ON i.ID_client = cl.ID_client WHERE i.ID_intervention = :id_intervention');
            $stmtIntervention->execute(['id_intervention' => $id_intervention]);
            $intervention = $stmtIntervention->fetch();
            if ($intervention) {

    // Affichage du tableau de bord du client

                echo "<div class='mt-8'>";
                echo "<h1 class='text-xl font-bold mb-4'>Détails de l'intervention</h1>";
                echo "<p><strong>Description :</strong> " . htmlspecialchars($intervention['description'])  . "</p>";

                // Séparation de la date et de l'heure
                $date = new DateTime($intervention['date_heure']);
                $dateFormatted = $date->format('d-m-Y');
                $heureFormatted = $date->format('H:i');

                echo "<p><strong>Date :</strong> " . htmlspecialchars($dateFormatted) . "</p>";
                echo "<p><strong>Heure :</strong> " . htmlspecialchars($heureFormatted) . "</p>";

                echo "<p><strong>Lieu :</strong> " . htmlspecialchars($intervention['num_rue'] . ' ' . $intervention['nom_rue'] . ', ' . $intervention['code_postal'] . ' ' . $intervention['ville']) . "</p>";
                echo "<p><strong>Client :</strong> " . htmlspecialchars($intervention['client_nom']) . "</p>";
                echo "<p><strong>Intervenant ou Standardiste :</strong> " . htmlspecialchars($intervention['intervenant_nom']) . "</p>";

                // Requête pour récupérer les commentaires associés à l'intervention
                $stmtCommentaires = $pdo->prepare('SELECT * FROM Commentaire WHERE ID_intervention = :id_intervention');
                $stmtCommentaires->execute(['id_intervention' => $id_intervention]);
                $commentaires = $stmtCommentaires->fetchAll(PDO::FETCH_ASSOC);

                if ($commentaires) {
                    echo "<h1 class='text-xl font-bold my-4'>Commentaires :</h1>";
                    // Début du conteneur de grille
                    echo "<div class='grid-container'>";
                    foreach ($commentaires as $commentaire) {
                        // Récupérer le nom d'utilisateur associé à l'ID utilisateur du commentaire
                        $stmtNomUtilisateur = $pdo->prepare('SELECT nom_utilisateur FROM Utilisateur WHERE ID_utilisateur = ? ');
                        $stmtNomUtilisateur->execute([$commentaire['ID_utilisateur']]);
                        $nom_utilisateur = $stmtNomUtilisateur->fetch(PDO::FETCH_ASSOC)['nom_utilisateur'];
                
                        // Afficher le commentaire avec le nom d'utilisateur, la date et l'heure, puis le contenu
                        echo "<div class='commentaire'>";
                        echo "<strong class='text-lg'>" . htmlspecialchars($nom_utilisateur) . "</strong><br>";
                        echo "<small class='text-gray-500'>" . htmlspecialchars($dateFormatted) . " " . htmlspecialchars($heureFormatted) . "</small>";
                        echo "<p>" . htmlspecialchars($commentaire['contenu']) . "</p>";
                        echo "</div>";
                    }
                    // Fin du conteneur de grille
                    echo "</div>";
                } else {
                    echo "<p>Aucun commentaire pour cette intervention.</p>";
                }
                

                echo "</div>";
            } else {
                echo "<p class='text-red-500'>Aucune intervention trouvée avec l'identifiant \"$id_intervention\"</p>";
            }
        } catch (PDOException $e) {
            die('Erreur lors de la récupération des détails de l\'intervention : ' . $e->getMessage());
        }
    }

    ?>
</body>
</html>
