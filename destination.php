<?php
require('model/config/database.php');
require('model/config/util.php');

header("Cache-Control: no-cache, must-revalidate"); // Évite la mise en cache

$page = "Destination";

// Ajouter une destination
if (isset($_POST["ajouter"])) {
    if (!empty($_POST["nom"]) && !empty($_POST["description"]) && !empty($_FILES["image"]["name"])) {
        $nom = htmlspecialchars($_POST["nom"]);
        $description = htmlspecialchars($_POST["description"]);
        
        // Gestion de l'upload d'image
        $image = basename($_FILES["image"]["name"]);
        $target_dir = "uploads/";
        $target_file = $target_dir . $image;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            try {
                $sql = "INSERT INTO destination (nom, description, image, statut) VALUES (:nom, :description, :image, 'Activé')";
                $stmt = $bdd->prepare($sql);
                $stmt->execute([
                    ':nom' => $nom,
                    ':description' => $description,
                    ':image' => $image
                ]);
                echo "<script>alert('✅ Destination ajoutée avec succès.'); location.reload();</script>";
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

// Changer le statut d'une destination
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["toggle_id"])) {
    $id_destination = $_POST["toggle_id"];
    try {
        // Inverser le statut
        $stmt = $bdd->prepare("UPDATE destination SET statut = IF(statut='Activé', 'Désactivée', 'Activé') WHERE id_destination = :id_destination");
        $stmt->execute([':id_destination' => $id_destination]);
    } catch (PDOException $e) {
        echo "❌ Erreur lors de la mise à jour du statut : " . $e->getMessage();
    }
}

// Suppression d'une destination
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_destination'])) {
    $id_destination = $_POST['delete_destination'];

    // Mettre à jour le statut à "Supprimé" au lieu de supprimer la destination
    $stmt = $bdd->prepare("UPDATE destination SET statut = 'Supprimé' WHERE id_destination = :id_destination");
    $stmt->execute([':id_destination' => $id_destination]);

    echo "success";
    exit;
}


// Modifier une destination
if (isset($_POST["modifier"])) {
    if (!empty($_POST["nom"]) && !empty($_POST["description"])) {
        $id_destination = $_POST["edit_id"];
        $nom = htmlspecialchars($_POST["nom"]);
        $description = htmlspecialchars($_POST["description"]);
        $image = $_FILES["image"]["name"];

        if (!empty($image)) {
            $image = basename($image);
            $target_dir = "uploads/";
            $target_file = $target_dir . $image;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        } else {
            $stmt = $bdd->prepare("SELECT image FROM destination WHERE id_destination = :id_destination");
            $stmt->execute([':id_destination' => $id_destination]);
            $image = $stmt->fetchColumn();
        }

        try {
            $sql = "UPDATE destination SET nom = :nom, description = :description, image = :image WHERE id_destination = :id_destination";
            $stmt = $bdd->prepare($sql);
            $stmt->execute([
                ':id_destination' => $id_destination,
                ':nom' => $nom,
                ':description' => $description,
                ':image' => $image
            ]);
            echo "<script>alert('✅ Destination modifiée avec succès.'); location.reload();</script>";
        } catch (PDOException $e) {
            echo "<script>alert('❌ Erreur lors de la modification : " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('❌ Veuillez remplir tous les champs.');</script>";
    }
}

// Récupération des destinations
$destinations = $bdd->query("SELECT id_destination, nom, description, image, statut FROM destination")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include "include/common/head.php"; ?>
    <title><?= $page ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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

                        <div class="card">
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Image</th>
                                            <th>Statut</th>
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
                                                    <?php
                                                    // Ajoutez des classes ou des styles selon le statut de la destination
                                                    $statusClass = '';
                                                    $statusText = '';

                                                    switch ($destination['statut']) {
                                                        case 'Activé':
                                                            $statusClass = 'text-success';
                                                            $statusText = 'Activée';
                                                            break;
                                                        case 'Désactivée':
                                                            $statusClass = 'text-warning';
                                                            $statusText = 'Désactivée';
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
                                                    <form action="destination.php" method="post" style="display:inline;">
                                                        <input type="hidden" name="toggle_id" value="<?= $destination['id_destination'] ?>">
                                                        <button type="submit" name="toggle_statut" class="btn btn-link text-warning">
                                                            <i class="fa-solid <?= ($destination['statut'] === 'Activé') ? 'fa-lock-open' : 'fa-lock' ?>"></i>
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-link text-info" data-bs-toggle="modal" data-bs-target="#editDestinationModal"
                                                        data-id="<?= $destination['id_destination'] ?>"
                                                        data-nom="<?= htmlspecialchars($destination['nom']) ?>"
                                                        data-description="<?= htmlspecialchars($destination['description']) ?>"
                                                        data-image="<?= htmlspecialchars($destination['image']) ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="destination.php" method="post" style="display:inline;">
                                                       <input type="hidden" name="delete_destination" value="<?= $destination['id_destination'] ?>">
                                                       <button type="submit" class="btn btn-link text-danger">
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

    <!-- Modal pour ajouter une destination -->
    <div class="modal fade" id="addDestinationModal" tabindex="-1" aria-labelledby="addDestinationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDestinationModalLabel">Ajouter une destination</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="destination.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" required>
                        </div>
                        <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour modifier une destination -->
    <div class="modal fade" id="editDestinationModal" tabindex="-1" aria-labelledby="editDestinationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDestinationModalLabel">Modifier une destination</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="destination.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="edit_nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="edit_image" name="image">
                            <small class="form-text text-muted">Laissez vide si vous ne souhaitez pas modifier
                                l'image.</small>
                        </div>
                        <button type="submit" name="modifier" class="btn btn-primary">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Gestion des destinations supprimées
    function markAsDeleted(idDestination) {
        if (confirm("Êtes-vous sûr de vouloir marquer cette destination comme supprimée ?")) {
            let formData = new FormData();
            formData.append("delete_destination", idDestination);

            fetch("destination.php", {
                method: "POST",
                body: formData,
            }).then(response => response.text()).then(data => {
                if (data === "success") {
                    location.reload(); // Recharger la page pour voir les changements
                } else {
                    alert("Erreur lors de la suppression.");
                }
            });
        }
    }

    // Déjà présent : Préremplir le modal de modification
    function showEditModal(id, nom, description, statut) {
        document.getElementById("id_destination").value = id;
        document.getElementById("dNom").value = nom;
        document.getElementById("dDescription").value = description;
        document.getElementById("dStatut").value = statut;
    }

    // Nouveau : Gestion du changement de statut des destinations
    document.querySelectorAll('.toggle-status-destination').forEach(function (element) {
        element.addEventListener('click', function (e) {
            e.preventDefault();
            
            var idDestination = element.getAttribute('data-id');
            var statutActuel = element.getAttribute('data-status');
            var nouveauStatut = (statutActuel === 'Activée') ? 'Désactivée' : 'Activée'; // Alterne entre 'Activée' et 'Désactivée'
            
            let formData = new FormData();
            formData.append("update_status_destination", true);
            formData.append("id_destination", idDestination);
            formData.append("statut", nouveauStatut);
            
            fetch('destination.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    // Met à jour l'affichage du statut sur la page sans recharger
                    element.closest('tr').querySelector('td:nth-child(5) span').textContent = nouveauStatut;
                    element.closest('tr').querySelector('td:nth-child(5) span').className = (nouveauStatut === 'Activée') ? 'text-success' : 'text-warning';
                    
                    // Met à jour l'attribut data-status de l'élément cliqué
                    element.setAttribute('data-status', nouveauStatut);
                } else {
                    alert("Erreur lors de la mise à jour du statut.");
                }
            })
            .catch(error => console.error('Erreur : ', error));
        });
    });
</script>

</body>

</html>