-- Migration 002: Design Templates Feature
-- Created: 2026-03-15
-- Adds certificate and letter design system

USE `nexsoft_hub`;

-- Design Templates Table
CREATE TABLE IF NOT EXISTS `design_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'certificate',
  `description` text,
  `logo_image` varchar(255),
  `logo_position` varchar(50) DEFAULT 'top-center',
  `logo_width` int(11) DEFAULT 200,
  `header_html` longtext,
  `body_html` longtext NOT NULL,
  `footer_html` longtext,
  `is_active` tinyint(1) DEFAULT 1,
  `is_default` tinyint(1) DEFAULT 0,
  `created_by` int(11),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `is_active` (`is_active`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Template Logos Table
CREATE TABLE IF NOT EXISTS `template_logos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11),
  `width` int(11) DEFAULT 300,
  `height` int(11),
  `uploaded_by` int(11),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`),
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Text Styles Table
CREATE TABLE IF NOT EXISTS `text_styles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `font_family` varchar(100),
  `font_size` varchar(50),
  `font_weight` varchar(50),
  `color` varchar(7),
  `text_align` varchar(50),
  `line_height` varchar(50),
  `letter_spacing` varchar(50),
  `is_preset` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_preset` (`is_preset`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Design Elements Table
CREATE TABLE IF NOT EXISTS `design_elements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `element_type` varchar(100),
  `position` varchar(50),
  `size` varchar(50),
  `style_id` int(11),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`template_id`) REFERENCES `design_templates`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`style_id`) REFERENCES `text_styles`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Issued Documents Table
CREATE TABLE IF NOT EXISTS `issued_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` varchar(50) UNIQUE NOT NULL,
  `template_id` int(11) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `recipient_email` varchar(255),
  `issue_date` date NOT NULL,
  `body_content` longtext,
  `status` varchar(50) DEFAULT 'active',
  `created_by` int(11),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `document_id` (`document_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`template_id`) REFERENCES `design_templates`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default text styles
INSERT IGNORE INTO `text_styles` (`name`, `font_family`, `font_size`, `font_weight`, `color`, `text_align`, `is_preset`) VALUES
('Heading 1', 'Georgia', '32px', 'bold', '#1a1a1a', 'center', 1),
('Heading 2', 'Georgia', '24px', 'bold', '#333333', 'center', 1),
('Body Text', 'Arial', '14px', 'normal', '#000000', 'left', 1),
('Footer Text', 'Arial', '10px', 'normal', '#666666', 'center', 1),
('Accent Text', 'Georgia', '16px', 'italic', '#8B0000', 'center', 1);

-- Mark this migration as applied
INSERT IGNORE INTO `migrations` (`migration_name`, `batch`) VALUES
('002_design_templates', 1);
