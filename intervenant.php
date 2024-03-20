<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirection si l'utilisateur n'est pas connecté en tant qu'intervenant
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'intervenant') {
    header("Location: connexion.php");
    exit();
}

// Gestion de la déconnexion
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

$id_intervenant = isset($_GET['idINT']) ? $_GET['idINT'] : null;

if ($id_intervenant !== null) {
    $stmt_intervenant = $pdo->prepare("SELECT ID_intervenant FROM Intervenant WHERE ID_intervenant = ?");
    $stmt_intervenant->execute([$id_intervenant]);
    $id_intervenant_row = $stmt_intervenant->fetch(PDO::FETCH_ASSOC);

    if ($id_intervenant_row !== false) {
        // Par défaut, tri par ID_intervention si aucun tri spécifié
        $orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'ID_intervention';
        
        // Par défaut, tri ascendant si aucun ordre spécifié
        $orderDirection = isset($_GET['orderDir']) && strtoupper($_GET['orderDir']) === 'DESC' ? 'DESC' : 'ASC';

        // Préparation de la requête SQL avec le tri
        $stmt_interventions = $pdo->prepare("SELECT i.description, i.date_heure, i.degre_urgence, i.statut,CONCAT(c.prenom, ' ', c.nom) AS client , CONCAT(c.nom_rue, ' ', c.num_rue, ', ', c.code_postal, ' ', c.ville) AS lieu_intervention FROM Intervention i INNER JOIN Client c ON i.ID_client = c.ID_client WHERE i.ID_intervenant = ? AND (i.statut = 'En cours' OR i.statut = 'En attente') ORDER BY $orderBy $orderDirection");
        $stmt_interventions->execute([$id_intervenant]);
        
        
        $interventions = $stmt_interventions->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error_message = "Aucun intervenant correspondant trouvé.";
    }
} else {
    $error_message = "ID intervenant non spécifié.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Intervenant</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"></head>


    
    <!-- Barre de navigation -->
    <nav class="p-4 mb-8">
        <div class="mx-auto flex justify-between items-center">
            <h2 class="text-2xl font-bold">Intervenant Dashboard</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <button type="submit" name="logout" class="delete bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600"><i class="fas fa-sign-out-alt"></i></button>
            </form>
        </div>
    </nav>

<?php if (isset($error_message)) : ?>
    <div class="container mx-auto p-2">
        <h1 class="text-3xl font-bold mb-4">Erreur</h1>
        <p class="text-red-500"><?php echo $error_message; ?></p>
    </div>
<?php else : ?>

    <div class="flex justify-between items-center p-2">
    <h1 class="text-3xl font-bold m-4">Mes interventions</h1>
        <div class="flex items-center"> <!-- Ajout de la classe "flex" -->
            <a href="intervenant_modif.php?idINT=<?php echo htmlspecialchars($id_intervenant); ?>" class="button"><i class="fas fa-edit"></i></a>
            <a href="intervenant_commentaire.php?idINT=<?php echo htmlspecialchars($id_intervenant); ?>" class="button"><i class="fa-regular fa-comment"></i></a>
            <a href="intervenant_historique.php?idINT=<?php echo htmlspecialchars($id_intervenant); ?>" class="button"><i class="fas fa-book"></i></a>
        </div>
    </div>
    <div class="container mx-auto p-2">



       
        <table class="table min-w-full bg-white">
                <thead class=" text-white">
                <tr class="bg-gray-200">
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idINT=<?php echo htmlspecialchars($id_intervenant); ?>&orderBy=nom_utilisateur&orderDir=<?php echo $orderBy === 'nom_utilisateur' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">Client</a></th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idINT=<?php echo htmlspecialchars($id_intervenant); ?>&orderBy=description&orderDir=<?php echo $orderBy === 'description' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">Description de l'intervention</a></th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idINT=<?php echo htmlspecialchars($id_intervenant); ?>&orderBy=date_heure&orderDir=<?php echo $orderBy === 'date_heure' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">Date</a></th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idINT=<?php echo htmlspecialchars($id_intervenant); ?>&orderBy=date_heure&orderDir=<?php echo $orderBy === 'date_heure' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">Heure</a></th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="intervenant.php?idINT=<?php echo htmlspecialchars($id_intervenant); ?>">Lieu</a></th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idINT=<?php echo htmlspecialchars($id_intervenant); ?>&orderBy=degre_urgence&orderDir=<?php echo $orderBy === 'degre_urgence' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">Degré d'urgence</a></th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><a href="?idINT=<?php echo htmlspecialchars($id_intervenant); ?>&orderBy=statut&orderDir=<?php echo $orderBy === 'statut' && $orderDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">Statut</a></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($interventions as $intervention) : ?>
                <tr>
                <td class="py-3 px-4"><?php echo isset($intervention['client']) ? htmlspecialchars($intervention['client']) : ''; ?></td>
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
    </div>
<?php endif; ?>

</body>
</html>
