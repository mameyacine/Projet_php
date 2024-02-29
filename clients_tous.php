<?php

$id_standardiste = isset($_GET['idST']) ? $_GET['idST'] : null;

// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Paramètres de tri par défaut
$sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'nom';
$order = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'asc';

// Requête SQL pour récupérer tous les clients avec tri
$stmt = $pdo->prepare("SELECT * FROM Client ORDER BY $sortBy $order");
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour inverser l'ordre de tri
function reverseOrder($order)
{
    return $order === 'asc' ? 'desc' : 'asc';
}

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
            <a href="voir_tout.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button">Toutes les interventions</a>
                <a href="standardiste.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i> </a>
            </div>
        </div>
    </nav>



<div class="container mx-auto">
    <h1 class="text-3xl font-bold m-4 pt-4 text-center">Tous les clients</h1>

    <!-- Tableau des clients -->
    
    <table class="table min-w-full bg-white">
        <thead class=" text-white">
            <tr>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">

                    <a href="?idST=<?php echo htmlspecialchars($id_standardiste); ?>&sort_by=nom&order=<?php echo reverseOrder($order); ?>">Nom</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">

                    <a href="?idST=<?php echo htmlspecialchars($id_standardiste); ?>&sort_by=prenom&order=<?php echo reverseOrder($order); ?>">Prénom</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">

                    <a href="?idST=<?php echo htmlspecialchars($id_standardiste); ?>&sort_by=nom_utilisateur&order=<?php echo reverseOrder($order); ?>">Nom d'utilisateur</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">

                    <a href="?idST=<?php echo htmlspecialchars($id_standardiste); ?>&sort_by=telephone&order=<?php echo reverseOrder($order); ?>">Téléphone</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">

                    <a href="clients_tous.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>">Adresse</a>
                </th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">

                    <a href="?idST=<?php echo htmlspecialchars($id_standardiste); ?>&sort_by=email&order=<?php echo reverseOrder($order); ?>">Email</a>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($client['nom']); ?></td>
                    <td><?php echo htmlspecialchars($client['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($client['nom_utilisateur']); ?></td>
                    <td><?php echo htmlspecialchars($client['telephone']); ?></td>
                    <td><?php echo htmlspecialchars($client['num_rue'] . ' ' . $client['nom_rue'] . ', ' . $client['code_postal'] . ' ' . $client['ville']); ?></td>
                    <td><?php echo htmlspecialchars($client['email']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
