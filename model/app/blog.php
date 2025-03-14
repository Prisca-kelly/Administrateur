<?php
require_once "../config/database.php";
require_once "../config/util.php";
init_session();
if (is_connected()) {
    $extensions = array('jpeg', 'jpg', 'png', 'gif');
    $target_dir = "../../uploads/";
    chechUploarDirectory($target_dir);
    // Ajouter un article
    if (isset($_POST["ajouter"])) {
        if (isset($_POST["titre"]) && isset($_POST["contenu"]) && isset($_FILES['image'])) {
            $titre = htmlspecialchars($_POST["titre"]);
            $contenu = htmlspecialchars($_POST["contenu"]);
            if (!hasInvalidString($titre, $contenu, $_FILES['image']['name'])) {
                $fileName = generateRandomSerialNumber(20) . '.png';
                $chemin = $target_dir . $fileName;
                $deplacement = move_uploaded_file($_FILES['image']['tmp_name'], $chemin);
                if ($deplacement) {
                    $sql = $bdd->prepare("INSERT INTO articleblog (titre, contenu, image, statut) VALUES (:titre, :contenu, :image, 'publié')");
                    $sql->execute([
                        ':titre' => $titre,
                        ':contenu' => $contenu,
                        ':image' => $fileName
                    ]);
                    if ($sql) {
                        echo json_encode(["code" => 200, "message" => "Article de blog enregistré avec succès !"]);
                    } else {
                        echo json_encode(["code" => 500, "message" => "Erreur du serveur !"]);
                    }
                } else {
                    echo json_encode(["code" => 400, "message" => "Images non téléversé !"]);
                }
            } else {
                echo json_encode(["code" => 400, "message" => "Saisissez le titre et le contenu !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }


    // Modifier un article
    if (isset($_POST['modifier'])) {
        if (isset($_POST["titre"]) && isset($_POST["contenu"]) && isset($_POST['id_article'])) {
            $id_article = $_POST['id_article'];
            $titre = htmlspecialchars($_POST['titre']);
            $contenu = htmlspecialchars($_POST['contenu']);
            $fineName = empty($_POST['imageName']) ? generateRandomSerialNumber(20) . '.png' : $_POST['imageName'];
            if (!hasInvalidString($titre, $contenu) && intval($id_article) > 0) {
                $sql = $bdd->prepare("UPDATE articleblog SET titre = :titre, contenu = :contenu, `image` = :image WHERE id_article = :id_article");

                if (isset($_FILES['image']) &&  !empty($_FILES['image']['name'])) {
                    $chemin = $target_dir . $fineName;
                    $deplacement = move_uploaded_file($_FILES['image']['tmp_name'], $chemin);
                    if (!$deplacement) {
                        echo json_encode(["code" => 400, "message" => "Images non téléversé !"]);
                        return;
                    }
                }
                $sql->execute([':titre' => $titre, ':contenu' => $contenu, "image" => $fineName, ':id_article' => $id_article]);
                if ($sql) {
                    echo json_encode(["code" => 200, "message" => "Article de blog enregistré avec succès !"]);
                } else {
                    echo json_encode(["code" => 500, "message" => "Erreur du serveur !"]);
                }
            } else {
                echo json_encode(["code" => 400, "message" => "Saisissez le titre et le contenu !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }


    // Changer le statut de l'article
    if (isset($_POST['changer_statut'])) {
        if (isset($_POST['id_article']) && isset($_POST['statut'])) {
            $id_article = $_POST['id_article'];
            $statut = $_POST['statut'];
            if (!hasInvalidString($id_article, $statut)) {
                $stmt = $bdd->prepare("UPDATE articleblog SET statut = :statut WHERE id_article = :id_article");
                $stmt->execute([':statut' => $statut, ':id_article' => $id_article]);
                if ($stmt) {
                    echo json_encode(["code" => 200, "message" => "Article de blog " . $statut . " avec succès !"]);
                } else {
                    echo json_encode(["code" => 500, "message" => "Erreur du serveur !"]);
                }
            } else {
                echo json_encode(["code" => 400, "message" => "Erreur technique !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }
}
