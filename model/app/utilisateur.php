<?php
require_once "../config/database.php";
require_once "../config/util.php";
init_session();
if (is_connected()) {

    if (isset($_POST['add_user'])) {
        if (isset($_POST['nom']) && isset($_POST['email']) && isset($_POST['adresse']) && isset($_POST['telephone']) && isset($_POST['mot_de_passe'])) {
            $nom = htmlspecialchars($_POST['nom']);
            $email = htmlspecialchars($_POST['email']);
            $telephone = htmlspecialchars($_POST['telephone']);
            $adresse = htmlspecialchars($_POST['adresse']);
            $mot_de_passe = htmlspecialchars($_POST['mot_de_passe']);
            if (!hasInvalidString($nom, $email, $telephone, $adresse, $password)) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $stmt_check = $bdd->prepare("SELECT * FROM utilisateur WHERE email = :email");
                    $stmt_check->execute([':email' => $email]);
                    if ($stmt_check->rowCount() == 0) {
                        $sql = $bdd->prepare("INSERT INTO utilisateur (nom, email, mot_de_passe, adresse, telephone, role) VALUES (?, ?, ?, ?, ?,'ADMIN')");
                        $sql->execute(array($nom, $email, sha1($mot_de_passe), $adresse, $telephone));
                        if ($sql) {
                            echo json_encode(["code" => 200, "message" => "Mise à jour réussie !"]);
                        } else {
                            echo json_encode(["code" => 500, "message" => "Erreur lors de l'enregistrement !"]);
                        }
                    } else {
                        echo json_encode(["code" => 400, "message" => "Cette adresse Email este déjà utilisée !"]);
                    }
                } else {
                    echo json_encode(["code" => 400, "message" => "E-mail invalide !"]);
                }
            } else {
                echo json_encode(["code" => 400, "message" => "Replissez tous les champs !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }

    if (isset($_POST['update_user'])) {
        if (isset($_POST['nom']) && isset($_POST['adresse']) && isset($_POST['telephone']) && isset($_POST['id_utilisateur'])) {
            $nom = htmlspecialchars($_POST['nom']);
            $adresse = htmlspecialchars($_POST['adresse']);
            $telephone = htmlspecialchars($_POST['telephone']);
            $id_utilisateur = htmlspecialchars($_POST['id_utilisateur']);

            $sql = $bdd->prepare("UPDATE utilisateur SET nom = ?, adresse = ?, telephone = ? WHERE id_utilisateur = ?");
            $sql->execute(array($nom, $adresse, $telephone, $id_utilisateur));
            if ($sql) {
                echo json_encode(["code" => 200, "message" => "Mise à jour réussie !"]);
            } else {
                echo json_encode(["code" => 500, "message" => "Erreur lors de la mise à jour de l'utilisateur !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Replissez tous les champs !"]);
        }
    }

    if (isset($_POST['updateStatus'])) {
        if (isset($_POST['id_utilisateur']) && intval($_POST['id_utilisateur']) > 0 && isset($_POST['statut']) && !empty($_POST['statut'])) {

            $id_utilisateur = intval($_POST['id_utilisateur']);
            $statut = htmlspecialchars($_POST['statut']);

            $sql = $bdd->prepare("UPDATE utilisateur SET statut = ? WHERE id_utilisateur = ?");
            $sql->execute(array($statut, $id_utilisateur));
            if ($sql) {
                echo json_encode(["code" => 200, 'message' => "Utilisateur " . strtolower($statut) . " avec succès !"]);
            } else {
                echo json_encode(["code" => 500, "message" => "Erreur lors de la modification du statut !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Erreur de la requête !"]);
        }
    }
}


if (isset($_POST['login'])) {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];
        if (!hasInvalidString($email, $password)) {
            $sql = $bdd->prepare("SELECT * FROM utilisateur WHERE email = ? AND mot_de_passe = ?");
            $sql->execute(array($email, sha1($password)));
            $row = $sql->rowCount();
            if ($row == 1) {
                $utilisateur = $sql->fetch();
                if ($utilisateur['role'] == "ADMIN") {
                    init_session();
                    $_SESSION["id"] = $utilisateur["id_utilisateur"];
                    $_SESSION["role"] = $utilisateur["role"];
                    echo json_encode(["code" => 200, "message" => "Connexion réussie !"]);
                } else {
                    echo json_encode(["code" => 400, "message" => "Accès refusé !"]);
                }
            } else {
                echo json_encode(["code" => 400, "message" => "E-mail ou mot de passe incorrect !"]);
            }
        } else {
            echo json_encode(["code" => 400, "message" => "Replissez tous les champs !"]);
        }
    }
}
