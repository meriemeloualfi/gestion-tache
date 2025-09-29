-- Création de la base de données
CREATE DATABASE IF NOT EXISTS gestion_taches;
USE gestion_taches;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des projets
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des tâches
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    priority ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
    status ENUM('pending', 'completed') NOT NULL DEFAULT 'pending',
    due_date DATE,
    user_id INT,
    project_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Vérifier les tâches affectées à un utilisateur
SELECT * FROM tasks WHERE user_id IS NOT NULL;

-- Table des notifications (optionnelle, si vous souhaitez suivre les rappels)
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    user_id INT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertion des utilisateurs par défaut
INSERT INTO users (username, password, role) VALUES
('admin', MD5('admin123'), 'admin'),   -- Utilisateur admin
('user1', MD5('user123'), 'user'),     -- Utilisateur standard
('user2', MD5('user456'), 'user');     -- Autre utilisateur standard

-- Insertion des projets par défaut
INSERT INTO projects (title, description) VALUES
('Gestion des tâches', 'Projet pour gérer les tâches quotidiennes.'),
('Gestion des utilisateurs', 'Projet pour gérer les utilisateurs.');

-- Insertion des tâches par défaut
INSERT INTO tasks (title, description, priority, status, due_date, user_id, project_id) VALUES
('Créer la base de données', 'Concevoir les tables et relations.', 'high', 'pending', '2024-12-15', 1, 1),
('Créer l\'interface utilisateur', 'Mettre en place les pages HTML.', 'medium', 'completed', '2024-12-12', 2, 1),
('Test du système', 'Effectuer les tests pour valider le fonctionnement.', 'low', 'pending', '2024-12-18', 3, 1),
('Ajouter un utilisateur', 'Créer un formulaire pour ajouter un utilisateur.', 'medium', 'completed', '2024-12-10', 2, 2),
('Gérer les rôles', 'Créer la gestion des rôles admin et user.', 'high', 'pending', '2024-12-16', 1, 2);

-- Insertion des notifications par défaut
INSERT INTO notifications (message, user_id, is_read) VALUES
('Tâche en retard : Créer la base de données', 1, FALSE),
('Tâche en retard : Test du système', 3, FALSE);
