<?php
session_start(); // Démarre la session

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Vérifie si les champs sont définis et non vides
    if (isset($_POST["username"]) && isset($_POST["new_password"]) && isset($_POST["confirm_password"]) && !empty($_POST["username"]) && !empty($_POST["new_password"]) && !empty($_POST["confirm_password"])) {
        $username = $_POST["username"];
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];

        // Vérifie si les mots de passe correspondent
        if ($new_password === $confirm_password) {
            try {
                // Connexion à la base de données
                $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Vérifie si l'utilisateur existe dans la base de données
                $stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE nom_utilisateur = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Met à jour le mot de passe de l'utilisateur
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt_update = $pdo->prepare("UPDATE Utilisateur SET mdp = ? WHERE nom_utilisateur = ?");
                    $stmt_update->execute([$hashed_password, $username]);

                    echo "<p class='text-green-500 font-bold'>Mot de passe mis à jour avec succès pour l'utilisateur : $username</p>";
                    // Redirection vers la page de connexion
                    header("Location: connexion.php");
                    exit();
                } else {
                    echo "<p class='text-red-500 font-bold'>Utilisateur non trouvé.</p>";
                }
            } catch(PDOException $e) {
                die("<p class='text-red-500 font-bold'>Erreur lors de la mise à jour du mot de passe : " . $e->getMessage() . "</p>");
            }
        } else {
            echo "<p class='text-red-500 font-bold'>Les mots de passe ne correspondent pas.</p>";
        }
    } else {
        echo "<p class='text-red-500 font-bold'>Veuillez remplir tous les champs.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>

<h1 class="text-3xl font-bold mb-4">Réinitialiser le mot de passe</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="mb-4">
        <label class="block mb-2" for="username">Nom d'utilisateur :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="text" id="username" name="username" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="new_password">Nouveau mot de passe :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="password" id="new_password" name="new_password" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="confirm_password">Confirmez le nouveau mot de passe :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="password" id="confirm_password" name="confirm_password" required>
    </div>

    <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" type="submit">Réinitialiser le mot de passe</button>
</form>

</body>
</html>
