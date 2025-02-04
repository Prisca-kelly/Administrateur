<?php  
require('model/config/database.php'); // Ce code inclut les fichiers nécessaires pour établir une connexion avec la base de données 
require('model/config/util.php');  // et inclure des fonctions utilitaires.
$page = "destination";

// Ajouter une destination
if (isset($_POST["ajouter"])) {
    if (!empty($_POST["nom"]) && !empty($_POST["description"]) && !empty($_FILES["image"]["name"])) {
        $nom = htmlspecialchars($_POST["nom"]);
        $description = htmlspecialchars($_POST["description"]);

        // Gestion de l'upload d'image
        $image = $_FILES["image"]["name"];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            try {
                $sql = "INSERT INTO destination (nom, description, image) VALUES (:nom, :description, :image)";
                $stmt = $bdd->prepare($sql);
                $stmt->execute([
                    ':nom' => $nom,
                    ':description' => $description,
                    ':image' => $image
                ]);

                echo "✅ Destination ajoutée avec succès.";
            } catch (PDOException $e) {
                echo "❌ Erreur : " . $e->getMessage();
            }
        } else {
            echo "❌ Erreur lors du téléchargement de l'image.";
        }
    } else {
        echo "❌ Veuillez remplir tous les champs.";
    }
}

// Supprimer une destination
if (isset($_POST["supprimer"])) {
    if (!empty($_POST["delete_id"])) {
        $delete_id = htmlspecialchars($_POST["delete_id"]);

        try {
            $sql = "DELETE FROM destination WHERE id_destination = :delete_id";
            $stmt = $bdd->prepare($sql);
            $stmt->execute([':delete_id' => $delete_id]);

            echo "✅ Destination supprimée avec succès.";
        } catch (PDOException $e) {
            echo "❌ Erreur : " . $e->getMessage();
        }
    } else {
        echo "❌ Veuillez entrer un ID de destination.";
    }
}

// Affichage de la liste des destinations
$destinations = $bdd->query("SELECT id_destination, nom, description, image FROM destination")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/"
    data-template="vertical-menu-template-free">

<head>
    <?php include "include/common/head.php"; ?>
    <title><?= $page ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include "include/common/sidebar.php"; ?>
            <div class="layout-page">
                <?php include "include/common/navbar.php"; ?>

                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">
                        <!-- Content -->
                        <h3>Ajouter ou Supprimer une Destination</h3>

                        <!-- Formulaire pour ajouter une destination -->
                        <form action="destination.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom de la destination :</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description :</label>
                                <textarea class="form-control" id="description" name="description" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Image :</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            </div>

                            <button type="submit" name="ajouter" class="btn btn-primary">Ajouter Destination</button>
                        </form>

                        <!-- Affichage des destinations -->
                        <hr>
                        <h4>Liste des destinations</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($destinations as $destination) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($destination['nom']) ?></td>
                                        <td><?= htmlspecialchars($destination['description']) ?></td>
                                        <td><img src="uploads/<?= htmlspecialchars($destination['image']) ?>" width="80" height="60" alt="Image de destination"></td>
                                        <td>
                                            <!-- Icône de suppression -->
                                            <form action="destination.php" method="post" style="display:inline;">
                                                <input type="hidden" name="delete_id" value="<?= htmlspecialchars($destination['id_destination']) ?>">
                                                <button type="submit" name="supprimer" class="btn btn-link text-danger" style="border: none; background: none;">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>
