<?php
session_start(); // Démarre la session

// Vérifie si l'utilisateur est connecté en tant que standardiste, sinon redirige-le vers la page de connexion
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'standardiste') {
    header("Location: connexion.php");
    exit();
}

$id_standardiste = isset($_GET['idST']) ? $_GET['idST'] : null;

// Détermine l'ordre de tri
$orderBy = isset($_GET['sort']) ? $_GET['sort'] : 'date_heure';
$orderDirection = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Requête pour récupérer les interventions terminées du standardiste
$stmt_interventions = $pdo->prepare("SELECT i.description, i.date_heure, i.degre_urgence, i.statut, CONCAT(c.prenom, ' ', c.nom) AS client , CONCAT(c.num_rue, ' ', c.nom_rue, ', ', c.code_postal, ' ', c.ville) AS lieu_intervention FROM Intervention i INNER JOIN Client c ON i.ID_client = c.ID_client WHERE i.ID_standardiste = ? AND i.statut = 'Terminé' ORDER BY $orderBy $orderDirection");
$stmt_interventions->execute([$id_standardiste]);

// Vérifie si aucune intervention terminée n'a été trouvée
if ($stmt_interventions->rowCount() == 0) {
    $message = "Aucune intervention terminée trouvée.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des interventions du standardiste</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

<!-- Barre de navigation -->
<nav class="p-4 mb-8">
    <div class="mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Standardiste Dashboard</h2>
        <div class="flex">
        <a href="standardiste.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i> </a>
        </div>
    </div>
</nav>
<h1 class="text-3xl font-bold mb-4 text-center">Historique de Mes Interventions</h1>

<div class="container mx-auto p-2">

    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php else: ?>
        <table class="table min-w-full bg-white">
            <thead class="text-white bg-gray-800">
            <tr>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="standardiste_historique.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&sort=description&order=<?php echo ($orderBy === 'description' && $orderDirection === 'ASC') ? 'desc' : 'asc'; ?>">Description</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="standardiste_historique.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&sort=date_heure&order=<?php echo ($orderBy === 'date_heure' && $orderDirection === 'ASC') ? 'desc' : 'asc'; ?>">Date</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="standardiste_historique.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&sort=date_heure&order=<?php echo ($orderBy === 'date_heure' && $orderDirection === 'ASC') ? 'desc' : 'asc'; ?>">Heure</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="standardiste_historique.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&sort=date_heure&order=<?php echo ($orderBy === 'date_heure' && $orderDirection === 'ASC') ? 'desc' : 'asc'; ?>">Degré d'urgence</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="standardiste_historique.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&sort=date_heure&order=<?php echo ($orderBy === 'date_heure' && $orderDirection === 'ASC') ? 'desc' : 'asc'; ?>">Client</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">
                    <a href="standardiste_historique.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>&sort=date_heure&order=<?php echo ($orderBy === 'date_heure' && $orderDirection === 'ASC') ? 'desc' : 'asc'; ?>">Lieu</a>
                </th>
            </tr>
            </thead>
            <tbody class="text-gray-700">
            <?php while ($row = $stmt_interventions->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['description']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars(date("d-m-Y", strtotime($row['date_heure']))); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars(date("H:i", strtotime($row['date_heure']))); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['degre_urgence']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['client']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['lieu_intervention']); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
