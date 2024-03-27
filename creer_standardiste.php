

<?php

$id_standardiste = isset($_GET['idST']) ? $_GET['idST'] : null;

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $description = $_POST['description'];
    $statut = $_POST['statut'];
    $degre_urgence = $_POST['degre_urgence'];
    $client = $_POST['client'];
    $date_heure = $_POST['date_heure'];
    
    // Récupérer l'ID du standardiste depuis l'URL
    $id_standardiste = $_GET['idST'];

    try {
        // Connexion à la base de données
        $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer l'ID du client à partir de son nom d'utilisateur
        $stmt_client = $pdo->prepare("SELECT ID_client FROM Client WHERE nom_utilisateur = ?");
        $stmt_client->execute([$client]);
        $row = $stmt_client->fetch(PDO::FETCH_ASSOC);
        
        // Vérifier si le client existe
        if (!$row) {
            throw new Exception("Le client spécifié n'existe pas.");
        }
        
        // Récupérer l'ID du client
        $id_client = $row['ID_client'];
        // Vérifier si le standardiste a déjà une intervention à la même date et heure
        $stmt_check_intervention = $pdo->prepare("SELECT * FROM Intervention WHERE ID_standardiste = ? AND date_heure = ?");
        $stmt_check_intervention->execute([$id_standardiste, $date_heure]);
        $intervention_existante = $stmt_check_intervention->fetch(PDO::FETCH_ASSOC);

        if ($intervention_existante) {
            throw new Exception("Le standardiste a déjà une intervention à cette date et heure.");
        }


        // Requête d'insertion de l'intervention
        $stmt_insert_intervention = $pdo->prepare("INSERT INTO Intervention (description, date_heure, degre_urgence, statut, ID_standardiste, ID_client) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert_intervention->execute([$description, $date_heure, $degre_urgence, $statut, $id_standardiste, $id_client]);

        // Redirection vers une page de confirmation ou de tableau de bord
        header("Location: standardiste.php?idST=" . htmlspecialchars($id_standardiste));
        exit();
    } catch (PDOException $e) {
        // Gérer les erreurs de base de données
        echo "Erreur de base de données : " . $e->getMessage();
    } catch (Exception $e) {
        // Gérer d'autres erreurs
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<nav class="  p-4 mb-8">
    <div class="mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Standardiste Dashboard</h2>
        <a href="standardiste.php?idST=<?php echo htmlspecialchars($id_standardiste); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i></a>
       

    </div>
</nav>
<body>



    <h1 class="text-3xl text-center font-bold m-4">Créer une intervention</h1>

    <div class=" mx-auto flex justify-center">

        <form action="" method="post" class="form">
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2" for="description">Description :</label>
                <textarea id="description" name="description" class="w-full px-3 py-2 border rounded-lg focus:outline-none" rows="4" placeholder="Entrez la description de l'intervention"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2" for="statut">Statut :</label>
                <select id="statut" name="statut" class="w-full px-3 py-2 border rounded-lg focus:outline-none">
                    <option value="En attente">En attente</option>
                    <option value="En cours">En cours</option>
                    <option value="Terminé">Terminée</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2" for="degre_urgence">Degré d'urgence :</label>
                <select id="degre_urgence" name="degre_urgence" class="w-full px-3 py-2 border rounded-lg focus:outline-none">
                    <option value="Faible">Faible</option>
                    <option value="Moyen">Moyen</option>
                    <option value="Élevé">Élevé</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2" for="client">Client :</label>
                <select id="client" name="client" class="w-full px-3 py-2 border rounded-lg focus:outline-none">
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
                <label class="block text-sm font-bold mb-2" for="date_heure">Date et heure :</label>
                <input type="datetime-local" id="date_heure" name="date_heure" class="w-full px-3 py-2 border rounded-lg focus:outline-none">
            </div>
            <button type="submit" class="button bg-blue-700 py-2 px-4 ">Créer l'intervention</button>
        </form>
    </div>

</body>
</html>