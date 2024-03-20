<?php
$id_standardiste = isset($_GET['idST']) ? $_GET['idST'] : null;

// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Déterminer le champ de tri et l'ordre
$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'date_heure';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Requête pour récupérer toutes les interventions avec la date/heure et les noms des clients, intervenants et standardistes
$stmt_interventions = $pdo->prepare("
    SELECT Intervention.*, CONCAT(Client.prenom, ' ', Client.nom) AS client ,CONCAT(Intervenant.prenom, ' ', Intervenant.nom) AS intervenant , CONCAT(standardiste.prenom, ' ', standardiste.nom) AS standardiste
    FROM Intervention
    LEFT JOIN Client ON Intervention.ID_client = Client.ID_client
    LEFT JOIN Intervenant ON Intervention.ID_intervenant = Intervenant.ID_intervenant
    LEFT JOIN Standardiste ON Intervention.ID_standardiste = Standardiste.ID_standardiste
    WHERE  (Intervention.statut = 'En cours' OR Intervention.statut = 'En attente' ) 

    ORDER BY $orderBy $order
");
$stmt_interventions->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les interventions</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"></head>
<body>

<!-- Barre de navigation -->
<nav class="p-4 mb-8">
        <div class="mx-auto flex justify-between items-center">
            <h2 class="text-2xl font-bold">Standardiste Dashboard</h2>
            <div>
            <a href="clients_tous.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button">Tous les clients</a>
                <a href="standardiste.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i> </a>
            </div>
        </div>
    </nav>

<!-- Contenu de la page -->
<div class="mx-auto flex justify-between">
    <h1 class="text-3xl font-bold m-4 pt-4 text-center">Toutes les interventions</h1>
    <div class="flex items-center"> <!-- Ajout de la classe "items-center" pour centrer verticalement -->
        <a href="standardiste_historiquevoirtout.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class=" button mr-4 inline-flex items-center"> <!-- Ajout de classes pour styliser le bouton -->
            <i class="fas fa-book mr-2"></i> <!-- Ajout de la classe "mr-2" pour un espace entre l'icône et le texte -->

        </a>
    </div>
</div>

<div class="container mx-auto p-2">

    <table class="table min-w-full bg-white">
        <thead class=" text-white">
        <tr>
        <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="voir_tout.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=description&order=<?php echo ($orderBy == 'description' && $order == 'asc') ? 'desc' : 'asc'; ?>">Description</a>
            </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="voir_tout.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=statut&order=<?php echo ($orderBy == 'statut' && $order == 'asc') ? 'desc' : 'asc'; ?>">Statut</a>
            </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="voir_tout.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=degre_urgence&order=<?php echo ($orderBy == 'degre_urgence' && $order == 'asc') ? 'desc' : 'asc'; ?>">Degré d'urgence</a>
            </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="voir_tout.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=client_nom&order=<?php echo ($orderBy == 'client_nom' && $order == 'asc') ? 'desc' : 'asc'; ?>">Client</a>
            </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="voir_tout.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=intervenant_nom&order=<?php echo ($orderBy == 'intervenant_nom' && $order == 'asc') ? 'desc' : 'asc'; ?>">Intervenant</a>
            </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="voir_tout.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=standardiste_nom&order=<?php echo ($orderBy == 'standardiste_nom' && $order == 'asc') ? 'desc' : 'asc'; ?>">Standardiste</a>
            </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="voir_tout.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=date_heure&order=<?php echo ($orderBy == 'date_heure' && $order == 'asc') ? 'desc' : 'asc'; ?>">Date</a>
            </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                <a href="voir_tout.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=date_heure&order=<?php echo ($orderBy == 'date_heure' && $order == 'asc') ? 'desc' : 'asc'; ?>">Heure</a>
            </th>
        </tr>
        </thead>
        <tbody class="text-gray-700">
        <?php
        // Boucle pour afficher les interventions
        while ($row = $stmt_interventions->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td class='py-3 px-4'>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td class='py-3 px-4'>" . htmlspecialchars($row['statut']) . "</td>";
            echo "<td class='py-3 px-4'>" . htmlspecialchars($row['degre_urgence']) . "</td>";
            echo "<td class='py-3 px-4'>" . htmlspecialchars($row['client']) . "</td>";
            echo "<td class='py-3 px-4'>" . htmlspecialchars($row['intervenant']) . "</td>";
            echo "<td class='py-3 px-4'>" . htmlspecialchars($row['standardiste']) . "</td>";
            echo "<td class='py-3 px-4'>" . htmlspecialchars(date('d-m-Y', strtotime($row['date_heure']))) . "</td>";
            echo "<td class='py-3 px-4'>" . htmlspecialchars(date('H:i', strtotime($row['date_heure']))) . "</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>
