<?php 
$id_admin = $_GET['idA'];
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
    <nav class="p-4 mb-8">
        <div class="mx-auto flex justify-between items-center">
            <h2 class="text-2xl font-bold">Admin Dashboard</h2>
            <div class="flex">
                <a href="admin.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i></a>
       
            </div>
        </div>
    </nav>


<?php

// Gestion des erreurs et début de session
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Vérifie si l'utilisateur est connecté en tant qu'administrateur, sinon le redirige vers la page de connexion
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

// Vérifie si l'ID de l'administrateur est passé dans l'URL
if (!isset($_GET['idA'])) {
    echo "ID administrateur non spécifié dans l'URL.";
    exit();
}



// Connexion à la base de données (à adapter selon votre configuration)
$pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// Vérifie si le formulaire précédent est soumis et traite la demande de validation ou de refus
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    // Récupérer l'ID de la demande
    $demande_id = $_POST['demande_id'];

    // Valider la demande
    if ($_POST['action'] === 'valider') {
        // Mettre à jour le statut de la demande à "Validée"
        $sql = "UPDATE Demande SET statut_demande = 'validée' WHERE ID_demande = ?";
        // Exécuter la requête SQL
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$demande_id]);

    }
    // Refuser la demande
    elseif ($_POST['action'] === 'refuser') {
        // Mettre à jour le statut de la demande à "Refusée"
        $sql = "UPDATE Demande SET statut_demande = 'refusée' WHERE ID_demande = ?";
        // Exécuter la requête SQL
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$demande_id]);
        // Rediriger vers la même page pour actualiser
        header("Location: voir_demandes.php?idA=" . htmlspecialchars($id_admin));
        exit();
    }
}

// Afficher le formulaire de création d'intervention si une demande a été validée
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'valider') {
    // Récupérer l'ID de la demande validée
    $demande_id_validee = $_POST['demande_id'];

    // Récupérer les informations de la demande validée
    $stmt_demande_validee = $pdo->prepare("SELECT * FROM Demande WHERE ID_demande = ?");
    $stmt_demande_validee->execute([$demande_id_validee]);
    $demande_validee = $stmt_demande_validee->fetch(PDO::FETCH_ASSOC);
    $client_id = $demande_validee['ID_client'];
    //var_dump($client_id);

   
    
    ?>
    <!-- Affichage du formulaire de création d'intervention pré-rempli -->
    <div class=" mx-auto flex justify-center">
        <form action="traitement_demande.php?idA=<?php echo htmlspecialchars($id_admin); ?>" method="post" class="form">
            <div class="mb-4">
                <input type="hidden" name="id_admin" value="<?php echo htmlspecialchars($id_admin); ?>">
                <input type="hidden" name="demande_id" value="<?php echo htmlspecialchars($demande_id_validee); ?>">
                <input type="hidden" name="id_client" value="<?php echo htmlspecialchars($client_id); ?>">
            </div>

            <div class="mb-4">
            <label class="block text-sm font-bold mb-2" for="description">Description :</label><br>
            <textarea id="description" name="description" class="w-full px-3 py-2 border rounded-lg focus:outline-none" rows="4" cols="50"><?php echo htmlspecialchars($demande_validee['description']); ?></textarea><br><br>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-bold mb-2" for="date_heure">Date et heure :</label>
            <input type="datetime-local" id="date_heure" name="date_heure" class="w-full px-3 py-2 border rounded-lg focus:outline-none" value="<?php echo date('Y-m-d\TH:i', strtotime($demande_validee['date_heure'])); ?>">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-bold mb-2" for="statut">Statut :</label>
            <select id="statut" name="statut" class="w-full px-3 py-2 border rounded-lg focus:outline-none">
                <option value="En cours">En cours</option>
                <option value="Terminée">Terminée</option>
                <option value="En attente">En attente</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block  text-sm font-bold mb-2" for="intervenant">Intervenant :</label>
            <select id="intervenant" name="intervenant" class="w-full px-3 py-2  border rounded-lg focus:outline-none">
                <?php
                // Requête pour récupérer les noms d'utilisateurs des intervenants
                $stmt_intervenants = $pdo->query("SELECT nom_utilisateur FROM Intervenant");
                while ($row = $stmt_intervenants->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . htmlspecialchars($row['nom_utilisateur']) . "'>" . htmlspecialchars($row['nom_utilisateur']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-bold mb-2" for="urgence">Degré d'urgence :</label>
            <select id="urgence" name="urgence" class="w-full px-3 py-2 border rounded-lg focus:outline-none">
                <option value="Faible" <?php if ($demande_validee['degre_urgence'] === 'Faible') echo 'selected'; ?>>Faible</option>
                <option value="Moyen" <?php if ($demande_validee['degre_urgence'] === 'Moyen') echo 'selected'; ?>>Moyen</option>
                <option value="Élevé" <?php if ($demande_validee['degre_urgence'] === 'Élevé') echo 'selected'; ?>>Élevé</option>
</select>
</div>
    <button type="submit" class="button">Créer Intervention</button>

    </form>
</div>
<?php
} else {
    $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date_heure';
$order = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'asc';

    // Afficher le tableau des demandes uniquement si le formulaire de création d'intervention n'est pas affiché
    if (isset($pdo)) {
    // Récupérer toutes les demandes
    $stmt_demandes = $pdo->prepare("SELECT * FROM Demande WHERE statut_demande != 'validée'     ORDER BY $sortBy $order
    ");
    $stmt_demandes->execute();
    } else {
    echo "Erreuer de connexion";
    }
    

    ?>
    <div class="container mx-auto p-2">
        <h1 class="text-3xl font-bold mb-4 text-center">Demandes</h1>
        <table class="table min-w-full bg-white">
            <thead class="text-white">
                <tr class="bg-gray-200">
                <th class="border border-gray-400 px-4 py-2"><a href="?idA=<?php echo htmlspecialchars($id_admin); ?>&sort_by=description&order=<?php echo ($sortBy == 'description' && $order == 'asc') ? 'desc' : 'asc'; ?>">Description</a></th>
                <th class="border border-gray-400 px-4 py-2"><a href="?idA=<?php echo htmlspecialchars($id_admin); ?>&sort_by=date_heure&order=<?php echo ($sortBy == 'date_heure' && $order == 'asc') ? 'desc' : 'asc'; ?>">Date</a></th>
                <th class="border border-gray-400 px-4 py-2"><a href="?idA=<?php echo htmlspecialchars($id_admin); ?>&sort_by=date_heure&order=<?php echo ($sortBy == 'date_heure' && $order == 'asc') ? 'desc' : 'asc'; ?>">Heure</a></th>
     
                <th class="border border-gray-400 px-4 py-2"><a href="?idA=<?php echo htmlspecialchars($id_admin); ?>&sort_by=degre_urgence&order=<?php echo ($sortBy == 'degre_urgence' && $order == 'asc') ? 'desc' : 'asc'; ?>">Degré d'urgence</a></th>
                <th class="border border-gray-400 px-4 py-2"><a href="?idA=<?php echo htmlspecialchars($id_admin); ?>&sort_by=statut_demande&order=<?php echo ($sortBy == 'statut_demande' && $order == 'asc') ? 'desc' : 'asc'; ?>">Statut de la demande</a></th>

                    <th class="border border-gray-400 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt_demandes->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php
                    // Séparer la date et l'heure
                    $date_heure = new DateTime($row['date_heure']);
                    $date = $date_heure->format('d-m-Y');
                    $heure = $date_heure->format('H:i');
                    ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['description']); ?></td>
                        <td class="border py-3 px-4"><?php echo htmlspecialchars($date); ?></td>
                        <td class="border py-3 px-4"><?php echo htmlspecialchars($heure); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['degre_urgence']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['statut_demande']); ?></td>
                        <td class="border px-4 py-2">
                            <form action="" method="post">
                                <input type="hidden" name="demande_id" value="<?php echo htmlspecialchars($row['ID_demande']); ?>">
                                <button type="submit" name="action" value="valider" class="button"><i class="fa-solid fa-check"></i></button>
                                <button type="submit" name="action" value="refuser" class="button"><i class="fa-solid fa-ban"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php
    }
    ?>
    
    </body>
    </html>