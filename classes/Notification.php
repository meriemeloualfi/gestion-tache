<?php

// Notification.php
class Notification {
    private $id;
    private $message;
    private $user_id;
    private $is_read;
    private $created_at;

    public function __construct($id = null, $message = '', $user_id = '', $is_read = false, $created_at = '') {
        $this->id = $id;
        $this->message = $message;
        $this->user_id = $user_id;
        $this->is_read = $is_read;
        $this->created_at = $created_at;
    }

    // Méthode pour créer une notification
    public function create($db) {
        $stmt = $db->prepare("INSERT INTO notifications (message, user_id, is_read, created_at) VALUES (:message, :user_id, :is_read, :created_at)");
        $stmt->execute([
            'message' => $this->message,
            'user_id' => $this->user_id,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at
        ]);
    }

    // Méthode pour vérifier les tâches et créer des notifications
    public static function checkAndNotifyTasks($db, $user_id) {
        // Récupérer l'objet PDO via la méthode getPDO() de la classe Database
        $pdo = $db->getPDO();

        // Récupérer les tâches de l'utilisateur
        $stmt = $pdo->prepare("SELECT id, title, due_date, status FROM tasks WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculer la différence de jours entre la date actuelle et la date d'échéance
        $now = new DateTime();

        foreach ($tasks as $task) {
            $due_date = new DateTime($task['due_date']);
            $interval = $now->diff($due_date);
            $days_left = $interval->days;

            // Si la tâche n'est pas terminée et que son échéance est dans les 2 prochains jours
            if ($task['status'] != 'completed' && $days_left <= 2 && $due_date >= $now) {
                $message = "La tâche '{$task['title']}' a une échéance proche ({$task['due_date']}).";
                $notification = new Notification(null, $message, $user_id, false, $now->format('Y-m-d H:i:s'));
                $notification->create($pdo);  // Créer la notification dans la base de données
            }
        }
    }
}

?>