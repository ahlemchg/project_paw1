-- create_tables.sql
-- Script SQL pour créer toutes les tables nécessaires au projet
-- Exécute ce fichier dans phpMyAdmin ou via la ligne de commande MySQL

-- Table: students
-- Stocke les informations des étudiants
CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(255) NOT NULL,
  matricule VARCHAR(50) NOT NULL UNIQUE,
  group_id VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: attendance_sessions
-- Stocke les sessions de présence (cours)
CREATE TABLE IF NOT EXISTS attendance_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  group_id INT NOT NULL,
  date DATE NOT NULL,
  opened_by INT NOT NULL COMMENT 'Professor ID',
  status VARCHAR(20) NOT NULL DEFAULT 'open' COMMENT 'open or closed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour améliorer les performances
CREATE INDEX idx_attendance_sessions_date ON attendance_sessions(date);
CREATE INDEX idx_attendance_sessions_status ON attendance_sessions(status);
CREATE INDEX idx_students_group_id ON students(group_id);

