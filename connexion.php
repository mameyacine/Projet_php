<?php
session_start(); // Démarre la session

// Vérifie si l'utilisateur est déjà connecté, s'il l'est, redirige-le vers la page appropriée
try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si l'utilisateur est déjà connecté et récupérer ses informations
    if (isset($_SESSION['ID_utilisateur'])) {
        $stmt = $pdo->prepare("SELECT role, ID_utilisateur FROM Utilisateur WHERE ID_utilisateur = ?");
        $stmt->execute([$_SESSION['ID_utilisateur']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Redirection en fonction du rôle de l'utilisateur
        switch ($user['role']) {
            case 'admin':
                // Récupérer l'ID de l'administrateur
                $stmt_admin = $pdo->prepare("SELECT ID_admin FROM Admin WHERE ID_utilisateur = ?");
                $stmt_admin->execute([$user['ID_utilisateur']]);
                $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);
                // Rediriger vers la page admin avec l'ID de l'administrateur
                header("Location: admin.php?idA={$admin['ID_admin']}");
                exit();
            case 'standardiste':
                // Récupérer l'ID du standardiste
                $stmt_standardiste = $pdo->prepare("SELECT ID_standardiste FROM Standardiste WHERE ID_utilisateur = ?");
                $stmt_standardiste->execute([$user['ID_utilisateur']]);
                $standardiste = $stmt_standardiste->fetch(PDO::FETCH_ASSOC);
                // Rediriger vers la page standardiste avec l'ID du standardiste
                header("Location: standardiste.php?idST={$standardiste['ID_standardiste']}");
                exit();
            case 'client':
                // Récupérer l'ID du client
                $stmt_client = $pdo->prepare("SELECT ID_client FROM Client WHERE ID_utilisateur = ?");
                $stmt_client->execute([$user['ID_utilisateur']]);
                $client = $stmt_client->fetch(PDO::FETCH_ASSOC);
                // Rediriger vers la page client avec l'ID du client
                header("Location: client.php?idC={$client['ID_client']}");
                exit();
            case 'intervenant':
                // Récupérer l'ID de l'intervenant
                $stmt_intervenant = $pdo->prepare("SELECT ID_intervenant FROM Intervenant WHERE ID_utilisateur = ?");
                $stmt_intervenant->execute([$user['ID_utilisateur']]);
                $intervenant = $stmt_intervenant->fetch(PDO::FETCH_ASSOC);
                // Rediriger vers la page intervenant avec l'ID de l'intervenant
                header("Location: intervenant.php?idINT={$intervenant['ID_intervenant']}");
                exit();
            default:
                header("Location: homepage.php");
                exit();
        }
    }
} catch(PDOException $e) {
    die("<p class='text-red-500 font-bold'>Erreur lors de la récupération des informations : " . $e->getMessage() . "</p>");
}

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    try {
        $stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE nom_utilisateur = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['mdp'])) {
            // Stocke les informations de l'utilisateur dans la session
            $_SESSION['role'] = $user['role'];
            $_SESSION['ID_utilisateur'] = $user['ID_utilisateur']; // Stocke l'ID de l'utilisateur dans la session

            // Redirection en fonction du rôle de l'utilisateur avec l'ID spécifique au type d'utilisateur
            switch ($user['role']) {
                case 'admin':
                    // Récupérer l'ID de l'administrateur
                    $stmt_admin = $pdo->prepare("SELECT ID_admin FROM Admin WHERE ID_utilisateur = ?");
                    $stmt_admin->execute([$user['ID_utilisateur']]);
                    $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);
                    // Rediriger vers la page admin avec l'ID de l'administrateur
                    header("Location: admin.php?idA={$admin['ID_admin']}");
                    exit();
                case 'standardiste':
                    // Récupérer l'ID du standardiste
                    $stmt_standardiste = $pdo->prepare("SELECT ID_standardiste FROM Standardiste WHERE ID_utilisateur = ?");
                    $stmt_standardiste->execute([$user['ID_utilisateur']]);
                    $standardiste = $stmt_standardiste->fetch(PDO::FETCH_ASSOC);
                    // Rediriger vers la page standardiste avec l'ID du standardiste
                    header("Location: standardiste.php?idST={$standardiste['ID_standardiste']}");
                    exit();
                case 'client':
                    // Récupérer l'ID du client
                    $stmt_client = $pdo->prepare("SELECT ID_client FROM Client WHERE ID_utilisateur = ?");
                    $stmt_client->execute([$user['ID_utilisateur']]);
                    $client = $stmt_client->fetch(PDO::FETCH_ASSOC);
                    // Rediriger vers la page client avec l'ID du client
                    header("Location: client.php?idC={$client['ID_client']}");
                    exit();
                case 'intervenant':
                    // Récupérer l'ID de l'intervenant
                    $stmt_intervenant = $pdo->prepare("SELECT ID_intervenant FROM Intervenant WHERE ID_utilisateur = ?");
                    $stmt_intervenant->execute([$user['ID_utilisateur']]);
                    $intervenant = $stmt_intervenant->fetch(PDO::FETCH_ASSOC);
                    // Rediriger vers la page intervenant avec l'ID de l'intervenant
                    header("Location: intervenant.php?idINT={$intervenant['ID_intervenant']}");
                    exit();
                default:
                    header("Location: homepage.php");
                    exit();
            }
        } else {
            echo "<p class='text-red-500 font-bold'>Nom d'utilisateur ou mot de passe incorrect.</p>";
        }
    } catch(PDOException $e) {
        die("<p class='text-red-500 font-bold'>Erreur lors de la connexion : " . $e->getMessage() . "</p>");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>

<h1 class="text-3xl font-bold m-4 p-4 text-center">Connexion</h1>
<div class=" mx-auto flex justify-center">

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form">
    <div class="mb-4">
        <label class="block mb-2" for="username">Nom d'utilisateur :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="text" id="username" name="username" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2" for="password">Mot de passe :</label>
        <input class="w-full px-3 py-2 border rounded-md" type="password" id="password" name="password" required>
    </div>

    <div class="mb-4 flex justify-between">
        <a href="forgot_password.php" class="">Mot de passe oublié ?</a>
        <a href="inscription.php" class="">Inscription</a>


    </div>

    <button class="button bg-blue-700 py-2 px-4 " type="submit">Se connecter</button>
</form>

</div>

</body>
</html>
