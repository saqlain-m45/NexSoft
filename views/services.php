<!-- ================================================================
   SERVICES PAGE VIEW — NexSoft Hub
================================================================ -->

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-breadcrumb reveal">
            <a href="<?php echo baseUrl(); ?>">Home</a>
            <span><i class="bi bi-chevron-right"></i></span>
            <span class="active">Services</span>
        </div>
        <h1 class="page-header-title reveal">Our Services</h1>
        <p class="page-header-subtitle reveal">End-to-end digital solutions crafted with precision and passion.</p>
    </div>
</section>

<!-- Services Grid -->
<section class="services-page-section">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-7 reveal">
                <span class="section-tag">What We Offer</span>
                <h2 class="section-title">Comprehensive <span>Digital Solutions</span></h2>
                <p class="section-subtitle mx-auto">Every service is delivered with cutting-edge tools, a dedicated team, and a relentless commitment to excellence.</p>
            </div>
        </div>
        <div class="row g-4">
            <?php
            $services = [
                [
                    'icon'     => 'bi-globe2',
                    'title'    => 'Web Development',
                    'desc'     => 'We build powerful, high-performance web applications using the latest frameworks and best practices. From corporate websites to complex SaaS platforms, we engineer digital experiences that drive results.',
                    'features' => ['React, Next.js, Vue.js frontends','PHP, Node.js, Python backends','RESTful API architecture','MySQL, PostgreSQL, MongoDB','Cloud deployment (AWS, GCP, Vercel)','Performance & SEO optimization'],
                    'tags'     => ['React','PHP','Node.js','MySQL'],
                ],
                [
                    'icon'     => 'bi-phone',
                    'title'    => 'App Development',
                    'desc'     => 'Native and cross-platform mobile applications for iOS and Android built with Flutter and React Native. We create intuitive, feature-rich apps that users love and businesses depend on.',
                    'features' => ['Flutter & React Native cross-platform','Native iOS (Swift) & Android (Kotlin)','Offline-first architecture','Push notification systems','In-app purchase integration','4.5+ App Store ratings target'],
                    'tags'     => ['Flutter','React Native','iOS','Android'],
                ],
                [
                    'icon'     => 'bi-wordpress',
                    'title'    => 'WordPress Development',
                    'desc'     => 'Custom WordPress development that goes beyond themes. We build highly optimized, secure, and scalable WordPress solutions including WooCommerce stores, membership sites, and complex CMS platforms.',
                    'features' => ['Custom theme development','Plugin architecture & development','WooCommerce e-commerce','Gutenberg block development','Performance optimization','Security hardening & backups'],
                    'tags'     => ['WordPress','WooCommerce','PHP','Gutenberg'],
                ],
                [
                    'icon'     => 'bi-palette2',
                    'title'    => 'UI/UX & Graphic Design',
                    'desc'     => 'Award-winning design that combines aesthetics with strategy. From initial wireframes to polished final designs, we create interfaces that are both visually stunning and conversion-optimized.',
                    'features' => ['User research & persona development','Wireframing & prototyping','Design system creation','Figma / Adobe XD designs','Brand identity & logo design','Usability testing & iteration'],
                    'tags'     => ['Figma','Adobe XD','Illustrator','Prototyping'],
                ],
                [
                    'icon'     => 'bi-pen',
                    'title'    => 'Content Writing',
                    'desc'     => 'Strategic content that ranks in search engines and resonates with your audience. Our writers combine SEO expertise with compelling storytelling to produce content that drives traffic and converts visitors.',
                    'features' => ['SEO-optimized blog content','Website copywriting','Technical writing & documentation','Social media content calendars','Email marketing copy','Product descriptions & case studies'],
                    'tags'     => ['SEO','Copywriting','Blogging','Social Media'],
                ],
                [
                    'icon'     => 'bi-camera-video',
                    'title'    => 'Video Editing',
                    'desc'     => 'Professional video content that captivates audiences and communicates your brand message powerfully. From raw footage to polished final cuts, we handle every aspect of post-production.',
                    'features' => ['Corporate & promotional videos','YouTube & social media optimization','Motion graphics & animations','Color grading & sound design','Explainer video production','Testimonial & case study videos'],
                    'tags'     => ['Premiere Pro','After Effects','Color Grading','Motion'],
                ],
            ];
            foreach($services as $i => $svc): ?>
            <div class="col-md-6 col-lg-4 reveal" data-delay="<?php echo ($i % 3) * 100; ?>">
                <div class="service-page-card">
                    <div class="service-page-icon"><i class="bi <?php echo $svc['icon']; ?>"></i></div>
                    <h3 class="service-page-title"><?php echo $svc['title']; ?></h3>
                    <p class="service-page-desc"><?php echo $svc['desc']; ?></p>
                    <ul class="service-page-features mb-3">
                        <?php foreach($svc['features'] as $f): ?>
                        <li><i class="bi bi-check2-circle"></i><?php echo $f; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:auto;padding-top:1rem;border-top:1px solid var(--border);">
                        <?php foreach($svc['tags'] as $tag): ?>
                        <span style="background:rgba(14,165,164,0.08);color:var(--secondary);font-size:0.7rem;font-weight:700;padding:3px 10px;border-radius:50px;border:1px solid rgba(14,165,164,0.15);"><?php echo $tag; ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Process Section -->
<section style="padding: 100px 0; background: var(--bg);">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-6 reveal">
                <span class="section-tag">How We Work</span>
                <h2 class="section-title">Our <span>Delivery Process</span></h2>
                <p class="section-subtitle mx-auto">A proven, transparent process that ensures quality delivery every time.</p>
            </div>
        </div>
        <div class="row g-4">
            <?php
            $steps = [
                ['num'=>'01','icon'=>'bi-search',        'title'=>'Discovery & Strategy',   'text'=>'We dig deep into your business goals, target audience, and competitive landscape to build a winning digital strategy.'],
                ['num'=>'02','icon'=>'bi-palette',        'title'=>'Design & Prototype',     'text'=>'Our designers create stunning wireframes and high-fidelity prototypes for your review before a single line of code is written.'],
                ['num'=>'03','icon'=>'bi-code-slash',     'title'=>'Development & Testing',  'text'=>'Agile sprints, continuous integration, and rigorous QA testing ensure a bug-free, performant final product.'],
                ['num'=>'04','icon'=>'bi-rocket-takeoff', 'title'=>'Launch & Support',       'text'=>'We deploy to production, monitor performance, and provide ongoing support to ensure long-term success.'],
            ];
            foreach($steps as $i => $step): ?>
            <div class="col-md-6 col-lg-3 reveal" data-delay="<?php echo $i * 100; ?>">
                <div style="text-align:center;padding:2rem 1.5rem;">
                    <div style="position:relative;display:inline-block;margin-bottom:1.5rem;">
                        <div style="width:80px;height:80px;background:linear-gradient(135deg,var(--secondary),var(--secondary-dark));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.8rem;color:white;margin:0 auto;">
                            <i class="bi <?php echo $step['icon']; ?>"></i>
                        </div>
                        <span style="position:absolute;top:-8px;right:-8px;background:var(--primary);color:white;font-size:0.65rem;font-weight:800;padding:3px 7px;border-radius:50px;"><?php echo $step['num']; ?></span>
                    </div>
                    <h4 style="font-size:1.05rem;font-weight:700;color:var(--primary);margin-bottom:0.6rem;"><?php echo $step['title']; ?></h4>
                    <p style="font-size:0.88rem;color:var(--text-muted);line-height:1.7;"><?php echo $step['text']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center reveal">
                <h2 class="section-title mb-4">Which Service Do You Need?</h2>
                <p class="mb-4">Let's talk about your project. We'll recommend the right mix of services to achieve your goals.</p>
                <a href="<?php echo baseUrl('?route=contact'); ?>" class="btn-cta-white">
                    <i class="bi bi-chat-dots-fill"></i> Start the Conversation
                </a>
            </div>
        </div>
    </div>
</section>
