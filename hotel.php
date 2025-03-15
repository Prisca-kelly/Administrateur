<?php
// inclusion des fichiers
require('model/config/database.php'); // Gère la connexion à la base de données
require('model/config/util.php');
init_session(); // Initialiser la session
if (!is_connected()) {
    echo "<script>alert('Veuillez vous connecter avant de continuer !');</script>";
    echo '<script> window.location="index.php"</script>';
}
checkRole();
$page = "Hotel";  // Page actuelle

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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold py-3 mb-0">Hôtels</h4>
                            <button class="btn btn-primary mb-3" data-bs-toggle="modal"
                                data-bs-target="#addHotelModal">Ajouter un hôtel
                            </button>
                        </div>
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
                                                    <img src="uploads/<?= htmlspecialchars($hotel['image']) ?>"
                                                        alt="Image de l'hôtel"
                                                        style="width: 80px; height: 60px; object-fit: cover;">
                                                </td>
                                                <td><?= htmlspecialchars($hotel['nom']) ?></td>
                                                <td><?= htmlspecialchars($hotel['description']) ?></td>
                                                <td><?= htmlspecialchars($hotel['prix']) ?>€</td>
                                                <td><?= htmlspecialchars($hotel['ville']) ?></td>
                                                <td><?= htmlspecialchars($hotel['dure']) ?> jours</td>
                                                <td>
                                                    <?php
                                                    // Ajoutez des classes ou des styles selon le statut de l'hôtel
                                                    $statusClass = '';
                                                    $statusText = '';

                                                    switch ($hotel['statut']) {
                                                        case 'activé':
                                                            $statusClass = 'text-success';
                                                            $statusText = 'Activé';
                                                            break;
                                                        case 'désactivé':
                                                            $statusClass = 'text-danger';
                                                            $statusText = 'Désactivé';
                                                            break;
                                                        case 'supprimé':
                                                            $statusClass = 'text-muted';
                                                            $statusText = 'Supprimé';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>

                                                <td class="justify-content-start">
                                                    <!-- Bouton pour ouvrir le modal de modification -->

                                                    <?php
                                                    if ($hotel['statut'] == "activé") { ?>
                                                        <a href="#!" data-bs-toggle="tooltip" data-bs-placement="right"
                                                            title="Modifier" class="text-primary me-2"
                                                            onclick='editHotelModal("<?= addslashes(json_encode($hotel)); ?>")'>
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#!" data-bs-toggle="tooltip" data-bs-placement="right"
                                                            title="Désactiver"
                                                            onclick="updateStatus('<?= $hotel['id_hotel'] ?>', 'désactivé')"
                                                            class="text-warning me-2">
                                                            <i class="fas fa-lock"></i>
                                                        </a>
                                                        <a href="#!" data-bs-toggle="tooltip" data-bs-placement="right"
                                                            title="Supprimer"
                                                            onclick="updateStatus('<?= $hotel['id_hotel'] ?>', 'supprimé')"
                                                            class="text-danger me-2">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    <?php } elseif ($hotel['statut'] == "désactivé") { ?>
                                                        <a href="#!" data-bs-toggle="tooltip" data-bs-placement="right"
                                                            title="Modifier" class="text-primary me-2"
                                                            onclick='editHotelModal("<?= addslashes(json_encode($hotel)); ?>")'>
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#!" data-bs-toggle="tooltip" data-bs-placement="right"
                                                            title="Activer"
                                                            onclick="updateStatus('<?= $hotel['id_hotel'] ?>',  'activé')"
                                                            class="text-success me-2">
                                                            <i class="fas fa-lock-open"></i>
                                                        </a>
                                                        <a href="#!" data-bs-toggle="tooltip" data-bs-placement="right"
                                                            title="Supprimer"
                                                            onclick="updateStatus('<?= $hotel['id_hotel'] ?>', 'supprimé')"
                                                            class="text-danger me-2">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="#!" data-bs-toggle="tooltip" data-bs-placement="right"
                                                            title="Activer"
                                                            onclick="updateStatus('<?= $hotel['id_hotel'] ?>', 'activé')"
                                                            class="text-success me-2">
                                                            <i class="fas fa-lock-open"></i>
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
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary" id="add_hotel" name="add_hotel">Ajouter</button>
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
                    <form method="POST">
                        <input type="hidden" id="id_hotel" name="id_hotel">
                        <input type="hidden" id="image_old" name="image_old">
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
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*" required>
                            <img src="uploads/" id="imgPreview" width="80" height="60" class="mt-2">
                        </div>
                        <button type="button" class="btn btn-primary" id="edit_hotel" value="">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include "include/common/script.php"; ?>
    <script>
        function editHotelModal(hotel) {
            let value = JSON.parse(hotel);

            $("#id_hotel").val(value.id_hotel);
            $("#edit_nom").val(value.nom);
            $("#edit_description").val(value.description);
            $("#edit_prix").val(value.prix);
            $("#edit_ville").val(value.ville);
            $("#edit_dure").val(value.dure);
            $("#image_old").val(value.image);
            $("#imgPreview").prop("src", "uploads/" + value.image);
            $('#editHotelModal').modal('show');
        }

        $("#edit_hotel").click((e) => {
            e.preventDefault();
            let formData = new FormData();
            formData.append('id_hotel', $("#id_hotel").val());
            formData.append('nom', $('#edit_nom').val());
            formData.append('description', $('#edit_description').val());
            formData.append('prix', $('#edit_prix').val());
            formData.append('ville', $('#edit_ville').val());
            formData.append('dure', $('#edit_dure').val());
            formData.append('imageName', $('#image_old').val());
            formData.append('image', $('#edit_image')[0].files[0]);
            formData.append('edit_hotel', 'edit_hotel');

            $.ajax({
                type: "post",
                url: "model/app/hotel.php",
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
        })

        $("#add_hotel").click((e) => {
            e.preventDefault();
            let formData = new FormData();
            formData.append('nom', $('#nom').val());
            formData.append('description', $('#description').val());
            formData.append('prix', $('#prix').val());
            formData.append('ville', $('#ville').val());
            formData.append('dure', $('#dure').val());
            formData.append('image', $('#image')[0].files[0]);
            formData.append('add_hotel', 'add_hotel');
            $.ajax({
                type: "post",
                url: "model/app/hotel.php",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(res) {
                    console.log(res);

                    if (res.code === 200) {
                        successSweetAlert(res.message);
                    } else if (res.code === 400 || res.code === 500) {
                        errorSweetAlert(res.message);
                    }
                }
            });
        });

        function updateStatus(id, status) {
            let verbe = status == "activé" ? "activer" : status == "désactivé" ? "désactiver" : "supprimer";
            confirmSweetAlert("Voulez-vous vraiment " + verbe + " cet hôtel ?").then((out) => {
                if (out.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "model/app/hotel.php",
                        data: {
                            id_hotel: id,
                            status: status,
                            updateStatus: "updateStatus"
                        },
                        dataType: "text",
                        success: function(response) {
                            let res = JSON.parse(response);
                            if (res.code === 200) {
                                successSweetAlert(res.message);
                            } else if (res.code === 400 || res.code === 500) {
                                errorSweetAlert(res.message);
                            }
                        }
                    });
                }
            })
        }
    </script>

</body>

</html>