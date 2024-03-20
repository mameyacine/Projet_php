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

<!-- Barre de navigation -->
<nav class="  p-4 mb-8">
    <div class="mx-auto flex justify-between items-center">
        <h2 class="  text-2xl font-bold">Admin Dashboard</h2>
        <div class="flex">
            <a href="admin.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button" ><i class="fas fa-arrow-rotate-left"></i></a>

        </div>

    </div>
</nav>


<body>

        <h1 class="text-3xl text-center font-bold m-4">Historique des Interventions</h1>
      

    <div class="container mx-auto p-2">


        <table class="table min-w-full bg-white">
            <thead class=" text-white">
            <tr>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=description&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Description</a>
                </th>
        
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=degre_urgence&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Degré d'urgence</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=client_nom&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Client</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=intervenant_nom&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Intervenant</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=standardiste_nom&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Standardiste</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=date_heure&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Date</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sort=date_heure&order=" . $nextOrder . "&idA=" . htmlspecialchars($id_admin); ?>">Heure</a>
                </th>
            </tr>
            </thead>
            <tbody class="text-gray-700">
            <?php
            // Connexion à la base de données
            $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Requête pour récupérer toutes les interventions terminées avec la date/heure
            $stmt_interventions = $pdo->prepare("
            SELECT i.*, 
                   CONCAT(c.prenom, ' ', c.nom) AS client, 
                   CONCAT(inv.prenom, ' ', inv.nom) AS intervenant, 
                   CONCAT(s.prenom, ' ', s.nom) AS standardiste 
            FROM Intervention i 
            LEFT JOIN Client c ON i.ID_client = c.ID_client 
            LEFT JOIN Intervenant inv ON i.ID_intervenant = inv.ID_intervenant 
            LEFT JOIN Standardiste s ON i.ID_standardiste = s.ID_standardiste 
            WHERE i.statut = 'Terminé'
            ORDER BY $sortBy $order
        ");            $stmt_interventions->execute();
            while ($row = $stmt_interventions->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td class='py-3 px-4'>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td class='py-3 px-4'>" . htmlspecialchars($row['degre_urgence']) . "</td>";

                echo "<td class='py-3 px-4'>" . ($row['client'] ? htmlspecialchars($row['client']) : '') . "</td>";
                echo "<td class='py-3 px-4'>" . ($row['intervenant'] ? htmlspecialchars($row['intervenant']) : '') . "</td>";
                echo "<td class='py-3 px-4'>" . ($row['standardiste'] ? htmlspecialchars($row['standardiste']) : '') . "</td>";

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

</body>
</html>
