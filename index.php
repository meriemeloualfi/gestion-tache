<?php 
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: auth/login.php');
    exit();
}

$role = $_SESSION['role'];

// Rediriger vers le tableau de bord approprié si l'utilisateur visite la page index
if ($role === 'admin') {
    header('Location: admin/dashboard.php');  // Redirection vers le tableau de bord de l'administrateur
    exit();
} else {
    header('Location: user/dashboard.php');  // Redirection vers le tableau de bord de l'utilisateur
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Barre latérale -->
            <div class="col-md-3 bg-light">
                <?php include_once __DIR__ . '/includes/sidebar.php'; ?>
            </div>
            <!-- Contenu principal -->
            <div class="col-md-9">
                <!-- Ce contenu est dynamique selon le rôle, donc la redirection s'effectue au-dessus -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
