<?php
// Démarrer la session pour gérer l'authentification et les rôles
session_start();

class Session {
    // Enregistrer l'utilisateur dans la session
    public static function setUser($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
    }

    // Vérifier si l'utilisateur est connecté
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Récupérer le rôle de l'utilisateur
    public static function getRole() {
        return $_SESSION['role'] ?? null;
    }

    // Déconnecter l'utilisateur
    public static function logout() {
        session_destroy();
        header('Location: login.php');
        exit();
    }
}
?>
