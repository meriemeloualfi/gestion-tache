<?php
session_start();
include_once __DIR__ . '/../includes/db.php';

// Vérification de la session
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']; // ID de l'utilisateur connecté

// Connexion à la base de données
$db = new Database();

// Récupérer les informations de l'utilisateur
$stmt = $db->pdo->prepare("SELECT username, role, created_at FROM users WHERE username = :username LIMIT 1");
$username = $_SESSION['user'];
$stmt->bindParam(':username', $username);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f5f7;
            font-family: 'Arial', sans-serif;
        }

        .profile-header {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: white;
            border-radius: 20px;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        }

        .profile-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            margin-bottom: 15px;
        }

        .card {
            border: none;
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }

        .card .icon {
            font-size: 1.7rem;
            color: #4f46e5;
            margin-right: 15px;
        }

        .card-title {
            font-weight: bold;
            color: #4f46e5;
        }

        
        

        .card-text {
            color: #444;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="sidebar">
                <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9">
            <!-- Profile Header -->
            <div class="profile-header">
                <img src="https://via.placeholder.com/120" alt="Avatar de l'utilisateur">
                <h2><?= htmlspecialchars($user['username']) ?></h2>
                <p class="mb-1"><?= htmlspecialchars($user['role']) ?></p>
                <small class="fst-italic">Membre depuis <?= date("d-m-Y", strtotime($user['created_at'])) ?></small>
            </div>

            <!-- Profile Details -->
            <div class="card p-4">
                <div class="row align-items-center mb-4">
                    <div class="col-auto">
                        <i class="icon bi bi-person-circle"></i>
                    </div>
                    <div class="col">
                        <h5 class="card-title mb-1">Nom d'utilisateur</h5>
                        <p class="card-text"><?= htmlspecialchars($user['username']) ?></p>
                    </div>
                </div>
                <hr>
                <div class="row align-items-center mb-4">
                    <div class="col-auto">
                        <i class="icon bi bi-shield-lock-fill"></i>
                    </div>
                    <div class="col">
                        <h5 class="card-title mb-1">Rôle</h5>
                        <p class="card-text"><?= htmlspecialchars($user['role']) ?></p>
                    </div>
                </div>
                <hr>
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="icon bi bi-calendar-check-fill"></i>
                    </div>
                    <div class="col">
                        <h5 class="card-title mb-1">Date de création</h5>
                        <p class="card-text"><?= date("d-m-Y", strtotime($user['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
