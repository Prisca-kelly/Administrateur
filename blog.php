<?php
require('model/config/database.php');
require('model/config/util.php');
init_session(); // Initialiser la session
if (!is_connected()) {
    echo "<script>alert('Veuillez vous connecter avant de continuer !');</script>";
    echo '<script> window.location="index.php"</script>';
}
checkRole();
$page = "Blog";

// Récupération des articles avec la colonne statut
$sqlArticle = $bdd->query("SELECT id_article, titre, contenu, image, date_publication, statut FROM articleblog WHERE statut <>'supprimé'");
$articles = $sqlArticle->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include "include/common/head.php"; ?>
    <title><?= $page ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
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
                            <h4 class="fw-bold py-3 mb-0">Blog</h4>
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
                                            <th>Date de création</th>
                                            <th>Image</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($sqlArticle->rowCount() === 0) {
                                            echo "<tr><td colspan='5' class='text-center'>Aucun article trouvé.</td></tr>";
                                        }
                                        foreach ($articles as $article) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($article['titre']) ?></td>
                                                <td><?= htmlspecialchars($article['date_publication']) ?></td>
                                                <td><img src="uploads/<?= htmlspecialchars($article['image']) ?>" width="80"
                                                        height="60"></td>
                                                <td>
                                                    <?php if ($article['statut'] == 'publié'): ?>
                                                        <span class="text-success">Publié</span>
                                                    <?php elseif ($article['statut'] == 'non-publié'): ?>
                                                        <span class="text-warning">Non publié</span>
                                                    <?php elseif ($article['statut'] == 'supprimé'): ?>
                                                        <span class="text-danger">Supprimé</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <!-- Bouton Modifier -->
                                                    <button class="btn btn-no-style" onclick='showUpdatModal("<?= addslashes(json_encode($article)) ?>")'>
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    <?php
                                                    if ($article['statut'] === "publié") { ?>
                                                        <a href="#!" class="text-warning me-3" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Masquer" onclick="updateStatus(<?= $article['id_article'] ?>, 'non-publié')">
                                                            <i class="fas fa-eye-slash"></i>
                                                        </a>
                                                        <a href="#!" class="text-danger me-3" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Supprimer" onclick="updateStatus(<?= $article['id_article'] ?>, 'supprimé')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="#!" class="text-success me-3" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Publier" onclick="updateStatus(<?= $article['id_article'] ?>, 'publié')">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </a>
                                                        <a href="#!" class="text-danger me-3" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Supprimer" onclick="updateStatus(<?= $article['id_article'] ?>, 'supprimé')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>

                                            <!-- Modal Modifier Article -->
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
                            <input type="text" class="form-control" id="titre" name="titre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contenu</label>
                            <div id="editor"></div>
                            <textarea class="form-control d-none" id="contenu" name="contenu"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Annuler</button>
                            <button class="btn btn-primary" type="submit" id="ajouter" name="ajouter">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editBlogModal" tabindex="-1" aria-labelledby="editBlogModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBlogModalLabel"> Modifier l'article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="id_article" name="id_article">
                        <input type="hidden" id="image_old" name="image_old">
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control" id="uTitre" name="titre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contenu</label>
                            <div id="uEditor"></div>
                            <textarea class="form-control d-none" id="uContenu" name="contenu"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" id="uImage" name="image" accept="image/*">
                            <img src="uploads/" id="imgPreview" width="80" height="60" class="mt-2">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button class="btn btn-primary" type="submit" id="modifier" name="modifier">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include "include/common/script.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['link', ]
                ]
            },
            placeholder: 'Écrivez quelque chose...'
        });
        quill.on('text-change', function() {
            $('#contenu').val(quill.root.innerHTML)
        });

        var uQuill = new Quill('#uEditor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['link', ]
                ]
            },
            placeholder: 'Écrivez quelque chose...'
        });
        uQuill.on('text-change', function() {
            $('#uContenu').val(uQuill.root.innerHTML)
        });
    </script>

    <script>
        function showUpdatModal(data) {
            let res = JSON.parse(data);
            $('#uTitre').val(res.titre);
            $('#uContenu').val(res.contenu);
            $('#image_old').val(res.image);
            $('#id_article').val(res.id_article)
            $('#imgPreview').prop("src", "uploads/" + res.image)
            uQuill.clipboard.dangerouslyPasteHTML(res.contenu);
            var editBlogModal = new bootstrap.Modal(document.getElementById('editBlogModal'));
            editBlogModal.show();
        }
    </script>

    <script>
        $('#modifier').click((e) => {
            e.preventDefault();
            let formData = new FormData();
            formData.append('titre', $('#uTitre').val());
            formData.append('contenu', $('#uContenu').val());
            formData.append('imageName', $('#image_old').val());
            formData.append('id_article', $('#id_article').val());
            formData.append('image', $('#uImage')[0].files[0]);
            formData.append('modifier', 'modifier');
            $.ajax({
                type: "post",
                url: "model/app/blog.php",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.code === 200) {
                        successSweetAlert(res.message);
                    } else if (res.code === 400 || res.code === 500) {
                        errorSweetAlert(res.message);
                    }
                }
            });
        })
        $('#ajouter').click((e) => {
            e.preventDefault();
            let formData = new FormData();
            formData.append('titre', $('#titre').val());
            formData.append('contenu', $('#contenu').val());
            formData.append('image', $('#image')[0].files[0]);
            formData.append('ajouter', 'ajouter');
            $.ajax({
                type: "post",
                url: "model/app/blog.php",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.code === 200) {
                        successSweetAlert(res.message);
                    } else if (res.code === 400 || res.code === 500) {
                        errorSweetAlert(res.message);
                    }
                }
            });
        })


        function updateStatus(id_article, statut) {
            let data = {
                id_article: id_article,
                statut: statut,
                changer_statut: "changer_statut",
            };
            let verbe = null;

            if (statut == "publié") {
                verbe = "publier";
            } else if (statut == "non-publié") {
                verbe = "masquer";
            } else {
                verbe = "supprimer";
            }

            confirmSweetAlert("Voulez-vous vraiment " + verbe + " ce article de blog ?").then((out) => {
                if (out.isConfirmed) {
                    ajaxRequest("post", "model/app/blog.php", data);
                }
            })
        }
    </script>
</body>

</html>