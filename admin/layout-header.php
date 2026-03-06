<?php
/**
 * Admin Sidebar - Reusable
 * $activePage must be set before including this file
 */
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin — NexSoft Hub'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="<?php echo adminAsset('css/admin.css'); ?>" rel="stylesheet">
</head>
<body>
<div class="admin-wrapper">
    <!-- ===== SIDEBAR ===== -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <span class="sidebar-brand-icon"><i class="bi bi-hexagon-fill"></i></span>
            <span class="sidebar-brand-text">NexSoft <span>Hub</span></span>
        </div>

        <nav class="sidebar-menu">
            <div class="sidebar-section-label">Main</div>
            <a href="<?php echo adminUrl('dashboard.php'); ?>" class="sidebar-link <?php echo $activePage==='dashboard'?'active':''; ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="sidebar-section-label" style="margin-top:1rem;">Content</div>
            <a href="<?php echo adminUrl('projects.php'); ?>" class="sidebar-link <?php echo $activePage==='projects'?'active':''; ?>">
                <i class="bi bi-folder-fill"></i> Projects
            </a>
            <a href="<?php echo adminUrl('blogs.php'); ?>" class="sidebar-link <?php echo $activePage==='blogs'?'active':''; ?>">
                <i class="bi bi-journal-richtext"></i> Blog Posts
            </a>
            <a href="<?php echo adminUrl('team.php'); ?>" class="sidebar-link <?php echo $activePage==='team'?'active':''; ?>">
                <i class="bi bi-people-fill"></i> Team Members
            </a>

            <div class="sidebar-section-label" style="margin-top:1rem;">CRM</div>
            <a href="<?php echo adminUrl('registrations.php'); ?>" class="sidebar-link <?php echo $activePage==='registrations'?'active':''; ?>">
                <i class="bi bi-people-fill"></i> Registrations
            </a>
            <a href="<?php echo adminUrl('messages.php'); ?>" class="sidebar-link <?php echo $activePage==='messages'?'active':''; ?>">
                <i class="bi bi-chat-left-text-fill"></i> Messages
            </a>

            <div class="sidebar-section-label" style="margin-top:1rem;">Site</div>
            <a href="/NexSoft/" target="_blank" class="sidebar-link">
                <i class="bi bi-box-arrow-up-right"></i> View Website
            </a>
        </nav>

        <div class="sidebar-footer">
            <div style="font-size:0.75rem;color:rgba(255,255,255,0.25);">NexSoft Hub v1.0</div>
        </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <div class="admin-main">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="admin-topbar-title"><?php echo $pageTitle ?? 'Dashboard'; ?></div>
            <div class="admin-topbar-right">
                <div class="admin-badge">
                    <i class="bi bi-shield-fill-check"></i>
                    <?php echo adminUsername(); ?>
                </div>
                <a href="<?php echo adminUrl('logout.php'); ?>" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div class="admin-content">
