<?php
// inclusion des fichiers
require('model/config/database.php'); // Gère la connexion à la base de données
$page = "Hotel";  // Page actuelle

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
        $query = $bdd->prepare("INSERT INTO hotel (image, nom, description, prix, ville, dure, statut) VALUES (?, ?, ?, ?, ?, ?, 'activé')");
        $query->execute([$target_file, $nom, $description, $prix, $ville, $dure]);

        // Redirection pour éviter le renvoi du formulaire
        header("Location: hotel.php");
        exit();
    } else {
        echo "Erreur lors de l'upload de l'image.";
    }
}

// Suppression d'un hôtel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_hotel'])) {
    $id_hotel = $_POST['delete_hotel'];

    // Mettre à jour le statut à "Supprimé" au lieu de supprimer l'hôtel
    $stmt = $bdd->prepare("UPDATE hotel SET statut = 'Supprimé' WHERE id_hotel = :id_hotel");
    $stmt->execute([':id_hotel' => $id_hotel]);

    echo "success";
    exit;
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
    $statut = $_POST['statut'] == 'activé' ? 'désactivé' : 'activé'; // Bascule le statut

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
                        <button class="btn btn-primary mb-3" data-bs-toggle="modal"
                            data-bs-target="#addHotelModal">Ajouter un hôtel
                        </button>

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
                                                    <img src="<?= htmlspecialchars($hotel['image']) ?>"
                                                        alt="Image de l'hôtel"
                                                        style="width: 80px; height: 60px; object-fit: cover;">
                                                </td>
                                                <td><?= htmlspecialchars($hotel['nom']) ?></td>
                                                <td><?= htmlspecialchars($hotel['description']) ?></td>
                                                <td><?= htmlspecialchars($hotel['prix']) ?>€</td>
                                                <td><?= htmlspecialchars($hotel['ville']) ?></td>
                                                <td><?= htmlspecialchars($hotel['dure']) ?> jours</td>
                                                <td><?= htmlspecialchars($hotel['statut']) ?></td>
                                                <td>
                                                    <?php
                                                    // Ajoutez des classes ou des styles selon le statut de l'hôtel
                                                    $statusClass = '';
                                                    $statusText = '';

                                                    switch ($hotel['statut']) {
                                                        case 'Activé':
                                                            $statusClass = 'text-success';
                                                            $statusText = 'Activé';
                                                            break;
                                                        case 'Désactivé':
                                                            $statusClass = 'text-danger';
                                                            $statusText = 'Désactivé';
                                                            break;
                                                        case 'Supprimé':
                                                            $statusClass = 'text-muted';
                                                            $statusText = 'Supprimé';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>

                                                <td>
                                                    <!-- Bouton pour changer le statut -->
                                                    <form action="hotel.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="id_hotel"
                                                            value="<?= $hotel['id_hotel'] ?>">
                                                        <input type="hidden" name="statut" value="<?= $hotel['statut'] ?>">
                                                        <button type="submit" name="change_status"
                                                            class="btn btn-link text-warning">
                                                            <i
                                                                class="fas <?= $hotel['statut'] == 'activé' ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Bouton pour ouvrir le modal de modification -->
                                                    <button class="btn btn-link text-primary"
                                                        onclick="editHotelModal('<?= $hotel['id_hotel'] ?>', '<?= htmlspecialchars($hotel['nom']) ?>', '<?= htmlspecialchars($hotel['description']) ?>', '<?= htmlspecialchars($hotel['prix']) ?>', '<?= htmlspecialchars($hotel['ville']) ?>', '<?= htmlspecialchars($hotel['dure']) ?>', '<?= htmlspecialchars($hotel['image']) ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <!-- Bouton pour supprimer un hôtel -->
                                                    <a href="#" onclick="markHotelAsDeleted('<?= $hotel['id_hotel'] ?>')"
                                                        class="text-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
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
        // Gestion des hôtels supprimés
        function markHotelAsDeleted(idHotel) {
            if (confirm("Êtes-vous sûr de vouloir marquer cet hôtel comme supprimé ?")) {
                let formData = new FormData();
                formData.append("delete_hotel", idHotel);

                fetch("hotel.php", {
                    method: "POST",
                    body: formData,
                }).then(response => response.text()).then(data => {
                    if (data === "success") {
                        location.reload(); // Recharger la page pour voir les changements
                    } else {
                        alert("Erreur lors de la suppression de l'hôtel.");
                    }
                });
            }
        }

        function editHotelModal(id, nom, description, prix, ville, dure, image) {
            console.log(id, nom, description, prix, ville, dure, image); // Pour déboguer

            // Préremplir le modal avec les informations de l'hôtel
            document.getElementById("id_hotel").value = id;
            document.getElementById("edit_nom").value = nom;
            document.getElementById("edit_description").value = description;
            document.getElementById("edit_prix").value = prix;
            document.getElementById("edit_ville").value = ville;
            document.getElementById("edit_dure").value = dure;
            document.getElementById("edit_image").value = image;

            // Ouvrir le modal d'édition
            $('#editHotelModal').modal('show');
        }

        // Gestion du changement de statut de l'hôtel
        document.querySelectorAll('.toggle-hotel-status').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();

                var idHotel = element.getAttribute('data-id');
                var statutActuel = element.getAttribute('data-status');
                var nouveauStatut = (statutActuel === 'Activé') ? 'Désactivé' :
                    'Activé'; // Alterne entre 'Activé' et 'Désactivé'

                let formData = new FormData();
                formData.append("update_hotel_status", true);
                formData.append("id_hotel", idHotel);
                formData.append("statut", nouveauStatut);

                fetch('hotel.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'success') {
                            // Met à jour l'affichage du statut sur la page sans recharger
                            element.closest('tr').querySelector('td:nth-child(5) span').textContent =
                                nouveauStatut;
                            element.closest('tr').querySelector('td:nth-child(5) span').className = (
                                nouveauStatut === 'Activé') ? 'text-success' : 'text-danger';

                            // Met à jour l'attribut data-status de l'élément cliqué
                            element.setAttribute('data-status', nouveauStatut);
                        } else {
                            alert("Erreur lors de la mise à jour du statut de l'hôtel.");
                        }
                    })
                    .catch(error => console.error('Erreur : ', error));
            });
        });
    </script>

</body>

</html>