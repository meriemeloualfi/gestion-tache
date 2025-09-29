<?php 
include_once __DIR__ . '/../includes/db.php';

// ID de l'utilisateur
$user_id = 7;

// Connexion à la base de données
$db = new Database();

// Récupérer les projets de l'utilisateur
$stmt = $db->pdo->prepare("SELECT DISTINCT p.id AS project_id, p.title AS project_name
                            FROM projects p
                            INNER JOIN tasks t ON p.id = t.project_id
                            WHERE t.user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialisation des statistiques de tâches
$task_stats = ['pending' => 0, 'completed' => 0];

// Récupérer les statistiques des tâches pour chaque projet
foreach ($projects as $project) {
    $stmt = $db->pdo->prepare("SELECT t.status, COUNT(t.id) AS task_count
                                FROM tasks t
                                WHERE t.project_id = :project_id AND t.user_id = :user_id
                                GROUP BY t.status");
    $stmt->bindParam(':project_id', $project['project_id']);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $task_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($task_status as $status) {
        if ($status['status'] === 'pending') {
            $task_stats['pending'] += $status['task_count'];
        } elseif ($status['status'] === 'completed') {
            $task_stats['completed'] += $status['task_count'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Sidebar -->
            <div  ">
                <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
            </div>

            <!-- Main content -->
            <div class="col-span-3 bg-white rounded-lg shadow-md p-4">
                <h3 class="text-2xl font-semibold mb-4">Projets et Tâches</h3>

                <!-- Graphique des tâches par statut -->
                <div class="mb-6">
                    <canvas id="taskChart" width="400" height="200"></canvas>
                </div>

                <!-- Liste des projets -->
                <h4 class="text-xl font-medium mb-4">Liste des projets</h4>
                <table class="table-auto w-full text-sm text-gray-700">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Projet</th>
                            <th class="px-4 py-2 text-left">Statut des tâches</th>
                            <th class="px-4 py-2 text-left">Nombre de tâches</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2"><?= htmlspecialchars($project['project_name']) ?></td>
                                <td class="px-4 py-2">
                                    <?php
                                    $stmt = $db->pdo->prepare("SELECT t.status FROM tasks t
                                                                WHERE t.project_id = :project_id AND t.user_id = :user_id
                                                                GROUP BY t.status");
                                    $stmt->bindParam(':project_id', $project['project_id']);
                                    $stmt->bindParam(':user_id', $user_id);
                                    $stmt->execute();
                                    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    $status_list = [];
                                    foreach ($statuses as $status) {
                                        $status_list[] = $status['status'];
                                    }
                                    echo implode(', ', $status_list);
                                    ?>
                                </td>
                                <td class="px-4 py-2">
                                    <?php
                                    $stmt = $db->pdo->prepare("SELECT COUNT(t.id) AS total_tasks
                                                                FROM tasks t
                                                                WHERE t.project_id = :project_id AND t.user_id = :user_id");
                                    $stmt->bindParam(':project_id', $project['project_id']);
                                    $stmt->bindParam(':user_id', $user_id);
                                    $stmt->execute();
                                    $total_tasks = $stmt->fetch(PDO::FETCH_ASSOC);
                                    echo $total_tasks['total_tasks'];
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Graphique des tâches (statuts : pending et completed)
        var ctx = document.getElementById('taskChart').getContext('2d');
        var taskChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['En attente', 'Terminées'],
                datasets: [{
                    label: 'Nombre de tâches',
                    data: [<?= $task_stats['pending'] ?>, <?= $task_stats['completed'] ?>],
                    backgroundColor: ['#FFCE56', '#4BC0C0'],
                    borderColor: ['#FFCE56', '#4BC0C0'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
