<?php
include_once __DIR__ . '/../includes/db.php';  
require_once '../classes/Task.php'; 
require_once '../classes/User.php'; 
require_once '../classes/Project.php'; 

$database = new Database();
$pdo = $database->pdo;

// Récupérer toutes les tâches
$tasks = Task::getAll($pdo);

// Récupérer les utilisateurs et les projets
$users = User::getAll($pdo);  // Assurez-vous d'avoir la classe User avec une méthode getAll()
$projects = Project::getAll($pdo);  // Assurez-vous d'avoir la classe Project avec une méthode getAll()

if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    $user_id = $_POST['user_id'];
    $project_id = $_POST['project_id'];

    $task = new Task(null, $title, $description, $priority, $status, $due_date, $user_id, $project_id);
    $taskId = $task->create($pdo);

    if ($taskId) {
        $message = "Tâche créée avec succès.  " ;
        $tasks = Task::getAll($pdo);  // Recharger les tâches après la création
    } else {
        $message = $taskId;  // Afficher l'erreur retournée par create()
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    $user_id = $_POST['user_id'];
    $project_id = $_POST['project_id'];

    $task = new Task($id, $title, $description, $priority, $status, $due_date, $user_id, $project_id);
    $updateResult = $task->update($pdo);

    $message = $updateResult === true ? "Tâche mise à jour avec succès." : $updateResult;

    if ($updateResult === true) {
        $tasks = Task::getAll($pdo);  // Recharger les tâches après la mise à jour
    }
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $deleteResult = Task::delete($pdo, $id);

    $message = $deleteResult === true ? "Tâche supprimée avec succès." : $deleteResult;

    if ($deleteResult === true) {
        $tasks = Task::getAll($pdo);  // Recharger les tâches après la suppression
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .wrapper {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 30px;
        }
        .sidebar a {
            color: white;
            padding: 15px;
            text-decoration: none;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            flex-grow: 1;
            padding: 30px;
        }
        .card-text {
            font-weight: bold;
        }
        .alert-info {
            margin-bottom: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
        <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container">
            <h1 class="mb-4">Gestion des Tâches</h1>

            <!-- Message de succès ou d'erreur -->
            <?php if (isset($message)) : ?>
                <div class="alert alert-info">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire pour créer une tâche -->
            <form method="POST" class="mb-4">
                <h3>Créer une tâche</h3>
                <div class="mb-3">
                    <label for="title" class="form-label">Titre</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="priority" class="form-label">Priorité</label>
                    <select name="priority" id="priority" class="form-select">
                        <option value="low">Basse</option>
                        <option value="medium">Moyenne</option>
                        <option value="high">Haute</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Statut</label>
                    <select name="status" id="status" class="form-select">
                        <option value="pending">En attente</option>
                        <option value="completed">Terminé</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="due_date" class="form-label">Date d'échéance</label>
                    <input type="date" name="due_date" id="due_date" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="user_id" class="form-label">Utilisateur</label>
                    <select name="user_id" id="user_id" class="form-select" required>
                        <option value="">Sélectionner un utilisateur</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="project_id" class="form-label">Projet</label>
                    <select name="project_id" id="project_id" class="form-select" required>
                        <option value="">Sélectionner un projet</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="create" class="btn btn-primary">Créer</button>
            </form>

            <!-- Table des tâches existantes -->
            <h3>Liste des tâches</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Priorité</th>
                        <th>Statut</th>
                        <th>Date d'échéance</th>
                        <th>Projet</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1; // Compteur pour remplacer l'ID
                    foreach ($tasks as $task) : ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo $task->title; ?></td>
                            <td><?php echo $task->description; ?></td>
                            <td><?php echo ucfirst($task->priority); ?></td>
                            <td><?php echo ucfirst($task->status); ?></td>
                            <td><?php echo $task->due_date; ?></td>
                            <td>
                                <?php
                                // Trouver le projet correspondant à la tâche
                                $project = array_filter($projects, fn($p) => $p['id'] == $task->project_id);
                                $project = reset($project);
                                echo $project ? $project['title'] : 'Non défini';
                                ?>
                            </td>
                            <td>
    <a href="#" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editTaskModal" 
       data-task-id="<?php echo $task->id; ?>"
       data-task-title="<?php echo $task->title; ?>"
       data-task-description="<?php echo $task->description; ?>"
       data-task-priority="<?php echo $task->priority; ?>"
       data-task-status="<?php echo $task->status; ?>"
       data-task-due_date="<?php echo $task->due_date; ?>"
       data-task-user_id="<?php echo $task->user_id; ?>"
       data-task-project_id="<?php echo $task->project_id; ?>">Modifier</a>
    <form method="POST" action="" style="display:inline;">
        <input type="hidden" name="id" value="<?php echo $task->id; ?>">
        <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
    </form>
</td>


                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal pour modifier une tâche -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Modifier la tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulaire de modification de tâche -->
                <form method="POST">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-title" class="form-label">Titre</label>
                        <input type="text" name="title" id="edit-title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-description" class="form-label">Description</label>
                        <textarea name="description" id="edit-description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-priority" class="form-label">Priorité</label>
                        <select name="priority" id="edit-priority" class="form-select">
                            <option value="low">Basse</option>
                            <option value="medium">Moyenne</option>
                            <option value="high">Haute</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Statut</label>
                        <select name="status" id="edit-status" class="form-select">
                            <option value="pending">En attente</option>
                            <option value="completed">Terminé</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-due_date" class="form-label">Date d'échéance</label>
                        <input type="date" name="due_date" id="edit-due_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit-user_id" class="form-label">Utilisateur</label>
                        <select name="user_id" id="edit-user_id" class="form-select" required>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-project_id" class="form-label">Projet</label>
                        <select name="project_id" id="edit-project_id" class="form-select" required>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Remplir le modal avec les informations de la tâche à modifier
    const taskModal = document.getElementById('editTaskModal');
    taskModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        document.getElementById('edit-id').value = button.getAttribute('data-task-id');
        document.getElementById('edit-title').value = button.getAttribute('data-task-title');
        document.getElementById('edit-description').value = button.getAttribute('data-task-description');
        document.getElementById('edit-priority').value = button.getAttribute('data-task-priority');
        document.getElementById('edit-status').value = button.getAttribute('data-task-status');
        document.getElementById('edit-due_date').value = button.getAttribute('data-task-due_date');
        document.getElementById('edit-user_id').value = button.getAttribute('data-task-user_id');
        document.getElementById('edit-project_id').value = button.getAttribute('data-task-project_id');
    });
</script>

</body>
</html>
