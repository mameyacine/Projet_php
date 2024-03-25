<?php

session_start();
$id_client = isset($_GET['idC']) ? $_GET['idC'] : null;

// Vérifie si l'utilisateur est connecté en tant que client, sinon le redirige vers la page de connexion
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
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

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $description = $_POST['description'];
    $degre_urgence = $_POST['degre_urgence'];
    $date_heure = $_POST['date_heure'];
    
    // Assurez-vous d'effectuer une validation et une évasion appropriées des données avant de les utiliser dans la requête SQL pour éviter les attaques par injection SQL
    
    // Connexion à la base de données (à adapter selon votre configuration)
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Requête SQL pour insérer les données
    $sql = "INSERT INTO Demande (ID_client,description, degre_urgence, date_heure) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ $id_client, $description, $degre_urgence, $date_heure]);
    
    if ($stmt) {
        header("Location: mes_demandes.php?idC=" . htmlspecialchars($id_client));
    } else {
        echo "Erreur lors de la création de la demande d'intervention.";
    }
}

// Maintenant, nous affichons le formulaire dans le même fichier
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
        <h2 class="text-2xl font-bold">Client Dashboard</h2>
        <div>
            <a href="mes_demandes.php?idC=<?php echo htmlspecialchars($id_client); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i> </a>
        </div>
    </div>
</nav>

<body>
    <h1 class="text-3xl text-center font-bold m-4">Demander une intervention</h1>
        <div class=" mx-auto flex justify-center">

            <form action="" method="post" class="form"  >
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="description">Description :</label>
                    <textarea id="description" name="description" class="w-full px-3 py-2  border rounded-lg focus:outline-none" rows="4" placeholder="Entrez la description de l'intervention"></textarea>
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
                    <label class="block  text-sm font-bold mb-2" for="date_heure">Date et heure :</label>
                    <input type="datetime-local" id="date_heure" name="date_heure" class="w-full px-3 py-2  border rounded-lg focus:outline-none">
                </div>
                <button type="submit" class="button bg-blue-700 py-2 px-4 ">Créer l'intervention</button>
            </form>
    
    </div>

</body>
</html>
