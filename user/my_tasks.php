<?php
session_start();
include_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']; // ID de l'utilisateur connecté

// Connexion à la base de données
$db = new Database();

// Récupérer les tâches assignées à l'utilisateur
$stmt = $db->pdo->prepare("SELECT t.id, t.title, t.status, t.due_date, p.title AS project_name 
                            FROM tasks t
                            JOIN projects p ON t.project_id = p.id
                            WHERE t.user_id = 7
                            ORDER BY t.due_date ASC");
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour changer le statut de la tâche
if (isset($_POST['change_status'])) {
    $task_id = $_POST['task_id'];
    $new_status = $_POST['new_status'];
    $update_stmt = $db->pdo->prepare("UPDATE tasks SET status = :status WHERE id = :id");
    $update_stmt->execute(['status' => $new_status, 'id' => $task_id]);
    header("Location: " . $_SERVER['PHP_SELF']); // Recharger la page après mise à jour
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Couleurs professionnelles */
        .task-due-soon {
            background-color: #FF4D4D; /* Rouge clair pour alerter */
            font-weight: bold;
        }
        .task-card {
            margin-bottom: 1.5rem;
        }
        .task-card-header {
            background-color: #2D3748; /* Gris foncé avec un léger bleu */
            color: white;
            font-weight: bold;
        }
        .task-btn {
            margin-top: 10px;
        }
        .badge {
            font-size: 0.9rem;
        }

        /* Couleurs pour les badges */
        .badge-warning {
            background-color: #F59E0B; /* Jaune doux */
        }
        .badge-success {
            background-color: #16A34A; /* Vert vif */
        }
        .badge-secondary {
            background-color: #9CA3AF; /* Gris clair pour statut inconnu */
        }
        .btn-info {
            background-color: #2563EB; /* Bleu professionnel */
            border: none;
        }
        .btn-info:hover {
            background-color: #1D4ED8; /* Bleu plus foncé au survol */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
        </div>

        <!-- Main content -->
        <div class="col-md-9">
            <div class="card shadow-lg task-card">
                <div class="card-header task-card-header">
                    <h4>Mes Tâches</h4>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Titre de la tâche</th>
                                <th scope="col">Projet</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Date d'échéance</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tasks)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Aucune tâche assignée.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tasks as $task): ?>
                                    <?php 
                                    // Calculer la différence de jours avec la date d'échéance
                                    $due_date = new DateTime($task['due_date']);
                                    $now = new DateTime();
                                    $interval = $now->diff($due_date);
                                    $days_left = $interval->days;
                                    ?>
                                    <tr class="<?= ($days_left <= 2 && $task['status'] != 'completed') ? 'task-due-soon' : '' ?>">
                                        <td><?= htmlspecialchars($task['title']) ?></td>
                                        <td><?= htmlspecialchars($task['project_name']) ?></td>
                                        <td>
                                            <?php
                                            switch ($task['status']) {
                                                case 'pending':
                                                    echo '<span class="badge badge-warning">À faire</span>';
                                                    break;
                                                case 'completed':
                                                    echo '<span class="badge badge-success">Terminée</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge badge-secondary">Statut inconnu</span>';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($task['due_date']) ?></td>
                                        <td>
                                            <?php if ($task['status'] !== 'completed'): ?>
                                                <form action="" method="POST" class="d-inline">
                                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                                    <input type="hidden" name="new_status" value="<?= ($task['status'] == 'pending') ? 'completed' : 'pending' ?>">
                                                    <button type="submit" name="change_status" class="btn btn-sm btn-info task-btn">
                                                        <?= ($task['status'] == 'pending') ? 'Marquer comme terminée' : 'Revenir à faire' ?>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted">Aucune action possible</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
