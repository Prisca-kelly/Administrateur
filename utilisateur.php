<?php
// inclusion des fichiers
require('model/config/database.php'); // Gère la connexion à la base de données
require('model/config/util.php'); // contient des fonctions utilitaires
$page = "Utilisateur";  // Page actuelle

// Mise à jour des informations d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id_utilisateur = $_POST['id_utilisateur'];
    $nom = htmlspecialchars($_POST['nom']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $statut = htmlspecialchars($_POST['statut']); // Récupérer le statut

    $stmt = $bdd->prepare("UPDATE utilisateur SET nom = :nom, adresse = :adresse, telephone = :telephone, statut = :statut WHERE id_utilisateur = :id_utilisateur");
    $stmt->execute([
        ':nom' => $nom,
        ':adresse' => $adresse,
        ':telephone' => $telephone,
        ':statut' => $statut, // Mettre à jour le statut
        ':id_utilisateur' => $id_utilisateur
    ]);
    echo "<script>alert('Mise à jour réussie !');</script>";
}

// Changement de statut avec validation des statuts possibles
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    $statut = $_POST['statut']; // Récupère le statut du formulaire
    $id_utilisateur = $_POST['id_utilisateur'];
    $statuts_valides = ['Actif', 'Bloqué', 'Supprimé']; // Liste des statuts valides
    if (!in_array($statut, $statuts_valides)) {
        echo "<script>alert('Statut invalide');</script>";
        exit;
    }

    // Préparation et exécution de la mise à jour du statut
    $stmt = $bdd->prepare("UPDATE utilisateur SET statut = :statut WHERE id_utilisateur = :id_utilisateur");
    $stmt->execute([
        ':statut' => $statut,
        ':id_utilisateur' => $id_utilisateur
    ]);

    echo "success";
    exit;    
}


// Suppression d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $id_utilisateur = $_POST['delete_user'];
    try {
        $stmt = $bdd->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id_utilisateur");
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);
        echo "<script>alert('Utilisateur supprimé avec succès !');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Erreur lors de la suppression : " . $e->getMessage() . "');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $statut = htmlspecialchars($_POST['statut']); // Ajouter statut
    $mot_de_passe = sha1($_POST['mot_de_passe']);

    $stmt = $bdd->prepare("INSERT INTO utilisateur (nom, email, telephone, adresse, mot_de_passe, statut) VALUES (:nom, :email, :telephone, :adresse, :mot_de_passe, :statut)");
    $stmt->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':telephone' => $telephone,
        ':adresse' => $adresse,
        ':mot_de_passe' => $mot_de_passe,
        ':statut' => $statut // Ajouter statut
    ]);
    echo "<script>alert('Utilisateur ajouté avec succès !');</script>";
}

// Récupérer uniquement les utilisateurs avec le rôle 'admin'
$users = $bdd->query("SELECT id_utilisateur, nom, email, telephone, adresse, statut FROM utilisateur WHERE role = 'admin'")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include "include/common/head.php"; ?>
    <title><?= $page ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include "include/common/sidebar.php"; ?>
            <div class="layout-page">
                <?php include "include/common/navbar.php"; ?>
                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold py-3 mb-0">Utilisateurs</h4>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        <div class="card">
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Adresse</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['nom']) ?></td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td><?= htmlspecialchars($user['telephone']) ?></td>
                                                <td><?= htmlspecialchars($user['adresse']) ?></td>
                                                <td>
                                                    <?php
                                                    // Ajoutez des classes ou des styles selon le statut
                                                    $statusClass = '';
                                                    $statusText = '';

                                                    switch ($user['statut']) {
                                                        case 'Actif':
                                                            $statusClass = 'text-success';
                                                            $statusText = 'Actif';
                                                            break;
                                                        case 'Bloqué':
                                                            $statusClass = 'text-danger';
                                                            $statusText = 'Bloqué';
                                                            break;
                                                        case 'Supprimé':
                                                            $statusClass = 'text-muted';
                                                            $statusText = 'Supprimé';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>
                                                <td>
                                                    <!-- Bouton pour changer le statut -->
                                                    <a href="#" 
                                                       class="text-primary toggle-status me-2" 
                                                       data-id="<?= $user['id_utilisateur'] ?>" 
                                                       data-status="<?= $user['statut'] ?>">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </a>
                                                    
                                                    <!-- Bouton pour modifier -->
                                                    <a href="#"
                                                       onclick="showEditModal('<?= $user['id_utilisateur'] ?>', '<?= $user['nom'] ?>', '<?= $user['email'] ?>', '<?= $user['telephone'] ?>', '<?= $user['adresse'] ?>')"
                                                       class="text-warning me-2">
                                                        <i class="fas fa-edit text-primary" data-bs-toggle="modal" data-bs-target="#updateUserModal"></i>
                                                    </a>
                                                    
                                                    <!-- Bouton pour supprimer -->
                                                    <form action="utilisateur.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="delete_user" value="<?= $user['id_utilisateur'] ?>">
                                                        <button type="submit" class="btn btn-link text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout d'utilisateur -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                    </div>
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" required>
                    </div>
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="adresse" name="adresse" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary" type="submit" name="add_user">Ajouter</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal de modification d'utilisateur -->
    <div class="modal fade" id="updateUserModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier un utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="uNom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="uNom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="uEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="uEmail" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="uTelephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="uTelephone" name="telephone" required>
                    </div>
                    <div class="mb-3">
                        <label for="uAdresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="uAdresse" name="adresse" required>
                    </div>
                </div>
                <input type="number" id="id_utilisateur" name="id_utilisateur" hidden>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary" type="submit" name="update_user">Mise à jour</button>
                </div>
            </div>
        </form>
    </div>

    <?php include "include/common/script.php"; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".toggle-status").forEach(button => {
                button.addEventListener("click", function (e) {
                    e.preventDefault();
                    let userId = this.getAttribute("data-id");
                    let currentStatus = this.getAttribute("data-status");
                    let newStatus;

            // Logique pour gérer les trois statuts
            if (currentStatus === "Actif") {
                newStatus = "Bloqué";
            } else if (currentStatus === "Bloqué") {
                newStatus = "Supprimé";
            } else {
                newStatus = "Actif"; // Retourne à "Actif"
            }

            let icon = this.querySelector("i");

            let formData = new FormData();
            formData.append("update_status", true);
            formData.append("id_utilisateur", userId);
            formData.append("statut", newStatus);


                    // Mise à jour du statut via AJAX
                    fetch("utilisateur.php", {
                        method: "POST",
                        body: new URLSearchParams({
                            "update_status": true,
                            "id_utilisateur": userId,
                            "statut": newStatus
                        }),
                    }).then(response => response.text()).then(data => {
                        if (data === "success") {
                            location.reload(); // Recharger la page après la mise à jour
                        } else {
                            alert("Erreur lors de la mise à jour du statut");
                        }
                    });
                });
            });
        });

        function showEditModal(id, nom, email, telephone, adresse) {
            document.getElementById("id_utilisateur").value = id;
            document.getElementById("uNom").value = nom;
            document.getElementById("uEmail").value = email;
            document.getElementById("uTelephone").value = telephone;
            document.getElementById("uAdresse").value = adresse;
        }
    </script>
</body>

</html>Le statut ne change pas  