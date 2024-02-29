<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'standardiste') {
    header("Location: connexion.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: connexion.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$id_standardiste = isset($_GET['idST']) ? $_GET['idST'] : null;

// Gestion du tri
$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'nom_utilisateur';
$orderDirection = isset($_GET['orderDir']) ? $_GET['orderDir'] : 'ASC'; // Par défaut, le tri est ascendant
$allowedColumns = ['nom_utilisateur', 'description', 'date_heure', 'degre_urgence', 'statut'];
if (!in_array($orderBy, $allowedColumns)) {
    $orderBy = 'nom_utilisateur';
}

if ($id_standardiste !== null) {
    $stmt_standardiste = $pdo->prepare("SELECT ID_standardiste FROM standardiste WHERE ID_standardiste = ?");
    $stmt_standardiste->execute([$id_standardiste]);
    $id_standardiste_row = $stmt_standardiste->fetch(PDO::FETCH_ASSOC);

    if ($id_standardiste_row !== false) {
        $stmt_interventions = $pdo->prepare("SELECT i.description, i.date_heure, i.degre_urgence, i.statut, c.nom_utilisateur, CONCAT(c.nom_rue, ' ', c.num_rue, ', ', c.code_postal, ' ', c.ville) AS lieu_intervention FROM Intervention i INNER JOIN Client c ON i.ID_client = c.ID_client WHERE i.ID_standardiste = ? ORDER BY $orderBy $orderDirection");
        $stmt_interventions->execute([$id_standardiste]);
        $interventions = $stmt_interventions->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error_message = "Aucun standardiste correspondant trouvé.";
    }
} else {
    $error_message = "ID standardiste non spécifié.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Standardiste</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"></head>


<!-- Barre de navigation -->
<nav class="p-4 mb-8">
    <div class="mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Standardiste Dashboard</h2>
        <div class="flex items-center"> <!-- Ajout de la classe "items-center" pour aligner les éléments verticalement -->
            <a href="voir_tout.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button" >Toutes les interventions</a>
            <a href="clients_tous.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button">Tous les clients</a>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <button type="submit" name="logout" class="delete bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600"><i class="fas fa-sign-out-alt"></i></button>
                </form>
        </div>
    </div>
</nav>




<body>

<?php if (isset($error_message)) : ?>
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-4">Erreur</h1>
        <p class="text-red-500"><?php echo $error_message; ?></p>
    </div>
<?php else : ?>

    <div class="flex justify-between p-2">
        <h1 class="text-3xl font-bold m-4">Mes Interventions</h1>
        <div clas="flex">
            <a href="creer_standardiste.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button"><i class="fas fa-plus-circle"></i></a>
            <a href="standardiste_modif.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button"><i class="fas fa-edit"></i></a>
            <a href="standardiste_commentaire.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button"><i class="fa-regular fa-comment"></i></a>
        </div>
    </div>

    <div class="container mx-auto">
    
        <?php if (isset($interventions)) : ?>
            <table class="table min-w-full bg-white">
                <thead class=" text-white">
                    <tr class="bg-gray-200">
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=nom_utilisateur&orderDir=<?php echo ($orderBy == 'nom_utilisateur' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Client</a></th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=description&orderDir=<?php echo ($orderBy == 'description' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Description de l'intervention</a></th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=date_heure&orderDir=<?php echo ($orderBy == 'date_heure' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Date</a></th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=date_heure&orderDir=<?php echo ($orderBy == 'date_heure' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Heure</a></th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="standardiste.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>">Lieu</a></th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=degre_urgence&orderDir=<?php echo ($orderBy == 'degre_urgence' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Degré d'urgence</a></th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idST=<?php echo htmlspecialchars($id_standardiste); ?>&orderBy=statut&orderDir=<?php echo ($orderBy == 'statut' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Statut</a></th>
                    </tr>
                </thead>
                <tbody  class="text-gray-700">
                    <?php foreach ($interventions as $intervention) : ?>
                        <tr>
                            <td class="py-3 px-4"><?php echo isset($intervention['nom_utilisateur']) ? htmlspecialchars($intervention['nom_utilisateur']) : ''; ?></td>
                            <td class="py-3 px-4"><?php echo isset($intervention['description']) ? htmlspecialchars($intervention['description']) : ''; ?></td>
                            <td class="py-3 px-4"><?php echo isset($intervention['date_heure']) ? htmlspecialchars(date("d-m-Y", strtotime($intervention['date_heure']))) : ''; ?></td>
                            <td class="py-3 px-4"><?php echo isset($intervention['date_heure']) ? htmlspecialchars(date("H:i", strtotime($intervention['date_heure']))) : ''; ?></td>
                            <td class="py-3 px-4"><?php echo isset($intervention['lieu_intervention']) ? htmlspecialchars($intervention['lieu_intervention']) : ''; ?></td>
                            <td class="py-3 px-4"><?php echo isset($intervention['degre_urgence']) ? htmlspecialchars($intervention['degre_urgence']) : ''; ?></td>
                            <td class="py-3 px-4"><?php echo isset($intervention['statut']) ? htmlspecialchars($intervention['statut']) : ''; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune intervention trouvée pour cet intervenant.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

</body>
</html>
