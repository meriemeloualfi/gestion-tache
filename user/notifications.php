<?php
session_start();

// Inclure les fichiers nécessaires
include_once __DIR__ . '/../includes/db.php'; // Assure-toi du bon chemin du fichier Database.php
include_once __DIR__ . '/../classes/Notification.php'; // Inclure la classe Notification

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Récupère l'ID de l'utilisateur connecté
$user_id = $_SESSION['user']; 

// Crée une instance de la classe Database
$db = new Database();

// Vérifier et créer des notifications pour les tâches de l'utilisateur
Notification::checkAndNotifyTasks($db, $user_id);

// Récupère la connexion PDO directement depuis l'attribut $pdo
$conn = $db->pdo;

// Préparer la requête pour récupérer les notifications non lues
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = :user_id AND is_read = 0 ORDER BY created_at DESC");
$stmt->execute(['user_id' => $user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Marquer les notifications comme lues
if (isset($_GET['mark_read'])) {
    $notification_id = $_GET['mark_read'];
    $notification = new Notification($notification_id);
    $notification->is_read = true;
    $notification->update($db); // Mettre à jour la notification comme lue
    header('Location: notifications.php'); // Recharger la page après mise à jour
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4>Mes Notifications</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($notifications)): ?>
                        <p>Aucune notification pour le moment.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($notifications as $notification): ?>
                                <li class="list-group-item <?= $notification['is_read'] ? '' : 'list-group-item-warning' ?>">
                                    <?= htmlspecialchars($notification['message']) ?>
                                    <div class="text-end mt-2">
                                        <a href="notifications.php?mark_read=<?= $notification['id'] ?>" class="btn btn-sm btn-info">Marquer comme lu</a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
