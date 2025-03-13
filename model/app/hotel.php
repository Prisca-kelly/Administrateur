<?php
require_once "../config/database.php";
require_once "../config/util.php";
init_session();
if (is_connected()) {

    if (isset($_POST['updateStatus'])) {
        $id_hotel = intval($_POST['id_hotel']);
        $status = htmlspecialchars($_POST['status']);
        if (intval($id_hotel) > 0 && !empty($status)) {
            $stmt = $bdd->prepare("UPDATE hotel SET statut = ? WHERE id_hotel = ?");
            $stmt->execute(array($status, $id_hotel));
            if ($stmt) {
                echo json_encode(["code" => 200, 'message' => "Hotel " . $status . " avec succès !"]);
            } else {
                echo json_encode(["code" => 500, "message" => "Erreur lors de la modification du statut !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }

    if (isset($_POST['edit_hotel'])) {
        $id_hotel = $_POST['id_hotel'];
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        $prix = $_POST['prix'];
        $ville = $_POST['ville'];
        $dure = $_POST['dure'];

        if (empty($nom) || empty($description) || empty($prix) || empty($ville) || empty($dure) || empty($id_hotel)) {
            echo json_encode(["code" => 400, "message" => "Veuillez remplir tous les champs !"]);
        } else {
            $query = $bdd->prepare("UPDATE hotel SET nom = ?, description = ?, prix = ?, ville = ?, dure = ? WHERE id_hotel = ?");
            $query->execute(array($nom, $description, $prix, $ville, $dure, $id_hotel));
            if ($query) {
                echo json_encode(["code" => 200, "message" => "Hotel modifié avec succès !"]);
            } else {
                echo json_encode(["code" => 500, "message" => "Erreur lors de la modification de l'hotel !"]);
            }
        }
    }
}
