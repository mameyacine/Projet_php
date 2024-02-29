<?php
$id_client = isset($_GET['idC']) ? $_GET['idC'] : null;
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Démarrez la session si ce n'est pas déjà fait

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header("Location: connexion.php");
    exit();
}

// Initialiser les variables pour stocker les informations du client
$email = $telephone = $nom_rue = $num_rue = $ville = $code_postal = '';

// Vérifie si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $telephone = $_POST["telephone"];
    $nom_rue = $_POST['nom_rue'];
    $num_rue = $_POST['num_rue'];
    $ville = $_POST['ville'];
    $code_postal = $_POST['code_postal'];
    $id_client = isset($_POST['idC']) ? $_POST['idC'] : null;
    
    // Valider les données (vous pouvez ajouter vos propres règles de validation ici)
    // Assurez-vous de gérer les erreurs de validation
    
    // Mettre à jour les informations du client dans la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("UPDATE Client SET email=?, telephone=?, nom_rue=?, num_rue=?, ville=?, code_postal=? WHERE ID_utilisateur=?");
    $stmt->execute([$email,$telephone, $nom_rue, $num_rue, $ville, $code_postal, $_SESSION['ID_utilisateur']]);
    
    // Rediriger l'utilisateur vers une page de confirmation ou tout autre page appropriée
    header("Location: client.php?idC=$id_client");
    exit();
} else {
    // Si le formulaire n'a pas été soumis, récupérer les informations du client à partir de la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT email, telephone, nom_rue, num_rue, ville, code_postal FROM Client WHERE ID_utilisateur = ?");
    $stmt->execute([$_SESSION['ID_utilisateur']]);
    $client_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Mettre à jour les variables avec les informations récupérées du client
    if ($client_info) {
        $email = $client_info['email'];
        $telephone = $client_info['telephone'];
        $nom_rue = $client_info['nom_rue'];
        $num_rue = $client_info['num_rue'];
        $ville = $client_info['ville'];
        $code_postal = $client_info['code_postal'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compléter les informations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<nav class="p-4 mb-8">
    <div class="mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Client Dashboard</h2>
        
        <a href="client.php?idC=<?php echo htmlspecialchars($id_client); ?>" class="delete bg-red-500"><i class="fas fa-arrow-rotate-left"></i></a>

    </div>
</nav>


<body>

    <h1 class="text-3xl font-bold mb-4 text-center">Compléter mes informations</h1>
    <div class=" mx-auto flex justify-center">

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form">
            <!-- Champ caché pour l'ID client -->
            <input type="hidden" name="idC" value="<?php echo htmlspecialchars($id_client); ?>">
            
            <div class="mb-4">
                <label for="email" class="block  mb-2">Email :</label>
                <input type="email" name="email" id="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"  class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="telephone" class="block  mb-2">Téléphone :</label>
                <input type="tel" name="telephone" id="telephone" value="<?php echo isset($telephone)? htmlspecialchars($telephone): '' ; ?>" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="nom_rue" class="block mb-2">Nom de la rue :</label>
                <input type="text" name="nom_rue" id="nom_rue" value="<?php echo isset($nom_rue) ? htmlspecialchars($nom_rue): '' ; ?>" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="num_rue" class="block mb-2">Numéro de rue :</label>
                <input type="text" name="num_rue" id="num_rue" value="<?php echo isset($num_rue) ? htmlspecialchars($num_rue): '' ; ?>" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="ville" class="block mb-2">Ville :</label>
                <input type="text" name="ville" id="ville" value="<?php echo isset($ville) ? htmlspecialchars($ville): '' ; ?>" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="code_postal" class="block mb-2">Code postal :</label>
                <input type="text" name="code_postal" id="code_postal" value="<?php echo isset($code_postal) ? htmlspecialchars($code_postal): '' ; ?>" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 mt-2 button">Soumettre</button>
            </div>
        </form>
    
</div>
</body>
</html>
