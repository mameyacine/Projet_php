<?php

session_start();

// Vérifie si l'utilisateur est connecté en tant que client, sinon le redirige vers la page de connexion
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
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

// Vérifie si l'ID client est passé dans l'URL
if (!isset($_GET['idC'])) {
    echo "ID client non spécifié dans l'URL.";
    exit();
}

$idClient = $_GET['idC'];

// Récupérer les paramètres de tri depuis l'URL
$sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date_heure';
$order = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'asc';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Client</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>




<!-- Barre de navigation -->
<nav class="  p-4 mb-8">
    <div class="mx-auto flex justify-between items-center">
        <h2 class="  text-2xl font-bold">Client Dashboard</h2>
        <div class="flex ">
            <a href="completer.php?idC=<?php echo htmlspecialchars($idClient); ?>" class="button">Modifier mes informations</a>

                    <!-- Bouton de déconnexion -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <button type="submit" name="logout" class="delete bg-red-500 text-white rounded-md"><i class="fas fa-sign-out-alt"></i></button>
            </form>
        
        </div>
    </div>
</nav>
<body>

    <div class="flex justify-between">
        <h1 class="text-3xl font-bold m-4">Mes interventions</h1>
        <div clas="flex">
            <a href="client_commentaire.php?idC=<?php echo htmlspecialchars($idClient); ?>" class="button"><i class="fa-regular fa-comment"></i></a>
         </div>
    </div>

<div class="container mx-auto">
 

    <table class="table min-w-full bg-white">
        <thead class="text-white">
            <tr class="bg-gray-200">
                <th class="border border-gray-400 px-4 py-2">
                    <a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=nom_intervenant&order=<?php echo ($sortBy == 'nom_intervenant' && $order == 'asc') ? 'desc' : 'asc'; ?>">Intervenant</a>
                </th>
                <th class="border border-gray-400 px-4 py-2">
                    <a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=nom_standardiste&order=<?php echo ($sortBy == 'nom_standardiste' && $order == 'asc') ? 'desc' : 'asc'; ?>">Standardiste</a>
                </th>

                <th class="border border-gray-400 px-4 py-2">
                    <a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=description&order=<?php echo ($sortBy == 'description' && $order == 'asc') ? 'desc' : 'asc'; ?>">Description de l'intervention</a>
                </th>
                <th class="border border-gray-400 px-4 py-2">
                    <a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=date_heure&order=<?php echo ($sortBy == 'date_heure' && $order == 'asc') ? 'desc' : 'asc'; ?>">Date</a>
                </th>

                <th class="border border-gray-400 px-4 py-2">
                    <a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=date_heure&order=<?php echo ($sortBy == 'date_heure' && $order == 'asc') ? 'desc' : 'asc'; ?>">Heure</a>
                </th>
                <th class="border border-gray-400 px-4 py-2">
                    <a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=degre_urgence&order=<?php echo ($sortBy == 'degre_urgence' && $order == 'asc') ? 'desc' : 'asc'; ?>">Degré d'urgence</a>
                </th>
                <th class="border border-gray-400 px-4 py-2">
                    <a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=statut&order=<?php echo ($sortBy == 'statut' && $order == 'asc') ? 'desc' : 'asc'; ?>">Statut</a>
                </th>
            </tr>
        </thead>

        <tbody class="text-gray-700">
        <?php
        // Connexion à la base de données
        $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Requête pour récupérer toutes les interventions avec la date/heure et les noms d'utilisateur des intervenants et des standardistes
        $stmt_interventions = $pdo->prepare("
            SELECT Intervention.*, 
                Intervenant.nom_utilisateur AS nom_intervenant, 
                Standardiste.nom_utilisateur AS nom_standardiste
            FROM Intervention
            LEFT JOIN Intervenant ON Intervention.ID_intervenant = Intervenant.ID_intervenant
            LEFT JOIN Standardiste ON Intervention.ID_standardiste = Standardiste.ID_standardiste
            WHERE Intervention.ID_client = ?
            ORDER BY $sortBy $order
        ");


            $stmt_interventions->execute([$idClient]);

            // Boucle principale pour récupérer les données des interventions
            while ($row = $stmt_interventions->fetch(PDO::FETCH_ASSOC)) :
                // Récupérer le nom de l'intervenant
                $stmt_intervenant = $pdo->prepare("SELECT nom_utilisateur FROM Intervenant WHERE ID_intervenant = ?");
                $stmt_intervenant->execute([$row['ID_intervenant']]);
                $intervenant_username = $stmt_intervenant->fetch(PDO::FETCH_COLUMN);


                // Récupérer le nom du standardiste
                $stmt_standardiste = $pdo->prepare("SELECT nom_utilisateur FROM Standardiste WHERE ID_standardiste = ?");
                $stmt_standardiste->execute([$row['ID_standardiste']]);
                $standardiste_username = $stmt_standardiste->fetch(PDO::FETCH_COLUMN);
            ?>
    <tr class="border-b">
        <td class='border-r py-3 px-4'><?php echo htmlspecialchars($intervenant_username); ?></td>
        <td class='border-r py-3 px-4'><?php echo htmlspecialchars($standardiste_username); ?></td>
        <td class='border-r py-3 px-4'><?php echo htmlspecialchars($row['description']); ?></td>
        <?php
        // Séparer la date et l'heure
        $date_heure = new DateTime($row['date_heure']);
        $date = $date_heure->format('d-m-Y');
        $heure = $date_heure->format('H:i');

        echo "<td class='border-r py-3 px-4'>" . htmlspecialchars($date) . "</td>";
        echo "<td class='border-r py-3 px-4'>" . htmlspecialchars($heure) . "</td>";

        echo "<td class='border-r py-3 px-4'>" . htmlspecialchars($row['degre_urgence']) . "</td>";
        echo "<td class='py-3 px-4'>" . htmlspecialchars($row['statut']) . "</td>";
        ?>
    </tr>
<?php endwhile; ?>


        </tbody>
    </table>
</div>
</body>
</html>
