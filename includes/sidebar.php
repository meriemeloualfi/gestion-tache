<?php
require_once __DIR__ . '/../config.php'; // Adaptez le chemin si nécessaire

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: auth/login.php');
    exit();
}

$role = isset($_SESSION['role']) ? $_SESSION['role'] : ''; // Assurez-vous que $role est défini
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Stylée</title>
    <!-- Inclusion de FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Style de la sidebar avec un dégradé et des icônes */
        .sidebar {
            width: 250px; /* Largeur de la sidebar */
            background: linear-gradient(135deg, #4b79a1, #283e51); /* Dégradé élégant */
            color: white;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            height: 100vh; /* Prend toute la hauteur */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Ombre subtile */
        }
        .content {
    margin-left: 250px; /* Décalage du contenu pour qu'il ne soit pas masqué par la sidebar */
    padding: 30px;
    background-color: #f8f9fa;
    min-height: 100vh;
    transition: margin-left 0.3s ease; /* Transition pour les écrans plus petits */
}

/* Utiliser flexbox pour le layout principal */
.wrapper {
    display: flex;
    min-height: 100vh;
}
        /* Style pour chaque lien de la sidebar */
        .sidebar a {
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 16px;
            transition: all 0.3s ease;
            border-radius: 8px; /* Coins arrondis */
            margin: 8px 10px; /* Espacement entre les liens */
        }

        /* Effet au survol des liens */
        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2); /* Fond semi-transparent */
            color: #fff;
            transform: translateX(5px); /* Déplacement vers la droite */
        }

        /* Icônes dans les liens */
        .sidebar a i {
            margin-right: 10px; /* Espacement entre l'icône et le texte */
            font-size: 18px;
        }

        /* Bouton de déconnexion */
        .sidebar .logout-btn {
            margin-top: auto; /* Placer en bas */
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            text-align: center;
            cursor: pointer;
            font-size: 16px;
            border-radius: 8px; /* Coins arrondis */
            margin: 8px 10px; /* Espacement avec les autres éléments */
            transition: all 0.3s ease;
        }

        .sidebar .logout-btn:hover {
            background-color: #b52a34;
            transform: scale(1.05); /* Légère mise en valeur */
        }

        /* Titre du tableau de bord */
        .sidebar h3 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 20px;
            color: #f1f1f1;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3); /* Ombre au texte */
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .sidebar a {
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Tableau de bord</h3>

        <?php if ($role === 'admin'): ?>
            <a href="<?php echo BASE_URL; ?>admin/dashboard.php">
                <i class="fas fa-home"></i> Accueil
            </a>
            <a href="<?php echo BASE_URL; ?>admin/profil.php">
                <i class="fas fa-user"></i> Mon Profil
            </a>
            <a href="<?php echo BASE_URL; ?>admin/manage_users.php">
                <i class="fas fa-users"></i> Gérer les utilisateurs
            </a>
            <a href="<?php echo BASE_URL; ?>admin/manage_projects.php">
                <i class="fas fa-briefcase"></i> Gérer les projets
            </a>
            <a href="<?php echo BASE_URL; ?>admin/manage_tasks.php">
                <i class="fas fa-tasks"></i> Gérer les tâches
            </a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>user/dashboard.php">
                <i class="fas fa-home"></i> Accueil
            </a>
            <a href="<?php echo BASE_URL; ?>user/profile.php">
                <i class="fas fa-user"></i> Mon Profil
            </a>
            <a href="<?php echo BASE_URL; ?>user/my_tasks.php">
                <i class="fas fa-tasks"></i> Mes tâches
            </a>
            <!-- Lien vers les notifications avec badge pour les notifications non lues -->
            <a href="<?php echo BASE_URL; ?>user/notifications.php">
                <i class="fas fa-bell"></i> Notifications
    
            </a>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>auth/logout.php" method="POST">
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Se déconnecter
            </button>
        </form>
    </div>
</body>
</html>

