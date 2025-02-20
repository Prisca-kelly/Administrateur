<?php
// inclusion des fichiers
require('model/config/database.php'); // Gère la connexion à la base de données
$page = "Hôtel";  // Page actuelle

// Gestion de l'ajout d'un hôtel
if (isset($_POST['add_hotel'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $ville = $_POST['ville'];
    $dure = $_POST['dure'];
    
    // Gestion de l'upload d'image
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";  // Dossier de base 'uploads'
    $target_file = $target_dir . basename($image);

    // Vérifiez et déplacez l'image téléchargée
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Insertion dans la base de données
        $query = $bdd->prepare("INSERT INTO hotel (image, nom, description, prix, ville, dure, statut) VALUES (?, ?, ?, ?, ?, ?, 'actif')");
        $query->execute([$target_file, $nom, $description, $prix, $ville, $dure]);

        // Redirection pour éviter le renvoi du formulaire
        header("Location: hotel.php");
        exit();
    } else {
        echo "Erreur lors de l'upload de l'image.";
    }
}

// Suppression d'un hôtel
if (isset($_POST['delete_hotel'])) {
    $id_hotel = $_POST['delete_hotel'];
    // Supprimer l'hôtel de la base de données
    $query = $bdd->prepare("DELETE FROM hotel WHERE id_hotel = ?");
    $query->execute([$id_hotel]);
    
    // Redirection après suppression
    header("Location: hotel.php");
    exit();
}

// Modification d'un hôtel
if (isset($_POST['edit_hotel'])) {
    $id_hotel = $_POST['id_hotel'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $ville = $_POST['ville'];
    $dure = $_POST['dure'];
    $image = $_FILES['image']['name'];

    // Gestion de l'upload d'image
    if (!empty($image)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        $image_query = ", image = ?";
        $image_value = $target_file;
    } else {
        $image_query = "";
        $image_value = null;
    }

    // Mise à jour de l'hôtel dans la base de données
    $query = $bdd->prepare("UPDATE hotel SET nom = ?, description = ?, prix = ?, ville = ?, dure = ? $image_query WHERE id_hotel = ?");
    $params = [$nom, $description, $prix, $ville, $dure];
    
    if ($image_query) {
        $params[] = $image_value;
    }

    $params[] = $id_hotel;
    $query->execute($params);

    // Redirection après modification
    header("Location: hotel.php");
    exit();
}

// Changer le statut de l'hôtel
if (isset($_POST['change_status'])) {
    $id_hotel = $_POST['id_hotel'];
    $statut = $_POST['statut'] == 'actif' ? 'désactivé' : 'actif'; // Bascule le statut

    // Mise à jour du statut dans la base de données
    $query = $bdd->prepare("UPDATE hotel SET statut = ? WHERE id_hotel = ?");
    $query->execute([$statut, $id_hotel]);

    // Redirection après changement de statut
    header("Location: hotel.php");
    exit();
}

// Récupération des hôtels depuis la base de données
$hotels = $bdd->query("SELECT id_hotel, image, nom, description, prix, ville, dure, statut FROM hotel")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include "include/common/head.php"; ?>
    <title><?= $page ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include "include/common/sidebar.php"; ?>
            <div class="layout-page">
                <?php include "include/common/navbar.php"; ?>
                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="fw-bold py-3 mb-0">Liste des Hôtels</h4>

                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addHotelModal">Ajouter un hôtel</button>

                        <div class="card">
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Prix</th>
                                            <th>Ville</th>
                                            <th>Durée</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($hotels as $hotel) : ?>
                                            <tr>
                                                <td>
                                                    <img src="<?= htmlspecialchars($hotel['image']) ?>" alt="Image de l'hôtel" style="width: 80px; height: 60px; object-fit: cover;">
                                                </td>
                                                <td><?= htmlspecialchars($hotel['nom']) ?></td>
                                                <td><?= htmlspecialchars($hotel['description']) ?></td>
                                                <td><?= htmlspecialchars($hotel['prix']) ?>€</td>
                                                <td><?= htmlspecialchars($hotel['ville']) ?></td>
                                                <td><?= htmlspecialchars($hotel['dure']) ?> jours</td>
                                                <td><?= htmlspecialchars($hotel['statut']) ?></td>
                                                <td>
                                                    <!-- Bouton pour changer le statut -->
                                                    <form action="hotel.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="id_hotel" value="<?= $hotel['id_hotel'] ?>">
                                                        <input type="hidden" name="statut" value="<?= $hotel['statut'] ?>">
                                                        <button type="submit" name="change_status" class="btn btn-link text-warning">
                                                            <i class="fas <?= $hotel['statut'] == 'actif' ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Bouton pour ouvrir le modal de modification -->
                                                    <button class="btn btn-link text-primary" onclick="showEditModal('<?= $hotel['id_hotel'] ?>', '<?= htmlspecialchars($hotel['nom']) ?>', '<?= htmlspecialchars($hotel['description']) ?>', '<?= htmlspecialchars($hotel['prix']) ?>', '<?= htmlspecialchars($hotel['ville']) ?>', '<?= htmlspecialchars($hotel['dure']) ?>', '<?= htmlspecialchars($hotel['image']) ?>')">
                                                     <i class="fas fa-edit"></i>
                                                    </button>

                                                    <!-- Formulaire de suppression -->
                                                    <form action="hotel.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="delete_hotel" value="<?= $hotel['id_hotel'] ?>">
                                                        <button type="submit" class="btn btn-link text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet hôtel ?')">
                                                            <i class="fas fa-trash-alt"></i>
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

    <!-- Modal pour ajouter un hôtel -->
    <div class="modal fade" id="addHotelModal" tabindex="-1" aria-labelledby="addHotelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addHotelModalLabel">Ajouter un Hôtel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="hotel.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="prix" class="form-label">Prix</label>
                            <input type="number" class="form-control" id="prix" name="prix" required>
                        </div>
                        <div class="mb-3">
                            <label for="ville" class="form-label">Ville</label>
                            <input type="text" class="form-control" id="ville" name="ville" required>
                        </div>
                        <div class="mb-3">
                            <label for="dure" class="form-label">Durée (en jours)</label>
                            <input type="number" class="form-control" id="dure" name="dure" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="add_hotel">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour modifier un hôtel -->
    <div class="modal fade" id="editHotelModal" tabindex="-1" aria-labelledby="editHotelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editHotelModalLabel">Modifier un Hôtel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="hotel.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="id_hotel" name="id_hotel">
                        <div class="mb-3">
                            <label for="edit_nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="edit_nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_prix" class="form-label">Prix</label>
                            <input type="number" class="form-control" id="edit_prix" name="prix" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_ville" class="form-label">Ville</label>
                            <input type="text" class="form-control" id="edit_ville" name="ville" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_dure" class="form-label">Durée (en jours)</label>
                            <input type="number" class="form-control" id="edit_dure" name="dure" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="edit_image" name="image">
                        </div>
                        <button type="submit" class="btn btn-primary" name="edit_hotel">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour ouvrir le modal de modification et remplir les champs
        function showEditModal(id_hotel, nom, description, prix, ville, dure, image) {
            console.log(id_hotel, nom, description, prix, ville, dure, image); // Vérifiez les valeurs dans la console
            document.getElementById("editHotelModal").querySelector("#id_hotel").value = id_hotel;
            document.getElementById("editHotelModal").querySelector("#edit_nom").value = nom;
            document.getElementById("editHotelModal").querySelector("#edit_description").value = description;
            document.getElementById("editHotelModal").querySelector("#edit_prix").value = prix;
            document.getElementById("editHotelModal").querySelector("#edit_ville").value = ville;
            document.getElementById("editHotelModal").querySelector("#edit_dure").value = dure;
            document.getElementById("editHotelModal").querySelector("#edit_image").value = ""; // Réinitialiser le champ de l'image
            $('#editHotelModal').modal('show');  // Afficher le modal
        }
    </script>
</body>

</html>
