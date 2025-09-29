<?php
class Database {
    private $host = 'localhost';      // Hôte de la base de données
    private $dbname = 'gestion_taches';  // Nom de la base de données
    private $username = 'root'; // Nom d'utilisateur pour la connexion à la base de données
    private $password = '';  // Mot de passe pour la connexion à la base de données
    public $pdo;

    public function __construct() {
        try {
            // Connexion à la base de données avec PDO
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            // Configuration du mode d'erreur pour afficher les exceptions
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Si la connexion échoue, afficher l'erreur
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
    public function getPDO() {
        return $this->pdo;
    }
}
?>
