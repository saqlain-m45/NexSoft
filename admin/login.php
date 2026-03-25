<?php
require_once __DIR__ . '/auth.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: ' . adminUrl('dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!adminValidateCsrfFromRequest()) {
        $error = 'Security token expired. Please try again.';
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($error) && (empty($username) || empty($password))) {
        $error = 'Please enter both username and password.';
    } elseif (empty($error) && adminLogin($username, $password)) {
        header('Location: ' . adminUrl('dashboard.php'));
        exit;
    } elseif (empty($error)) {
        $error = 'Invalid username or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — NexSoft Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="<?php echo adminAsset('css/admin.css'); ?>" rel="stylesheet">
</head>
<body>
<div class="admin-login-page">
    <div class="admin-login-box">
        <!-- Brand -->
        <div class="admin-login-logo">
            <span class="brand-icon"><i class="bi bi-hexagon-fill"></i></span>
            <span class="brand-text">NexSoft <span class="brand-accent">Hub</span></span>
        </div>
        <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:2rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;">Admin Dashboard</p>

        <h1 style="font-size:1.6rem;font-weight:800;color:var(--primary);margin-bottom:0.4rem;">Welcome Back</h1>
        <p style="font-size:0.88rem;color:var(--text-muted);margin-bottom:2rem;font-weight:300;">Sign in to manage your NexSoft Hub platform.</p>

        <?php if (!empty($error)): ?>
        <div class="admin-alert-error mb-3">
            <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php echo adminCsrfField(); ?>
            <div class="mb-3">
                <label for="username" style="font-size:0.85rem;font-weight:600;color:var(--text);display:block;margin-bottom:6px;">Username</label>
                <div style="position:relative;">
                    <i class="bi bi-person" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:1rem;"></i>
                    <input type="text" id="username" name="username"
                           class="form-control" placeholder="Enter username"
                           style="padding-left:2.5rem;font-family:var(--font);border-radius:var(--radius-sm);border:1.5px solid var(--border);padding-top:0.75rem;padding-bottom:0.75rem;"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label for="password" style="font-size:0.85rem;font-weight:600;color:var(--text);display:block;margin-bottom:6px;">Password</label>
                <div style="position:relative;">
                    <i class="bi bi-lock" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:1rem;"></i>
                    <input type="password" id="password" name="password"
                           class="form-control" placeholder="Enter password"
                           style="padding-left:2.5rem;font-family:var(--font);border-radius:var(--radius-sm);border:1.5px solid var(--border);padding-top:0.75rem;padding-bottom:0.75rem;"
                           required>
                </div>
            </div>
            <button type="submit" class="btn-admin-primary w-100 justify-content-center" style="padding:0.9rem;font-size:0.95rem;border-radius:var(--radius-sm);">
                <i class="bi bi-box-arrow-in-right"></i> Sign In to Dashboard
            </button>
        </form>

        <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border);text-align:center;">
            <a href="<?php echo baseUrl(); ?>" style="font-size:0.82rem;color:var(--secondary);font-weight:600;display:inline-flex;align-items:center;gap:5px;">
                <i class="bi bi-arrow-left"></i> Back to Website
            </a>
        </div>

        <div style="margin-top:1rem;background:#f0f9ff;border-radius:var(--radius-sm);padding:0.8rem;text-align:center;">
            <p style="font-size:0.75rem;color:var(--text-muted);margin:0;">
                <i class="bi bi-info-circle me-1" style="color:var(--secondary);"></i>
                Default login: <strong>admin</strong> / <strong>admin123</strong>
            </p>
        </div>
    </div>
</div>
</body>
</html>
