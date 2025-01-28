<?php
require('model/config/database.php'); // Connexion à la base de données
require('model/config/util.php');
$page = "Utilisateur";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $email = $_POST['email'];
    $password = sha1($_POST['password']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);

    /**
     * Il faut vérifier si l'email est correcte
     * Il faut vérifier si l'email existe déjà dans la base de donnée
     *  Si oui, envoie une erreur [Ce email existe déj)]
     *  Si non, alors tu peux enregistrer
     */
    // Insertion dans la base de données
    $stmt = $bdd->prepare("
        INSERT INTO utilisateur (nom, email, mot_de_passe, adresse, telephone) 
        VALUES (:nom, :email, :mot_de_passe, :adresse, :telephone)
    ");
    try {
        $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':mot_de_passe' => $password, // Hash du mot de passe
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
    <title><?= $page ?></title>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include "include/common/sidebar.php"; ?>
            <div class="layout-page">
                <?php include "include/common/navbar.php"; ?>

                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="fw-bold py-3 mb-4">
                            <span class="text-muted fw-light">Utilisateurs</span>
                        </h4>


                        <div class="card">
                            <h5 class="card-header d-flex justify-content-end align-items-center">
                                <button type="button" class="btn btn-trensparent" data-bs-toggle="modal"
                                    data-bs-target="#modalCenter">
                                    <i class="bx bx-plus fs-3"></i>
                                </button>
                            </h5>
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Adresse</th>
                                            <th> </th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <tr>
                                            <td>
                                                <strong>Angular Project</strong>
                                            </td>
                                            <td>Albert Cook</td>
                                            <td>
                                                077898989
                                            </td>
                                            <td>
                                                Chantier moderne
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="bx bx-trash me-1"></i> Supprimer
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>React Project</strong></td>
                                            <td>Barry Hunter</td>
                                            <td>
                                                077899999
                                            </td>
                                            <td><span class="badge bg-label-success me-1">Completed</span></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="bx bx-edit-alt me-2"></i> Edit
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="bx bx-trash me-2"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>VueJs Project</strong></td>
                                            <td>Trevor Baker</td>
                                            <td>
                                                077654321
                                            </td>
                                            <td><span class="badge bg-label-info me-1">Scheduled</span></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="bx bx-edit-alt me-2"></i> Edit
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="bx bx-trash me-2"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Bootstrap Project</strong>
                                            </td>
                                            <td>Jerry Milton</td>
                                            <td>
                                                077895431
                                            </td>
                                            <td><span class="badge bg-label-warning me-1">Pending</span></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="bx bx-edit-alt me-2"></i> Edit
                                                        </a>
                                                        <a class="dropdown-item">
                                                            <i class="bx bx-trash me-2"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" role="document" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" placeholder="Entrez votre nom"
                                required />
                        </div>
                        <div class="col-md mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Entrez votre email" required />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" required />
                                <span class="input-group-text cursor-pointer">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" class="form-control" id="adresse" name="adresse"
                                placeholder="Entrez votre adresse" required />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone"
                                placeholder="Entrez votre numéro de téléphone" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button class="btn btn-primary" type="submit">Enregistrer</button>
                </div>
            </div>
    </div>
    </div>

    <?php include "include/common/script.php"; ?>
    <script>
        function showEditModal(id, nom, email, password, telephone, adresse) {
            var modal = new bootstrap.Modal(document.getElementById('modalCenter'));
            modal.show();
            $("#nom").val(nom);
            $("#email").val(email);
            $("#password").val(password);
            $("#adresse").val(adresse);
            $("#telephone").val(telephone);
        }
    </script>
</body>

</html>