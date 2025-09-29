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
$stmt = $db->pdo->prepare("SELECT username, role, created_at, password FROM users WHERE username = :username LIMIT 1");
$username = $_SESSION['user'];
$stmt->bindParam(':username', $username);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit();
}

// Traitement du changement de mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérification de l'ancien mot de passe
    if (!password_verify($old_password, $user['password'])) {
        $error_message = "L'ancien mot de passe est incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($new_password) < 6) {
        $error_message = "Le nouveau mot de passe doit comporter au moins 6 caractères.";
    } else {
        // Mise à jour du mot de passe
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $db->pdo->prepare("UPDATE users SET password = :password WHERE username = :username");
        $update_stmt->bindParam(':password', $hashed_password);
        $update_stmt->bindParam(':username', $username);
        $update_stmt->execute();

        $success_message = "Mot de passe mis à jour avec succès.";
    }
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

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

            <!-- Change Password Form -->
            <div class="card p-4 mt-4">
                <h5 class="card-title mb-3">Changer le mot de passe</h5>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php elseif (isset($success_message)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="old_password">Ancien mot de passe</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Changer le mot de passe</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
