-- Migration 001: Initial Schema Setup
-- Created: 2026-03-01
-- This is the base schema

USE `nexsoft_hub`;

-- Migration tracking table (stores which migrations have been applied)
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(255) NOT NULL UNIQUE,
  `batch` int(11) NOT NULL,
  `executed_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `batch` (`batch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mark this migration as applied
INSERT IGNORE INTO `migrations` (`migration_name`, `batch`) VALUES
('001_initial_schema', 1);
