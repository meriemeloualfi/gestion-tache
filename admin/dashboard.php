<?php
include_once __DIR__ . '/../includes/db.php'; // Connexion à la base de données

$database = new Database();
$pdo = $database->pdo;

// Récupération des données pour le dashboard
$projectCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$taskCount = $pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        

        

        .content {
            flex-grow: 1;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card h5 {
            font-size: 1.25rem;
        }

        .card-text {
            font-weight: bold;
            font-size: 2rem;
        }

        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .chart-container canvas {
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: bold;
            color: #343a40;
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
                <div class="header">
                    <h1>Dashboard Administrateur</h1>
                </div>

                <!-- Stat Cards -->
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card text-bg-primary text-center p-3">
                            <h5>Projets</h5>
                            <p class="card-text"><?php echo $projectCount; ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-bg-success text-center p-3">
                            <h5>Tâches</h5>
                            <p class="card-text"><?php echo $taskCount; ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-bg-warning text-center p-3">
                            <h5>Utilisateurs</h5>
                            <p class="card-text"><?php echo $userCount; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mt-4 g-4">
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="projectsChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="generalChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Project Chart
        const projectsChartCtx = document.getElementById('projectsChart').getContext('2d');
        const projectsChart = new Chart(projectsChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['Projets', 'Restants'],
                datasets: [{
                    label: 'Nombre de projets',
                    data: [<?php echo $projectCount; ?>, Math.max(100 - <?php echo $projectCount; ?>, 0)],
                    backgroundColor: ['#0d6efd', '#e9ecef'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                }
            }
        });

        // General Chart
        const generalChartCtx = document.getElementById('generalChart').getContext('2d');
        const generalChart = new Chart(generalChartCtx, {
            type: 'bar',
            data: {
                labels: ['Projets', 'Tâches', 'Utilisateurs'],
                datasets: [{
                    label: 'Statistiques',
                    data: [<?php echo $projectCount; ?>, <?php echo $taskCount; ?>, <?php echo $userCount; ?>],
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107'],
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
