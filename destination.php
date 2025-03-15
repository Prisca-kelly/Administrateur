<?php
require('model/config/database.php');
require('model/config/util.php');
init_session(); // Initialiser la session
if (!is_connected()) {
    echo "<script>alert('Veuillez vous connecter avant de continuer !');</script>";
    echo '<script> window.location="index.php"</script>';
}
checkRole();
$page = "Destination";
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
                                        <?php
                                        if (count($destinations) == 0) {
                                            echo '<tr><td colspan="5" class="text-center">Aucune destination</td></tr>';
                                        }
                                        foreach ($destinations as $destination) : ?>
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
                                                    <?php
                                                    if ($destination['statut'] != "Supprimé") { ?>
                                                        <a class="btn btn-link text-info"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Modifier"
                                                            onclick='showEditModal("<?= addslashes(json_encode($destination)) ?>")'>
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    <?php }
                                                    if ($destination['statut'] == "Désactivée" || $destination['statut'] == "Supprimé") { ?>
                                                        <a href="#!" class="text-success me-3"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Activer"
                                                            onclick="updateStatut(<?= $destination['id_destination'] ?>,'Activé')">
                                                            <i class="fa-solid fa-lock-open"></i>
                                                        </a>
                                                    <?php }
                                                    if ($destination['statut'] === "Activé") { ?>
                                                        <a href="#!" class="text-warning me-3"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Désactiver"
                                                            onclick="updateStatut(<?= $destination['id_destination'] ?>,'Désactivée')">
                                                            <i class="fa-solid fa-lock"></i>
                                                        </a>
                                                    <?php }

                                                    if ($destination['statut'] != "Supprimé") { ?>
                                                        <a href="#!" class="text-danger me-3"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Supprimer"
                                                            onclick="updateStatut(<?= $destination['id_destination'] ?>,'Supprimé')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    <?php }
                                                    ?>



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
                            <input type="file" class="form-control" id="image" accept=".png, .jpg, .jpeg" name="image" required>
                        </div>
                        <button type="submit" id="ajouter" name="ajouter" class="btn btn-primary">Ajouter</button>
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
                        <input type="hidden" name="edit_id" id="id_destination">
                        <input type="hidden" id="image_old" name="image_old">
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
                            <input type="file" class="form-control" id="edit_image" accept=".png, .jpg, .jpeg" name="image">
                            <small class="form-text text-muted">Laissez vide si vous ne souhaitez pas modifier l'image.</small><br />
                            <img src="uploads/" id="imgPreview" width="80" height="60" class="mt-2">
                        </div>
                        <button type="submit" id="modifier" name="modifier" class="btn btn-primary">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include "include/common/script.php"; ?>
    <script>
        $('#ajouter').click((e) => {
            e.preventDefault();
            let formData = new FormData();
            formData.append('nom', $('#nom').val());
            formData.append('description', $('#description').val());
            formData.append('image', $('#image')[0].files[0]);
            formData.append('ajouter', 'ajouter');
            $.ajax({
                type: "post",
                url: "model/app/destination.php",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.code === 200) {
                        successSweetAlert(res.message);
                    } else if (res.code === 400 || res.code === 500) {
                        errorSweetAlert(res.message);
                    }
                }
            });
        });

        $('#modifier').click((e) => {
            e.preventDefault();

            let formData = new FormData();
            formData.append('nom', $('#edit_nom').val());
            formData.append('description', $('#edit_description').val());
            formData.append('id_destination', $('#id_destination').val());
            formData.append('imageName', $('#image_old').val());
            formData.append('image', $('#edit_image')[0].files[0]);
            formData.append('modifier', 'modifier');
            $.ajax({
                type: "post",
                url: "model/app/destination.php",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.code === 200) {
                        successSweetAlert(res.message);
                    } else if (res.code === 400 || res.code === 500) {
                        errorSweetAlert(res.message);
                    }
                }
            });
        });

        // Déjà présent : Préremplir le modal de modification
        function showEditModal(data) {
            let res = JSON.parse(data)
            $("#id_destination").val(res.id_destination);
            $("#edit_nom").val(res.nom);
            $("#edit_description").val(res.description);
            $("#image_old").val(res.image);
            $("#imgPreview").prop("src", "uploads/" + res.image)
            var editDestinationModal = new bootstrap.Modal(document.getElementById('editDestinationModal'));
            editDestinationModal.show();
        }

        function updateStatut(id_destination, statut) {
            let data = {
                id_destination: id_destination,
                statut: statut,
                updateStatut: "updateStatut",
            };
            let verbe = null;

            if (statut == "Activé") {
                verbe = "activer";
            } else if (statut == "Désactivée") {
                verbe = "désactiver";
            } else {
                verbe = "supprimer";
            }
            confirmSweetAlert("Voulez-vous vraiment " + verbe + " cette destination ?").then((out) => {
                if (out.isConfirmed) {
                    ajaxRequest("post", "model/app/destination.php", data);
                }
            })

        }
    </script>

</body>

</html>