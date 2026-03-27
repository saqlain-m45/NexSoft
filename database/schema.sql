-- ================================================================
-- NexSoft Hub - Complete Database Schema
-- ================================================================
-- This file contains the complete database schema for NexSoft Hub
-- Import this file in phpMyAdmin or via command line to set up the database

-- Create Database
CREATE DATABASE IF NOT EXISTS `nexsoft_hub` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nexsoft_hub`;

-- ================================================================
-- Table: users (Admin users and staff authentication)
-- ================================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'viewer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin user: username=admin, password=admin123
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('admin', 'admin@nexsofthub.com', '$2y$10$wkOGHQLsl/u.OrgngjWkvu6knkLWC0UfVY8w.0MyWdx1QF2ukib26', 'Administrator', 'super_admin');

-- ================================================================
-- Table: projects (Portfolio projects)
-- ================================================================
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: blog_posts (Blog articles)
-- ================================================================
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `slug` varchar(500) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `author` varchar(100) NOT NULL DEFAULT 'NexSoft Hub',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: registrations (Email list registrations)
-- ================================================================
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `portfolio_link` varchar(500) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: contact_messages (Contact form submissions)
-- ================================================================
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `message` text NOT NULL,
  `read_status` varchar(50) DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `read_status` (`read_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: message_replies (Replies to contact messages)
-- ================================================================
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
  CONSTRAINT `message_replies_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `contact_messages`(`id`) ON DELETE CASCADE,
  CONSTRAINT `message_replies_ibfk_2` FOREIGN KEY (`sent_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: site_settings (Global site configuration)
-- ================================================================
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default site settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'NexSoft Hub'),
('meta_title', 'NexSoft Hub — Premium Software Consulting Agency'),
('meta_description', 'NexSoft Hub — Premium Software Consulting Agency delivering world-class Web, App, and Digital Solutions.'),
('meta_keywords', 'software development, web design, app development, UI/UX, digital marketing'),
('site_email', 'hello@nexsofthub.com'),
('site_phone', '+1 (555) 234-5678'),
('site_address', '123 Innovation Drive, Tech City, CA 94105'),
('facebook_link', 'https://facebook.com'),
('twitter_link', 'https://twitter.com'),
('linkedin_link', 'https://linkedin.com'),
('instagram_link', 'https://instagram.com'),
('github_link', 'https://github.com'),
('smtp_host', 'smtp.gmail.com'),
('smtp_port', '587'),
('smtp_user', 'your-email@gmail.com'),
('smtp_pass', 'your-app-password'),
('smtp_encryption', 'tls'),
('google_analytics_id', ''),
('custom_head_scripts', ''),
('custom_footer_scripts', ''),
('responsive_enabled', '1'),
('custom_cursor_enabled', '1'),
('maintenance_mode', '0');

-- ================================================================
-- Table: services (Service offerings)
-- ================================================================
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `features` text DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT 'bi-gear',
  `order_no` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: testimonials (Client testimonials)
-- ================================================================
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `feedback` text NOT NULL,
  `rating` int(1) DEFAULT 5,
  `client_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: team_members (Team members)
-- ================================================================
CREATE TABLE IF NOT EXISTS `team_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: courses (Training courses)
-- ================================================================
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255),
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `registration_open` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: course_registrations (Student registrations for courses/internships)
-- ================================================================
CREATE TABLE IF NOT EXISTS `course_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11),
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `is_notified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  CONSTRAINT `course_registrations_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_registrations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: design_templates (Letter & Certificate templates)
-- ================================================================
CREATE TABLE IF NOT EXISTS `design_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL COMMENT 'letter, certificate, appreciation',
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `background_image` varchar(255) DEFAULT NULL,
  `logo_image` varchar(255) DEFAULT NULL,
  `logo_position` varchar(50) DEFAULT 'top-center',
  `logo_width` int(11) DEFAULT 150,
  `header_html` longtext DEFAULT NULL,
  `body_text` longtext DEFAULT NULL,
  `body_html` longtext DEFAULT NULL,
  `footer_html` longtext DEFAULT NULL,
  `styles` longtext DEFAULT NULL,
  `template_data` json DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_default` tinyint(1) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `is_active` (`is_active`),
  KEY `is_default` (`is_default`),
  CONSTRAINT `design_templates_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Email templates with personalization';

-- ================================================================
-- Table: template_logos (Logo assets for templates)
-- ================================================================
CREATE TABLE IF NOT EXISTS `template_logos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `template_logos_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: text_styles (Font & text style presets)
-- ================================================================
CREATE TABLE IF NOT EXISTS `text_styles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `style_type` varchar(50) NOT NULL,
  `font_family` varchar(100) DEFAULT 'Arial, sans-serif',
  `font_size` int(11) DEFAULT 16,
  `font_weight` varchar(20) DEFAULT 'normal',
  `font_color` varchar(7) DEFAULT '#000000',
  `line_height` varchar(10) DEFAULT '1.5',
  `letter_spacing` varchar(10) DEFAULT 'normal',
  `text_align` varchar(20) DEFAULT 'left',
  `text_decoration` varchar(50) DEFAULT 'none',
  `css_class` varchar(100) DEFAULT NULL,
  `custom_css` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `css_class` (`css_class`),
  KEY `style_type` (`style_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: design_elements (Design components)
-- ================================================================
CREATE TABLE IF NOT EXISTS `design_elements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `element_type` varchar(50) NOT NULL,
  `element_name` varchar(100) NOT NULL,
  `content` longtext DEFAULT NULL,
  `position_x` int(11) DEFAULT 0,
  `position_y` int(11) DEFAULT 0,
  `width` int(11) DEFAULT 100,
  `height` int(11) DEFAULT 50,
  `style_id` int(11) DEFAULT NULL,
  `custom_css` text DEFAULT NULL,
  `z_index` int(11) DEFAULT 1,
  `is_editable` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`),
  KEY `style_id` (`style_id`),
  CONSTRAINT `design_elements_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `design_templates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `design_elements_ibfk_2` FOREIGN KEY (`style_id`) REFERENCES `text_styles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: issued_documents (Generated certificates & letters)
-- ================================================================
CREATE TABLE IF NOT EXISTS `issued_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` varchar(50) NOT NULL UNIQUE,
  `recipient_name` varchar(255) NOT NULL,
  `recipient_email` varchar(255) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `body_content` longtext DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `issued_by` int(11) DEFAULT NULL,
  `issue_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'active',
  `pdf_file` varchar(255) DEFAULT NULL,
  `verification_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_id` (`document_id`),
  KEY `recipient_email` (`recipient_email`),
  KEY `status` (`status`),
  KEY `type` (`type`),
  KEY `issue_date` (`issue_date`),
  CONSTRAINT `issued_documents_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `design_templates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `issued_documents_ibfk_2` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: email_templates (Email templates with personalization)
-- ================================================================
CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Template name (e.g., "Welcome Email", "Course Completion")',
  `subject` varchar(255) NOT NULL COMMENT 'Email subject line with placeholders',
  `body` longtext NOT NULL COMMENT 'Email body HTML with placeholders',
  `description` text COMMENT 'Brief description of when to use this template',
  `available_placeholders` text COMMENT 'JSON array of available placeholders: {name}, {email}, {phone}, {company}, etc',
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `email_templates_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Email templates with personalization';

-- ================================================================
-- Table: email_logs (Email sending logs)
-- ================================================================
CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `sent_at` timestamp NULL,
  `error_message` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `email_logs_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `email_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log of all emails sent via templates';

-- ================================================================
-- Table: migrations (Track applied migrations)
-- ================================================================
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(255) NOT NULL UNIQUE,
  `batch` int(11) NOT NULL,
  `executed_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `batch` (`batch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert applied migrations
INSERT IGNORE INTO `migrations` (`migration_name`, `batch`) VALUES
('001_initial_schema', 1),
('002_design_templates', 1),
('003_message_replies', 1),
('004_email_templates', 2);

-- ================================================================
-- Create Index for better performance
-- ================================================================
CREATE INDEX idx_created_at ON blog_posts(created_at);
CREATE INDEX idx_created_at ON projects(created_at);
CREATE INDEX idx_user_email ON course_registrations(email);
CREATE INDEX idx_template_type ON design_templates(type);
CREATE INDEX idx_email_status ON email_logs(status);

-- ================================================================
-- Database Schema Complete
-- ================================================================
-- NexSoft Hub is now ready!
-- Login to admin with: username=admin password=admin123
-- Change the SMTP settings in site_settings table to send emails
