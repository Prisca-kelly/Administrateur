<?php
// inclusion des fichiers
require('model/config/database.php'); // Gère la connexion à la base de données
require('model/config/util.php'); // contient des fonctions utilitaires
$page = "Clients";  // Page actuelle

// Mise à jour des informations d'un client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_client'])) {
    $id_utilisateur = $_POST['id_utilisateur'];
    $nom = htmlspecialchars($_POST['nom']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);

    $stmt = $bdd->prepare("UPDATE utilisateur SET nom = :nom, adresse = :adresse, telephone = :telephone WHERE id_utilisateur = :id_utilisateur AND role = 'CLIENT'");
    $stmt->execute([
        ':nom' => $nom,
        ':adresse' => $adresse,
        ':telephone' => $telephone,
        ':id_utilisateur' => $id_utilisateur
    ]);
    echo "<script>alert('Mise à jour réussie !');</script>";
}

// Mise à jour du statut d'un client
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    $id_utilisateur = $_POST["id_utilisateur"];
    $statut = $_POST["statut"];

    // Vérification des statuts valides
    $statut_valid = ['Actif', 'Bloqué', 'Supprimé'];
    if (!in_array($statut, $statut_valid)) {
        echo "Statut invalide.";
        exit;
    }

    $stmt = $bdd->prepare("UPDATE utilisateur SET statut = :statut WHERE id_utilisateur = :id_utilisateur");
    if ($stmt->execute([':statut' => $statut, ':id_utilisateur' => $id_utilisateur])) {
        echo "success";
        exit;
    } else {
        echo "error";
        exit;
    }
}
 
// Suppression d'un client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_client'])) {
    $id_utilisateur = $_POST['delete_client'];
    try {
        $stmt = $bdd->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id_utilisateur AND role = 'CLIENT'");
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);
        echo "<script>alert('Client supprimé avec succès !');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Erreur lors de la suppression : " . $e->getMessage() . "');</script>";
    }
}


// Ajout d'un client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_client'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    $stmt = $bdd->prepare("INSERT INTO utilisateur (nom, email, telephone, adresse, mot_de_passe, role) VALUES (:nom, :email, :telephone, :adresse, :mot_de_passe, 'CLIENT')");
    $stmt->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':telephone' => $telephone,
        ':adresse' => $adresse,
        ':mot_de_passe' => $mot_de_passe
    ]);
    echo "<script>alert('Client ajouté avec succès !');</script>";
}

$clients = $bdd->query("SELECT id_utilisateur, nom, email, telephone, adresse, statut FROM utilisateur WHERE role = 'CLIENT'")->fetchAll();
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
                            <h4 class="fw-bold py-3 mb-0">Clients</h4>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal">
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
                                        <?php foreach ($clients as $client) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($client['nom']) ?></td>
                                                <td><?= htmlspecialchars($client['email']) ?></td>
                                                <td><?= htmlspecialchars($client['telephone']) ?></td>
                                                <td><?= htmlspecialchars($client['adresse']) ?></td>
                                                <td>
    <?php
    // Ajoutez des classes ou des styles selon le statut
    $statusClass = '';
    $statusText = '';

    switch ($client['statut']) {
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
                                                <td>
                                   <!-- Bouton pour changer le statut -->
                                       <a href="#" 
                                          class="text-primary toggle-status me-2" 
                                          data-id="<?= $client['id_utilisateur'] ?>" 
                                          data-status="<?= $client['statut'] ?>">
                                           <i class="fas fa-sync-alt"></i>
                                       </a>
                                    <!-- Bouton pour modifier -->
                                       <a href="#"
                                          onclick="showEditModal('<?= $client['id_utilisateur'] ?>', '<?= $client['nom'] ?>', '<?= $client['email'] ?>', '<?= $client['telephone'] ?>', '<?= $client['adresse'] ?>')"
                                          class="text-warning me-2">
                                          <i class="fas fa-edit"></i>
                                       </a>
                                    <!-- Bouton pour supprimer -->
                                       <form action="client.php" method="POST" style="display:inline;">
                                           <input type="hidden" name="delete_client" value="<?= $client['id_utilisateur'] ?>">
                                           <button type="submit" class="btn btn-link text-danger"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">
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

    <!-- Modal d'ajout de client -->
    <div class="modal fade" id="addClientModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un client</h5>
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
                    <button class="btn btn-primary" type="submit" name="add_client">Ajouter</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal de modification de client -->
    <div class="modal fade" id="updateClientModal" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier un client</h5>
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
                    <button class="btn btn-primary" type="submit" name="update_client">Mettre à jour</button>
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

            fetch("client.php", {
    method: "POST",
    body: formData
})
.then(response => response.text())
.then(data => {
    if (data.trim() === "success") {
        this.setAttribute("data-status", newStatus);
        icon.classList.toggle("text-success", newStatus === "Actif");
        icon.classList.toggle("text-danger", newStatus === "Bloqué");
        icon.classList.toggle("text-muted", newStatus === "Supprimé"); // Couleur pour "Supprimé"

        // Recharge la page après la mise à jour du statut
        location.reload();
    } else {
        alert("Erreur lors de la mise à jour du statut.");
    }
})
.catch(error => console.error("Erreur:", error));

        });
    });
});
</script>
</body>

</html>
