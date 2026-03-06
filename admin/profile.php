<?php
require_once __DIR__ . '/auth.php';
adminCheck();

$db    = getDB();
$msg   = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currUser = $_SESSION['admin_user'];
    $newEmail = trim($_POST['email'] ?? '');
    $newPass  = trim($_POST['password'] ?? '');

    try {
        if (!empty($newPass)) {
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->execute([$hashed, $currUser]);
            $msg = 'Password updated successfully!';
        }
        
        // If you had an email field in users table, update it here.
        // For now, let's assume we just update password as per primary need.
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$pageTitle  = 'My Account — NexSoft Hub Admin';
$activePage = 'profile';
require_once __DIR__ . '/layout-header.php';
?>

<div class="admin-card" style="max-width:600px;">
    <div class="admin-card-header">
        <span class="admin-card-title">Account Security</span>
    </div>
    <div class="admin-card-body">
        <?php if ($msg): ?><div class="admin-alert-success mb-3"><?php echo $msg; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="admin-alert-error mb-3"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" class="admin-form">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" class="form-control" value="<?php echo adminUsername(); ?>" disabled>
            </div>
            <div class="mb-4">
                <label>New Password (Leave blank to keep current)</label>
                <input type="password" name="password" class="form-control" autocomplete="new-password">
            </div>
            <button type="submit" class="btn-admin-primary">Update Profile</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
