<!-- ================================================================
   ABOUT US PAGE VIEW — NexSoft Hub
================================================================ -->

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-breadcrumb reveal">
            <a href="<?php echo baseUrl(); ?>">Home</a>
            <span><i class="bi bi-chevron-right"></i></span>
            <span class="active">About Us</span>
        </div>
        <h1 class="page-header-title reveal">About NexSoft Hub</h1>
        <p class="page-header-subtitle reveal">The story behind the agency transforming businesses through technology.</p>
    </div>
</section>

<!-- Intro Section -->
<section style="padding: 100px 0; background: var(--bg);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal">
                <span class="section-tag">Who We Are</span>
                <h2 class="section-title">A Team of <span>Digital Pioneers</span></h2>
                <p style="color:var(--text-muted); font-weight:300; font-size:1.05rem; line-height:1.85; margin-bottom:1.5rem;">
                    NexSoft Hub is a full-service software consulting agency founded by a team of passionate developers, designers, and strategists who believe technology should be accessible, powerful, and beautifully crafted for every business.
                </p>
                <p style="color:var(--text-muted); font-weight:300; font-size:1rem; line-height:1.85; margin-bottom:2rem;">
                    Since our founding, we have partnered with startups, SMEs, and enterprises across 30+ industries — helping them launch faster, scale smarter, and stand out in an increasingly digital world. Every line of code we write, every pixel we design, and every strategy we craft is done with one goal: <strong style="color:var(--primary);">your success.</strong>
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?php echo baseUrl('?route=contact'); ?>" class="btn-hero-primary" style="display:inline-flex;">
                        <i class="bi bi-chat-dots"></i> Work With Us
                    </a>
                    <a href="<?php echo baseUrl('?route=services'); ?>" class="btn-hero-outline" style="display:inline-flex;border-color:var(--secondary);color:var(--secondary);">
                        <i class="bi bi-grid"></i> Our Services
                    </a>
                </div>
            </div>
            <div class="col-lg-6 reveal">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;">
                    <?php
                    $stats = [
                        ['icon'=>'bi-briefcase-fill','num'=>'200+','label'=>'Projects Delivered'],
                        ['icon'=>'bi-people-fill','num'=>'50+','label'=>'Happy Clients'],
                        ['icon'=>'bi-globe2','num'=>'15+','label'=>'Countries Served'],
                        ['icon'=>'bi-star-fill','num'=>'5.0','label'=>'Average Rating'],
                    ];
                    foreach($stats as $s): ?>
                    <div style="background:var(--bg-alt);border:1px solid var(--border);border-radius:var(--radius);padding:2rem 1.5rem;text-align:center;">
                        <i class="bi <?php echo $s['icon']; ?>" style="font-size:2rem;color:var(--secondary);margin-bottom:0.75rem;display:block;"></i>
                        <div style="font-size:2rem;font-weight:900;color:var(--primary);"><?php echo $s['num']; ?></div>
                        <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-top:4px;font-weight:600;"><?php echo $s['label']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section style="padding: 0 0 100px; background: var(--bg);">
    <div class="container">
        <div class="row g-4">
            <?php
            $mvs = [
                ['label'=>'Our Mission','title'=>'Empowering Businesses Through Technology','text'=>'Our mission is to deliver innovative, high-quality software solutions that empower businesses to reach their full potential. We combine cutting-edge technology with strategic thinking to create digital products that drive measurable growth, improve efficiency, and create lasting competitive advantages for every client we serve.','icon'=>'bi-rocket-takeoff-fill'],
                ['label'=>'Our Vision','title'=>'Becoming the Global Leader in Digital Transformation','text'=>'We envision a world where every business — regardless of size or location — has access to world-class software development expertise. Our vision is to be the most trusted and innovative software consulting agency globally, known for exceptional quality, transparent processes, and transformative results that genuinely improve lives.','icon'=>'bi-eye-fill'],
            ];
            foreach($mvs as $mv): ?>
            <div class="col-lg-6 reveal">
                <div class="about-mission-card">
                    <h4><i class="bi <?php echo $mv['icon']; ?> me-2"></i><?php echo $mv['label']; ?></h4>
                    <h3><?php echo $mv['title']; ?></h3>
                    <p><?php echo $mv['text']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════ TEAM ORG TREE ═══════════════════════════════ -->
<section class="team-tree-section">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-6 reveal">
                <span class="section-tag">The People Behind NexSoft</span>
                <h2 class="section-title">Meet Our <span>Team</span></h2>
                <p class="section-subtitle mx-auto">Talented specialists united by a shared passion for building exceptional digital experiences.</p>
            </div>
        </div>

        <?php if (empty($teamMembers)): ?>
        <div class="text-center py-5 reveal">
            <div style="width:80px;height:80px;background:rgba(14,165,164,0.08);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;border:2px dashed rgba(14,165,164,0.25);">
                <i class="bi bi-people" style="font-size:2rem;color:var(--secondary);opacity:0.5;"></i>
            </div>
            <h4 style="color:var(--primary);margin-bottom:0.4rem;">Team Coming Soon</h4>
            <p style="color:var(--text-muted);font-size:0.9rem;">Our team directory is being set up. Check back soon!</p>
        </div>

        <?php else:
            $root   = [$teamMembers[0]];
            $rest   = array_slice($teamMembers, 1);
            $rows   = [$root];
            foreach (array_chunk($rest, 4) as $chunk) $rows[] = $chunk;
        ?>

        <div class="org-tree reveal">
            <?php foreach ($rows as $rowIdx => $row):
                $rowClass = $rowIdx === 0 ? 'org-row org-root' : 'org-row';
            ?>
            <?php if ($rowIdx > 0): ?>
            <div class="org-connector-v"></div>
            <?php if (count($row) > 1): ?>
            <div class="org-connector-h" style="--cols:<?php echo count($row); ?>;"></div>
            <?php endif; ?>
            <?php endif; ?>

            <div class="<?php echo $rowClass; ?>" style="--count:<?php echo count($row); ?>;">
                <?php foreach ($row as $member):
                    $parts    = explode(' ', trim($member['name']));
                    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                ?>
                <div class="org-node">
                    <div class="org-avatar-wrap" <?php if (!empty($member['bio'])): ?>title="<?php echo htmlspecialchars($member['bio']); ?>"<?php endif; ?>>
                        <?php if (!empty($member['photo'])): ?>
                        <img src="<?php echo baseUrl('assets/uploads/team/' . htmlspecialchars($member['photo'])); ?>"
                             alt="<?php echo htmlspecialchars($member['name']); ?>"
                             class="org-avatar-img"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="org-avatar-initials" style="display:none;"><?php echo $initials; ?></div>
                        <?php else: ?>
                        <div class="org-avatar-initials"><?php echo $initials; ?></div>
                        <?php endif; ?>
                        <?php if ($rowIdx === 0): ?>
                        <div class="org-root-ring"></div>
                        <?php endif; ?>
                        <?php if (!empty($member['bio'])): ?>
                        <div class="org-tooltip"><?php echo htmlspecialchars($member['bio']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="org-node-label">
                        <div class="org-node-name"><?php echo htmlspecialchars($member['name']); ?></div>
                        <div class="org-node-role"><?php echo htmlspecialchars($member['designation']); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>
</section>

<!-- Company Story -->
<section style="padding: 100px 0; background: var(--bg-alt);">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-6 reveal">
                <span class="section-tag">Our Story</span>
                <h2 class="section-title">How We <span>Started</span></h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8 reveal">
                <div style="position:relative;">
                    <?php
                    $story = [
                        ['year'=>'2019','title'=>'The Beginning','text'=>'NexSoft Hub was born in a small home office with a bold vision: to make enterprise-grade software accessible to businesses of all sizes. Two co-founders, a laptop, and an unwavering belief that great code changes the world.'],
                        ['year'=>'2020','title'=>'First 10 Clients','text'=>'Within our first year, we secured 10 anchor clients — ranging from local retail shops to regional e-commerce platforms. We bootstrapped through dedication, referrals, and a reputation for delivering on every promise.'],
                        ['year'=>'2021','title'=>'Team Expansion','text'=>'We grew from 2 to 15 specialists. Designers, backend engineers, mobile developers, and strategists joined our ranks. We moved to a proper office and launched our signature Agile delivery framework.'],
                        ['year'=>'2022','title'=>'International Growth','text'=>'NexSoft Hub went global — serving clients from the USA, UK, UAE, Australia, and Southeast Asia. We delivered our 100th project and achieved a 98% client satisfaction score.'],
                        ['year'=>'2024','title'=>'AI & Innovation Hub','text'=>'We launched our AI integration practice, helping clients embrace machine learning, automation, and intelligent analytics. Today, NexSoft Hub is recognized as a top-tier innovation partner for modern businesses.'],
                    ];
                    foreach($story as $i => $s): ?>
                    <div style="display:flex;gap:2rem;margin-bottom:2.5rem;align-items:flex-start;">
                        <div style="flex-shrink:0;text-align:center;">
                            <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--secondary),var(--secondary-dark));border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.75rem;"><?php echo $s['year']; ?></div>
                            <?php if($i < count($story)-1): ?><div style="width:2px;height:40px;background:var(--border);margin:8px auto;"></div><?php endif; ?>
                        </div>
                        <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;flex:1;">
                            <h4 style="font-size:1.1rem;color:var(--primary);margin-bottom:0.5rem;"><?php echo $s['title']; ?></h4>
                            <p style="font-size:0.9rem;color:var(--text-muted);line-height:1.75;margin:0;"><?php echo $s['text']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section style="padding: 100px 0; background: var(--bg);">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-6 reveal">
                <span class="section-tag">What We Stand For</span>
                <h2 class="section-title">Our Core <span>Values</span></h2>
            </div>
        </div>
        <div class="row g-4">
            <?php
            $values = [
                ['icon'=>'bi-gem','title'=>'Quality First','text'=>'We never ship code we are not proud of. Every project undergoes rigorous quality assurance before delivery.'],
                ['icon'=>'bi-transparency','title'=>'Full Transparency','text'=>'No surprises. You get weekly reports, open communication channels, and honest timelines from day one.'],
                ['icon'=>'bi-lightbulb-fill','title'=>'Innovation Driven','text'=>'We stay ahead of technological trends so our clients always get solutions built for tomorrow, not yesterday.'],
                ['icon'=>'bi-heart-fill','title'=>'Client Obsessed','text'=>'Your success is our success. We go above and beyond to ensure every engagement exceeds expectations.'],
                ['icon'=>'bi-people-fill','title'=>'Team Excellence','text'=>'Our team consists of senior specialists who bring both expertise and genuine passion to every project.'],
                ['icon'=>'bi-shield-fill-check','title'=>'Security Focused','text'=>'Every system we build is designed with security best practices, data privacy, and compliance at the core.'],
            ];
            foreach($values as $i => $v): ?>
            <div class="col-md-6 col-lg-4 reveal" data-delay="<?php echo ($i % 3) * 100; ?>">
                <div class="about-value-card">
                    <i class="bi <?php echo $v['icon']; ?> about-value-icon"></i>
                    <h4 class="about-value-title"><?php echo $v['title']; ?></h4>
                    <p class="about-value-text"><?php echo $v['text']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>



<!-- Why Work With Us CTA -->
<section class="cta-section">
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center reveal">
                <h2 class="section-title mb-4">Ready to Partner With the Best?</h2>
                <p class="mb-4">Let's discuss how NexSoft Hub can help your business achieve its digital goals. First consultation is always free.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="<?php echo baseUrl('?route=contact'); ?>" class="btn-cta-white">
                        <i class="bi bi-calendar-check"></i> Book Free Consultation
                    </a>
                    <a href="<?php echo baseUrl('?route=pricing'); ?>" class="btn-cta-outline-white">
                        <i class="bi bi-tag"></i> View Pricing
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
