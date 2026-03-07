<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>NexSoft Hub - Database Fix Script</h2>";

try {
    $db = getDB();
    
    // 1. Create site_settings table
    $sqlTable = "CREATE TABLE IF NOT EXISTS `site_settings` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `setting_key` varchar(100) NOT NULL,
      `setting_value` text DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `setting_key` (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->exec($sqlTable);
    echo "<p style='color:green;'>[SUCCESS] Table 'site_settings' created or already exists.</p>";

    // 1.1 Add role support to users for role-based access
    try { $db->exec("ALTER TABLE users ADD COLUMN role varchar(50) NOT NULL DEFAULT 'viewer' AFTER password"); } catch(PDOException $e){}
    $db->exec("UPDATE users SET role='super_admin' WHERE username='admin' AND (role='' OR role='viewer' OR role IS NULL)");
    echo "<p style='color:green;'>[SUCCESS] Table 'users' role access updated.</p>";

    // 2. Create services table
    $sqlServices = "CREATE TABLE IF NOT EXISTS `services` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `features` text DEFAULT NULL,
      `tags` varchar(255) DEFAULT NULL,
      `icon` varchar(100) DEFAULT 'bi-gear',
      `order_no` int(11) DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->exec($sqlServices);
    
    // Add columns if they dont exist (for existing tables)
    try { $db->exec("ALTER TABLE services ADD COLUMN features text DEFAULT NULL AFTER description"); } catch(PDOException $e){}
    try { $db->exec("ALTER TABLE services ADD COLUMN tags varchar(255) DEFAULT NULL AFTER features"); } catch(PDOException $e){}
    
    echo "<p style='color:green;'>[SUCCESS] Table 'services' updated.</p>";

    // 3. Create testimonials table
    $sqlTestimonials = "CREATE TABLE IF NOT EXISTS `testimonials` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `client_name` varchar(255) NOT NULL,
      `designation` varchar(255) DEFAULT NULL,
      `feedback` text NOT NULL,
      `rating` int(1) DEFAULT 5,
      `client_image` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->exec($sqlTestimonials);
    echo "<p style='color:green;'>[SUCCESS] Table 'testimonials' created.</p>";

    // 4. Insert default settings
    $settings = [
        ['site_name', 'NexSoft Hub'],
        ['meta_title', 'NexSoft Hub — Premium Software Consulting Agency'],
        ['meta_description', 'NexSoft Hub — Premium Software Consulting Agency delivering world-class Web, App, and Digital Solutions.'],
        ['meta_keywords', 'software development, web design, app development, UI/UX, digital marketing'],
        ['site_email', 'hello@nexsofthub.com'],
        ['site_phone', '+1 (555) 234-5678'],
        ['site_address', '123 Innovation Drive, Tech City, CA 94105'],
        ['facebook_link', 'https://facebook.com'],
        ['twitter_link', 'https://twitter.com'],
        ['linkedin_link', 'https://linkedin.com'],
        ['instagram_link', 'https://instagram.com'],
        ['github_link', 'https://github.com'],
        ['smtp_host', 'smtp.gmail.com'],
        ['smtp_port', '587'],
        ['smtp_user', 'user@gmail.com'],
        ['smtp_pass', 'password'],
        ['smtp_encryption', 'tls'],
        ['google_analytics_id', ''],
        ['custom_head_scripts', ''],
        ['custom_footer_scripts', ''],
        ['custom_cursor_enabled', '1'],
        ['maintenance_mode', '0']
    ];

    $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    
    foreach ($settings as $s) {
        $stmt->execute([$s[0], $s[1]]);
    }

    // 5. Insert default services if empty
    $count = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO services (title, description, icon) VALUES 
            ('Web Development', 'High-performance websites tailored to your business needs.', 'bi-laptop'),
            ('App Development', 'Custom iOS and Android applications for a seamless mobile experience.', 'bi-phone'),
            ('UI/UX Design', 'Beautiful and intuitive interfaces focused on user engagement.', 'bi-palette')");
    }
    
    echo "<p style='color:green;'>[SUCCESS] Data initialization completed.</p>";
    echo "<br><p><strong>Everything is fixed!</strong> You can now use the <a href='admin/settings.php'>Site Settings</a> page.</p>";
    echo "<p style='color:red;'>Please DELETE this file (fix-db.php) once you are done for security.</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>[ERROR] " . $e->getMessage() . "</p>";
}
?>
