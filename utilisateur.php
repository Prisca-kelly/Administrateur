<?php 
require('model/config/database.php');
require('model/config/util.php');
$page = "Utilisateur";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    // Récupérer et sécuriser les données du formulaire
    $id_utilisateur = $_POST['id_utilisateur'];
    $nom = htmlspecialchars($_POST['nom']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);
    
    // Mise à jour de l'utilisateur dans la base de données
    $stmt = $bdd->prepare("UPDATE utilisateur SET nom = :nom, adresse = :adresse, telephone = :telephone WHERE id_utilisateur = :id_utilisateur");
    $stmt->execute([
        ':nom' => $nom,
        ':adresse' => $adresse,
        ':telephone' => $telephone,
        ':id_utilisateur' => $id_utilisateur
    ]);
    echo "<script>alert('Mise à jour réussie !');</script>";
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

$users = $bdd->query("SELECT id_utilisateur, nom, email, telephone, adresse, mot_de_passe FROM utilisateur")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
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
                        <h4 class="fw-bold py-3 mb-4">Utilisateurs</h4>
                        <div class="card">
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Adresse</th>
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
                                                    <button class="btn btn-warning" onclick="showEditModal('<?= addslashes($user['id_utilisateur']) ?>', '<?= addslashes($user['nom']) ?>', '<?= addslashes($user['email']) ?>', '<?= addslashes($user['mot_de_passe']) ?>', '<?= addslashes($user['telephone']) ?>', '<?= addslashes($user['adresse']) ?>')">Modifier</button>
                                                    
                                                    <!-- Formulaire de suppression -->
                                                    <form action="utilisateur.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="delete_user" value="<?= $user['id_utilisateur'] ?>">
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</button>
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

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" role="document" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier l'utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_utilisateur" name="id_utilisateur">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="text" class="form-control" id="password" name="password" readonly>
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
                    <button class="btn btn-primary" type="submit" name="update_user">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>

    <?php include "include/common/script.php"; ?>
    <script>
        function showEditModal(id, nom, email, password, telephone, adresse) {
            document.getElementById('id_utilisateur').value = id;
            document.getElementById('nom').value = nom;
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('telephone').value = telephone;
            document.getElementById('adresse').value = adresse;
            var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();
        }
    </script>
</body>
</html>
