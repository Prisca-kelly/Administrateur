<?php
require_once "../config/database.php";
require_once "../config/util.php";
init_session();
if (is_connected()) {

    if (isset($_POST['updateStatus'])) {
        $id_reservation = intval($_POST['id_reservation']);
        $montant = 0;
        $status = htmlspecialchars($_POST['status']);

        if ($status == 'validée') $montant = intval($_POST['montant']);
        if (intval($id_reservation) > 0 && !empty($status)) {
            if ($status == 'validée') {
                $stmt = $bdd->prepare("UPDATE reservation SET statut = ?, montant=? WHERE id_reservation = ?");
                $stmt->execute(array($status, $montant, $id_reservation));
            } else {
                $stmt = $bdd->prepare("UPDATE reservation SET statut = ? WHERE id_reservation = ?");
                $stmt->execute(array($status, $id_reservation));
            }
            if ($stmt) {
                echo json_encode(["code" => 200, 'message' => "Statut modifié avec succès !"]);
            } else {
                echo json_encode(["code" => 500, "message" => "Erreur lors de la modification du statut !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }

    if (isset($_POST['detail'])) {
        $id_reservation = intval($_POST['id_reservation']);
        if (intval($id_reservation) > 0) {
            $reservation = $bdd->query("SELECT r.*, u.nom AS nom_utilisateur, d.nom AS nom_destination
                             FROM reservation r
                             JOIN utilisateur u ON r.id_utilisateur = u.id_utilisateur
                             JOIN destination d ON r.id_destination = d.id_destination
                             WHERE r.id_reservation = $id_reservation")->fetch();

            if ($reservation) {
                $result;
                $paiement = null;
                if ($reservation['statut'] == "payée") {
                    $sqlPay = $bdd->query("SELECT m.nom_modepaiement, p.date_paiement, p.telephone, p.numero_carte, p.email FROM paiement p, modepaiement m WHERE m.id_modepaiement = p.id_mode_paiement AND p.id_reservation = $id_reservation");
                    if ($sqlPay->rowCount() > 0) {
                        $paiement = $sqlPay->fetch();
                    }
                }
                $result = [
                    "reservation" => $reservation,
                    "paiement" => $paiement
                ];
                echo json_encode(["code" => 200, "data" => $result]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }
}
