<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifie si le client est connecté, sinon redirige-le vers la page de connexion
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header("Location: connexion.php");
    exit();
}

$id_client = isset($_GET['idC']) ? $_GET['idC'] : null;

// Détermine l'ordre de tri
$sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date_heure';
$order = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'asc';

// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des interventions terminées du client</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

<!-- Barre de navigation -->
<nav class="p-4 mb-8">
    <div class="mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Client Dashboard</h2>
        <div class="flex">
            <a href="client.php?idC=<?php echo htmlspecialchars($id_client); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i></a>
        </div>
    </div>
</nav>
<h1 class="text-3xl font-bold mb-4 text-center">Historique des Interventions</h1>

<div class="container mx-auto p-2">

    <table class="table min-w-full bg-white">
        <thead class="text-white bg-gray-800">
        <tr>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="client_historique.php?idC=<?php echo htmlspecialchars($id_client); ?>&sort_by=description&order=<?php echo reverseOrder($order); ?>">Description</a>
            </th>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="client_historique.php?idC=<?php echo htmlspecialchars($id_client); ?>&sort_by=date_heure&order=<?php echo reverseOrder($order); ?>">Date</a>
            </th>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="client_historique.php?idC=<?php echo htmlspecialchars($id_client); ?>&sort_by=date_heure&order=<?php echo reverseOrder($order); ?>">Heure</a>
            </th>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="client_historique.php?idC=<?php echo htmlspecialchars($id_client); ?>&sort_by=degre_urgence&order=<?php echo reverseOrder($order); ?>">Degré d'urgence</a>
            </th>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="client_historique.php?idC=<?php echo htmlspecialchars($id_client); ?>&sort_by=intervenant&order=<?php echo reverseOrder($order); ?>">Intervenant</a>
            </th>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="client_historique.php?idC=<?php echo htmlspecialchars($id_client); ?>&sort_by=standardiste&order=<?php echo reverseOrder($order); ?>">Standardiste</a>
            </th>
        </tr>
        </thead>
        <tbody class="text-gray-700">
        <?php
        // Requête pour récupérer les interventions terminées du client avec les noms de l'intervenant et du standardiste
        $stmt_interventions = $pdo->prepare("
        SELECT 
            i.*, 
            CONCAT(inv.prenom, ' ', inv.nom) AS intervenant,
            CONCAT(s.prenom, ' ', s.nom) AS standardiste
        FROM Intervention i
        LEFT JOIN Intervenant inv ON i.ID_Intervenant = inv.ID_Intervenant
        LEFT JOIN Standardiste s ON i.ID_Standardiste = s.ID_Standardiste
        WHERE i.ID_Client = ? AND i.statut = 'Terminé'
        ORDER BY $sortBy $order;
    ");
    $stmt_interventions->execute([$id_client]);
    
        // Vérifie si aucune intervention terminée n'a été trouvée
        if ($stmt_interventions->rowCount() == 0) {
            echo "<tr><td colspan='5'>Aucune intervention terminée trouvée pour ce client.</td></tr>";
        } else {
            while ($row = $stmt_interventions->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td class='py-3 px-4'>" . htmlspecialchars($row['description']) . "</td>";
                
                // Convertir la date_heure en objet DateTime
                $date_heure = new DateTime($row['date_heure']);
                // Afficher la date au format d-m-Y
                echo "<td class='py-3 px-4'>" . $date_heure->format('d-m-Y') . "</td>";
                // Afficher l'heure au format H:i
                echo "<td class='py-3 px-4'>" . $date_heure->format('H:i') . "</td>";
                
                echo "<td class='py-3 px-4'>" . htmlspecialchars($row['degre_urgence']) . "</td>";
                echo "<td class='py-3 px-4'>" . ($row['intervenant'] ? htmlspecialchars($row['intervenant']) : '') . "</td>";
                echo "<td class='py-3 px-4'>" . ($row['standardiste'] ? htmlspecialchars($row['standardiste']) : '') . "</td>";
                echo "</tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Fonction pour déterminer l'ordre de tri inversé
function reverseOrder($currentOrder) {
    return $currentOrder === 'asc' ? 'desc' : 'asc';
}
?>
