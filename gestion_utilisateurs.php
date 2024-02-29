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

// Connexion à la base de données (remplacez les paramètres par les vôtres)
try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Fonction pour récupérer les utilisateurs par rôle avec les détails et trier les données
function getUtilisateursDetails($pdo, $table, $orderColumn, $sortDirection) {
    $stmt = $pdo->prepare("SELECT prenom, nom, nom_utilisateur FROM $table ORDER BY $orderColumn $sortDirection");
    $stmt->execute();
    $utilisateurs = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $utilisateurs[] = $row;
    }
    return [$utilisateurs, $sortDirection];
}

// Récupérer les utilisateurs par rôle avec les détails et trier les données
$sort_clients = isset($_GET['sort_clients']) ? $_GET['sort_clients'] : 'nom';
$sort_standardistes = isset($_GET['sort_standardistes']) ? $_GET['sort_standardistes'] : 'nom';
$sort_intervenants = isset($_GET['sort_intervenants']) ? $_GET['sort_intervenants'] : 'nom';
$sort_direction_clients = isset($_GET['sort_direction_clients']) ? $_GET['sort_direction_clients'] : 'asc';
$sort_direction_standardistes = isset($_GET['sort_direction_standardistes']) ? $_GET['sort_direction_standardistes'] : 'asc';
$sort_direction_intervenants = isset($_GET['sort_direction_intervenants']) ? $_GET['sort_direction_intervenants'] : 'asc';

if(isset($_GET['sort_clients'])){
    $sort_direction_clients = $sort_direction_clients === 'asc' ? 'desc' : 'asc';
}
if(isset($_GET['sort_standardistes'])){
    $sort_direction_standardistes = $sort_direction_standardistes === 'asc' ? 'desc' : 'asc';
}
if(isset($_GET['sort_intervenants'])){
    $sort_direction_intervenants = $sort_direction_intervenants === 'asc' ? 'desc' : 'asc';
}

list($clients, $sort_direction_clients) = getUtilisateursDetails($pdo, "Client", $sort_clients, $sort_direction_clients);
list($standardistes, $sort_direction_standardistes) = getUtilisateursDetails($pdo, "Standardiste", $sort_standardistes, $sort_direction_standardistes);
list($intervenants, $sort_direction_intervenants) = getUtilisateursDetails($pdo, "Intervenant", $sort_intervenants, $sort_direction_intervenants);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">    
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"></head>







<!-- Barre de navigation -->
<nav class="p-4 mb-8">
    <div class="mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Admin Dashboard</h2>
        <div class="flex">
            <a href="admin.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button">Toutes les interventions</a>
            <a href="detail_intervention.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button">Détail d'intervention</a>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <button type="submit" name="logout" class="delete bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600"><i class="fas fa-sign-out-alt"></i></button>
            </form>
        </div>
    </div>
</nav>


<body>

    <div class="flex justify-between p-2">
        <h1 class="text-3xl font-bold ">Gestion des utilisateurs</h1>
        <div clas="flex">
            <a href="ajouter_utilisateur.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button"><i class="fas fa-plus-circle"></i></a>
            <a href="modifier_utilisateur.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button"><i class="fas fa-edit"></i></a>
    
        </div>
    </div>
    

    <div class="container mx-auto">
 

    <!-- Tableau des utilisateurs -->
    <table class="table w-full border-collapse border border-gray-500 mb-8">
        <!-- Entête du tableau -->
        <thead class="text-white">
            <tr class="">
                <th class="border border-gray-500 px-4 py-2" colspan="3">Clients</th>
                <th class="border border-gray-500 px-4 py-2" colspan="3">Standardistes</th>
                <th class="border border-gray-500 px-4 py-2" colspan="3">Intervenants</th>
            </tr>
            <tr class="">
                <th class="border border-gray-500 px-4 py-2"><a href="?sort_clients=nom&sort_direction_clients=<?php echo htmlspecialchars($sort_direction_clients); ?>&idA=<?php echo htmlspecialchars($id_admin); ?>">Nom</a></th>
                <th class="border border-gray-500 px-4 py-2"><a href="?sort_clients=prenom&sort_direction_clients=<?php echo htmlspecialchars($sort_direction_clients); ?>&idA=<?php echo htmlspecialchars($id_admin); ?>">Prénom</a></th>
                <th class="border border-gray-500 px-4 py-2"><a href="?sort_clients=nom_utilisateur&sort_direction_clients=<?php echo htmlspecialchars($sort_direction_clients); ?>&idA=<?php echo htmlspecialchars($id_admin); ?>">Nom d'utilisateur</a></th>
                <th class="border border-gray-500 px-4 py-2"><a href="?sort_standardistes=nom&sort_direction_standardistes=<?php echo htmlspecialchars($sort_direction_standardistes); ?>&idA=<?php echo htmlspecialchars($id_admin); ?>">Nom</a></th>
                <th class="border border-gray-500 px-4 py-2"><a href="?sort_standardistes=prenom&sort_direction_standardistes=<?php echo htmlspecialchars($sort_direction_standardistes); ?>&idA=<?php echo htmlspecialchars($id_admin); ?>">Prénom</a></th>
                <th class="border border-gray-500 px-4 py-2"><a href="?sort_standardistes=nom_utilisateur&sort_direction_standardistes=<?php echo htmlspecialchars($sort_direction_standardistes); ?>&idA=<?php echo htmlspecialchars($id_admin); ?>">Nom d'utilisateur</a></th>
                <th class="border border-gray-500 px-4 py-2"><a href="?sort_intervenants=nom&sort_direction_intervenants=<?php echo htmlspecialchars($sort_direction_intervenants); ?>&idA=<?php echo htmlspecialchars($id_admin); ?>">Nom</a></th>
                <th class="border border-gray-500 px-4 py-2"><a href="?sort_intervenants=prenom&sort_direction_intervenants=<?php echo htmlspecialchars($sort_direction_intervenants); ?>&idA=<?php echo htmlspecialchars($id_admin); ?>">Prénom</a></th>
                <th class="border border-gray-500 px-4 py-2"><a href="?sort_intervenants=nom_utilisateur&sort_direction_intervenants=<?php echo htmlspecialchars($sort_direction_intervenants); ?>&idA=<?php echo htmlspecialchars($id_admin); ?>">Nom d'utilisateur</a></th>
            </tr>
        </thead>
        <!-- Contenu du tableau -->
        <tbody>
            <?php for ($i = 0; $i < max(count($clients), count($standardistes), count($intervenants)); $i++) : ?>
                <tr>
                    <!-- Colonnes pour les clients -->
                    <td class="border border-gray-500 px-4 py-2">
                        <?php if (isset($clients[$i])) : ?>
                            <p><?= htmlspecialchars($clients[$i]['nom']); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="border border-gray-500 px-4 py-2">
                        <?php if (isset($clients[$i])) : ?>
                            <p><?= htmlspecialchars($clients[$i]['prenom']); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="border border-gray-500 px-4 py-2">
                        <?php if (isset($clients[$i])) : ?>
                            <p><?= htmlspecialchars($clients[$i]['nom_utilisateur']); ?></p>
                        <?php endif; ?>
                    </td>
                    <!-- Colonnes pour les standardistes -->
                    <td class="border border-gray-500 px-4 py-2">
                        <?php if (isset($standardistes[$i])) : ?>
                            <p><?= htmlspecialchars($standardistes[$i]['nom']); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="border border-gray-500 px-4 py-2">
                        <?php if (isset($standardistes[$i])) : ?>
                            <p><?= htmlspecialchars($standardistes[$i]['prenom']); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="border border-gray-500 px-4 py-2">
                        <?php if (isset($standardistes[$i])) : ?>
                            <p><?= htmlspecialchars($standardistes[$i]['nom_utilisateur']); ?></p>
                        <?php endif; ?>
                    </td>
                    <!-- Colonnes pour les intervenants -->
                    <td class="border border-gray-500 px-4 py-2">
                        <?php if (isset($intervenants[$i])) : ?>
                            <p><?= htmlspecialchars($intervenants[$i]['nom']); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="border border-gray-500 px-4 py-2">
                        <?php if (isset($intervenants[$i])) : ?>
                            <p><?= htmlspecialchars($intervenants[$i]['prenom']); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="border border-gray-500 px-4 py-2">
                        <?php if (isset($intervenants[$i])) : ?>
                            <p><?= htmlspecialchars($intervenants[$i]['nom_utilisateur']); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>

</body>
</html>
