<?php
include_once __DIR__ . '/../includes/db.php';  
require_once '../classes/Project.php';  

$database = new Database();
$pdo = $database->pdo;

$projects = Project::getAll($pdo);

if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $project = new Project(null, $title, $description);
    $projectId = $project->create($pdo);

    // Vérifiez si la création a réussi et si un ID a été renvoyé
    if (is_numeric($projectId)) {
        $message = "Projet créé avec succès.";
        $messageType = "alert-success";  // Type de message : succès
    } else {
        $message = $projectId;  // Le message d'erreur est retourné par create()
        $messageType = "alert-danger";  // Type de message : erreur
    }

    // Recharger la liste des projets
    $projects = Project::getAll($pdo);
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $project = new Project($id, $title, $description);
    $updateResult = $project->update($pdo);
    
    // Vérifier si la mise à jour a réussi et recharger la liste des projets
    if ($updateResult === true) {
        $message = "Projet mis à jour avec succès.";
        $messageType = "alert-info";  // Type de message : information
    } else {
        $message = $updateResult;
        $messageType = "alert-danger";  // Type de message : erreur
    }
    
    // Recharger les projets après la mise à jour
    if ($updateResult === true) {
        $projects = Project::getAll($pdo);
    }
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $deleteResult = Project::delete($pdo, $id);
    
    // Vérifier si la suppression a réussi et recharger la liste des projets
    if ($deleteResult === true) {
        $message = "Projet supprimé avec succès.";
        $messageType = "alert-warning";  // Type de message : avertissement
    } else {
        $message = $deleteResult;
        $messageType = "alert-danger";  // Type de message : erreur
    }
    
    // Recharger les projets après la suppression
    if ($deleteResult === true) {
        $projects = Project::getAll($pdo);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Projets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style général pour le wrapper */
        body, html {
            margin: 0;
            padding: 0;
        }
        .wrapper {
            display: flex;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="wrapper">
        <!-- Sidebar -->
        <div>
            <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <div class="container">
            <h1 class="mb-4">Gestion des Projets</h1>

            <!-- Message de succès ou d'erreur avec des couleurs différenciées -->
            <?php if (isset($message)) : ?>
                <div class="alert <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire pour créer un projet -->
            <form method="POST" class="mb-4">
                <h3>Créer un projet</h3>
                <div class="mb-3">
                    <label for="title" class="form-label">Titre</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" name="create" class="btn btn-primary">Créer</button>
            </form>

            <!-- Table des projets existants -->
            <h3>Liste des projets</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; ?>
                    <?php foreach ($projects as $project) : ?>
                        <tr>
                            <td><?php echo $counter++; ?></td> <!-- Compteur à la place de l'ID -->
                            <td><?php echo $project['title']; ?></td>
                            <td><?php echo $project['description']; ?></td>
                            <td>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $project['id']; ?>">Modifier</button>
                                <form method="POST" class="d-inline-block">
                                    <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">Supprimer</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal de modification -->
                        <div class="modal fade" id="editModal<?php echo $project['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Modifier le projet</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Titre</label>
                                                <input type="text" name="title" id="title" class="form-control" value="<?php echo $project['title']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea name="description" id="description" class="form-control" rows="4" required><?php echo $project['description']; ?></textarea>
                                            </div>
                                            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                                            <button type="submit" name="update" class="btn btn-primary">Mettre à jour</button>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
