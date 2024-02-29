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

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
    // Connexion à la base de données
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
    
    // Récupération des données du formulaire
    $username = $_POST["username"];
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hacher le mot de passe

    $role = $_POST["role"];
    $prenom = $_POST["prenom"];
    $nom = $_POST["nom"];
    
    // Requête d'insertion dans la table Utilisateur
    $stmt_user = $pdo->prepare("INSERT INTO Utilisateur (nom_utilisateur, mdp, role, prenom, nom) VALUES (?, ?, ?, ?, ?)");
    $stmt_user->execute([$username, $hashed_password, $role, $prenom, $nom]);
    
    // Récupération de l'ID de l'utilisateur inséré
    $user_id = $pdo->lastInsertId();
    
    // Insérer l'ID de l'utilisateur et le nom d'utilisateur dans la table correspondante en fonction du rôle
    switch ($role) {
        case "client":
            $stmt_client = $pdo->prepare("INSERT INTO Client (ID_utilisateur, nom_utilisateur, prenom, nom) VALUES (?, ?, ?, ?)");
            $stmt_client->execute([$user_id, $username, $prenom, $nom]);
            break;
        case "standardiste":
            $stmt_standardiste = $pdo->prepare("INSERT INTO Standardiste (ID_utilisateur, nom_utilisateur, prenom, nom) VALUES (?, ?, ?, ?)");
            $stmt_standardiste->execute([$user_id, $username, $prenom, $nom]);
            break;
        case "intervenant":
            $stmt_intervenant = $pdo->prepare("INSERT INTO Intervenant (ID_utilisateur, nom_utilisateur, prenom, nom) VALUES (?, ?, ?, ?)");
            $stmt_intervenant->execute([$user_id, $username, $prenom, $nom]);
            break;
        default:
            // Gérer le cas où le rôle est invalide
            break;
    }

    // Redirection vers une autre page
    header("Location: gestion_utilisateurs.php?idA=" . htmlspecialchars($id_admin));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<nav class="  p-4 mb-8">
    <div class=" mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Admin Dashboard</h2>
        <div>
            <a href="admin.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button">Toutes les interventions</a>
            <a href="gestion_utilisateurs.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i> </a>
        </div>
    </div>
</nav>


<body>
    <h1 class="text-3xl text-center font-bold m-4">Ajouter un utilisateur</h1>
    <div class=" mx-auto flex justify-center">
        
    
        <!-- Formulaire pour ajouter un utilisateur -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?idA=<?php echo $id_admin; ?>"class="form">
            <div class="mb-4">
                <label class="block mb-2" for="prenom">Prénom :</label>
                <input class="w-full px-3 py-2 border rounded-md" type="text" id="prenom" name="prenom" required>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2" for="nom">Nom :</label>
                <input class="w-full px-3 py-2 border rounded-md" type="text" id="nom" name="nom" required>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2" for="username">Nom d'utilisateur :</label>
                <input class="w-full px-3 py-2 border rounded-md" type="text" id="username" name="username" required>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2" for="password">Mot de passe :</label>
                <input class="w-full px-3 py-2 border rounded-md" type="password" id="password" name="password" required>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2" for="role">Rôle :</label>
                <select class="w-full px-3 py-2 border rounded-md" id="role" name="role" required>
                    <option value="client">Client</option>
                    <option value="standardiste">Standardiste</option>
                    <option value="intervenant">Intervenant</option>
                </select>
            </div>
            
            <button class="button bg-blue-700 py-2 px-4 " type="submit">Ajouter</button>
        </form>
    </div>

</body>



</html>

