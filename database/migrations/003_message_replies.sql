-- Migration 003: Message Replies Feature
-- Created: 2026-03-28
-- Adds ability to track replies sent to contact messages

USE `nexsoft_hub`;

-- Message Replies Table
CREATE TABLE IF NOT EXISTS `message_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `reply_subject` varchar(255),
  `reply_message` longtext NOT NULL,
  `sent_by` int(11),
  `read_by_sender` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`),
  FOREIGN KEY (`message_id`) REFERENCES `contact_messages`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`sent_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mark this migration as applied
INSERT IGNORE INTO `migrations` (`migration_name`, `batch`) VALUES
('003_message_replies', 1);
