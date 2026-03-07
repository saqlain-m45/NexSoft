<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('blogs');

$db     = getDB();
$action = $_GET['action'] ?? 'list';
$msg    = '';
$error  = '';

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionPost = $_POST['action'] ?? '';

    if ($actionPost === 'add' || $actionPost === 'edit') {
        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $author  = trim($_POST['author'] ?? 'NexSoft Hub');
        $slug    = slugify($title);
        $imgName = null;

        if (!empty($_FILES['featured_image']['name'])) {
            $allowed = ['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
            if (in_array($_FILES['featured_image']['type'], $allowed) && $_FILES['featured_image']['size'] < 5000000) {
                $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
                $imgName = uniqid('blog_') . '.' . $ext;
                $uploadDir = __DIR__ . '/../assets/uploads/blogs/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                move_uploaded_file($_FILES['featured_image']['tmp_name'], $uploadDir . $imgName);
            } else {
                $error = 'Invalid image file.';
            }
        }

        if (empty($title) || empty($content)) {
            $error = 'Title and content are required.';
        } elseif (empty($error)) {
            if ($actionPost === 'add') {
                // Ensure unique slug
                $existing = $db->prepare("SELECT id FROM blog_posts WHERE slug = ?");
                $existing->execute([$slug]);
                if ($existing->fetch()) { $slug .= '-' . time(); }

                $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, content, excerpt, featured_image, author) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $content, $excerpt, $imgName, $author]);
                $msg = 'Blog post published!';
            } else {
                $id = (int)($_POST['id'] ?? 0);
                if ($imgName) {
                    $stmt = $db->prepare("UPDATE blog_posts SET title=?, slug=?, content=?, excerpt=?, featured_image=?, author=? WHERE id=?");
                    $stmt->execute([$title, $slug, $content, $excerpt, $imgName, $author, $id]);
                } else {
                    $stmt = $db->prepare("UPDATE blog_posts SET title=?, slug=?, content=?, excerpt=?, author=? WHERE id=?");
                    $stmt->execute([$title, $slug, $content, $excerpt, $author, $id]);
                }
                $msg = 'Blog post updated!';
            }
            $action = 'list';
        }
    } elseif ($actionPost === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $b = $db->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
        $b->execute([$id]);
        $bp = $b->fetch();
        if ($bp && $bp['featured_image']) {
            $imgPath = __DIR__ . '/../assets/uploads/blogs/' . $bp['featured_image'];
            if (file_exists($imgPath)) unlink($imgPath);
        }
        $db->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$id]);
        $msg = 'Blog post deleted.';
    }
}

$editPost = null;
if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $s = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $s->execute([$id]);
    $editPost = $s->fetch();
    if (!$editPost) $action = 'list';
}

$posts = $db->query("SELECT id, title, author, created_at FROM blog_posts ORDER BY created_at DESC")->fetchAll();

$pageTitle  = 'Manage Blogs — NexSoft Hub Admin';
$activePage = 'blogs';
require_once __DIR__ . '/layout-header.php';
?>

<?php if (!empty($msg)): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">
            <i class="bi bi-<?php echo $action==='add'?'plus-circle':'pencil'; ?> me-2" style="color:var(--secondary);"></i>
            <?php echo $action==='add' ? 'Add New Blog Post' : 'Edit Blog Post'; ?>
        </span>
        <a href="<?php echo adminUrl('blogs.php'); ?>" class="btn-action btn-view"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id" value="<?php echo $editPost['id']; ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-12">
                    <label>Title *</label>
                    <input type="text" name="title" class="form-control" required
                           value="<?php echo htmlspecialchars($editPost['title'] ?? $_POST['title'] ?? ''); ?>"
                           placeholder="e.g. 10 Web Development Trends in 2025">
                </div>
                <div class="col-md-6">
                    <label>Author</label>
                    <input type="text" name="author" class="form-control"
                           value="<?php echo htmlspecialchars($editPost['author'] ?? $_POST['author'] ?? 'NexSoft Hub'); ?>">
                </div>
                <div class="col-md-6">
                    <label>Featured Image (JPG/PNG/WebP)</label>
                    <?php if (!empty($editPost['featured_image'])): ?>
                    <div class="mb-2">
                        <img src="/NexSoft/assets/uploads/blogs/<?php echo htmlspecialchars($editPost['featured_image']); ?>"
                             class="img-preview" alt="Current">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="featured_image" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <label>Excerpt (short summary for blog cards)</label>
                    <textarea name="excerpt" class="form-control" rows="2"
                              placeholder="Brief description shown in blog listings..."><?php echo htmlspecialchars($editPost['excerpt'] ?? $_POST['excerpt'] ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <label>Content * (HTML allowed)</label>
                    <textarea name="content" class="form-control" rows="12" required
                              placeholder="Write your blog post content here. You can use HTML tags like &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, etc."><?php echo htmlspecialchars($editPost['content'] ?? $_POST['content'] ?? ''); ?></textarea>
                    <small style="color:var(--text-muted);">HTML is supported: &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;, &lt;em&gt;, etc.</small>
                </div>
                <div class="col-12 pt-2">
                    <button type="submit" class="btn-admin-primary">
                        <i class="bi bi-<?php echo $action==='add'?'send':'save'; ?>"></i>
                        <?php echo $action==='add' ? 'Publish Post' : 'Update Post'; ?>
                    </button>
                    <a href="<?php echo adminUrl('blogs.php'); ?>" class="btn-admin-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title"><i class="bi bi-journal-richtext me-2" style="color:var(--secondary);"></i>All Blog Posts (<?php echo count($posts); ?>)</span>
        <a href="<?php echo adminUrl('blogs.php?action=add'); ?>" class="btn-admin-primary">
            <i class="bi bi-plus"></i> Add Post
        </a>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead><tr><th>#</th><th>Title</th><th>Author</th><th>Published</th><th>Actions</th></tr></thead>
            <tbody>
                <?php if (empty($posts)): ?>
                <tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--text-muted);">
                    <i class="bi bi-journal-x" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>
                    No posts yet. <a href="<?php echo adminUrl('blogs.php?action=add'); ?>" style="color:var(--secondary);">Add your first post.</a>
                </td></tr>
                <?php else: ?>
                <?php foreach($posts as $i => $post): ?>
                <tr>
                    <td style="color:var(--text-muted);"><?php echo $i+1; ?></td>
                    <td><strong><?php echo htmlspecialchars(mb_strimwidth($post['title'], 0, 70, '...')); ?></strong></td>
                    <td><?php echo htmlspecialchars($post['author']); ?></td>
                    <td><span class="badge-teal"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo adminUrl('blogs.php?action=edit&id=' . $post['id']); ?>" class="btn-action btn-edit"><i class="bi bi-pencil"></i> Edit</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                <button type="submit" class="btn-action btn-delete confirm-delete"><i class="bi bi-trash"></i> Delete</button>
                            </form>
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
