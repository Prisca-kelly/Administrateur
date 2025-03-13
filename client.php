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
$page = "Client";  // Page actuelle

$sqlClients = $bdd->query("SELECT id_utilisateur, nom, email, telephone, adresse, statut FROM utilisateur WHERE role = 'CLIENT'");
$clients = $sqlClients->fetchAll();
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
                            <h4 class="fw-bold py-3 mb-0">Clients</h4>
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
                                        if ($sqlClients->rowCount() === 0) {
                                            echo "<tr><td colspan='6' class='text-center'>Aucun client trouvé.</td></tr>";
                                        }
                                        foreach ($clients as $client) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($client['nom']) ?></td>
                                                <td><?= htmlspecialchars($client['email']) ?></td>
                                                <td><?= htmlspecialchars($client['telephone']) ?></td>
                                                <td><?= htmlspecialchars($client['adresse']) ?></td>
                                                <td>
                                                    <?php
                                                    // Ajoutez des classes ou des styles selon le statut
                                                    $statusClass = '';
                                                    $statusText = '';

                                                    switch ($client['statut']) {
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
                                                    <!-- Bouton pour changer le statut -->

                                                    <a href="#"
                                                        onclick="showEditModal('<?= $client['id_utilisateur'] ?>', '<?= $client['nom'] ?>', '<?= $client['email'] ?>', '<?= $client['telephone'] ?>', '<?= $client['adresse'] ?>')"
                                                        class="text-warning me-2">
                                                        <i class="fas fa-edit text-primary" data-bs-toggle="modal"
                                                            data-bs-target="#updateClientModal"></i>
                                                    </a>

                                                    <?php
                                                    if ($client['statut'] == "Actif") { ?>
                                                        <a href="#!" class="text-warning me-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Bloquer" onclick="changeStatus(<?= $client['id_utilisateur'] ?>,'Bloqué')">
                                                            <i class="fas fa-lock"></i>
                                                        </a>
                                                    <?php } elseif ($client['statut'] == "Bloqué" || $client['statut'] == "Supprimé") { ?>
                                                        <a href="#!" class="text-success me-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Activer" onclick="changeStatus(<?= $client['id_utilisateur'] ?>,'Actif')">
                                                            <i class="fas fa-lock-open"></i>
                                                        </a>
                                                    <?php }
                                                    if ($client['statut'] != "Supprimé") { ?>
                                                        <a href="#!" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Supprimer" onclick="changeStatus(<?= $client['id_utilisateur'] ?>,'Supprimé')"
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

    <!-- Modal de modification de client -->
    <div class="modal fade" id="updateClientModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier un client</h5>
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
                    <button class="btn btn-primary" type="submit" name="update_client" id="update_client">Mettre à jour</button>
                </div>
            </div>
        </form>
    </div>

    <?php include "include/common/script.php"; ?>
    <script>
        // Déjà présent : Préremplir le modal de modification
        function showEditModal(id, nom, email, telephone, adresse) {
            document.getElementById("id_utilisateur").value = id;
            document.getElementById("uNom").value = nom;
            document.getElementById("uEmail").value = email;
            document.getElementById("uTelephone").value = telephone;
            document.getElementById("uAdresse").value = adresse;
        }

        // Modifier un utilisateur
        $('#update_client').click((e) => {
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

        // Activer, bloquer et supprimer un utilisateur
        function changeStatus(id_utilisateur, statut = "Actif" || "Bloqué") {
            let data = {
                id_utilisateur: id_utilisateur,
                statut: statut,
                updateStatus: "updateStatus",
            };
            let verbe = statut == "Actif" ? "activer" : statut == "Bloqué" ? "bloquer" : "supprimer";
            confirmSweetAlert("Voulez-vous vraiment " + verbe + " ce utilisateur ?").then((out) => {
                if (out.isConfirmed) {
                    ajaxRequest("post", "model/app/utilisateur.php", data);
                }
            })
        }
    </script>
</body>

</html>