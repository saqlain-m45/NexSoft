<!-- ================================================================
   BLOG SINGLE POST VIEW — NexSoft Hub
================================================================ -->

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-breadcrumb reveal">
            <a href="<?php echo baseUrl(); ?>">Home</a>
            <span><i class="bi bi-chevron-right"></i></span>
            <a href="<?php echo baseUrl('?route=blog'); ?>">Blog</a>
            <span><i class="bi bi-chevron-right"></i></span>
            <span class="active"><?php echo mb_strimwidth(htmlspecialchars($post['title']), 0, 35, '...'); ?></span>
        </div>
        <h1 class="page-header-title reveal" style="font-size:clamp(1.8rem,4vw,3rem);max-width:800px;margin:0 auto;">
            <?php echo htmlspecialchars($post['title']); ?>
        </h1>
        <div class="blog-meta justify-content-center mt-3 reveal" style="display:flex;gap:1.5rem;flex-wrap:wrap;">
            <span style="color:rgba(255,255,255,0.6);font-size:0.85rem;display:flex;align-items:center;gap:5px;">
                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($post['author']); ?>
            </span>
            <span style="color:rgba(255,255,255,0.6);font-size:0.85rem;display:flex;align-items:center;gap:5px;">
                <i class="bi bi-calendar3"></i> <?php echo date('F d, Y', strtotime($post['created_at'])); ?>
            </span>
            <span style="background:rgba(14,165,164,0.2);color:var(--secondary);font-size:0.75rem;font-weight:700;padding:4px 14px;border-radius:50px;">Technology</span>
        </div>
    </div>
</section>

<!-- Blog Content -->
<section style="padding: 80px 0 100px; background: var(--bg);">
    <div class="container">
        <div class="row g-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Featured Image -->
                <?php if (!empty($post['featured_image']) && file_exists(__DIR__ . '/../assets/uploads/blogs/' . $post['featured_image'])): ?>
                <div style="border-radius: var(--radius); overflow: hidden; margin-bottom: 2.5rem; box-shadow: var(--shadow-lg);">
                    <img src="<?php echo baseUrl('assets/uploads/blogs/' . htmlspecialchars($post['featured_image'])); ?>"
                         alt="<?php echo htmlspecialchars($post['title']); ?>"
                         style="width:100%;display:block;">
                </div>
                <?php endif; ?>

                <!-- Article Content -->
                <article class="blog-single-content reveal">
                    <?php echo $post['content']; // Content from DB, already HTML ?>
                </article>

                <!-- Tags -->
                <div style="margin-top: 2.5rem; padding: 1.5rem; background: var(--bg-alt); border-radius: var(--radius); border: 1px solid var(--border);">
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        <span style="font-size:0.85rem;font-weight:700;color:var(--primary);">Tags:</span>
                        <?php foreach(['Technology','Development','Digital','NexSoft'] as $tag): ?>
                        <a href="<?php echo baseUrl('?route=blog'); ?>" style="background:rgba(14,165,164,0.1);color:var(--secondary);font-size:0.78rem;font-weight:600;padding:5px 14px;border-radius:50px;border:1px solid rgba(14,165,164,0.2);transition:var(--transition);"><?php echo $tag; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Author Card -->
                <div style="margin-top: 2rem; background: linear-gradient(135deg, var(--primary), var(--primary-light)); border-radius: var(--radius); padding: 2rem; display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
                    <div style="width:64px;height:64px;background:linear-gradient(135deg,var(--secondary),var(--secondary-dark));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:white;flex-shrink:0;">
                        <?php echo strtoupper(substr($post['author'], 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:1px;color:var(--secondary);font-weight:600;margin-bottom:4px;">Written by</div>
                        <div style="font-size:1.1rem;font-weight:700;color:white;margin-bottom:4px;"><?php echo htmlspecialchars($post['author']); ?></div>
                        <p style="font-size:0.85rem;color:rgba(255,255,255,0.6);margin:0;">Senior Writer & Digital Strategist at NexSoft Hub</p>
                    </div>
                </div>

                <!-- Back to Blog -->
                <div style="margin-top: 2rem;">
                    <a href="<?php echo baseUrl('?route=blog'); ?>" class="btn-hero-outline" style="display:inline-flex;border-color:var(--secondary);color:var(--secondary);">
                        <i class="bi bi-arrow-left"></i> Back to Blog
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Recent Posts -->
                <div class="blog-sidebar-card reveal">
                    <h4 class="blog-sidebar-title">Recent Posts</h4>
                    <?php if (empty($recentPosts)): ?>
                    <p style="color:var(--text-muted);font-size:0.85rem;">No other posts yet.</p>
                    <?php else: ?>
                    <?php foreach($recentPosts as $rp): ?>
                    <div class="recent-post-item">
                        <div class="recent-post-icon"><i class="bi bi-journal-text"></i></div>
                        <div>
                            <a href="<?php echo baseUrl('?route=blog-single&slug=' . urlencode($rp['slug'])); ?>" class="rp-title">
                                <?php echo htmlspecialchars(mb_strimwidth($rp['title'], 0, 60, '...')); ?>
                            </a>
                            <div class="rp-date"><?php echo date('M d, Y', strtotime($rp['created_at'])); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- CTA Card -->
                <div class="blog-sidebar-card reveal">
                    <div style="background:linear-gradient(135deg,var(--secondary),var(--secondary-dark));border-radius:var(--radius-sm);padding:2rem;text-align:center;">
                        <i class="bi bi-rocket-takeoff-fill" style="font-size:2.5rem;color:white;margin-bottom:1rem;display:block;"></i>
                        <h5 style="color:white;font-weight:700;margin-bottom:0.5rem;">Ready to Start?</h5>
                        <p style="color:rgba(255,255,255,0.8);font-size:0.85rem;margin-bottom:1.2rem;">Let's build something amazing together.</p>
                        <a href="<?php echo baseUrl('?route=contact'); ?>" style="display:block;background:white;color:var(--secondary);border-radius:50px;padding:0.7rem;font-weight:700;font-size:0.88rem;transition:var(--transition);">
                            Get Free Consultation
                        </a>
                    </div>
                </div>

                <!-- Services Widget -->
                <div class="blog-sidebar-card reveal">
                    <h4 class="blog-sidebar-title">Our Services</h4>
                    <?php
                    $sideServices = [
                        ['icon'=>'bi-globe2','name'=>'Web Development'],
                        ['icon'=>'bi-phone','name'=>'App Development'],
                        ['icon'=>'bi-palette2','name'=>'UI/UX Design'],
                        ['icon'=>'bi-pen','name'=>'Content Writing'],
                        ['icon'=>'bi-camera-video','name'=>'Video Editing'],
                    ];
                    foreach($sideServices as $ss): ?>
                    <a href="<?php echo baseUrl('?route=services'); ?>" style="display:flex;align-items:center;gap:10px;padding:0.6rem 0;border-bottom:1px solid var(--border);color:var(--text);font-size:0.88rem;font-weight:500;transition:var(--transition);">
                        <i class="bi <?php echo $ss['icon']; ?>" style="color:var(--secondary);"></i>
                        <?php echo $ss['name']; ?>
                        <i class="bi bi-arrow-right ms-auto" style="color:var(--text-light);font-size:0.75rem;"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
