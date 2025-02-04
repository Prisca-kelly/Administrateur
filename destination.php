<?php  
require('model/config/database.php');
require('model/config/util.php');
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

                echo "<script>alert('✅ Destination ajoutée avec succès.'); window.location.href='destination.php';</script>";
            } catch (PDOException $e) {
                echo "<script>alert('❌ Erreur : " . $e->getMessage() . "');</script>";
            }
        } else {
            echo "<script>alert('❌ Erreur lors du téléchargement de l\'image.');</script>";
        }
    } else {
        echo "<script>alert('❌ Veuillez remplir tous les champs.');</script>";
    }
}

// Suppression d'une destination
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["supprimer"])) {
    $id_destination = $_POST["delete_id"];

    try {
        $stmt = $bdd->prepare("DELETE FROM destination WHERE id_destination = :id_destination");
        $stmt->execute([':id_destination' => $id_destination]);
        echo "<script>alert('✅ Destination supprimée avec succès.'); window.location.href='destination.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('❌ Erreur lors de la suppression : " . $e->getMessage() . "');</script>";
    }
}

// Récupération des destinations
$destinations = $bdd->query("SELECT id_destination, nom, description, image FROM destination")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold py-3 mb-0">Destinations</h4>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDestinationModal">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        
                        <!-- Tableau des destinations -->
                        <div class="card">
                            <div class="table-responsive text-nowrap">
                                <table class="table">
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
                                                <td><img src="uploads/<?= htmlspecialchars($destination['image']) ?>" width="80" height="60"></td>
                                                <td>
                                                    <form action="destination.php" method="post" style="display:inline;">
                                                        <input type="hidden" name="delete_id" value="<?= $destination['id_destination'] ?>">
                                                        <button type="submit" name="supprimer" class="btn btn-link text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette destination ?')">
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
        </div>
    </div>

    <!-- Modale d'ajout de destination -->
    <div class="modal fade" id="addDestinationModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" role="document" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une destination</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary" type="submit" name="ajouter">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>

    <?php include "include/common/script.php"; ?>
</body>
</html>
