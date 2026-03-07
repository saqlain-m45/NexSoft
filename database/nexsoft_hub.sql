-- NexSoft Hub Database Schema
-- Import this file in phpMyAdmin

CREATE DATABASE IF NOT EXISTS `nexsoft_hub` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nexsoft_hub`;

-- --------------------------------------------------------
-- Table: users (admin authentication)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'viewer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin user: username=admin, password=admin123
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@nexsofthub.com', '$2y$10$wkOGHQLsl/u.OrgngjWkvu6knkLWC0UfVY8w.0MyWdx1QF2ukib26', 'super_admin');

-- --------------------------------------------------------
-- Table: projects
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample projects
INSERT INTO `projects` (`title`, `description`, `image`, `link`) VALUES
('E-Commerce Platform', 'A full-featured e-commerce platform with payment gateway integration, inventory management, and real-time analytics dashboard.', NULL, 'https://example.com'),
('Healthcare Management System', 'A comprehensive healthcare management system for hospitals including patient records, appointment scheduling, and billing.', NULL, 'https://example.com'),
('Real Estate Portal', 'A modern real estate listing portal with advanced search filters, map integration, and virtual tour capabilities.', NULL, 'https://example.com'),
('FinTech Mobile App', 'A secure mobile banking application with biometric authentication, instant transfers, and expense tracking.', NULL, 'https://example.com'),
('EdTech Learning Platform', 'An interactive online learning platform with live classes, assignments, progress tracking, and certification.', NULL, 'https://example.com'),
('Restaurant Management', 'A complete restaurant management suite with POS, online ordering, kitchen display system, and reporting.', NULL, 'https://example.com');

-- --------------------------------------------------------
-- Table: blog_posts
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `slug` varchar(500) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `author` varchar(100) NOT NULL DEFAULT 'NexSoft Hub',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample blog posts
INSERT INTO `blog_posts` (`title`, `slug`, `content`, `excerpt`, `author`) VALUES
('10 Web Development Trends Dominating 2025', '10-web-development-trends-2025', '<p>The web development landscape is evolving rapidly. From AI-powered development tools to edge computing and WebAssembly, here are the top trends shaping 2025.</p><h2>1. AI-Assisted Development</h2><p>Artificial intelligence is transforming how developers write code. Tools like GitHub Copilot and similar AI assistants are boosting productivity by up to 55%, allowing developers to focus on architecture and problem-solving rather than boilerplate code.</p><h2>2. Edge Computing</h2><p>Moving computation closer to users through edge networks dramatically improves performance. CDN providers are now offering serverless functions at the edge, reducing latency to near-zero for global users.</p><h2>3. WebAssembly (WASM)</h2><p>WebAssembly is opening the door to high-performance web applications. Languages like Rust, C++, and Go can now compile to WASM, enabling desktop-class performance in the browser.</p><h2>4. Progressive Web Apps</h2><p>PWAs continue to bridge the gap between web and native apps, offering offline functionality, push notifications, and device hardware access without app store distribution.</p><p>These trends represent exciting opportunities for businesses to leverage cutting-edge technology for competitive advantage.</p>', 'Discover the top web development trends reshaping the industry in 2025, from AI-assisted coding to edge computing and WebAssembly.', 'Ahmad Raza'),
('Why Your Business Needs a Mobile App in 2025', 'why-business-needs-mobile-app-2025', '<p>With over 7 billion smartphone users worldwide, mobile apps are no longer optional — they are essential business infrastructure.</p><h2>Mobile-First Consumer Behavior</h2><p>Studies show that users spend 90% of their mobile time in apps rather than browsers. A well-designed mobile app creates a direct channel to your customers, allows push notifications, and provides a seamless brand experience.</p><h2>Competitive Differentiation</h2><p>A professional mobile app signals credibility and innovation. In competitive markets, businesses with apps consistently outperform those without in customer retention metrics.</p><h2>Revenue Impact</h2><p>Mobile commerce accounts for over 73% of e-commerce sales. Businesses with native apps see 3x higher conversion rates compared to mobile websites.</p><h2>Cost-Effective Customer Service</h2><p>From chatbots to self-service portals, mobile apps reduce customer service costs by 30% while improving satisfaction scores.</p><p>Investing in a mobile app is one of the highest-ROI decisions a modern business can make.</p>', 'Explore why having a mobile app is critical for business growth in 2025 and how it boosts revenue and customer retention.', 'Sara Khan'),
('UI/UX Design Principles That Convert Visitors to Customers', 'ui-ux-design-principles-convert-visitors', '<p>Great design is not just about aesthetics — it is about guiding users toward conversion. Here are the core principles that separate good design from great design.</p><h2>Clarity Over Cleverness</h2><p>Users should understand your value proposition within 3 seconds of landing on your page. Clear headlines, concise copy, and obvious CTAs outperform creative but confusing alternatives every time.</p><h2>The F-Pattern Layout</h2><p>Eye-tracking studies show users read web content in an F-shaped pattern. Place your most important content — headlines, CTAs, key benefits — along the top and left side of your layout.</p><h2>Visual Hierarchy</h2><p>Guide user attention through size, color, and spacing. Your primary CTA should be the most visually prominent element on the page after your headline.</p><h2>Reduce Cognitive Load</h2><p>Every unnecessary decision you ask users to make increases dropout rates. Simplify navigation, reduce form fields, and provide sensible defaults to reduce friction.</p><h2>Mobile-First Design</h2><p>With mobile traffic exceeding desktop, design for small screens first then progressively enhance for larger viewports.</p>', 'Learn the proven UI/UX design principles that transform website visitors into paying customers through strategic design decisions.', 'Usman Ali'),
('The Complete Guide to WordPress Development', 'complete-guide-wordpress-development', '<p>WordPress powers over 43% of all websites on the internet. Understanding how to build professional WordPress sites is one of the most valuable skills in web development today.</p><h2>Custom Theme Development</h2><p>While premium themes offer convenience, custom WordPress themes give you complete control over performance, design, and functionality. Learn template hierarchy, the WordPress loop, and theme functions to build from scratch.</p><h2>Plugin Development</h2><p>WordPress plugins extend core functionality. From custom post types and taxonomies to REST API integrations, plugin development allows you to create virtually any feature.</p><h2>Gutenberg Block Development</h2><p>The modern WordPress editor (Gutenberg) uses React-based blocks. Developing custom blocks creates flexible, reusable content components for content editors.</p><h2>Performance Optimization</h2><p>A fast WordPress site requires caching (Redis, object cache), image optimization, database query optimization, and CDN integration. Premium sites achieve sub-1-second load times with proper optimization.</p>', 'A comprehensive guide to professional WordPress development covering custom themes, plugins, Gutenberg blocks, and performance optimization.', 'Fatima Malik'),
('How to Choose the Right Software Agency for Your Project', 'how-to-choose-right-software-agency', '<p>Choosing the wrong software agency can cost your business thousands of dollars and months of wasted time. Here is how to evaluate and select the right partner.</p><h2>Evaluate Their Portfolio</h2><p>Look for agencies with experience in your industry and project type. Portfolio pieces should demonstrate technical depth, design quality, and business impact — not just visual polish.</p><h2>Check Communication Style</h2><p>The best agency is one that communicates proactively, asks the right questions, and explains complex concepts clearly. Poor communication early on predicts project disasters.</p><h2>Understand Their Process</h2><p>Professional agencies follow structured processes: discovery, planning, design, development, testing, and deployment. Ask about their project management approach and tools they use.</p><h2>Consider Transparency and Pricing</h2><p>Beware of agencies that give quotes without proper discovery sessions. Transparent agencies provide detailed breakdowns of costs, timelines, and deliverables in writing.</p><h2>Post-Launch Support</h2><p>Software requires maintenance. Ensure your agency offers post-launch support, bug fixes, and update services.</p>', 'A practical guide to evaluating and selecting the right software development agency for your project without wasting time or money.', 'Ahmad Raza'),
('Content Marketing Strategies for Tech Startups', 'content-marketing-strategies-tech-startups', '<p>For technology startups, content marketing is one of the most cost-effective growth channels. Here is how to build a content engine that drives organic traffic and generates leads.</p><h2>Define Your Target Audience</h2><p>Create detailed buyer personas before writing a single word. Understand their pain points, information consumption habits, preferred formats, and purchasing triggers.</p><h2>Build Topic Clusters</h2><p>Modern SEO rewards topical authority. Create pillar content covering broad subjects, then link to cluster articles covering specific subtopics. This signals expertise to search engines.</p><h2>Technical SEO Foundation</h2><p>Even the best content fails without technical SEO: fast page speed, mobile optimization, proper heading structure, schema markup, and clean URL architecture.</p><h2>Repurpose Content Across Channels</h2><p>Maximize ROI by repurposing each content piece: blog posts become social threads, videos, podcasts, infographics, and email newsletters.</p><h2>Measure and Iterate</h2><p>Track organic traffic, time-on-page, conversion rates, and backlinks. Double down on what works and cut what does not.</p>', 'Discover effective content marketing strategies tailored for tech startups to drive organic growth and attract quality leads.', 'Sara Khan');

-- --------------------------------------------------------
-- Table: registrations
-- --------------------------------------------------------
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: contact_messages
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: site_settings
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
('smtp_user', 'user@gmail.com'),
('smtp_pass', 'password'),
('smtp_encryption', 'tls'),
('google_analytics_id', ''),
('custom_head_scripts', ''),
('custom_footer_scripts', ''),
('responsive_enabled', '1'),
('custom_cursor_enabled', '1'),
('maintenance_mode', '0');

-- --------------------------------------------------------
-- Table: services
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `features` text DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT 'bi-gear',
  `order_no` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default services
INSERT INTO `services` (`title`, `description`, `features`, `tags`, `icon`) VALUES
('Web Development', 'High-performance websites tailored to your business needs.', 'React Frontends,Node.js Backends,RESTful APIs,Cloud Hosting', 'React,Node.js,PHP,MySQL', 'bi-laptop'),
('App Development', 'Custom iOS and Android applications for a seamless mobile experience.', 'iOS & Android Apps,Offline Support,Push Notifications,In-app Purchases', 'Flutter,Swift,Kotlin,Firebase', 'bi-phone'),
('UI/UX Design', 'Beautiful and intuitive interfaces focused on user engagement.', 'User Research,Prototyping,Design Systems,Logo Design', 'Figma,Adobe XD,Illustrator,UX', 'bi-palette');

-- --------------------------------------------------------
-- Table: testimonials
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `feedback` text NOT NULL,
  `rating` int(1) DEFAULT 5,
  `client_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: team_members
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `team_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample team members
INSERT INTO `team_members` (`name`, `designation`, `bio`, `sort_order`) VALUES
('Saqlain Muzaffar', 'CEO/Founder', 'Hey I am Founder of NexSoft Hub & a passionate developer...', 1),
('Syed Bilal Ahmed', 'Co-Founder', 'I am a Co-Founder & Full Stack developer at NexSoft Hub.', 2),
('M. Kashan', 'Flutter Developer', '', 3),
('Fawad Ali Shan', 'Flutter Developer', '', 4),
('Munim Abbas', 'Web Developer', '', 5);

-- --------------------------------------------------------
-- Table: courses
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `registration_open` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default courses
INSERT INTO `courses` (`title`, `category`) VALUES
('Web Development', 'Web Development'),
('WordPress Development', 'WordPress'),
('SEO Optimization', 'SEO'),
('App Development', 'App Development');

-- --------------------------------------------------------
-- Table: course_registrations
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `course_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `is_notified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `course_registrations_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
