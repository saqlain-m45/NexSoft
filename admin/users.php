<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('manage_users');

$db     = getDB();
$action = $_GET['action'] ?? 'list';
$msg    = '';
$error  = '';

ensureUsersRoleSchema($db);

$roleConfig = adminRoleConfig();
$assignableRoles = array_filter(
    array_keys($roleConfig),
    static fn ($role) => $role !== 'super_admin'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionPost = $_POST['action'] ?? '';

    if ($actionPost === 'add' || $actionPost === 'edit') {
        $id       = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = normalizeAdminRole($_POST['role'] ?? 'viewer');

        if ($actionPost === 'add' && $role === 'super_admin') {
            $error = 'Create super admins directly from database only.';
        } elseif (!in_array($role, $assignableRoles, true)) {
            $error = 'Invalid role selected.';
        } elseif ($username === '' || $email === '') {
            $error = 'Username and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif ($actionPost === 'add' && strlen($password) < 8) {
            $error = 'Password must be at least 8 characters for new users.';
        }

        if ($error === '') {
            try {
                if ($actionPost === 'add') {
                    $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
                    $stmt->execute([$username, $email]);
                    if ((int)$stmt->fetchColumn() > 0) {
                        $error = 'Username or email already exists.';
                    } else {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
                        $stmt->execute([$username, $email, $hash, $role]);
                        $msg = 'User created successfully.';
                        $action = 'list';
                    }
                } else {
                    $stmt = $db->prepare('SELECT id FROM users WHERE (username = ? OR email = ?) AND id <> ?');
                    $stmt->execute([$username, $email, $id]);
                    if ($stmt->fetch()) {
                        $error = 'Another user already uses that username or email.';
                    } else {
                        if ($password !== '') {
                            if (strlen($password) < 8) {
                                $error = 'Password must be at least 8 characters.';
                            } else {
                                $hash = password_hash($password, PASSWORD_DEFAULT);
                                $stmt = $db->prepare('UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?');
                                $stmt->execute([$username, $email, $role, $hash, $id]);
                                $msg = 'User updated successfully.';
                                $action = 'list';
                            }
                        } else {
                            $stmt = $db->prepare('UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?');
                            $stmt->execute([$username, $email, $role, $id]);
                            $msg = 'User updated successfully.';
                            $action = 'list';
                        }
                    }
                }
            } catch (Exception $e) {
                $error = 'Failed to save user: ' . $e->getMessage();
            }
        }
    } elseif ($actionPost === 'delete') {
        $id = (int)($_POST['id'] ?? 0);

        if ($id === (int)($_SESSION['admin_id'] ?? 0)) {
            $error = 'You cannot delete your own account.';
        } else {
            try {
                $stmt = $db->prepare('SELECT role FROM users WHERE id = ?');
                $stmt->execute([$id]);
                $userRole = $stmt->fetchColumn();

                if (!$userRole) {
                    $error = 'User not found.';
                } elseif ($userRole === 'super_admin') {
                    $error = 'Deleting super admin accounts is not allowed from panel.';
                } else {
                    $db->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
                    $msg = 'User deleted successfully.';
                }
            } catch (Exception $e) {
                $error = 'Failed to delete user: ' . $e->getMessage();
            }
        }
    }
}

$users = $db->query('SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();

$editUser = null;
if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare('SELECT id, username, email, role FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $editUser = $stmt->fetch();
    if (!$editUser || ($editUser['role'] ?? '') === 'super_admin') {
        $action = 'list';
        $error = 'That user cannot be edited here.';
    }
}

$pageTitle  = 'Admin Users - NexSoft Hub Admin';
$activePage = 'users';
require_once __DIR__ . '/layout-header.php';
?>

<?php if ($msg !== ''): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if ($error !== ''): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="admin-card" style="max-width:860px;">
    <div class="admin-card-header">
        <span class="admin-card-title">
            <i class="bi bi-person-plus-fill me-2" style="color:var(--secondary);"></i>
            <?php echo $action === 'add' ? 'Create Team User' : 'Edit Team User'; ?>
        </span>
        <a href="<?php echo adminUrl('users.php'); ?>" class="btn-action btn-view">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
    <div class="admin-card-body">
        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id" value="<?php echo (int)$editUser['id']; ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label>Username <span style="color:var(--secondary);">*</span></label>
                    <input type="text" name="username" class="form-control" required
                           value="<?php echo htmlspecialchars($editUser['username'] ?? $_POST['username'] ?? ''); ?>"
                           placeholder="e.g. team.junaid">
                </div>
                <div class="col-md-6">
                    <label>Email <span style="color:var(--secondary);">*</span></label>
                    <input type="email" name="email" class="form-control" required
                           value="<?php echo htmlspecialchars($editUser['email'] ?? $_POST['email'] ?? ''); ?>"
                           placeholder="e.g. junaid@company.com">
                </div>
                <div class="col-md-6">
                    <label>Role <span style="color:var(--secondary);">*</span></label>
                    <select name="role" class="form-control" required>
                        <?php
                        $selectedRole = $editUser['role'] ?? $_POST['role'] ?? 'team_manager';
                        foreach ($assignableRoles as $roleKey):
                            $selected = $selectedRole === $roleKey ? 'selected' : '';
                        ?>
                        <option value="<?php echo htmlspecialchars($roleKey); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($roleConfig[$roleKey]['label']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>
                        Password
                        <?php if ($action === 'add'): ?>
                        <span style="color:var(--secondary);">*</span>
                        <?php else: ?>
                        <small style="color:var(--text-muted);font-weight:400;">(leave blank to keep old password)</small>
                        <?php endif; ?>
                    </label>
                    <input type="password" name="password" class="form-control" <?php echo $action === 'add' ? 'required' : ''; ?>
                           autocomplete="new-password" minlength="8">
                </div>
                <div class="col-12" style="color:var(--text-muted);font-size:.92rem;">
                    Roles: Team Manager can access Team Members, CRM Manager can access Registrations/Messages, Content Manager can access website content modules.
                </div>
                <div class="col-12 pt-2 d-flex gap-2">
                    <button type="submit" class="btn-admin-primary">
                        <i class="bi bi-save"></i> <?php echo $action === 'add' ? 'Create User' : 'Save Changes'; ?>
                    </button>
                    <a href="<?php echo adminUrl('users.php'); ?>" class="btn-admin-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">
            <i class="bi bi-person-lines-fill me-2" style="color:var(--secondary);"></i>
            Admin Users (<?php echo count($users); ?>)
        </span>
        <a href="<?php echo adminUrl('users.php?action=add'); ?>" class="btn-admin-primary">
            <i class="bi bi-plus"></i> Create User
        </a>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;padding:3rem;color:var(--text-muted);">
                        No users found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <span class="badge-teal"><?php echo htmlspecialchars($roleConfig[normalizeAdminRole($u['role'])]['label'] ?? 'Viewer'); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars(date('M d, Y', strtotime((string)$u['created_at']))); ?></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <?php if (($u['role'] ?? '') !== 'super_admin'): ?>
                            <a href="<?php echo adminUrl('users.php?action=edit&id=' . (int)$u['id']); ?>" class="btn-action btn-edit">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                                <button type="submit" class="btn-action btn-delete confirm-delete" <?php echo (int)$u['id'] === (int)($_SESSION['admin_id'] ?? 0) ? 'disabled' : ''; ?>>
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                            <?php else: ?>
                            <span style="color:var(--text-muted);font-size:.88rem;">Protected account</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
