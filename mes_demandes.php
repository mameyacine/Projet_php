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

// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Requête pour récupérer toutes les interventions avec la date/heure et les noms d'utilisateur des intervenants et des standardistes
// Requête pour récupérer toutes les interventions avec la date/heure et les noms d'utilisateur des intervenants et des standardistes
$stmt_interventions = $pdo->prepare("
    SELECT * 
    FROM Demande
    WHERE id_client = ?
    ORDER BY $sortBy $order
");


$stmt_interventions->execute([$idClient]);
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
<body>

<!-- Barre de navigation -->
<nav class="p-4 mb-8">
    <div class="mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Client Dashboard</h2>
        <div class="flex">
            <a href="client.php?idC=<?php echo htmlspecialchars($idClient); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i> </a>

        </div>
    </div>
</nav>


<div class="flex justify-between items-center p-2">
    <h1 class="text-3xl font-bold m-4">Mes demandes</h1>
        <div class="flex items-center"> 
             <a href="demande.php?idC=<?php echo htmlspecialchars($idClient); ?>" class="button "><i class="fas fa-plus-circle"></i></a>

        </div>
    </div>
<div class="container mx-auto p-2">
    <table class="table min-w-full bg-white">
        <thead class="text-white">
            <tr class="bg-gray-200">
                <th class="border border-gray-400 px-4 py-2"><a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=description&order=<?php echo ($sortBy == 'description' && $order == 'asc') ? 'desc' : 'asc'; ?>">Description</a></th>
                <th class="border border-gray-400 px-4 py-2"><a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=date_heure&order=<?php echo ($sortBy == 'date_heure' && $order == 'asc') ? 'desc' : 'asc'; ?>">Date</a></th>
                <th class="border border-gray-400 px-4 py-2"><a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=date_heure&order=<?php echo ($sortBy == 'date_heure' && $order == 'asc') ? 'desc' : 'asc'; ?>">Heure</a></th>

                <th class="border border-gray-400 px-4 py-2"><a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=degre_urgence&order=<?php echo ($sortBy == 'degre_urgence' && $order == 'asc') ? 'desc' : 'asc'; ?>">Degré d'urgence</a></th>
                <th class="border border-gray-400 px-4 py-2"><a href="?idC=<?php echo htmlspecialchars($idClient); ?>&sort_by=statut_demande&order=<?php echo ($sortBy == 'statut_demande' && $order == 'asc') ? 'desc' : 'asc'; ?>">Statut de la demande</a></th>

            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php while ($row = $stmt_interventions->fetch(PDO::FETCH_ASSOC)): ?>
                <tr class="border-b">

                    <td class="border-r py-3 px-4"><?php echo htmlspecialchars($row['description']); ?></td>
                    <?php
                    // Séparer la date et l'heure
                    $date_heure = new DateTime($row['date_heure']);
                    $date = $date_heure->format('d-m-Y');
                    $heure = $date_heure->format('H:i');
                    ?>
                    <td class="border-r py-3 px-4"><?php echo htmlspecialchars($date); ?></td>
                    <td class="border-r py-3 px-4"><?php echo htmlspecialchars($heure); ?></td>
                    <td class="border-r py-3 px-4"><?php echo htmlspecialchars($row['degre_urgence']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['statut_demande']); ?></td>

                    </tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</body>
</html>
