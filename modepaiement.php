<?php
// inclusion des fichiers
require('model/config/database.php'); // Gère la connexion à la base de données
require('model/config/util.php'); // contient des fonctions utilitaires
init_session(); // Initialiser la session
if (!is_connected()) {
    echo "<script>alert('Veuillez vous connecter avant de continuer !');</script>";
    echo '<script> window.location="index.php"</script>';
}
checkRole();
$page = "Mode de Paiement";  // Page actuelle
// Récupérer tous les modes de paiement
$sqlModePaiement = $bdd->query("SELECT id_modepaiement, nom_modepaiement, description, statut FROM modepaiement");
$modePaiements = $sqlModePaiement->fetchAll();
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
                            <h4 class="fw-bold py-3 mb-0">Modes de Paiement</h4>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModePaiementModal">
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
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($sqlModePaiement->rowCount() === 0) {
                                            echo "<tr><td colspan='4' class='text-center'>Aucun mode de paiement trouvé</td></tr>";
                                        }
                                        foreach ($modePaiements as $mode) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($mode['nom_modepaiement']) ?></td>
                                                <td><?= htmlspecialchars($mode['description']) ?></td>
                                                <td>
                                                    <?php
                                                    // Ajoutez des classes ou des styles selon le statut
                                                    $statusClass = '';
                                                    $statusText = '';

                                                    switch ($mode['statut']) {
                                                        case 'actif':
                                                            $statusClass = 'text-success';
                                                            $statusText = 'Actif';
                                                            break;
                                                        case 'inactif':
                                                            $statusClass = 'text-warning';
                                                            $statusText = 'Inactif';
                                                            break;
                                                        case 'supprimé':
                                                            $statusClass = 'text-danger';
                                                            $statusText = 'Supprimé';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>
                                                <td>

                                                    <!-- Bouton pour modifier -->
                                                    <a href="#"
                                                        onclick="showEditModal('<?= $mode['id_modepaiement'] ?>', '<?= $mode['nom_modepaiement'] ?>', '<?= $mode['description'] ?>')"
                                                        class="text-warning me-2">
                                                        <i class="fas fa-edit text-primary" data-bs-toggle="modal" data-bs-target="#updateModePaiementModal"></i>
                                                    </a>
                                                    <?php
                                                    if ($mode['statut'] == "inactif" || $mode['statut'] == "supprimé") { ?>
                                                        <a href="#"
                                                            class="text-success me-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Activer"
                                                            onclick="changeStatus(<?= $mode['id_modepaiement'] ?>,'actif')">
                                                            <i class="fas fa-lock-open"></i>
                                                        </a>
                                                    <?php } elseif ($mode['statut'] == "actif") { ?>
                                                        <a href="#"
                                                            class="text-warning me-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Désactiver"
                                                            onclick="changeStatus(<?= $mode['id_modepaiement'] ?>,'inactif')">
                                                            <i class="fas fa-lock"></i>
                                                        </a>

                                                    <?php }
                                                    if ($mode['statut'] != "supprimé") { ?>
                                                        <a href="#"
                                                            class="text-danger me-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Supprimer"
                                                            onclick="changeStatus(<?= $mode['id_modepaiement'] ?>,'supprimé')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
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

    <!-- Modal d'ajout de mode de paiement -->
    <div class="modal fade" id="addModePaiementModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un mode de paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nom_modepaiement" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom_modepaiement" name="nom_modepaiement" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary" type="submit" id="add_modepaiement" name="add_modepaiement">Ajouter</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal de modification de mode de paiement -->
    <div class="modal fade" id="updateModePaiementModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier un mode de paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="uNom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="uNom" name="nom_modepaiement" required>
                    </div>
                    <div class="mb-3">
                        <label for="uDescription" class="form-label">Description</label>
                        <input type="text" class="form-control" id="uDescription" name="description" required>
                    </div>
                    <input type="number" id="id_modepaiement" name="id_modepaiement" hidden>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button class="btn btn-primary" type="submit" id="update_modepaiement" name="update_modepaiement">Mise à jour</button>
                    </div>
                </div>
        </form>
    </div>

    <?php include "include/common/script.php"; ?>

    <script>
        $('#add_modepaiement').click((e) => {
            e.preventDefault();
            let data = {
                nom_modepaiement: $('#nom_modepaiement').val(),
                description: $('#description').val(),
                add_modepaiement: "add_modepaiement",
            }
            ajaxRequest("post", "model/app/modepaiement.php", data);
        })

        $('#update_modepaiement').click((e) => {
            e.preventDefault();
            let data = {
                nom_modepaiement: $('#uNom').val(),
                description: $('#uDescription').val(),
                id_modepaiement: $('#id_modepaiement').val(),
                update_modepaiement: "update_modepaiement",
            }
            ajaxRequest("post", "model/app/modepaiement.php", data);
        })
        // Préremplir le modal de modification
        function showEditModal(id, nom, description) {
            document.getElementById("id_modepaiement").value = id;
            document.getElementById("uNom").value = nom;
            document.getElementById("uDescription").value = description;
        }

        // Activer, bloquer et supprimer un utilisateur
        function changeStatus(id_modepaiement, statut) {
            let data = {
                id_modepaiement: id_modepaiement,
                statut: statut,
                updateStatus: "updateStatus",
            };
            let verbe = null;
            if (statut == "actif") {
                verbe = "activer";
            } else if (statut == "inactif") {
                verbe = "désactiver";
            } else {
                verbe = "supprimer";
            }
            confirmSweetAlert("Voulez-vous vraiment " + verbe + " ce mode de paiement ?").then((out) => {
                if (out.isConfirmed) {
                    ajaxRequest("post", "model/app/modepaiement.php", data);
                }
            })
        }
    </script>
</body>

</html>