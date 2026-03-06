<?php
$currentRoute = $_GET['route'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $metaDescription ?? 'NexSoft Hub — Premium Software Consulting Agency delivering world-class Web, App, and Digital Solutions.'; ?>">
    <title><?php echo $pageTitle ?? 'NexSoft Hub — Premium Software Consulting Agency'; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo baseUrl('assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body>

<!-- ===================== NAVBAR ===================== -->
<nav class="navbar navbar-expand-lg nexsoft-navbar" id="mainNavbar">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="<?php echo baseUrl(); ?>">
            <span class="brand-icon"><i class="bi bi-hexagon-fill"></i></span>
            <span class="brand-text">NexSoft <span class="brand-accent">Hub</span></span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="toggler-icon">
                <span></span><span></span><span></span>
            </span>
        </button>

        <!-- Nav Links -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentRoute === 'home' || $currentRoute === '') ? 'active' : ''; ?>" href="<?php echo baseUrl('?route=home'); ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentRoute === 'about' ? 'active' : ''; ?>" href="<?php echo baseUrl('?route=about'); ?>">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentRoute === 'services' ? 'active' : ''; ?>" href="<?php echo baseUrl('?route=services'); ?>">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentRoute === 'blog' ? 'active' : ''; ?>" href="<?php echo baseUrl('?route=blog'); ?>">Blogs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentRoute === 'pricing' ? 'active' : ''; ?>" href="<?php echo baseUrl('?route=pricing'); ?>">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentRoute === 'contact' ? 'active' : ''; ?>" href="<?php echo baseUrl('?route=contact'); ?>">Contact</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-primary-custom nav-cta <?php echo $currentRoute === 'register' ? 'active' : ''; ?>" href="<?php echo baseUrl('?route=register'); ?>">
                        <i class="bi bi-person-plus me-1"></i> Join Us
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- =================== END NAVBAR =================== -->
