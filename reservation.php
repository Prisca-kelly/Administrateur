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
$page = "Réservation";  // Page actuelle

// Mise à jour des informations d'une réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_reservation'])) {
    $id_reservation = $_POST['id_reservation']; // Récupérer l'id de la réservation à mettre à jour
    $date_depart = $_POST['date_depart'];
    $date_retour = $_POST['date_retour'];
    $classe_souhaite = $_POST['classe_souhaite'];
    $nombre_passager = $_POST['nombre_passager'];
    $remarques = $_POST['remarques'];

    // Utiliser UPDATE pour modifier une réservation existante
    $stmt = $bdd->prepare("UPDATE reservation 
                           SET date_depart = :date_depart, 
                               date_retour = :date_retour, 
                               classe_souhaite = :classe_souhaite, 
                               nombre_passager = :nombre_passager, 
                               remarques = :remarques
                           WHERE id_reservation = :id_reservation");
    $stmt->execute([
        ':date_depart' => $date_depart,
        ':date_retour' => $date_retour,
        ':classe_souhaite' => $classe_souhaite,
        ':nombre_passager' => $nombre_passager,
        ':remarques' => $remarques,
        ':id_reservation' => $id_reservation // Ajouter l'id de la réservation à mettre à jour
    ]);
    echo "<script>alert('Réservation mise à jour avec succès !');</script>";
}



// Ajouter une réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_reservation'])) {
    // Récupérer les données du formulaire
    $id_utilisateur = $_POST['id_utilisateur'];
    $id_destination = $_POST['id_destination'];
    $date_depart = $_POST['date_depart'];
    $date_retour = $_POST['date_retour'];
    $classe_souhaite = $_POST['classe_souhaite'];
    $nombre_passager = $_POST['nombre_passager'];
    $remarques = $_POST['remarques'];

    // Correction de la requête SQL pour ne pas inclure $id_reservation
    $stmt = $bdd->prepare("INSERT INTO reservation (id_utilisateur, id_destination, date_depart, date_retour, classe_souhaite, nombre_passager, remarques, statut) VALUES (:id_utilisateur, :id_destination, :date_depart, :date_retour, :classe_souhaite, :nombre_passager, :remarques, 'nouveau')");

    // Exécution de la requête avec les bons paramètres
    $stmt->execute([
        ':id_utilisateur' => $id_utilisateur,
        ':id_destination' => $id_destination,
        ':date_depart' => $date_depart,
        ':date_retour' => $date_retour,
        ':classe_souhaite' => $classe_souhaite,
        ':nombre_passager' => $nombre_passager,
        ':remarques' => $remarques
    ]);

    echo "<script>alert('Réservation ajoutée avec succès !');</script>";
}

$reservations = $bdd->query("SELECT r.*, u.nom AS nom_utilisateur, d.nom AS nom_destination
                             FROM reservation r
                             JOIN utilisateur u ON r.id_utilisateur = u.id_utilisateur
                             JOIN destination d ON r.id_destination = d.id_destination")->fetchAll();
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
                            <h4 class="fw-bold py-3 mb-0">Réservations</h4>
                            <!-- <button class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addReservationModal">
                                <i class="fas fa-plus"></i> Ajouter
                            </button> -->
                        </div>
                        <div class="card">
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Utilisateur</th>
                                            <th>Destination</th>
                                            <th>Période de voyage</th>
                                            <th>Classe</th>
                                            <th>Passagers</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reservations as $reservation) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($reservation['nom_utilisateur']) ?></td>
                                                <td> <?= htmlspecialchars($reservation['nom_destination']) ?></td>
                                                <td>
                                                    <span class="text-info">
                                                        <?= htmlspecialchars($reservation['date_depart']) ?>
                                                    </span> <br>
                                                    <span class="text-warning">
                                                        <?= htmlspecialchars($reservation['date_retour']) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($reservation['classe_souhaite']) ?></td>
                                                <td><?= htmlspecialchars($reservation['nombre_passager']) ?></td>
                                                <td style="text-transform: uppercase;"><?= htmlspecialchars($reservation['statut']) ?></td>
                                                <td>

                                                    <button type="submit" class="btn btn-link text-info"
                                                        onclick="detail(<?= $reservation['id_reservation'] ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php
                                                    if ($reservation['statut'] === 'nouveau') { ?>
                                                        <a href="#!"
                                                            onclick="showEditModal('<?= $reservation['id_reservation'] ?>', '<?= $reservation['id_utilisateur'] ?>', '<?= $reservation['id_destination'] ?>', '<?= $reservation['date_depart'] ?>', '<?= $reservation['date_retour'] ?>', '<?= $reservation['classe_souhaite'] ?>', '<?= $reservation['nombre_passager'] ?>', '<?= $reservation['remarques'] ?>')"
                                                            class="text-warning me-2">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#!" class="text-success me-2"
                                                            onclick="updateStatus(<?= $reservation['id_reservation'] ?>, 'validée')">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="#!" class="text-danger me-2"
                                                            onclick="updateStatus(<?= $reservation['id_reservation'] ?>, 'rejetée')">
                                                            <i class="fas fa-times"></i>
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

    <div class="modal fade" id="detailReservationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détail de la réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client : </label>
                            <span class="fw-bold" id="dUtilisateur"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Destination : </label>
                            <span class="fw-bold" id="dDestination"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de départ : </label>
                            <span class="fw-bold" id="dDateDepart"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dDateRetour" class="form-label">Date de retour : </label>
                            <span class="fw-bold" id="dDateRetour"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Classe souhaitée : </label>
                            <span class="fw-bold" id="dClasseSouhaite"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre de passagers : </label>
                            <span class="fw-bold" id="dNombrePassager"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant : </label>
                            <span class="fw-bold" id="dMontant"></span>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Remarques : </label>
                            <small>
                                <p class="text-muted" id="dRemarques"></p>
                            </small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut : </label>
                            <span class="fw-bold" id="dStatut"></span>
                        </div>

                        <div id="paiementInfo" class="d-none">
                            <hr>
                            <h6 class="text-primary text-center">Paiement</h6>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mode de paiement : </label>
                                <span class="fw-bold" id="dNomModepaiement"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de paiement : </label>
                                <span class="fw-bold" id="dDatePaiement"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Téléphone : </label>
                                <span class="fw-bold" id="dTelephone"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email : </label>
                                <span class="fw-bold" id="dEmail"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Numéro de carte : </label>
                                <span class="fw-bold" id="dNumeroCarte"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de modification de réservation -->
    <div class="modal fade" id="updateReservationModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="uDateDepart" class="form-label">Date de départ</label>
                            <input type="date" class="form-control" id="uDateDepart" name="date_depart" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="uDateRetour" class="form-label">Date de retour</label>
                            <input type="date" class="form-control" id="uDateRetour" name="date_retour" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="uClasseSouhaite" class="form-label">Classe souhaitée</label>
                            <input type="text" class="form-control" id="uClasseSouhaite" name="classe_souhaite" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="uNombrePassager" class="form-label">Nombre de passagers</label>
                            <input type="number" class="form-control" id="uNombrePassager" name="nombre_passager" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="uRemarques" class="form-label">Remarques</label>
                            <textarea class="form-control" id="uRemarques" name="remarques"></textarea>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="id_reservation" name="id_reservation">
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary" type="submit" name="update_reservation">Modifier</button>
                </div>
            </div>
        </form>
    </div>

    <?php include "include/common/script.php"; ?>
    <script>
        function showEditModal(id, utilisateur, destination, depart, retour, classe, passager, remarques) {
            document.getElementById('id_reservation').value = id;
            document.getElementById('uDateDepart').value = depart;
            document.getElementById('uDateRetour').value = retour;
            document.getElementById('uClasseSouhaite').value = classe;
            document.getElementById('uNombrePassager').value = passager;
            document.getElementById('uRemarques').value = remarques;
            var modal = new bootstrap.Modal(document.getElementById('updateReservationModal'));
            modal.show();
        }

        function detail(id_reservation) {
            $('#paiementInfo').addClass("d-none")
            $.ajax({
                type: "post",
                url: "model/app/reservation.php",
                data: {
                    detail: "detail",
                    id_reservation: id_reservation
                },
                dataType: "json",
                success: function(response) {
                    if (response.code === 200) {
                        let res = response.data;
                        $('#dUtilisateur').text(res.reservation.nom_utilisateur);
                        $('#dDestination').text(res.reservation.nom_destination);
                        $('#dDateDepart').text(res.reservation.date_depart);
                        $('#dDateRetour').text(res.reservation.date_retour);
                        $('#dClasseSouhaite').text(res.reservation.classe_souhaite);
                        $('#dNombrePassager').text(res.reservation.nombre_passager);
                        $('#dMontant').text(res.reservation.montant + " FCFA");
                        $('#dRemarques').text(res.reservation.remarques);
                        $('#dStatut').text(res.reservation.statut);
                        if (res.paiement && res.paiement != null) {
                            $('#paiementInfo').removeClass("d-none")
                            $('#dNomModepaiement').text(res.paiement.nom_modepaiement);
                            $('#dDatePaiement').text(res.paiement.date_paiement);
                            $('#dTelephone').text(res.paiement.telephone);
                            $('#dEmail').text(res.paiement.email);
                            $('#dNumeroCarte').text(res.paiement.numero_carte);
                        }
                        var modalDetail = new bootstrap.Modal(document.getElementById('detailReservationModal'));
                        modalDetail.show();
                    } else if (response.code === 400 || response.code === 500) {
                        errorSweetAlert(response.message);
                    }

                }
            });
        }

        function updateStatus(id, status) {
            if (status === "validée") {
                confirmInputSweetAlert("Afin de valider cette reservation veuillez saisire le montant de la reservation.").then((out) => {
                    if (out.isConfirmed) {
                        $.ajax({
                            type: "post",
                            url: "model/app/reservation.php",
                            data: {
                                id_reservation: id,
                                status: status,
                                montant: out.value,
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
            } else {
                confirmSweetAlert("Voulez-vous vraiment effectuer cette action ?").then((out) => {
                    if (out.isConfirmed) {
                        $.ajax({
                            type: "post",
                            url: "model/app/reservation.php",
                            data: {
                                id_reservation: id,
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
                });
            }
        }
    </script>
</body>

</html>