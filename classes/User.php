<?php
class User {
    public $id;
    public $username;
    public $password;
    public $role;
    public $created_at;

    public function __construct($id = null, $username = null, $password = null, $role = 'user', $created_at = null) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->created_at = $created_at;
    }

    public function create($db) {
        $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            'username' => $this->username,
            'password' => md5($this->password),
            'role' => $this->role
        ]);
    }

    public function update($db) {
        $query = "UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            'id' => $this->id,
            'username' => $this->username,
            'password' => md5($this->password),
            'role' => $this->role
        ]);
    }

    public static function delete($db, $id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    // Ajout de la méthode getById pour récupérer un utilisateur par ID
    public static function getById($db, $id) {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne un utilisateur ou null
    }

    // Ajout de la méthode getAll pour récupérer tous les utilisateurs
    public static function getAll($db) {
        $query = "SELECT * FROM users";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Retourne tous les utilisateurs sous forme de tableau associatif
    }
}
?>
