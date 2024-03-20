<?php
$id_admin = isset($_GET['idA']) ? $_GET['idA'] : null;
session_start(); // Démarre la session

// Vérifie si l'utilisateur n'est pas connecté en tant qu'administrateur, si c'est le cas, redirige-le vers la page de connexion
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

// Gestion de la déconnexion
if (isset($_POST['logout'])) {
    session_unset(); // Supprime toutes les variables de session
    session_destroy(); // Détruit la session
    header("Location: connexion.php"); // Redirige vers la page de connexion après la déconnexion
    exit();
}

// Fonction pour déterminer l'ordre de tri inversé
function reverseOrder($currentOrder) {
    return $currentOrder === 'asc' ? 'desc' : 'asc';
}

// Paramètres de tri par défaut
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'description';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Détermine l'ordre de tri inverse pour le prochain clic
$nextOrder = reverseOrder($order);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les interventions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">    
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="p-4 mb-8">
        <div class="mx-auto flex justify-between items-center">
            <h2 class="text-2xl font-bold">Admin Dashboard</h2>
            <div class="flex">
                <a href="detail_intervention.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button">Détail d'intervention</a>
                <a href="gestion_utilisateurs.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button">Gestion des utilisateurs</a>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <button type="submit" name="logout" class="delete bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600"><i class="fas fa-sign-out-alt"></i></button>
                </form>
            </div>
        </div>
    </nav>

    <div class="flex justify-between p-2">
        <h1 class="text-3xl font-bold m-4">Toutes les interventions</h1>
        <div class="flex">
            <a href="creer_intervention.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button "><i class="fas fa-plus-circle"></i></a>
            <a href="modifier_intervention.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button"><i class="fas fa-edit"></i></a>
            <a href="historique_interventions.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button"><i class="fas fa-book"></i></a>
        </div>
    </div>

    <div class="container mx-auto p-2">
        <table class="table min-w-full bg-white">
            <thead class="text-white">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=description&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Description</a>
                    </th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=statut&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Statut</a>
                    </th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=degre_urgence&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Degré d'urgence</a>
                    </th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=client_nom&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Client</a>
                    </th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                        Intervenant
                    </th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                        Standardiste
                    </th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=date_heure&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Date</a>
                    </th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=heure&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Heure</a>
                    </th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php
                // Connexion à la base de données
                $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


      
                    // Requête pour récupérer toutes les interventions avec la date/heure
                    $stmt_interventions = $pdo->prepare("
                        SELECT i.*, 
                               CONCAT(c.prenom, ' ', c.nom) AS client_nom,
                               CONCAT(ih.prenom, ' ', ih.nom) AS intervenant_nom,
                               CONCAT(sh.prenom, ' ', sh.nom) AS standardiste_nom
                        FROM Intervention i 
                        LEFT JOIN Client c ON i.ID_client = c.ID_client 
                        LEFT JOIN Intervenant ih ON i.ID_intervenant = ih.ID_intervenant 
                        LEFT JOIN Standardiste sh ON i.ID_standardiste = sh.ID_standardiste
                        WHERE (i.statut = 'En cours' OR i.statut = 'En attente' ) 

                    ");
                    
                    // Exécution de la requête
                    $stmt_interventions->execute();
                    // Parcourir les résultats de la requête et afficher les données dans le tableau
                    while ($row = $stmt_interventions->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td class='py-3 px-4'>" . htmlspecialchars($row['description']) . "</td>";
                        echo "<td class='py-3 px-4'>" . htmlspecialchars($row['statut']) . "</td>";
                        echo "<td class='py-3 px-4'>" . htmlspecialchars($row['degre_urgence']) . "</td>";
                        echo "<td class='py-3 px-4'>" . htmlspecialchars($row['client_nom']) . "</td>";
                        echo "<td class='py-3 px-4'>" . htmlspecialchars($row['intervenant_nom']) . "</td>";
                        echo "<td class='py-3 px-4'>" . htmlspecialchars($row['standardiste_nom']) . "</td>";
        
                        // Séparer la date et l'heure
                        $date_heure = new DateTime($row['date_heure']);
                        $date = $date_heure->format('d-m-Y');
                        $heure = $date_heure->format('H:i');
        
                        echo "<td class='py-3 px-4'>" . htmlspecialchars($date) . "</td>";
                        echo "<td class='py-3 px-4'>" . htmlspecialchars($heure) . "</td>";
        
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        