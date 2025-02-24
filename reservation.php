<?php 
// inclusion des fichiers
require('model/config/database.php'); // Gère la connexion à la base de données
require('model/config/util.php'); // contient des fonctions utilitaires
$page = "Réservation";  // Page actuelle

// Mise à jour des informations d'une réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_reservation'])) {
    $id_reservation = $_POST['id_reservation']; // Récupérer l'id de la réservation à mettre à jour
    $id_utilisateur = $_POST['id_utilisateur'];
    $id_destination = $_POST['id_destination'];
    $date_depart = $_POST['date_depart'];
    $date_retour = $_POST['date_retour'];
    $classe_souhaite = $_POST['classe_souhaite'];
    $nombre_passager = $_POST['nombre_passager'];
    $remarques = $_POST['remarques'];

    // Utiliser UPDATE pour modifier une réservation existante
    $stmt = $bdd->prepare("UPDATE reservation 
                           SET id_utilisateur = :id_utilisateur, 
                               id_destination = :id_destination, 
                               date_depart = :date_depart, 
                               date_retour = :date_retour, 
                               classe_souhaite = :classe_souhaite, 
                               nombre_passager = :nombre_passager, 
                               remarques = :remarques
                           WHERE id_reservation = :id_reservation");
    $stmt->execute([
        ':id_utilisateur' => $id_utilisateur,
        ':id_destination' => $id_destination,
        ':date_depart' => $date_depart,
        ':date_retour' => $date_retour,
        ':classe_souhaite' => $classe_souhaite,
        ':nombre_passager' => $nombre_passager,
        ':remarques' => $remarques,
        ':id_reservation' => $id_reservation // Ajouter l'id de la réservation à mettre à jour
    ]);
    echo "<script>alert('Réservation mise à jour avec succès !');</script>";
}

// Vérifier si le formulaire de suppression a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_reservation'])) {
    $id_reservation = $_POST['delete_reservation'];

    try {
        // Vérifier si la réservation existe
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM reservation WHERE id_reservation = :id_reservation");
        $stmt->execute([':id_reservation' => $id_reservation]);
        $count = $stmt->fetchColumn();

        // Si la réservation existe, mettre à jour son statut
        if ($count > 0) {
            $stmt = $bdd->prepare("UPDATE reservation SET statut = 'supprimé' WHERE id_reservation = :id_reservation");
            $stmt->execute([':id_reservation' => $id_reservation]);
            echo "<script>alert('Réservation marquée comme supprimée avec succès !');</script>";
        } else {
            echo "<script>alert('Réservation non trouvée !');</script>";
        }
    } catch (PDOException $e) {
        // Afficher l'erreur détaillée en cas d'exception
        echo "<script>alert('Erreur lors de la mise à jour : " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}

// Modifier le statut d'une réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id_reservation = $_POST['update_status'];
    
    // Récupérer l'actuel statut de la réservation
    $stmt = $bdd->prepare("SELECT statut FROM reservation WHERE id_reservation = :id_reservation");
    $stmt->execute([':id_reservation' => $id_reservation]);
    $currentStatus = $stmt->fetchColumn();

    // Définir le nouveau statut (changer entre 'nouveau' et 'traité' par exemple)
    $newStatus = ($currentStatus === 'nouveau') ? 'traité' : 'nouveau';

    // Mise à jour du statut dans la base de données
    $stmt = $bdd->prepare("UPDATE reservation SET statut = :statut WHERE id_reservation = :id_reservation");
    $stmt->execute([
        ':statut' => $newStatus,
        ':id_reservation' => $id_reservation
    ]);

    echo "<script>alert('Statut mis à jour avec succès !');</script>";
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

$reservations = $bdd->query("SELECT * FROM reservation")->fetchAll();
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
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        <div class="card">
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Utilisateur</th>
                                            <th>Destination</th>
                                            <th>Date de départ</th>
                                            <th>Date de retour</th>
                                            <th>Classe</th>
                                            <th>Passagers</th>
                                            <th>Remarques</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reservations as $reservation) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($reservation['id_utilisateur']) ?></td>
                                                <td><?= htmlspecialchars($reservation['id_destination']) ?></td>
                                                <td><?= htmlspecialchars($reservation['date_depart']) ?></td>
                                                <td><?= htmlspecialchars($reservation['date_retour']) ?></td>
                                                <td><?= htmlspecialchars($reservation['classe_souhaite']) ?></td>
                                                <td><?= htmlspecialchars($reservation['nombre_passager']) ?></td>
                                                <td><?= htmlspecialchars($reservation['remarques']) ?></td>
                                                <td><?= htmlspecialchars($reservation['statut']) ?></td>
                                                <td>
                                                    <a href="#"
                                                        onclick="showEditModal('<?= $reservation['id_reservation'] ?>', '<?= $reservation['id_utilisateur'] ?>', '<?= $reservation['id_destination'] ?>', '<?= $reservation['date_depart'] ?>', '<?= $reservation['date_retour'] ?>', '<?= $reservation['classe_souhaite'] ?>', '<?= $reservation['nombre_passager'] ?>', '<?= $reservation['remarques'] ?>')"
                                                        class="text-warning me-2">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="reservation.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="delete_reservation"
                                                            value="<?= $reservation['id_reservation'] ?>">
                                                        <button type="submit" class="btn btn-link text-danger"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir marquer cette réservation comme supprimé?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                    <!-- Icône pour modifier le statut -->
                                                    <form action="reservation.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="update_status" value="<?= $reservation['id_reservation'] ?>">
                                                        <button type="submit" class="btn btn-link text-primary" onclick="return confirm('Êtes-vous sûr de vouloir modifier le statut de cette réservation ?')">
                                                            <i class="fas fa-sync-alt"></i>
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
    <!-- Modal d'ajout de réservation -->
    <div class="modal fade" id="addReservationModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_utilisateur" class="form-label">Utilisateur</label>
                        <input type="number" class="form-control" id="id_utilisateur" name="id_utilisateur" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_destination" class="form-label">Destination</label>
                        <input type="number" class="form-control" id="id_destination" name="id_destination" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_depart" class="form-label">Date de départ</label>
                        <input type="date" class="form-control" id="date_depart" name="date_depart" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_retour" class="form-label">Date de retour</label>
                        <input type="date" class="form-control" id="date_retour" name="date_retour" required>
                    </div>
                    <div class="mb-3">
                        <label for="classe_souhaite" class="form-label">Classe souhaitée</label>
                        <input type="text" class="form-control" id="classe_souhaite" name="classe_souhaite" required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre_passager" class="form-label">Nombre de passagers</label>
                        <input type="number" class="form-control" id="nombre_passager" name="nombre_passager" required>
                    </div>
                    <div class="mb-3">
                        <label for="remarques" class="form-label">Remarques</label>
                        <textarea class="form-control" id="remarques" name="remarques"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary" type="submit" name="add_reservation">Ajouter</button>
                </div>
            </div>
        </form>
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
                    <div class="mb-3">
                        <label for="uIdUtilisateur" class="form-label">Utilisateur</label>
                        <input type="number" class="form-control" id="uIdUtilisateur" name="id_utilisateur" required>
                    </div>
                    <div class="mb-3">
                        <label for="uIdDestination" class="form-label">Destination</label>
                        <input type="number" class="form-control" id="uIdDestination" name="id_destination" required>
                    </div>
                    <div class="mb-3">
                        <label for="uDateDepart" class="form-label">Date de départ</label>
                        <input type="date" class="form-control" id="uDateDepart" name="date_depart" required>
                    </div>
                    <div class="mb-3">
                        <label for="uDateRetour" class="form-label">Date de retour</label>
                        <input type="date" class="form-control" id="uDateRetour" name="date_retour" required>
                    </div>
                    <div class="mb-3">
                        <label for="uClasseSouhaite" class="form-label">Classe souhaitée</label>
                        <input type="text" class="form-control" id="uClasseSouhaite" name="classe_souhaite" required>
                    </div>
                    <div class="mb-3">
                        <label for="uNombrePassager" class="form-label">Nombre de passagers</label>
                        <input type="number" class="form-control" id="uNombrePassager" name="nombre_passager" required>
                    </div>
                    <div class="mb-3">
                        <label for="uRemarques" class="form-label">Remarques</label>
                        <textarea class="form-control" id="uRemarques" name="remarques"></textarea>
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
    console.log(id, utilisateur, destination, depart, retour, classe, passager, remarques);  // Ajoutez cette ligne pour déboguer
    document.getElementById('id_reservation').value = id;
    document.getElementById('uIdUtilisateur').value = utilisateur;
    document.getElementById('uIdDestination').value = destination;
    document.getElementById('uDateDepart').value = depart;
    document.getElementById('uDateRetour').value = retour;
    document.getElementById('uClasseSouhaite').value = classe;
    document.getElementById('uNombrePassager').value = passager;
    document.getElementById('uRemarques').value = remarques;
    var modal = new bootstrap.Modal(document.getElementById('updateReservationModal'));
    modal.show();
}
    </script>
</body>

</html>