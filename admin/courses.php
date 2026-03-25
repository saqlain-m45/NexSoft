<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('courses');

$db = getDB();
$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

$hasCourseSlug = false;
try {
    $colCheck = $db->query("SHOW COLUMNS FROM courses LIKE 'slug'");
    $hasCourseSlug = (bool)$colCheck->fetch();
}
catch (Throwable $e) {
    $hasCourseSlug = false;
}

// Handle CRUD Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        if ($_POST['action'] === 'add') {
            if ($hasCourseSlug) {
                $stmt = $db->prepare("INSERT INTO courses (title, slug, description, category, registration_open) VALUES (?, ?, ?, ?, 1)");
                $ok = $stmt->execute([$title, $slug, $description, $category]);
            }
            else {
                $stmt = $db->prepare("INSERT INTO courses (title, description, category, registration_open) VALUES (?, ?, ?, 1)");
                $ok = $stmt->execute([$title, $description, $category]);
            }

            if ($ok) {
                header('Location: courses.php?msg=Course added successfully');
                exit;
            }
            else {
                $error = "Failed to add course.";
            }
        }
        elseif ($_POST['action'] === 'edit' && $id) {
            if ($hasCourseSlug) {
                $stmt = $db->prepare("UPDATE courses SET title = ?, slug = ?, description = ?, category = ? WHERE id = ?");
                $ok = $stmt->execute([$title, $slug, $description, $category, $id]);
            }
            else {
                $stmt = $db->prepare("UPDATE courses SET title = ?, description = ?, category = ? WHERE id = ?");
                $ok = $stmt->execute([$title, $description, $category, $id]);
            }

            if ($ok) {
                header('Location: courses.php?msg=Course updated successfully');
                exit;
            }
            else {
                $error = "Failed to update course.";
            }
        }
        elseif ($_POST['action'] === 'delete' && $id) {
            $db->prepare("DELETE FROM courses WHERE id = ?")->execute([$id]);
            header('Location: courses.php?msg=Course deleted successfully');
            exit;
        }
    }
}

// Handle Status Toggle
if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $db->prepare("UPDATE courses SET registration_open = 1 - registration_open WHERE id = ?")->execute([$id]);
    header('Location: courses.php?msg=Status updated');
    exit;
}

$courses = $db->query("SELECT * FROM courses ORDER BY created_at DESC")->fetchAll();
$editItem = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$id]);
    $editItem = $stmt->fetch();
}

$pageTitle = 'Manage Courses — NexSoft Hub Admin';
$activePage = 'courses';
require_once __DIR__ . '/layout-header.php';
?>

<div class="row g-4">
    <!-- Form Side -->
    <div class="col-lg-4">
        <div class="admin-card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="admin-card-header bg-white border-0 py-3 px-4">
                <span class="admin-card-title h5 mb-0" style="font-weight: 800; color: #0B1F3B;">
                    <i
                        class="bi <?php echo $editItem ? 'bi-pencil-square' : 'bi-plus-circle-fill'; ?> text-primary me-2"></i>
                    <?php echo $editItem ? 'Edit Course' : 'Create Course'; ?>
                </span>
            </div>
            <div class="admin-card-body p-4 bg-white">
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $editItem ? 'edit' : 'add'; ?>">
                    <?php if ($editItem): ?>
                    <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
                    <?php
endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">TITLE</label>
                        <input type="text" name="title" class="form-control form-control-lg border-light bg-light"
                            value="<?php echo $editItem ? htmlspecialchars($editItem['title']) : ''; ?>" required
                            placeholder="e.g. Web Development" style="font-size: 0.95rem; border-radius: 10px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">CATEGORY</label>
                        <select name="category" class="form-select form-select-lg border-light bg-light" required
                            style="font-size: 0.95rem; border-radius: 10px;">
                            <option value="Web Dev" <?php echo ($editItem && $editItem['category']=='Web Dev' )
                                ? 'selected' : '' ; ?>>Web Development</option>
                            <option value="WordPress" <?php echo ($editItem && $editItem['category']=='WordPress' )
                                ? 'selected' : '' ; ?>>WordPress</option>
                            <option value="SEO" <?php echo ($editItem && $editItem['category']=='SEO' ) ? 'selected'
                                : '' ; ?>>SEO & Marketing</option>
                            <option value="App Dev" <?php echo ($editItem && $editItem['category']=='App Dev' )
                                ? 'selected' : '' ; ?>>App Development</option>
                            <option value="Graphics" <?php echo ($editItem && $editItem['category']=='Graphics' )
                                ? 'selected' : '' ; ?>>Graphic Design</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">DESCRIPTION</label>
                        <textarea name="description" class="form-control border-light bg-light" rows="4" required
                            placeholder="Course details..."
                            style="font-size: 0.95rem; border-radius: 10px;"><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-3 fw-bold"
                            style="border-radius: 12px; background: #0B1F3B; border: none;">
                            <?php echo $editItem ? 'Update Course' : 'Add Course'; ?>
                        </button>
                        <?php if ($editItem): ?>
                        <a href="courses.php" class="btn btn-outline-secondary py-3 fw-bold"
                            style="border-radius: 12px;">Cancel Edit</a>
                        <?php
endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table Side -->
    <div class="col-lg-8">
        <?php if ($msg): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;"><i
                class="bi bi-check-circle-fill me-2"></i>
            <?php echo htmlspecialchars($msg); ?>
        </div>
        <?php
endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;"><i
                class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php
endif; ?>

        <div class="admin-card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; background: white;">
            <div
                class="admin-card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="admin-card-title h5 mb-0" style="font-weight: 800; color: #0B1F3B;">
                    <i class="bi bi-journal-check text-primary me-2"></i>Course Offerings
                </span>
                <span class="badge bg-light text-dark shadow-sm"
                    style="font-size: 0.75rem; padding: 6px 12px; border-radius: 50px;">
                    <?php echo count($courses); ?> Courses Total
                </span>
            </div>
            <div class="table-wrap px-2 pb-2">
                <table class="admin-table align-middle">
                    <thead>
                        <tr class="text-muted small fw-bold">
                            <th class="ps-4">COURSE INFO</th>
                            <th class="text-center">REGISTRATION</th>
                            <th class="text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            <td class="ps-4 py-3">
                                <div class="fw-bold mb-1" style="color: #0B1F3B;">
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge"
                                        style="background: rgba(14,165,164,0.1); color: #0EA5A4; font-size: 0.65rem; border-radius: 50px;">
                                        <?php echo htmlspecialchars($course['category']); ?>
                                    </span>
                                    <span class="small text-muted" style="font-size: 0.7rem;"><i
                                            class="bi bi-link-45deg"></i> /
                                        <?php echo htmlspecialchars($course['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $course['title'] ?? '')))); ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="?action=toggle&id=<?php echo $course['id']; ?>" class="text-decoration-none">
                                    <?php if ($course['registration_open']): ?>
                                    <span class="badge-green rounded-pill"
                                        style="font-size: 0.7rem; padding: 5px 12px;"><i
                                            class="bi bi-unlock-fill me-1"></i>Open</span>
                                    <?php
    else: ?>
                                    <span class="badge-orange rounded-pill"
                                        style="font-size: 0.7rem; padding: 5px 12px;"><i
                                            class="bi bi-lock-fill me-1"></i>Closed</span>
                                    <?php
    endif; ?>
                                </a>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="?action=edit&id=<?php echo $course['id']; ?>"
                                        class="btn btn-sm btn-light p-2 shadow-sm"
                                        style="border-radius: 8px; width: 34px; height: 34px;"><i
                                            class="bi bi-pencil-fill text-primary"></i></a>
                                    <form method="POST"
                                        onsubmit="return confirm('Deleting this course will also delete all its associated applications. Continue?')"
                                        class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-light p-2 shadow-sm"
                                            style="border-radius: 8px; width: 34px; height: 34px;"><i
                                                class="bi bi-trash-fill text-danger"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php
endforeach; ?>
                        <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox h2 d-block mb-3"></i>
                                No courses added yet.
                            </td>
                        </tr>
                        <?php
endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .admin-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .btn-light:hover {
        background-color: #f1f5f9;
        border-color: #e2e8f0;
    }
</style>

<?php require_once __DIR__ . '/layout-footer.php'; ?>