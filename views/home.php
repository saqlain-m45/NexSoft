<!-- ================================================================
   HOME PAGE VIEW — NexSoft Hub
================================================================ -->

<!-- ===== HERO SECTION ===== -->
<section class="hero-section" id="home">
    <div class="hero-particles"></div>
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge">
                    <i class="bi bi-stars"></i>
                    Premium Software Consulting Agency
                </div>
                <h1 class="hero-headline">
                    We Build Digital<br>
                    <span class="text-accent">Experiences</span> That<br>
                    Drive <span class="text-outline">Growth</span>
                </h1>
                <p class="hero-description">
                    NexSoft Hub delivers world-class web applications, mobile apps, and digital solutions that transform your business vision into reality. Trusted by 200+ clients globally.
                </p>
                <div class="hero-cta">
                    <a href="<?php echo baseUrl('?route=contact'); ?>" class="btn-hero-primary">
                        <i class="bi bi-rocket-takeoff-fill"></i> Start Your Project
                    </a>
                    <a href="<?php echo baseUrl('?route=services'); ?>" class="btn-hero-outline">
                        <i class="bi bi-play-circle"></i> Explore Services
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="stat-number" data-count="200" data-suffix="+">200+</div>
                        <div class="stat-label">Projects Done</div>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <div class="stat-number" data-count="50" data-suffix="+">50+</div>
                        <div class="stat-label">Happy Clients</div>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <div class="stat-number" data-count="5" data-suffix="★">5★</div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-visual">
                    <div class="hero-card-stack">
                        <div class="hero-main-card">
                            <div class="hero-card-icon"><i class="bi bi-code-slash"></i></div>
                            <div class="hero-card-title">Full-Stack Development</div>
                            <div class="hero-card-desc">React, Node, PHP, Python, Flutter — we master every stack to deliver the best solution for your needs.</div>
                            <div style="margin-top: 1.5rem; display: flex; gap: 8px; flex-wrap: wrap;">
                                <?php foreach(['React','PHP','Flutter','Python','MySQL'] as $tech): ?>
                                <span style="background:rgba(14,165,164,0.15);color:#0EA5A4;font-size:0.7rem;font-weight:700;padding:4px 10px;border-radius:50px;border:1px solid rgba(14,165,164,0.2);"><?php echo $tech; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="hero-floating-badge hero-badge-1">
                            <i class="bi bi-check-circle-fill"></i> Project Delivered!
                        </div>
                        <div class="hero-floating-badge hero-badge-2">
                            <i class="bi bi-lightning-fill"></i> 3x Faster Launch
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== SERVICES OVERVIEW SECTION ===== -->
<section class="services-section">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-6 reveal">
                <span class="section-tag">What We Do</span>
                <h2 class="section-title">Our Core <span>Services</span></h2>
                <p class="section-subtitle mx-auto">From idea to launch, we cover every digital need your business requires to thrive online.</p>
            </div>
        </div>
        <div class="row g-4">
            <?php
            $services = [
                ['icon'=>'bi-globe2',         'title'=>'Web Development',    'desc'=>'Blazing-fast, scalable websites and web apps built with modern frameworks. From landing pages to enterprise portals.'],
                ['icon'=>'bi-phone',           'title'=>'App Development',    'desc'=>'Native and cross-platform mobile apps for iOS & Android with exceptional UX and performance using Flutter & React Native.'],
                ['icon'=>'bi-wordpress',       'title'=>'WordPress Dev',      'desc'=>'Custom WordPress themes, plugins, and e-commerce solutions via WooCommerce. Fully optimized and scalable.'],
                ['icon'=>'bi-palette2',        'title'=>'UI/UX & Design',     'desc'=>'Award-winning interface design that combines beauty and usability. Wireframes, prototypes, and design systems.'],
                ['icon'=>'bi-pen',             'title'=>'Content Writing',    'desc'=>'SEO-optimized, engaging content that resonates with your audience. Blog posts, copy, social media, and more.'],
                ['icon'=>'bi-camera-video',    'title'=>'Video Editing',      'desc'=>'Professional video production and editing for social media, ads, explainer videos, and corporate content.'],
            ];
            foreach($services as $i => $svc): ?>
            <div class="col-md-6 col-lg-4 reveal" data-delay="<?php echo $i * 80; ?>">
                <div class="service-card">
                    <div class="service-icon"><i class="bi <?php echo $svc['icon']; ?>"></i></div>
                    <h3 class="service-title"><?php echo $svc['title']; ?></h3>
                    <p class="service-desc"><?php echo $svc['desc']; ?></p>
                    <div class="service-arrow"><i class="bi bi-arrow-right"></i></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5 reveal">
            <a href="<?php echo baseUrl('?route=services'); ?>" class="btn-hero-primary" style="display:inline-flex;">
                <i class="bi bi-grid"></i> View All Services
            </a>
        </div>
    </div>
</section>

<!-- ===== WHY CHOOSE US ===== -->
<section class="why-us-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="reveal">
                    <span class="section-tag">Why NexSoft Hub</span>
                    <h2 class="section-title">The Agency That <span>Delivers</span> Results</h2>
                    <p class="section-subtitle mb-4">We don't just write code — we build solutions that grow your business. Here is what sets us apart.</p>
                </div>
                <div class="row g-3">
                    <?php
                    $features = [
                        ['icon'=>'bi-trophy-fill',    'title'=>'Proven Excellence',     'text'=>'200+ successful projects across 30+ industries. Our portfolio speaks for itself.'],
                        ['icon'=>'bi-shield-check',   'title'=>'Transparent Process',   'text'=>'Weekly progress reports, open communication, and zero hidden fees. Always.'],
                        ['icon'=>'bi-lightning-fill', 'title'=>'Agile Delivery',        'text'=>'Fast iteration cycles with continuous deployment. Go live 3x faster than traditional agencies.'],
                        ['icon'=>'bi-headset',        'title'=>'24/7 Support',          'text'=>'Round-the-clock technical support and dedicated account managers for every client.'],
                    ];
                    foreach($features as $i => $f): ?>
                    <div class="col-12 reveal" data-delay="<?php echo $i * 100; ?>">
                        <div class="feature-block">
                            <div class="feature-icon-wrap"><i class="bi <?php echo $f['icon']; ?>"></i></div>
                            <div>
                                <h4 class="feature-title"><?php echo $f['title']; ?></h4>
                                <p class="feature-text"><?php echo $f['text']; ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6 reveal">
                <div class="why-us-image-wrap">
                    <div class="why-us-image">
                        <div style="background:linear-gradient(135deg,#0B1F3B,#162d4f);padding:4rem 3rem;text-align:center;">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                                <?php
                                $stats = [
                                    ['200+','Projects Completed'],
                                    ['50+', 'Global Clients'],
                                    ['98%', 'Client Satisfaction'],
                                    ['5yr+', 'Industry Experience'],
                                ];
                                foreach($stats as $s): ?>
                                <div style="background:rgba(14,165,164,0.1);border:1px solid rgba(14,165,164,0.2);border-radius:16px;padding:1.5rem 1rem;text-align:center;">
                                    <div style="font-size:2rem;font-weight:900;color:#0EA5A4;"><?php echo $s[0]; ?></div>
                                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1px;margin-top:4px;font-weight:500;"><?php echo $s[1]; ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div style="margin-top:1.5rem;background:rgba(14,165,164,0.08);border:1px solid rgba(14,165,164,0.15);border-radius:16px;padding:1.5rem;">
                                <div style="font-size:0.9rem;color:rgba(255,255,255,0.7);font-style:italic;">"NexSoft Hub transformed our online presence. Sales up 340% in 6 months!"</div>
                                <div style="font-size:0.78rem;color:#0EA5A4;font-weight:600;margin-top:8px;">— Sarah Chen, CEO TechVenture</div>
                            </div>
                        </div>
                    </div>
                    <div class="why-us-badge">
                        <span class="why-badge-number">98%</span>
                        <span class="why-badge-text">Client Retention Rate</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== PREVIOUS PROJECTS ===== -->
<section class="projects-section">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-7 reveal">
                <span class="section-tag">Our Work</span>
                <h2 class="section-title">Previous <span>Projects</span></h2>
                <p class="section-subtitle mx-auto">A selection of work we are proud of — each project crafted with precision and purpose.</p>
            </div>
        </div>

        <?php $projectIcons = ['bi-cart4','bi-hospital','bi-house-heart','bi-bank','bi-mortarboard','bi-cup-hot']; ?>
        <?php if (empty($projects)): ?>
        <div class="text-center py-5 reveal">
            <i class="bi bi-folder2-open" style="font-size:4rem;color:var(--secondary);opacity:0.3;"></i>
            <p class="mt-3" style="color:var(--text-muted);">No projects added yet. Add your first project from the Admin panel.</p>
            <a href="<?php echo baseUrl('admin/'); ?>" class="btn-hero-primary mt-2" style="display:inline-flex;">Add Projects</a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach($projects as $i => $project): ?>
            <div class="col-md-6 col-lg-4 reveal" data-delay="<?php echo ($i % 3) * 100; ?>">
                <div class="project-card">
                    <div class="project-img-wrap">
                        <?php if (!empty($project['image']) && file_exists(__DIR__ . '/../assets/uploads/projects/' . $project['image'])): ?>
                            <img src="<?php echo baseUrl('assets/uploads/projects/' . htmlspecialchars($project['image'])); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                        <?php else: ?>
                            <div class="project-img-placeholder">
                                <i class="bi <?php echo $projectIcons[$i % count($projectIcons)]; ?>"></i>
                                <span>Project <?php echo $i+1; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($project['link'])): ?>
                        <div class="project-overlay">
                            <a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank" rel="noopener">
                                <i class="bi bi-box-arrow-up-right me-1"></i> View Live
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="project-body">
                        <h4 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h4>
                        <p class="project-desc"><?php echo htmlspecialchars(mb_strimwidth($project['description'], 0, 100, '...')); ?></p>
                    </div>
                    <div class="project-footer">
                        <span class="project-tag">Software</span>
                        <?php if (!empty($project['link'])): ?>
                        <a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank" rel="noopener" class="project-link">
                            View Project <i class="bi bi-arrow-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
<section class="testimonials-section">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-6 reveal">
                <span class="section-tag">Client Stories</span>
                <h2 class="section-title">What Our Clients <span>Say</span></h2>
                <p class="section-subtitle mx-auto" style="color:rgba(255,255,255,0.6);">Real results from real businesses who trusted NexSoft Hub.</p>
            </div>
        </div>
        <div class="row g-4">
            <?php
            $testimonials = [
                ['name'=>'Sarah Chen','role'=>'CEO, TechVenture Inc.','init'=>'SC','quote'=>'NexSoft Hub completely transformed our online presence. They delivered a world-class e-commerce platform in just 8 weeks. Our revenue increased by 340% in the first 6 months.','rating'=>5],
                ['name'=>'Marcus Johnson','role'=>'Founder, MediCare App','init'=>'MJ','quote'=>'The mobile app they built for us has over 50,000 active users and a 4.9-star rating on both stores. Their attention to UX detail is unmatched in the industry.','rating'=>5],
                ['name'=>'Aisha Patel','role'=>'Marketing Director, GrowthLab','init'=>'AP','quote'=>'Their content writing and SEO team helped us rank #1 for 15 competitive keywords within 4 months. The ROI has been phenomenal. Highly recommend!','rating'=>5],
                ['name'=>'David Park','role'=>'CTO, FinEdge Solutions','init'=>'DP','quote'=>'We have worked with many agencies before, but NexSoft Hub is in a different league. Transparent communication, clean code, and they always deliver on time.','rating'=>5],
                ['name'=>'Emma Rodriguez','role'=>'E-commerce Manager, StyleHub','init'=>'ER','quote'=>'Our WordPress WooCommerce store handles 10,000+ daily transactions flawlessly. The custom theme they built is both stunning and lightning fast.','rating'=>5],
                ['name'=>'James Liu','role'=>'Owner, FreshEats Restaurant','init'=>'JL','quote'=>'The restaurant management system saved us 20 hours per week and cut order errors by 95%. Best investment we have made for our business.','rating'=>5],
            ];
            foreach($testimonials as $i => $t): ?>
            <div class="col-md-6 col-lg-4 reveal" data-delay="<?php echo ($i % 3) * 100; ?>">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <?php for($s=0;$s<$t['rating'];$s++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                    </div>
                    <p class="testimonial-quote"><?php echo $t['quote']; ?></p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"><?php echo $t['init']; ?></div>
                        <div>
                            <div class="testimonial-name"><?php echo $t['name']; ?></div>
                            <div class="testimonial-role"><?php echo $t['role']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== PRICING PREVIEW ===== -->
<section class="pricing-section">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-6 reveal">
                <span class="section-tag">Transparent Pricing</span>
                <h2 class="section-title">Simple, <span>Flexible</span> Plans</h2>
                <p class="section-subtitle mx-auto">Choose the plan that fits your needs. No hidden charges, ever.</p>
            </div>
        </div>
        <div class="row g-4 justify-content-center align-items-center">
            <?php
            $plans = [
                ['name'=>'Starter','price'=>'499','period'=>'per project','featured'=>false,'badge'=>'','features'=>['1 Website / App','Up to 5 Pages','Basic SEO Setup','Mobile Responsive','2 Revisions','3 Months Support','Email Support'],'missing'=>['E-commerce','Custom Backend','Priority Queue']],
                ['name'=>'Premium','price'=>'1,299','period'=>'per project','featured'=>true,'badge'=>'Most Popular','features'=>['Up to 10 Pages','Advanced SEO','E-commerce Ready','Custom Backend','5 Revisions','6 Months Support','Priority Queue','Weekly Reports','Dedicated PM'],'missing'=>[]],
                ['name'=>'Business','price'=>'2,999','period'=>'per project','featured'=>false,'badge'=>'','features'=>['Unlimited Pages','Enterprise SEO','Full Custom App','API Integrations','Unlimited Revisions','12 Months Support','24/7 Priority Queue','Monthly Strategy Call','Dedicated Team'],'missing'=>[]],
            ];
            foreach($plans as $i => $plan): ?>
            <div class="col-md-6 col-lg-4 reveal" data-delay="<?php echo $i * 120; ?>">
                <div class="pricing-card <?php echo $plan['featured'] ? 'featured' : ''; ?>">
                    <?php if ($plan['badge']): ?><div class="pricing-badge"><?php echo $plan['badge']; ?></div><?php endif; ?>
                    <div class="pricing-plan-name"><?php echo $plan['name']; ?></div>
                    <div class="pricing-price"><sup>$</sup><?php echo $plan['price']; ?></div>
                    <p class="pricing-period"><?php echo $plan['period']; ?></p>
                    <hr class="pricing-divider">
                    <ul class="pricing-features">
                        <?php foreach($plan['features'] as $f): ?>
                        <li><i class="bi bi-check-circle-fill"></i> <?php echo $f; ?></li>
                        <?php endforeach; ?>
                        <?php foreach($plan['missing'] as $m): ?>
                        <li style="opacity:0.35;text-decoration:line-through;"><i class="bi bi-x-circle" style="color:var(--text-light)!important;"></i> <?php echo $m; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo baseUrl('?route=contact'); ?>" class="btn-pricing">Get Started</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4 reveal">
            <p style="color:var(--text-muted);font-size:0.9rem;">Need a custom quote? <a href="<?php echo baseUrl('?route=contact'); ?>" style="color:var(--secondary);font-weight:600;">Contact us</a> for enterprise pricing.</p>
        </div>
    </div>
</section>

<!-- ===== BLOG PREVIEW ===== -->
<?php if (!empty($blogs)): ?>
<section class="blog-section">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-6 reveal">
                <span class="section-tag">Latest Insights</span>
                <h2 class="section-title">From Our <span>Blog</span></h2>
                <p class="section-subtitle mx-auto">Expert perspectives on tech, design, and digital strategy.</p>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach($blogs as $i => $blog): ?>
            <div class="col-md-6 col-lg-4 reveal" data-delay="<?php echo $i * 100; ?>">
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
                        <h3 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h3>
                        <p class="blog-excerpt"><?php echo htmlspecialchars($blog['excerpt'] ?? ''); ?></p>
                        <a href="<?php echo baseUrl('?route=blog-single&slug=' . urlencode($blog['slug'])); ?>" class="blog-read-more">
                            Read More <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5 reveal">
            <a href="<?php echo baseUrl('?route=blog'); ?>" class="btn-hero-primary" style="display:inline-flex;">
                <i class="bi bi-journal-richtext"></i> View All Articles
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== CTA SECTION ===== -->
<section class="cta-section">
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center reveal">
                <span class="section-tag" style="background:rgba(255,255,255,0.15);border-color:rgba(255,255,255,0.3);color:white;">Let's Build Something Amazing</span>
                <h2 class="section-title mt-3 mb-4">Ready to Transform <br>Your Business Digitally?</h2>
                <p class="mb-4">Join 200+ businesses that trust NexSoft Hub. Get your free consultation today — no obligation, no pressure.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="<?php echo baseUrl('?route=contact'); ?>" class="btn-cta-white">
                        <i class="bi bi-chat-dots-fill"></i> Get Free Consultation
                    </a>
                    <a href="<?php echo baseUrl('?route=register'); ?>" class="btn-cta-outline-white">
                        <i class="bi bi-person-plus"></i> Join Our Team
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
