<?php  
require('model/config/database.php');
require('model/config/util.php');
$page = "blog";

// Ajouter un article
if (isset($_POST["ajouter"])) { //Vérifie si le formulaire a été soumis en cliquant sur un bouton d'ajout d'article (avec name="ajouter").
    if (!empty($_POST["titre"]) && !empty($_POST["contenu"]) && !empty($_FILES["image"]["name"])) {
        $titre = htmlspecialchars($_POST["titre"]);
        $contenu = htmlspecialchars($_POST["contenu"]);

        // Gestion de l'upload d'image
        $image = $_FILES["image"]["name"];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            try {
                $sql = "INSERT INTO articleblog (titre, contenu, image, statut) VALUES (:titre, :contenu, :image, 1)";
                $stmt = $bdd->prepare($sql);
                $stmt->execute([
                    ':titre' => $titre,
                    ':contenu' => $contenu,
                    ':image' => $image
                ]);
                echo "<script>alert('✅ Article ajouté avec succès.'); window.location.href='blog.php';</script>";
            } catch (PDOException $e) {
                echo "<script>alert('❌ Erreur : " . $e->getMessage() . "');</script>";
            }
        } else {
            echo "<script>alert('❌ Erreur lors du téléchargement de l\'image.');</script>";
        }
    } else {
        echo "<script>alert('❌ Veuillez remplir tous les champs.');</script>";
    }
}

// Récupération des articles
$articles = $bdd->query("SELECT id_article, titre, contenu, image, statut FROM articleblog")->fetchAll();

// Modifier un article
if (isset($_POST['modifier'])) {
    $id_article = $_POST['id_article'];
    $titre = htmlspecialchars($_POST['titre']);
    $contenu = htmlspecialchars($_POST['contenu']);
    $image = $_FILES['image']['name'] ? $_FILES['image']['name'] : $_POST['image_old'];

    if ($_FILES['image']['name']) {
        // Gestion de l'upload d'image
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }

    $sql = "UPDATE articleblog SET titre = :titre, contenu = :contenu, image = :image WHERE id_article = :id_article";
    $stmt = $bdd->prepare($sql);
    $stmt->execute([
        ':titre' => $titre,
        ':contenu' => $contenu,
        ':image' => $image,
        ':id_article' => $id_article
    ]);

    echo "<script>alert('✅ Article modifié avec succès.'); window.location.href='blog.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include "include/common/head.php"; ?>
    <title><?= $page ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
                            <h4 class="fw-bold py-3 mb-0">Articles de blog</h4>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBlogModal">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>

                        <!-- Tableau des articles -->
                        <div class="card">
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Contenu</th>
                                            <th>Image</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($articles as $article) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($article['titre']) ?></td>
                                                <td><?= htmlspecialchars(substr($article['contenu'], 0, 100)) ?>...</td>
                                                <td><img src="uploads/<?= htmlspecialchars($article['image']) ?>" width="80" height="60"></td>
                                                <td>
                                                    <!-- Bouton Modifier -->
                                                    <button class="btn btn-no-style mx-2" data-bs-toggle="modal" data-bs-target="#editBlogModal<?= $article['id_article'] ?>">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>

                                                    <!-- Bouton Supprimer -->
                                                    <a href="delete_article.php?id=<?= $article['id_article'] ?>" class="text-danger mx-2" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>

                                            <!-- Modal Modifier Article -->
                                            <div class="modal fade" id="editBlogModal<?= $article['id_article'] ?>" tabindex="-1" aria-labelledby="editBlogModalLabel<?= $article['id_article'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editBlogModalLabel<?= $article['id_article'] ?>">Modifier l'article</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="blog.php" method="post" enctype="multipart/form-data">
                                                                <input type="hidden" name="id_article" value="<?= $article['id_article'] ?>">
                                                                <input type="hidden" name="image_old" value="<?= $article['image'] ?>">

                                                                <div class="mb-3">
                                                                    <label class="form-label">Titre</label>
                                                                    <input type="text" class="form-control" name="titre" value="<?= htmlspecialchars($article['titre']) ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Contenu</label>
                                                                    <textarea class="form-control" name="contenu" required><?= htmlspecialchars($article['contenu']) ?></textarea>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Image</label>
                                                                    <input type="file" class="form-control" name="image" accept="image/*">
                                                                    <img src="uploads/<?= htmlspecialchars($article['image']) ?>" width="80" height="60" class="mt-2">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                    <button class="btn btn-primary" type="submit" name="modifier">Enregistrer</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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

    <!-- Modal d'ajout d'article -->
    <div class="modal fade" id="addBlogModal" tabindex="-1" aria-labelledby="addBlogModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBlogModalLabel">Ajouter un article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="blog.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control" name="titre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contenu</label>
                            <textarea class="form-control" name="contenu" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button class="btn btn-primary" type="submit" name="ajouter">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <?php include "include/common/script.php"; ?>
</body>
</html>