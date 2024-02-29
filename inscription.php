<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"></head>
<body>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Vérification de la présence de toutes les valeurs requises
    $required_fields = ["prenom", "nom", "nom_utilisateur", "email", "password", "tel", "nom_rue", "num_rue", "postcode", "ville"];
    $errors = [];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $errors[] = "Le champ '$field' est requis.";
        }
    }


    if (!empty($errors)) {
        echo "<p class='text-red-500 font-bold'>Erreurs lors de l'inscription :</p>";
        foreach ($errors as $error) {
            echo "<p class='text-red-500'>$error</p>";
        }
        die(); // Arrêter l'exécution du script
    }


    try {
        $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $prenom = $_POST["prenom"];
        $nom = $_POST["nom"];
        $nom_utilisateur = $_POST["nom_utilisateur"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $telephone = $_POST["tel"];
        $nom_rue = $_POST["nom_rue"];
        $num_rue = $_POST["num_rue"];
        $code_postal = $_POST["postcode"];
        $ville = $_POST["ville"];

        // Insertion dans la table Utilisateurs
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO Utilisateur (prenom,nom , nom_utilisateur, mdp) VALUES (?, ?, ?, ?)");
        $stmt->execute([$prenom, $nom, $nom_utilisateur, $password_hashed]);


        $user_id = $pdo->lastInsertId();

        // Insertion dans la table Clients
        $stmt = $pdo->prepare("INSERT INTO Client (ID_utilisateur, prenom, nom, nom_utilisateur, email, telephone, nom_rue, num_rue, code_postal, ville) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $prenom, $nom, $nom_utilisateur, $email, $telephone, $nom_rue, $num_rue, $code_postal, $ville]);
        echo "<p class='text-green-500 font-bold'>Inscription réussie !</p>";
        // Redirection vers la page de connexion
        header("Location: connexion.php");
        exit();
    } catch(PDOException $e) {
        die("<p class='text-red-500 font-bold'>Erreur lors de l'inscription : " . $e->getMessage() . "</p>");
    }
}
?>

<h1 class="text-3xl font-bold m-4 p-4 text-center">Inscription</h1>
<div class=" mx-auto flex justify-center">

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form">
    <div class="mb-4">
        <label class="block mb-2" for="prenom">Prénom :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="text" id="prenom" name="prenom" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="nom">Nom :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="text" id="nom" name="nom" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="nom_utilisateur">Nom d'utilisateur :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="text" id="nom_utilisateur" name="nom_utilisateur" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="email">Adresse e-mail :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="email" id="email" name="email" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="password">Mot de passe :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="password" id="password" name="password" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="tel">Téléphone :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="tel" id="tel" name="tel" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="nom_rue">Nom de rue :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="text" id="nom_rue" name="nom_rue" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="num_rue">Numéro de rue :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="number" id="num_rue" name="num_rue" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="postcode">Code postal :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="text" id="postcode" name="postcode" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="ville">Ville :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="text" id="ville" name="ville" required>
    </div>

    <button class="button bg-blue-700 py-2 px-4" type="submit">S'inscrire</button>
</form>
</div>

</body>
</html>