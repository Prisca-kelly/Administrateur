<?php
require_once "../config/database.php";
require_once "../config/util.php";
init_session();
if (is_connected()) {

    if (isset($_POST['updateStatus'])) {
        $id_reservation = intval($_POST['id_reservation']);
        $status = htmlspecialchars($_POST['status']);
        if (intval($id_reservation) > 0 && !empty($status)) {
            $stmt = $bdd->prepare("UPDATE reservation SET statut = ? WHERE id_reservation = ?");
            $stmt->execute(array($status, $id_reservation));
            if ($stmt) {
                echo json_encode(["code" => 200, 'message' => "Statut modifié avec succès !"]);
            } else {
                echo json_encode(["code" => 400, "message" => "Erreur lors de la modification du statut !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }
}
