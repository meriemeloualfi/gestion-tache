<?php
class Task {
    public $id;
    public $title;
    public $description;
    public $priority;
    public $status;
    public $due_date;
    public $user_id;
    public $project_id;
    public $created_at;

    public function __construct($id = null, $title = null, $description = null, $priority = 'medium', $status = 'pending', $due_date = null, $user_id = null, $project_id = null, $created_at = null) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->priority = $priority;
        $this->status = $status;
        $this->due_date = $due_date;
        $this->user_id = $user_id;
        $this->project_id = $project_id;
        $this->created_at = $created_at;
    }

    public function create($db) {
        $query = "INSERT INTO tasks (title, description, priority, status, due_date, user_id, project_id) VALUES (:title, :description, :priority, :status, :due_date, :user_id, :project_id)";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'user_id' => $this->user_id,
            'project_id' => $this->project_id
        ]);
    }

    public function update($db) {
        $query = "UPDATE tasks SET title = :title, description = :description, priority = :priority, status = :status, due_date = :due_date, user_id = :user_id, project_id = :project_id WHERE id = :id";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'user_id' => $this->user_id,
            'project_id' => $this->project_id
        ]);
    }

    public static function delete($db, $id) {
        $query = "DELETE FROM tasks WHERE id = :id";
        $stmt = $db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    // Fonction getAll pour récupérer toutes les tâches
    public static function getAll($db) {
        $query = "SELECT * FROM tasks";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        // Retourne les résultats sous forme de tableau d'objets Task
        $tasks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tasks[] = new Task(
                $row['id'],
                $row['title'],
                $row['description'],
                $row['priority'],
                $row['status'],
                $row['due_date'],
                $row['user_id'],
                $row['project_id'],
                $row['created_at']
            );
        }
        return $tasks;
    }
}
?>
