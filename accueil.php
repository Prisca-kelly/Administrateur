<?php
require('model/config/database.php'); // Inclure la connexion
require('model/config/util.php'); // Fichier qui gère les sessions
init_session(); // Initialiser la session
if (!is_connected()) {
    echo "<script>alert('Veuillez vous connecter avant de continuer !');</script>";
    echo '<script> window.location="index.php"</script>';
}
$page = "Accueil";

function countTout($table): int
{
    global $bdd;
    $sql = $bdd->query("SELECT COUNT(*) as total FROM $table");
    $sql->execute();
    return $sql->fetch()["total"];
}

function countParStatus($table, $statut): int
{
    global $bdd;
    $sql = $bdd->prepare("SELECT COUNT(*) as total FROM $table WHERE statut = ?");
    $sql->execute(array($statut));
    return $sql->fetch()["total"];
}

function getLastReservations()
{
    global $bdd;
    $sql = $bdd->query("SELECT r.date_depart, r.date_retour, u.nom, d.nom AS destination
                          FROM reservation r
                          JOIN utilisateur u ON r.id_utilisateur = u.id_utilisateur
                          JOIN destination d ON r.id_destination = d.id_destination
                          ORDER BY r.date_creation DESC
                          LIMIT 6");
    $sql->execute();
    return $sql->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/"
    data-template="vertical-menu-template-free">

<head>
    <?php include "include/common/head.php"; ?>
    <title><?= $page ?></title>
    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include "include/common/sidebar.php"; ?>
            <div class="layout-page">
                <?php include "include/common/navbar.php"; ?>

                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold d-flex align-items-center">
                                                <i class='bx bx-buildings fs-2'></i>
                                                <span>Hôtel</span>
                                            </span>
                                            <h5 class="card-title text-primary"><?= countTout("hotel") ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold d-flex align-items-center">
                                                <i class='bx bxs-plane-alt fs-2'></i>
                                                <span>Destination</span>
                                            </span>
                                            <h5 class="card-title text-primary"><?= countTout("destination") ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold d-flex align-items-center">
                                                <i class='bx bx-calendar fs-2'></i>
                                                <span>Reservations</span>
                                            </span>
                                            <h5 class="card-title text-primary"><?= countTout("reservation") ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold d-flex align-items-center">
                                                <i class='bx bx-file fs-2'></i>
                                                <span>Blogs</span>
                                            </span>
                                            <h5 class="card-title text-primary"><?= countTout("articleblog") ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between">
                                        <span>Taux de reservation de l'année</span>
                                    </div>
                                    <div class="card-body px-0">
                                        <div id="tauxReservations"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-12 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h5 class="card-title m-0 me-2">
                                            Dernières reservations
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                    <ul class="p-0 m-0">
    <?php
    $lastReservations = getLastReservations();
    $totalReservations = count($lastReservations);
    if ($totalReservations > 0) {
        foreach ($lastReservations as $item) { ?>
            <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3 bg-label-success d-flex justify-content-center align-items-center">
                    <i class="bx bx-calendar"></i>
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                        <small class="text-muted d-block mb-1">
                            <?= $item['destination'] ?> <?= $item['date_depart'] ?> - <?= $item['date_retour'] ?>
                        </small>
                        <h6 class="mb-0">
                            <?= $item['nom'] ?>
                        </h6>
                    </div>
                </div>
            </li>
        <?php }
    } else {
        echo "<h6 class='text-center'>Aucune réservation enregistrée !</h6>";
    }
    ?>
</ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between">
                                        <span>Taux de reservations par destination</span>
                                    </div>
                                    <div class="card-body px-0">
                                        <div id="chartHotel"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between">
                                        <span>Répartition des utilisateurs</span>
                                    </div>
                                    <div class="card-body px-0">
                                        <div id="repartitionUtilisateurs"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/dashboard.js"></script>

    <script>
        $.ajax({
            type: "get",
            url: "model/app/dashboard.php",
            data: {
                repatitionReservationParMois: true,
            },
            dataType: "text",
            success: function(response) {
                let res = JSON.parse(response);
                let data = {
                    mois: [],
                    number: []
                };
                if (res.length > 0) {
                    res.forEach(element => {
                        data.mois.push(element.month_name);
                        data.number.push(element.reservation_count);
                    });
                }
                repatitionReservationParMois(data.mois, data.number);
            },
        });

        $.ajax({
            type: "get",
            url: "model/app/dashboard.php",
            data: {
                repatitionReservation: true,
            },
            dataType: "text",
            success: function(response) {
                let res = JSON.parse(response);
                let data = {
                    noms: [],
                    number: []
                };
                if (res.length > 0) {
                    res.forEach(element => {
                        data.noms.push(element.nom);
                        data.number.push(element.count);
                    });
                }
                destinationReservation(data)
            },
        });
        $.ajax({
            type: "get",
            url: "model/app/dashboard.php",
            data: {
                repatitionUtilisateur: true,
            },
            dataType: "text",
            success: function(response) {
                let res = JSON.parse(response);
                utilisateurChart(res[0], res[1]);
            },
        });
    </script>
</body>

</html>