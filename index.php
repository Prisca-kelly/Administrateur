<?php
require('model/config/database.php'); // Inclure la connexion à la base de données
require('model/config/util.php'); // Fichier utilitaire
include 'model/config/checkAdmin.php';
init_session(); // Initialiser la session
if (is_connected()) {
    header("Location: accueil.php");
}
$debug = false;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et assainir les données du formulaire
    $email = $_POST['email'];
    $password = sha1($_POST['password']); // On récupère le mot de passe en clair

    try {
        // Vérifier si l'utilisateur existe
        $stmt = $bdd->prepare("SELECT * FROM utilisateur WHERE email = ? AND mot_de_passe = ? AND role = 'ADMIN'");
        $stmt->execute(array($email, $password));
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            init_session();
            $_SESSION["id"] = $user["id_utilisateur"];
            header("Location: accueil.php");
            exit;
        } else {
            echo "<script>alert('Email ou mot de passe incorrect');</script>";
        }
    } catch (PDOException $e) {
        if ($debug) {
            die("Erreur de connexion : " . $e->getMessage());
        } else {
            echo "<script>alert('Une erreur est survenue. Veuillez réessayer plus tard.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/"
    data-template="vertical-menu-template-free">

<head>
    <?php include "include/common/head.php"; ?>
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" />
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Connexion -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="accueil.php" class="app-brand-link gap-2">
                                <span class="app-brand-text demo text-body fw-bolder">A-S Financial</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Bienvenue à A-S Financial 👋</h4>
                        <p class="mb-4">Veuillez vous connecter à votre compte et commencer l’aventure</p>

                        <form id="formAuthentication" class="mb-3" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Entrez votre adresse e-mail" autofocus required />
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Mot de passe</label>
                                    <a href="auth-forgot-password-basic.html">
                                        <small>Mot de passe oublié ?</small>
                                    </a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        required />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" />
                                    <label class="form-check-label" for="remember-me"> Mémoriser mes informations
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Connexion</button>
                            </div>
                        </form>

                        <p class="text-center">
                            <span>Nouveau sur notre plateforme ?</span>
                            <a href="Formulaire.php">
                                <span>Créer un compte</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- /Connexion -->
            </div>
        </div>
    </div>

    <?php include "include/common/script.php"; ?>
</body>

</html>