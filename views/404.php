<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found | NexSoft Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700;900&display=swap" rel="stylesheet">
    <?php if (function_exists('baseUrl')): ?>
    <link href="<?php echo baseUrl('assets/css/style.css'); ?>" rel="stylesheet">
    <?php else: ?>
    <style>
        :root{--primary:#0B1F3B;--secondary:#0EA5A4;--font:'Montserrat',sans-serif;}
        body{font-family:var(--font);background:linear-gradient(135deg,var(--primary),#162d4f);min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;color:white;padding:2rem;}
        .err{font-size:8rem;font-weight:900;color:var(--secondary);opacity:.25;line-height:1;}
        h1{font-size:2.5rem;font-weight:900;margin:0 0 1rem;}
        p{font-size:1rem;opacity:.65;margin-bottom:2rem;}
        .btn{display:inline-flex;align-items:center;gap:8px;background:var(--secondary);color:white;border:none;padding:.9rem 2rem;border-radius:50px;font-family:var(--font);font-weight:700;font-size:.95rem;text-decoration:none;}
    </style>
    <?php endif; ?>
</head>
<body>
<div class="page-404">
    <div>
        <div class="error-code">404</div>
        <h1>Page Not Found</h1>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <?php if (function_exists('baseUrl')): ?>
        <a href="<?php echo baseUrl(); ?>" class="btn-hero-primary" style="display:inline-flex;margin-right:12px;">
            <i class="bi bi-house-fill"></i> Back to Home
        </a>
        <a href="<?php echo baseUrl('?route=contact'); ?>" class="btn-hero-outline" style="display:inline-flex;">
            <i class="bi bi-chat-dots"></i> Contact Us
        </a>
        <?php else: ?>
        <a href="/NexSoft/" class="btn"><i class="bi bi-house-fill"></i> Back to Home</a>
        <?php endif; ?>
    </div>
</div>
<?php if (function_exists('baseUrl')): ?>
<script src="<?php echo baseUrl('assets/js/main.js'); ?>"></script>
<?php endif; ?>
</body>
</html>
