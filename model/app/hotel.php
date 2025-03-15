<?php
require_once "../config/database.php";
require_once "../config/util.php";
init_session();
if (is_connected()) {

    $target_dir = "../../uploads/";
    chechUploarDirectory($target_dir);

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
        if (isset($_POST["id_hotel"]) && isset($_POST["nom"]) && isset($_POST["description"]) && isset($_POST["prix"]) && isset($_POST["ville"]) && isset($_POST["dure"]) && isset($_FILES['image'])) {
            $id_hotel = $_POST['id_hotel'];
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $prix = $_POST['prix'];
            $ville = $_POST['ville'];
            $dure = $_POST['dure'];
            $fineName = empty($_POST['imageName']) ? generateRandomSerialNumber(20) . '.png' : $_POST['imageName'];

            if (hasInvalidString($nom, $description, $prix, $ville, $dure, $id_hotel)) {
                echo json_encode(["code" => 400, "message" => "Veuillez remplir tous les champs !"]);
            } else {
                if (isset($_FILES['image']) &&  !empty($_FILES['image']['name'])) {
                    $chemin = $target_dir . $fineName;
                    $deplacement = move_uploaded_file($_FILES['image']['tmp_name'], $chemin);
                    if (!$deplacement) {
                        echo json_encode(["code" => 400, "message" => "Images non téléversé !"]);
                        return;
                    }
                }
                $query = $bdd->prepare("UPDATE hotel SET nom = ?, description = ?, prix = ?, ville = ?, dure = ?, image = ? WHERE id_hotel = ?");
                $query->execute(array($nom, $description, $prix, $ville, $dure, $fineName, $id_hotel));
                if ($query) {
                    echo json_encode(["code" => 200, "message" => "Hotel modifié avec succès !"]);
                } else {
                    echo json_encode(["code" => 500, "message" => "Erreur lors de la modification de l'hotel !"]);
                }
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }

    if (isset($_POST['add_hotel'])) {
        if (isset($_POST["nom"]) && isset($_POST["description"]) && isset($_POST["prix"]) && isset($_POST["ville"]) && isset($_POST["dure"]) && isset($_FILES['image'])) {
            $nom = htmlspecialchars($_POST['nom']);
            $description = htmlspecialchars($_POST['description']);
            $prix = intval($_POST['prix']);
            $ville = htmlspecialchars($_POST['ville']);
            $dure = intval($_POST['dure']);
            if (!hasInvalidString($nom, $description, $prix, $ville, $dure, $_FILES['image']['name'])) {
                $fileName = generateRandomSerialNumber(18) . '.png';
                $chemin = $target_dir . $fileName;
                $deplacement = move_uploaded_file($_FILES['image']['tmp_name'], $chemin);
                if ($deplacement) {
                    $query = $bdd->prepare("INSERT INTO hotel (image, nom, description, prix, ville, dure, statut) VALUES (?, ?, ?, ?, ?, ?, 'activé')");
                    $query->execute([$fileName, $nom, $description, $prix, $ville, $dure]);
                    if ($query) {
                        echo json_encode(["code" => 200, "message" => "Hotel enregistré avec succès !"]);
                    } else {
                        echo json_encode(["code" => 500, "message" => "Erreur du serveur !"]);
                    }
                } else {
                    echo json_encode(["code" => 400, "message" => "Images non téléversé !"]);
                }
            } else {
                echo json_encode(["code" => 400, "message" => "Remplissez tous les champs !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }
}
