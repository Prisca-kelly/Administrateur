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

// Mise à jour des informations d'un mode de paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_modepaiement'])) {
    $id_modepaiement = $_POST['id_modepaiement'];
    $nom_modepaiement = htmlspecialchars($_POST['nom_modepaiement']);
    $description = htmlspecialchars($_POST['description']); // Récupérer les détails
    $statut = htmlspecialchars($_POST['statut']); // Récupérer le statut

    $stmt = $bdd->prepare("UPDATE modepaiement SET nom_modepaiement = :nom_modepaiement, description = :description, statut = :statut WHERE id_modepaiement = :id_modepaiement");
    $stmt->execute([
        ':nom_modepaiement' => $nom_modepaiement,
        ':description' => $description,
        ':statut' => $statut,
        ':id_modepaiement' => $id_modepaiement
    ]);
    echo "<script>alert('Mise à jour réussie !');</script>";
}

// Suppression d'un mode de paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_modepaiement'])) {
    $id_modepaiement = $_POST['delete_modepaiement'];

    // Mettre à jour le statut à "supprimé"
    $stmt = $bdd->prepare("UPDATE modepaiement SET statut = 'supprimé' WHERE id_modepaiement = :id_modepaiement");
    $stmt->execute([':id_modepaiement' => $id_modepaiement]);

    echo "success";
    exit;
}

// Mise à jour du statut d'un mode de paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id_modepaiement = $_POST['id_modepaiement'];
    $new_status = $_POST['new_status'];

    // Mettre à jour le statut en base de données
    $stmt = $bdd->prepare("UPDATE modepaiement SET statut = :new_status WHERE id_modepaiement = :id_modepaiement");
    $stmt->execute([
        ':new_status' => $new_status,
        ':id_modepaiement' => $id_modepaiement
    ]);

    echo "success"; // Réponse pour indiquer le succès
    exit;
}

// Ajout d'un mode de paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_modepaiement'])) {
    $nom_modepaiement = htmlspecialchars($_POST['nom_modepaiement']);
    $description = htmlspecialchars($_POST['description']);
    $statut = htmlspecialchars($_POST['statut']); // Ajouter statut

    $stmt = $bdd->prepare("INSERT INTO modepaiement (nom_modepaiement, description, statut) VALUES (:nom_modepaiement, :description, :statut)");
    $stmt->execute([
        ':nom_modepaiement' => $nom_modepaiement,
        ':description' => $description,
        ':statut' => $statut
    ]);
    echo "<script>alert('Mode de paiement ajouté avec succès !');</script>";
}


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
                                                            $statusClass = 'text-danger';
                                                            $statusText = 'Inactif';
                                                            break;
                                                        case 'supprimé':
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
                                                        class="text-primary toggle-status me-2"
                                                        data-id="<?= $mode['id_modepaiement'] ?>"
                                                        data-status="<?= $mode['statut'] ?>">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </a>

                                                    <!-- Bouton pour modifier -->
                                                    <a href="#"
                                                        onclick="showEditModal('<?= $mode['id_modepaiement'] ?>', '<?= $mode['nom_modepaiement'] ?>', '<?= $mode['description'] ?>', '<?= $mode['statut'] ?>')"
                                                        class="text-warning me-2">
                                                        <i class="fas fa-edit text-primary" data-bs-toggle="modal" data-bs-target="#updateModePaiementModal"></i>
                                                    </a>

                                                    <!-- Bouton pour supprimer -->
                                                    <a href="#" onclick="markAsDeleted('<?= $mode['id_modepaiement'] ?>')" class="text-danger">
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
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-control" id="statut" name="statut" required>
                            <option value="actif">Actif</option>
                            <option value="inactif">Inactif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary" type="submit" name="add_modepaiement">Ajouter</button>
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
                        <button class="btn btn-primary" type="submit" name="update_modepaiement">Mise à jour</button>
                    </div>
                </div>
        </form>
    </div>

    <?php include "include/common/script.php"; ?>

    <script>
        document.querySelectorAll('.toggle-status').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                let idModePaiement = this.getAttribute('data-id');
                let currentStatus = this.getAttribute('data-status');

                // Changer le statut
                let newStatus = (currentStatus === 'actif') ? 'inactif' : 'actif';

                let formData = new FormData();
                formData.append('update_status', true);
                formData.append('id_modepaiement', idModePaiement);
                formData.append('new_status', newStatus);

                fetch('modepaiement.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'success') {
                            // Actualiser la page ou changer dynamiquement le statut
                            location.reload(); // Recharger pour voir le changement
                        } else {
                            alert('Erreur lors de la mise à jour du statut.');
                        }
                    });
            });
        });

        // Gestion des modes de paiement supprimés
        function markAsDeleted(idModePaiement) {
            if (confirm("Êtes-vous sûr de vouloir marquer ce mode de paiement comme supprimé ?")) {
                let formData = new FormData();
                formData.append("delete_modepaiement", idModePaiement);

                fetch("modepaiement.php", {
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

        // Préremplir le modal de modification
        function showEditModal(id, nom, description, statut) {
            document.getElementById("id_modepaiement").value = id;
            document.getElementById("uNom").value = nom;
            document.getElementById("uDescription").value = description;
            document.getElementById("uStatut").value = statut;
        }
    </script>
</body>

</html>