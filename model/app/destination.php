<?php
require_once "../config/database.php";
require_once "../config/util.php";
init_session();
if (is_connected()) {
    $target_dir = "../../uploads/";
    chechUploarDirectory($target_dir);

    if (isset($_POST['updateStatut'])) {
        $id_destination = intval($_POST['id_destination']);
        $statut = htmlspecialchars($_POST['statut']);
        if (intval($id_destination) > 0 && !empty($statut)) {
            $stmt = $bdd->prepare("UPDATE destination SET statut = ? WHERE id_destination = ?");
            $stmt->execute(array($statut, $id_destination));
            if ($stmt) {
                $verbe = null;
                if ($statut == "Activé") {
                    $verbe = "activée";
                } else if ($statut == "Désactivée") {
                    $verbe = "désactivée";
                } else {
                    $verbe = "supprimée";
                }
                echo json_encode(["code" => 200, 'message' => "Destination " . $verbe . " avec succès !"]);
            } else {
                echo json_encode(["code" => 500, "message" => "Erreur lors de la modification du statut !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }

    // Ajouter une destination
    if (isset($_POST["ajouter"])) {
        if (isset($_POST["nom"]) && isset($_POST["description"]) && isset($_FILES["image"])) {
            $nom = htmlspecialchars($_POST["nom"]);
            $description = htmlspecialchars($_POST["description"]);
            $fileName = generateRandomSerialNumber(18) . '.png';
            if (!hasInvalidString($nom, $description, $_FILES["image"]['name'])) {
                $chemin = $target_dir . $fileName;
                $deplacement = move_uploaded_file($_FILES['image']['tmp_name'], $chemin);
                if ($deplacement) {
                    $stmt = $bdd->prepare("INSERT INTO destination (nom, description, image, statut) VALUES (:nom, :description, :image, 'Activé')");
                    $stmt->execute([':nom' => $nom, ':description' => $description, ':image' => $fileName]);
                    if ($stmt) {
                        echo json_encode(["code" => 200, "message" => "Hotel enregistré avec succès !"]);
                    } else {
                        echo json_encode(["code" => 500, "message" => "Erreur du serveur !"]);
                    }
                } else {
                    echo json_encode(["code" => 400, "message" => "Images non téléversé !"]);
                }
            } else {
                echo json_encode(["code" => 400, "message" => "Veuillez remplir tous les champs !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }

    // Modifier une destination
    if (isset($_POST["modifier"])) {
        if (isset($_POST["nom"]) && isset($_POST["description"]) && isset($_POST["id_destination"])) {
            $id_destination = $_POST["id_destination"];
            $nom = htmlspecialchars($_POST["nom"]);
            $description = htmlspecialchars($_POST["description"]);
            $image = $_FILES["image"]["name"];
            $fineName = empty($_POST['imageName']) ? generateRandomSerialNumber(20) . '.png' : $_POST['imageName'];
            if (!hasInvalidString($nom, $description) && intval($id_destination) > 0) {
                if (isset($_FILES['image']) &&  !empty($_FILES['image']['name'])) {
                    $chemin = $target_dir . $fineName;
                    $deplacement = move_uploaded_file($_FILES['image']['tmp_name'], $chemin);
                    if (!$deplacement) {
                        echo json_encode(["code" => 400, "message" => "Images non téléversé !"]);
                        return;
                    }
                    $stmt = $bdd->prepare("UPDATE destination SET nom = :nom, description = :description, image = :image WHERE id_destination = :id_destination");
                    $stmt->execute([':nom' => $nom, ':description' => $description, ':image' => $fineName, ':id_destination' => $id_destination]);
                    if ($stmt) {
                        echo json_encode(["code" => 200, "message" => "Destination modifiée avec succès !"]);
                    } else {
                        echo json_encode(["code" => 500, "message" => "Erreur du serveur !"]);
                    }
                }
            } else {
                echo json_encode(["code" => 400, "message" => "Saisissez le nom et la destination !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }
}
