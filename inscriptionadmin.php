<?php
// Vérifie si la méthode POST est définie
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupère les données du formulaire
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = "admin"; // Définition du rôle comme "admin"
    
    // Hashage du mot de passe
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Connexion à la base de données
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Démarre une transaction
        $pdo->beginTransaction();

        // Insertion dans la table Utilisateur avec le rôle
        $stmt = $pdo->prepare("INSERT INTO Utilisateur (nom_utilisateur, mdp, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password_hashed, $role]); // Utilisation de $role

        // Récupération de l'ID de l'utilisateur inscrit
        $user_id = $pdo->lastInsertId();

        // Insertion dans la table Admin
        $stmt = $pdo->prepare("INSERT INTO Admin (ID_utilisateur, nom_utilisateur) VALUES (?, ?)");
        $stmt->execute([$user_id, $username]);

        // Valide la transaction
        $pdo->commit();

        // Redirection vers une page de confirmation
        header("Location: connexion.php");
        exit();
    } catch(PDOException $e) {
        // Annule la transaction en cas d'erreur
        $pdo->rollBack();

        // Affichage de l'erreur en cas d'échec
        echo "Erreur lors de l'inscription : " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <h2>Inscription d'un nouvel administrateur</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
