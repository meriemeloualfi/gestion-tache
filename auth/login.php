<?php
session_start(); // Démarrer la session

// Inclure la connexion à la base de données
include_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hachage du mot de passe avec MD5

    // Connexion à la base de données
    $db = new Database();
    $stmt = $db->pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($password === $user['password']) {
            // Stocker les données utilisateur dans la session
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Vérifier si l'utilisateur est un admin
            if ($user['role'] === 'admin') {
                // Si l'utilisateur est un admin, rediriger vers index.php
                header('Location: ../index.php');
                exit();
            } else {
                // Si l'utilisateur est un simple user, afficher le message d'erreur
                header('Location: ../index.php');
            }
        } else {
            $error = "Mot de passe ou nom d'utilisateur incorrect.";
        }
    } else {
        $error = "Mot de passe ou nom d'utilisateur incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
            color: #fff;
        }
        .card {
            background: #fff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            color: #333;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            background: #4e54c8;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #8f94fb;
            transform: scale(1.05);
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h2 class="text-center mb-4">Bienvenue !</h2>
                <p class="text-center text-muted">Connectez-vous pour accéder à votre tableau de bord.</p>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php endif; ?>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Votre nom d'utilisateur" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Votre mot de passe" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">Se connecter</button>
                </form>
                <div class="text-center mt-3">
                    <p class="text-muted">Vous n'avez pas de compte ? <a href="register.php" class="text-primary">S'inscrire</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
