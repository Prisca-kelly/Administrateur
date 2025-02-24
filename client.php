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

// Changement de statut avec validation des statuts possibles
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    $statut = $_POST['statut']; // Récupère le statut du formulaire
    $id_utilisateur = $_POST['id_utilisateur'];
    $statuts_valides = ['Actif', 'Bloqué']; // Liste des statuts valides
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

    // Mettre à jour le statut à "Supprimé" au lieu de supprimer l'utilisateur
    $stmt = $bdd->prepare("UPDATE utilisateur SET statut = 'Supprimé' WHERE id_utilisateur = :id_utilisateur");
    $stmt->execute([':id_utilisateur' => $id_utilisateur]);

    echo "success";
    exit;
}


$sqlClients = $bdd->query("SELECT id_utilisateur, nom, email, telephone, adresse, statut FROM utilisateur WHERE role = 'CLIENT'");
$clients = $sqlClients->fetchAll();
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
                                        <?php
                                        if ($sqlClients->rowCount() === 0) {
                                            echo "<tr><td colspan='6' class='text-center'>Aucun client trouvé.</td></tr>";
                                        }
                                        foreach ($clients as $client) : ?>
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
                                                    <!-- Bouton pour changer le statut -->
                                                    <a href="#" class="text-primary toggle-status me-2"
                                                        data-id="<?= $client['id_utilisateur'] ?>"
                                                        data-status="<?= $client['statut'] ?>">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </a>
                                                    <!-- Bouton pour modifier -->
                                                    <a href="#"
                                                       onclick="showEditModal('<?= $client['id_utilisateur'] ?>', '<?= $client['nom'] ?>', '<?= $client['email'] ?>', '<?= $client['telephone'] ?>', '<?= $client['adresse'] ?>')"
                                                       class="text-warning me-2">
                                                        <i class="fas fa-edit text-primary" data-bs-toggle="modal" data-bs-target="#updateClientModal"></i>
                                                    </a>
                                                    
                                                    <!-- Bouton pour supprimer -->
                                                    <a href="#" onclick="markAsDeleted('<?= $client['id_utilisateur'] ?>')" class="text-danger">
                                                    <i class="fas fa-trash-alt"></i>
                                                    </a>
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
    // Déjà présent : Gestion des utilisateurs supprimés
    function markAsDeleted(idUtilisateur) {
        if (confirm("Êtes-vous sûr de vouloir marquer cet utilisateur comme supprimé ?")) {
            let formData = new FormData();
            formData.append("delete_user", idUtilisateur);

            fetch("utilisateur.php", {
                method: "POST",
                body: formData,
            }).then(response => response.text()).then(data => {
                if (data === "success") {
                    location.reload(); // Recharger la page pour voir les changements
                } else {
                    alert("Erreur lors de la suppression.");
                }
            });
        }
    }

    // Déjà présent : Préremplir le modal de modification
    function showEditModal(id, nom, email, telephone, adresse) {
        document.getElementById("id_utilisateur").value = id;
        document.getElementById("uNom").value = nom;
        document.getElementById("uEmail").value = email;
        document.getElementById("uTelephone").value = telephone;
        document.getElementById("uAdresse").value = adresse;
    }

    // Nouveau : Gestion du changement de statut
    document.querySelectorAll('.toggle-status').forEach(function (element) {
        element.addEventListener('click', function (e) {
            e.preventDefault();
            
            var idUtilisateur = element.getAttribute('data-id');
            var statutActuel = element.getAttribute('data-status');
            var nouveauStatut = (statutActuel === 'Actif') ? 'Bloqué' : 'Actif'; // Alterne entre 'Actif' et 'Bloqué'
            
            let formData = new FormData();
            formData.append("update_status", true);
            formData.append("id_utilisateur", idUtilisateur);
            formData.append("statut", nouveauStatut);
            
            fetch('utilisateur.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    // Met à jour l'affichage du statut sur la page sans recharger
                    element.closest('tr').querySelector('td:nth-child(5) span').textContent = nouveauStatut;
                    element.closest('tr').querySelector('td:nth-child(5) span').className = (nouveauStatut === 'Actif') ? 'text-success' : 'text-danger';
                    
                    // Met à jour l'attribut data-status de l'élément cliqué
                    element.setAttribute('data-status', nouveauStatut);
                } else {
                    alert("Erreur lors de la mise à jour du statut.");
                }
            })
            .catch(error => console.error('Erreur : ', error));
        });
    });
</script>
</body>

</html>
