<?php
require_once __DIR__ . '/../config/database.php';

class BlogController {
    private int $perPage = 6;

    public function index(): void {
        $db = getDB();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $this->perPage;

        $totalStmt = $db->query("SELECT COUNT(*) FROM blog_posts");
        $total = (int)$totalStmt->fetchColumn();
        $totalPages = (int)ceil($total / $this->perPage);

        $stmt = $db->prepare("SELECT id, title, slug, excerpt, featured_image, author, created_at FROM blog_posts ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $this->perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $blogs = $stmt->fetchAll();

        $pageTitle = 'Blog — NexSoft Hub';
        $metaDescription = 'Read the latest insights on web development, digital marketing, UI/UX, and technology from the NexSoft Hub team.';

        require_once __DIR__ . '/../components/header.php';
        require_once __DIR__ . '/../views/blog.php';
        require_once __DIR__ . '/../components/footer.php';
    }

    public function single(): void {
        $db = getDB();
        $slug = sanitize($_GET['slug'] ?? '');

        if (empty($slug)) {
            redirect('blog');
        }

        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ?");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();

        if (!$post) {
            http_response_code(404);
            require_once __DIR__ . '/../views/404.php';
            return;
        }

        // Recent posts for sidebar
        $stmt2 = $db->prepare("SELECT id, title, slug, created_at FROM blog_posts WHERE id != ? ORDER BY created_at DESC LIMIT 4");
        $stmt2->execute([$post['id']]);
        $recentPosts = $stmt2->fetchAll();

        $pageTitle = htmlspecialchars($post['title']) . ' — NexSoft Hub';
        $metaDescription = $post['excerpt'] ?? '';

        require_once __DIR__ . '/../components/header.php';
        require_once __DIR__ . '/../views/blog-single.php';
        require_once __DIR__ . '/../components/footer.php';
    }
}
