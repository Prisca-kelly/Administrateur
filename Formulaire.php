<?php
require('model/config/database.php'); // Connexion à la base de données
require('model/config/util.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];

    // Insertion dans la base de données
    $stmt = $bdd->prepare("
        INSERT INTO utilisateur (nom, email, mot_de_passe, adresse, telephone) 
        VALUES (:nom, :email, :mot_de_passe, :adresse, :telephone)
    ");
    try {
        $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':mot_de_passe' => password_hash($password, PASSWORD_BCRYPT), // Hash du mot de passe
            ':adresse' => $adresse,
            ':telephone' => $telephone
        ]);
        echo "<script>alert('Inscription réussie !');</script>";
        header("Location:accueil.php");
    } catch (Exception $e) {
        echo "<script>alert('Erreur : " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/"
    data-template="vertical-menu-template-free">

<head>
    <?php include "include/common/head.php"; ?>
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" />
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="accueil.php" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <!-- SVG Logo -->
                                </span>
                                <span class="app-brand-text demo text-body fw-bolder">Sneat</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Créez votre compte Sneat! 😊</h4>
                        <p class="mb-4">Veuillez remplir les champs ci-dessous pour vous inscrire.</p>

                        <form id="formAuthentication" class="mb-3" method="POST" action="">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom"
                                    placeholder="Entrez votre nom" required />
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Entrez votre email" required />
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" required />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse"
                                    placeholder="Entrez votre adresse" required />
                            </div>
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone"
                                    placeholder="Entrez votre numéro de téléphone" required />
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">S'inscrire</button>
                            </div>
                        </form>

                        <p class="text-center">
                            <span>Déjà inscrit ?</span>
                            <a href="auth-login-basic.html">
                                <span>Connectez-vous ici</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>

    <?php include "include/common/script.php"; ?>
</body>

</html>
