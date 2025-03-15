<?php
require_once "../config/database.php";
require_once "../config/util.php";
init_session();
if (is_connected()) {

    if (isset($_POST['updateStatus'])) {
        if (isset($_POST['id_modepaiement']) && intval($_POST['id_modepaiement']) > 0 && isset($_POST['statut']) && !empty($_POST['statut'])) {

            $id_modepaiement = intval($_POST['id_modepaiement']);
            $statut = htmlspecialchars($_POST['statut']);

            $sql = $bdd->prepare("UPDATE modepaiement SET statut = ? WHERE id_modepaiement = ?");
            $sql->execute(array($statut, $id_modepaiement));
            if ($sql) {
                echo json_encode(["code" => 200, 'message' => "Mode de paiement " . $statut]);
            } else {
                echo json_encode(["code" => 500, "message" => "Erreur lors de la modification du statut !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }


    // Ajout d'un mode de paiement
    if (isset($_POST['add_modepaiement'])) {
        if (isset($_POST['nom_modepaiement']) && isset($_POST['description'])) {
            $nom_modepaiement = htmlspecialchars($_POST['nom_modepaiement']);
            $description = htmlspecialchars($_POST['description']);
            if (!hasInvalidString($nom_modepaiement, $description)) {
                $stmt = $bdd->prepare("INSERT INTO modepaiement (nom_modepaiement, description, statut) VALUES (:nom_modepaiement, :description, 'inactif')");
                $stmt->execute([':nom_modepaiement' => $nom_modepaiement, ':description' => $description]);
                if ($stmt) {
                    echo json_encode(["code" => 200, "message" => "Mode de paiement créé avec succès !"]);
                } else {
                    echo json_encode(["code" => 500, "message" => "Erreur du serveur !"]);
                }
            } else {
                echo json_encode(["code" => 400, "message" => "Veuillez remplir tous les champs !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }

    // Mise à jour des informations d'un mode de paiement
    if (isset($_POST['update_modepaiement'])) {
        if (isset($_POST['nom_modepaiement']) && isset($_POST['description']) && isset($_POST['id_modepaiement'])) {
            $id_modepaiement = $_POST['id_modepaiement'];
            $nom_modepaiement = htmlspecialchars($_POST['nom_modepaiement']);
            $description = htmlspecialchars($_POST['description']);
            if (!hasInvalidString($nom_modepaiement, $description, $id_modepaiement)) {
                $stmt = $bdd->prepare("UPDATE modepaiement SET nom_modepaiement = :nom_modepaiement, description = :description WHERE id_modepaiement = :id_modepaiement");
                $stmt->execute([
                    ':nom_modepaiement' => $nom_modepaiement,
                    ':description' => $description,
                    ':id_modepaiement' => $id_modepaiement
                ]);
                if ($stmt) {
                    echo json_encode(["code" => 200, "message" => "Mode de paiement modifié avec succès !"]);
                } else {
                    echo json_encode(["code" => 500, "message" => "Erreur du serveur !"]);
                }
            } else {
                echo json_encode(["code" => 400, "message" => "Veuillez remplir tous les champs !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }
}
