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
    if (!empty($_POST["delete_nom"])) {
        $delete_nom = htmlspecialchars($_POST["delete_nom"]);

        try {
            $sql = "DELETE FROM destination WHERE nom = :delete_nom";
            $stmt = $bdd->prepare($sql);
            $stmt->execute([':delete_nom' => $delete_nom]);

            echo "✅ Destination supprimée avec succès.";
        } catch (PDOException $e) {
            echo "❌ Erreur : " . $e->getMessage();
        }
    } else {
        echo "❌ Veuillez entrer un nom de destination.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/"
    data-template="vertical-menu-template-free">

<head>
    <?php include "include/common/head.php"; ?>
    <title><?= $page ?></title>
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

                        <!-- Formulaire pour supprimer une destination -->
                        <hr>
                        <h4>Supprimer une destination</h4>
                        <form action="destination.php" method="post">
                            <div class="mb-3">
                                <label for="delete_nom" class="form-label">Nom de la destination à supprimer :</label>
                                <input type="text" class="form-control" id="delete_nom" name="delete_nom" required>
                            </div>

                            <button type="submit" name="supprimer" class="btn btn-danger">Supprimer Destination</button>
                        </form>
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
