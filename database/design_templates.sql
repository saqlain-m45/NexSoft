-- ========================================
-- NexSoft Hub - Design Templates SQL
-- ========================================
-- Add these tables to your nexsoft_hub database
-- Copy and paste into phpMyAdmin SQL tab
-- ========================================

-- --------------------------------------------------------
-- Table: design_templates (Letters & Certificates)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `design_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL COMMENT 'letter, certificate',
  `description` text DEFAULT NULL,
  `background_image` varchar(255) DEFAULT NULL,
  `logo_image` varchar(255) DEFAULT NULL,
  `logo_position` varchar(50) DEFAULT 'top-center' COMMENT 'top-left, top-center, top-right, center',
  `logo_width` int(11) DEFAULT 150 COMMENT 'in pixels',
  `header_html` longtext DEFAULT NULL,
  `body_html` longtext DEFAULT NULL,
  `footer_html` longtext DEFAULT NULL,
  `template_data` json DEFAULT NULL COMMENT 'Store additional template settings',
  `is_active` tinyint(1) DEFAULT 1,
  `is_default` tinyint(1) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `design_templates_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: template_logos (Logo Assets)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `template_logos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL COMMENT 'in bytes',
  `width` int(11) DEFAULT NULL COMMENT 'original width in pixels',
  `height` int(11) DEFAULT NULL COMMENT 'original height in pixels',
  `description` text DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `template_logos_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: text_styles (Font & Text Style Presets)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `text_styles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `style_type` varchar(50) NOT NULL COMMENT 'heading, body, footer, accent',
  `font_family` varchar(100) DEFAULT 'Arial, sans-serif',
  `font_size` int(11) DEFAULT 16 COMMENT 'in pixels',
  `font_weight` varchar(20) DEFAULT 'normal' COMMENT 'normal, bold, 700, etc',
  `font_color` varchar(7) DEFAULT '#000000' COMMENT 'hex color',
  `line_height` varchar(10) DEFAULT '1.5',
  `letter_spacing` varchar(10) DEFAULT 'normal',
  `text_align` varchar(20) DEFAULT 'left' COMMENT 'left, center, right, justify',
  `text_decoration` varchar(50) DEFAULT 'none' COMMENT 'none, underline, overline, line-through',
  `css_class` varchar(100) DEFAULT NULL COMMENT 'CSS class name for reuse',
  `custom_css` text DEFAULT NULL COMMENT 'Additional CSS properties',
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `css_class` (`css_class`),
  KEY `style_type` (`style_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Text Styles
INSERT INTO `text_styles` (`name`, `style_type`, `font_family`, `font_size`, `font_weight`, `font_color`, `line_height`, `text_align`, `css_class`) VALUES
('Heading 1', 'heading', 'Georgia, serif', 28, 'bold', '#1a1a1a', '1.2', 'center', 'title-heading'),
('Heading 2', 'heading', 'Arial, sans-serif', 20, 'bold', '#333333', '1.3', 'left', 'sub-heading'),
('Body Text', 'body', 'Arial, sans-serif', 14, 'normal', '#000000', '1.6', 'justify', 'body-text'),
('Footer Text', 'footer', 'Arial, sans-serif', 12, 'normal', '#666666', '1.4', 'center', 'footer-text'),
('Accent Text', 'accent', 'Georgia, serif', 16, '500', '#0066cc', '1.5', 'left', 'accent-text');

-- --------------------------------------------------------
-- Table: design_elements (Additional Design Components)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `design_elements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `element_type` varchar(50) NOT NULL COMMENT 'text, line, box, image, shape',
  `element_name` varchar(100) NOT NULL,
  `content` longtext DEFAULT NULL,
  `position_x` int(11) DEFAULT 0,
  `position_y` int(11) DEFAULT 0,
  `width` int(11) DEFAULT 100,
  `height` int(11) DEFAULT 50,
  `style_id` int(11) DEFAULT NULL,
  `custom_css` text DEFAULT NULL,
  `z_index` int(11) DEFAULT 1,
  `is_editable` tinyint(1) DEFAULT 1 COMMENT 'Can user edit this when generating',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`),
  KEY `style_id` (`style_id`),
  CONSTRAINT `design_elements_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `design_templates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `design_elements_ibfk_2` FOREIGN KEY (`style_id`) REFERENCES `text_styles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: issued_documents (Generated Certificates & Letters)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `issued_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` varchar(50) NOT NULL UNIQUE,
  `recipient_name` varchar(255) NOT NULL,
  `recipient_email` varchar(255) DEFAULT NULL,
  `type` varchar(50) NOT NULL COMMENT 'certificate, letter, appreciation, credentials',
  `body_content` longtext DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `issued_by` int(11) DEFAULT NULL,
  `issue_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'active' COMMENT 'active, revoked, expired',
  `pdf_file` varchar(255) DEFAULT NULL COMMENT 'path to generated PDF if exported',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- END OF SQL IMPORT
-- ========================================
-- Tables created:
-- 1. design_templates (master templates)
-- 2. template_logos (logo assets)
-- 3. text_styles (text style presets)
-- 4. design_elements (design components)
-- 5. issued_documents (generated documents)
--
-- Default text styles included (5 styles)
-- All tables are properly indexed
-- Foreign key constraints enabled
-- ========================================
