<!-- ================================================================
   BLOG LISTING PAGE VIEW — NexSoft Hub
================================================================ -->

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-breadcrumb reveal">
            <a href="<?php echo baseUrl(); ?>">Home</a>
            <span><i class="bi bi-chevron-right"></i></span>
            <span class="active">Blog</span>
        </div>
        <h1 class="page-header-title reveal">Our Blog</h1>
        <p class="page-header-subtitle reveal">Insights, tutorials, and updates from the NexSoft Hub team.</p>
    </div>
</section>

<!-- Blog Listing -->
<section style="padding: 100px 0; background: var(--bg-alt);">
    <div class="container">
        <?php if (empty($blogs)): ?>
        <div class="text-center py-5 reveal">
            <i class="bi bi-journal-x" style="font-size:4rem;color:var(--secondary);opacity:0.3;"></i>
            <h3 style="color:var(--primary);margin-top:1rem;">No posts yet</h3>
            <p style="color:var(--text-muted);">Blog posts will appear here. Add them from the admin panel.</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach($blogs as $i => $blog): ?>
            <div class="col-md-6 col-lg-4 reveal" data-delay="<?php echo ($i % 3) * 80; ?>">
                <div class="blog-card">
                    <div class="blog-img-wrap">
                        <?php if (!empty($blog['featured_image']) && file_exists(__DIR__ . '/../assets/uploads/blogs/' . $blog['featured_image'])): ?>
                            <img src="<?php echo baseUrl('assets/uploads/blogs/' . htmlspecialchars($blog['featured_image'])); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                        <?php else: ?>
                            <div class="blog-img-placeholder"><i class="bi bi-journal-richtext"></i></div>
                        <?php endif; ?>
                        <span class="blog-category">Technology</span>
                    </div>
                    <div class="blog-body">
                        <div class="blog-meta">
                            <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($blog['author']); ?></span>
                            <span><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                        </div>
                        <h3 class="blog-title">
                            <a href="<?php echo baseUrl('blog-single?slug=' . urlencode($blog['slug'])); ?>" style="color:inherit;">
                                <?php echo htmlspecialchars($blog['title']); ?>
                            </a>
                        </h3>
                        <p class="blog-excerpt"><?php echo htmlspecialchars($blog['excerpt'] ?? ''); ?></p>
                        <a href="<?php echo baseUrl('blog-single?slug=' . urlencode($blog['slug'])); ?>" class="blog-read-more">
                            Read More <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-5 reveal">
            <nav aria-label="Blog pagination">
                <ul class="pagination pagination-nexsoft gap-1">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo baseUrl('blog?page=' . ($page - 1)); ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo baseUrl('blog?page=' . $p); ?>"><?php echo $p; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo baseUrl('blog?page=' . ($page + 1)); ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center reveal">
                <h2 class="section-title mb-4">Want Us to Write for Your Brand?</h2>
                <p class="mb-4">Our content writing team creates SEO-optimized, conversion-focused content that drives organic traffic.</p>
                <a href="<?php echo baseUrl('services'); ?>" class="btn-cta-white">
                    <i class="bi bi-pen-fill"></i> Explore Content Services
                </a>
            </div>
        </div>
    </div>
</section>
