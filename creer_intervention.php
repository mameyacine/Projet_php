<?php

// Get the admin ID from the URL, if available
$id_admin = isset($_GET['idA']) ? $_GET['idA'] : null;

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve form data
    $description = $_POST["description"];
    $statut = $_POST["statut"];
    $degre_urgence = $_POST["degre_urgence"];
    $client_username = $_POST["client"];
    $intervenant_username = $_POST["intervenant"];
    $date_heure = $_POST["date_heure"];

    try {
        // Connect to the database
        $pdo = new PDO("mysql:host=localhost;port=8080;dbname=projet_php", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get the client ID
        $stmt_client = $pdo->prepare("SELECT ID_client FROM Client WHERE nom_utilisateur = ?");
        $stmt_client->execute([$client_username]);
        $client_row = $stmt_client->fetch(PDO::FETCH_ASSOC);
        $client_id = $client_row['ID_client'];

        // Get the intervenant ID
        $stmt_intervenant = $pdo->prepare("SELECT ID_intervenant FROM Intervenant WHERE nom_utilisateur = ?");
        $stmt_intervenant->execute([$intervenant_username]);
        $intervenant_row = $stmt_intervenant->fetch(PDO::FETCH_ASSOC);
        $intervenant_id = $intervenant_row['ID_intervenant'];

        // Check if the intervenant already has an intervention at the same date and time
        $stmt_check_intervention = $pdo->prepare("SELECT * FROM Intervention WHERE ID_intervenant = ? AND date_heure = ?");
        $stmt_check_intervention->execute([$intervenant_id, $date_heure]);
        $intervention_existante = $stmt_check_intervention->fetch(PDO::FETCH_ASSOC);

        if ($intervention_existante) {
            throw new Exception("L'intervenant a déjà une intervention à cette date et heure.");
        }

        // Prepare the intervention insertion query
        $stmt_insert = $pdo->prepare("INSERT INTO Intervention (description, statut, degre_urgence, ID_client, ID_intervenant, date_heure) VALUES (?, ?, ?, ?, ?, ?)");

        // Execute the query
        $stmt_insert->execute([$description, $statut, $degre_urgence, $client_id, $intervenant_id, $date_heure]);

        // Redirect to the page with all interventions
        header("Location: admin.php?idA=" . htmlspecialchars($id_admin));
        exit();
    } catch (PDOException $e) {
        // In case of error, display an error message
        echo "Erreur lors de la création de l'intervention : " . $e->getMessage();
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une intervention</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
</head>


<nav class="  p-4 mb-8">
    <div class=" mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Admin Dashboard</h2>
        <div>
            <a href="gestion_utilisateurs.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button">Gestion des utilisateurs</a>
            <a href="admin.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i> </a>
        </div>
    </div>
</nav>

<body>
    <h1 class="text-3xl text-center font-bold m-4">Créer une intervention</h1>
        <div class=" mx-auto flex justify-center">

            <form action="" method="post" class="form"  >
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="description">Description :</label>
                    <textarea id="description" name="description" class="w-full px-3 py-2  border rounded-lg focus:outline-none" rows="4" placeholder="Entrez la description de l'intervention"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block  text-sm font-bold mb-2" for="statut">Statut :</label>
                    <select id="statut" name="statut" class="w-full px-3 py-2  border rounded-lg focus:outline-none">
                        <option value="En attente">En attente</option>
                        <option value="En cours">En cours</option>
                        <option value="Terminé">Terminée</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block  text-sm font-bold mb-2" for="degre_urgence">Degré d'urgence :</label>
                    <select id="degre_urgence" name="degre_urgence" class="w-full px-3 py-2  border rounded-lg focus:outline-none">
                        <option value="Faible">Faible</option>
                        <option value="Moyen">Moyen</option>
                        <option value="Élevé">Élevé</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block  text-sm font-bold mb-2" for="client">Client :</label>
                    <select id="client" name="client" class="w-full px-3 py-2  border rounded-lg focus:outline-none">
                        <?php
                        // Connexion à la base de données
                        $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Requête pour récupérer les noms d'utilisateurs des clients
                        $stmt_clients = $pdo->query("SELECT nom_utilisateur FROM Client");
                        while ($row = $stmt_clients->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . htmlspecialchars($row['nom_utilisateur']) . "'>" . htmlspecialchars($row['nom_utilisateur']) . "</option>";
                        }
                        ?>
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
                    <label class="block  text-sm font-bold mb-2" for="date_heure">Date et heure :</label>
                    <input type="datetime-local" id="date_heure" name="date_heure" class="w-full px-3 py-2  border rounded-lg focus:outline-none">
                </div>
                <button type="submit" class="button bg-blue-700 py-2 px-4 ">Créer l'intervention</button>
            </form>
    
    </div>

</body>
</html>


