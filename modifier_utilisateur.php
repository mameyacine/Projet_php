<?php
$id_admin = isset($_GET['idA']) ? $_GET['idA'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un utilisateur</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="public/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>


<nav class="p-4 mb-8">
    <div class=" mx-auto flex justify-between items-center">
        <h2 class="text-2xl font-bold">Admin Dashboard</h2>
        <div>
            <a href="admin.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button">Toutes les interventions</a>
            <a href="gestion_utilisateurs.php?idA=<?php echo htmlspecialchars($id_admin); ?>" class="button"><i class="fas fa-arrow-rotate-left"></i> </a>
        </div>
    </div>
</nav>

<body>
<div class="container mx-auto p-4 ">
    <h1 class="text-3xl font-bold mb-4">Modifier un utilisateur</h1>
    <!-- Formulaire de recherche -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?idA=<?php echo $id_admin; ?>" method="post" class="mb-4">
        <input type="hidden" name="idA" value="<?php echo htmlspecialchars($id_admin); ?>">
            <label class="block form text-sm font-bold mb-2" for="query">Recherche par nom, prénom ou nom d'utilisateur :</label>
            <input type="text" id="query" name="query" class='w-5/6 px-3 py-2 border rounded-lg focus:outline-none mb-2' placeholder="Entrez le nom, prénom ou nom d'utilisateur">
        <button type="submit" class="button rounded"><i class="fas fa-search"></i></button>
    </form>
    
    <?php
    // Vérification si le formulaire de recherche a été soumis
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["query"])) {
        // Récupération de l'ID de l'administrateur depuis l'URL
        $id_admin = isset($_GET['idA']) ? $_GET['idA'] : null;
        // Connexion à la base de données
        $pdo = new PDO("mysql:host=localhost;dbname=projet_php", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupération de la requête de recherche
        $query = $_POST["query"];

        // Requête SQL pour récupérer les utilisateurs correspondant à la recherche (excluant les admins)
        $stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE (nom LIKE ? OR prenom LIKE ? OR nom_utilisateur LIKE ?) AND role != 'admin'");
        $stmt->execute(["%$query%", "%$query%", "%$query%"]);
        $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($utilisateurs) {
            // Affichage des utilisateurs trouvés
            echo "<h1 class='text-2xl font-bold mt-8 mb-4'>Utilisateurs trouvés :</h1>";
            foreach ($utilisateurs as $utilisateur) {
                echo "<div class='mb-4 flex items-center'>"; // Ajout de la classe "flex" pour afficher les éléments sur la même ligne
                echo "<p class='mr-4' ><strong>Nom d'utilisateur :</strong> " . htmlspecialchars($utilisateur['nom_utilisateur']) . "</p>";
                echo "<p class='mr-4'><strong> Rôle :</strong> " . htmlspecialchars($utilisateur['role']) . "</p>";
                echo "<form action='action_modifier_utilisateur.php?idA=$id_admin' method='post'>";
                echo "<input type='hidden' name='ID_utilisateur' value='" . htmlspecialchars($utilisateur['ID_utilisateur']) . "'>";
                echo "<input type='hidden' name='idA' value='" . htmlspecialchars($id_admin) . "'>";
                echo "<label for='nouveau_role'><strong>Nouveau rôle :</strong> </label>";
                echo "<select name='nouveau_role' id='nouveau_role'>";
                
                // Récupération des rôles restants en fonction du rôle actuel de l'utilisateur
                $roles_restants = getRolesRestants($utilisateur['role']);
                foreach ($roles_restants as $role) {
                    echo "<option value='$role'>$role</option>";
                }
                echo "</select>";
                echo "<button type='submit' class='button text-white font-bold py-2 px-4 ml-2 rounded'><i class='fas fa-edit'></i></button>";
                echo "</form>";
                echo "<form action='supprimer_utilisateur.php?idA=$id_admin' method='post'>";
                echo "<input type='hidden' name='ID_utilisateur' value='" . htmlspecialchars($utilisateur['ID_utilisateur']) . "'>";
                echo "<input type='hidden' name='idA' value='" . htmlspecialchars($id_admin) . "'>";
                echo "<button type='submit' class='delete bg-red-600 text-white font-bold py-2 px-4 rounded ml-2'><i class='fas fa-trash'></i></button>";
                echo "</form>";
                echo "</div>";
            }
            
        } else {
            echo "<p class='text-red-500'>Aucun utilisateur trouvé avec cette recherche.</p>";
        }
    }

    // Fonction pour récupérer les rôles restants en fonction du rôle actuel de l'utilisateur
    function getRolesRestants($roleActuel) {
        switch ($roleActuel) {
            case 'standardiste':
                return ['intervenant', 'client']; // Si l'utilisateur est un standardiste, les rôles restants sont intervenant et client
            case 'intervenant':
                return ['standardiste', 'client']; // Si l'utilisateur est un intervenant, les rôles restants sont standardiste et client
            default:
                return ['standardiste', 'intervenant']; // Par défaut, les rôles restants sont standardiste et intervenant
        }
    }
    ?>
</div>
</body>
</html>





