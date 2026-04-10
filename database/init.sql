-- Script d'initialisation de la base de données Kenam Services

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS kenamservices CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Création de l'utilisateur (à adapter avec les bons identifiants)
CREATE USER IF NOT EXISTS 'kenam_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_securise';
GRANT ALL PRIVILEGES ON kenamservices.* TO 'kenam_user'@'localhost';
FLUSH PRIVILEGES;

-- Utilisation de la base de données
USE kenamservices;

-- Vérification des privilèges
SHOW GRANTS FOR 'kenam_user'@'localhost';
