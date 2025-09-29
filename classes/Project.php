<?php
class Project {
    public $id;
    public $title;
    public $description;
    public $created_at;

    public function __construct($id = null, $title = null, $description = null, $created_at = null) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->created_at = $created_at;
    }

    public function create($pdo) {
        $stmt = $pdo->prepare("INSERT INTO projects (title, description) VALUES (:title, :description)");
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
    
        if ($stmt->execute()) {
            // Retourne l'ID du projet créé
            return $pdo->lastInsertId();
        } else {
            // Si une erreur se produit, retourne un message d'erreur
            return "Erreur lors de la création du projet";
        }
    }
    

    public function update($db) {
        $query = "UPDATE projects SET title = :title, description = :description WHERE id = :id";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description
        ]);
    }

    public static function delete($db, $id) {
        $query = "DELETE FROM projects WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute(['id' => $id]);
        
        // Vérifiez si une ligne a été affectée
        if ($stmt->rowCount() > 0) {
            return true; // Succès
        } else {
            return "Aucun projet trouvé avec cet ID."; // Erreur si aucune ligne n'a été supprimée
        }
    }
    
    // Méthode pour récupérer tous les projets
    public static function getAll($db) {
        $query = "SELECT * FROM projects";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>