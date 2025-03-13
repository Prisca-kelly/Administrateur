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

$page = "Utilisateur";

// Récupérer uniquement les utilisateurs avec le rôle 'admin'
$sqlUsers = $bdd->query("SELECT id_utilisateur, nom, email, telephone, adresse, statut FROM utilisateur WHERE role = 'admin'");
$users = $sqlUsers->fetchAll();
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
                            <h4 class="fw-bold py-3 mb-0">Utilisateurs</h4>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        <div class="card">
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Adresse</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($sqlUsers->rowCount() === 0) {
                                            echo "<tr><td colspan='6' class='text-center'>Aucun utilisateur trouvé</td></tr>";
                                        }
                                        foreach ($users as $user) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['nom']) ?></td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td><?= htmlspecialchars($user['telephone']) ?></td>
                                                <td><?= htmlspecialchars($user['adresse']) ?></td>
                                                <td>
                                                    <?php
                                                    // Ajoutez des classes ou des styles selon le statut
                                                    $statusClass = '';
                                                    $statusText = '';

                                                    switch ($user['statut']) {
                                                        case 'Actif':
                                                            $statusClass = 'text-success';
                                                            $statusText = 'Actif';
                                                            break;
                                                        case 'Bloqué':
                                                            $statusClass = 'text-danger';
                                                            $statusText = 'Bloqué';
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
                                                    <!-- Bouton pour modifier -->
                                                    <a href="#"
                                                        onclick="showEditModal('<?= $user['id_utilisateur'] ?>', '<?= $user['nom'] ?>', '<?= $user['email'] ?>', '<?= $user['telephone'] ?>', '<?= $user['adresse'] ?>')"
                                                        class="text-warning me-2">
                                                        <i class="fas fa-edit text-primary" data-bs-toggle="modal"
                                                            data-bs-target="#updateUserModal"></i>
                                                    </a>

                                                    <!-- Bouton pour changer le statut -->
                                                    <?php
                                                    if ($user['statut'] == "Actif") { ?>
                                                        <a href="#!" class="text-warning me-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Bloquer" onclick="changeStatus(<?= $user['id_utilisateur'] ?>,'Bloqué')">
                                                            <i class="fas fa-lock"></i>
                                                        </a>
                                                    <?php } elseif ($user['statut'] == "Bloqué" || $user['statut'] == "Supprimé") { ?>
                                                        <a href="#!" class="text-success me-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Activer" onclick="changeStatus(<?= $user['id_utilisateur'] ?>,'Actif')">
                                                            <i class="fas fa-lock-open"></i>
                                                        </a>
                                                    <?php }
                                                    if ($user['statut'] != "Supprimé") { ?>
                                                        <a href="#!" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Supprimer" onclick="changeStatus(<?= $user['id_utilisateur'] ?>,'Supprimé')"
                                                            class="text-danger">
                                                            <i class="fas fa-trash-alt"></i>
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

    <!-- Modal d'ajout d'utilisateur -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                    </div>
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" required>
                    </div>
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="adresse" name="adresse" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary" type="submit" id="add_user" name="add_user">Ajouter</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal de modification d'utilisateur -->
    <div class="modal fade" id="updateUserModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier un utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="uNom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="uNom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="uEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="uEmail" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="uTelephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="uTelephone" name="telephone" required>
                    </div>
                    <div class="mb-3">
                        <label for="uAdresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="uAdresse" name="adresse" required>
                    </div>
                </div>
                <input type="number" id="id_utilisateur" name="id_utilisateur" hidden>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary" type="submit" id="update_user" name="update_user">Mise à jour</button>
                </div>
            </div>
        </form>
    </div>

    <?php include "include/common/script.php"; ?>

    <script>
        // Créer un utilisateur
        $('#add_user').click((e) => {
            e.preventDefault();
            let data = {
                nom: $('#nom').val(),
                email: $('#email').val(),
                add_user: "add_user",
                adresse: $('#adresse').val(),
                telephone: $('#telephone').val(),
                mot_de_passe: $('#mot_de_passe').val(),
            }
            ajaxRequest("post", "model/app/utilisateur.php", data);
        })

        // Modifier un utilisateur
        $('#update_user').click((e) => {
            e.preventDefault();
            let data = {
                nom: $('#uNom').val(),
                update_user: "update_user",
                adresse: $('#uAdresse').val(),
                telephone: $('#uTelephone').val(),
                id_utilisateur: $('#id_utilisateur').val(),
            }
            ajaxRequest("post", "model/app/utilisateur.php", data);
        })
        // Déjà présent : Préremplir le modal de modification
        function showEditModal(id, nom, email, telephone, adresse) {
            document.getElementById("id_utilisateur").value = id;
            document.getElementById("uNom").value = nom;
            document.getElementById("uEmail").value = email;
            document.getElementById("uTelephone").value = telephone;
            document.getElementById("uAdresse").value = adresse;
        }

        // Activer, bloquer et supprimer un utilisateur
        function changeStatus(id_utilisateur, statut = "Actif" || "Bloqué") {
            let data = {
                id_utilisateur: null,
                statut: statut,
                updateStatus: "updateStatus",
            };
            let verbe = null;

            if (statut == "Actif") {
                verbe = "activer";
            } else if (statut == "Bloqué") {
                verbe = "bloquer";
            } else {
                verbe = "supprimer";
            }

            confirmSweetAlert("Voulez-vous vraiment " + verbe + " ce utilisateur ?").then((out) => {
                if (out.isConfirmed) {
                    ajaxRequest("post", "model/app/utilisateur.php", data);
                }
            })
        }
    </script>
</body>

</html>