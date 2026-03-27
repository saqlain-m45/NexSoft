<!-- ================================================================
   PRICING PAGE VIEW — NexSoft Hub
================================================================ -->

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-breadcrumb reveal">
            <a href="<?php echo baseUrl(); ?>">Home</a>
            <span><i class="bi bi-chevron-right"></i></span>
            <span class="active">Pricing</span>
        </div>
        <h1 class="page-header-title reveal">Transparent Pricing</h1>
        <p class="page-header-subtitle reveal">Simple, honest plans for every stage of your business journey.</p>
    </div>
</section>

<!-- Pricing Plans -->
<section class="pricing-page-section">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-6 reveal">
                <span class="section-tag">Choose Your Plan</span>
                <h2 class="section-title">Flexible Plans for <span>Every Need</span></h2>
                <p class="section-subtitle mx-auto">All plans include access to our senior developers, dedicated project manager, and 100% satisfaction guarantee.</p>
            </div>
        </div>
        <div class="row g-4 justify-content-center align-items-stretch">
            <?php
            $plans = [
                [
                    'name'=>'Starter','price'=>'499','period'=>'per project','featured'=>false,'badge'=>'',
                    'desc'=>'Perfect for small businesses and startups launching their first digital product.',
                    'features'=>[
                        '1 Website or Landing Page',
                        'Up to 5 Pages',
                        'Mobile Responsive Design',
                        'Contact Form Integration',
                        'Basic SEO Setup',
                        'Google Analytics Setup',
                        '2 Design Revisions',
                        '3 Months Free Support',
                        'Email Support (48h response)',
                    ],
                    'missing'=>['E-commerce Functionality','Custom Backend/Database','API Integrations','Priority Queue'],
                ],
                [
                    'name'=>'Premium','price'=>'1,299','period'=>'per project','featured'=>true,'badge'=>'Most Popular',
                    'desc'=>'Ideal for growing businesses that need a full-featured web solution.',
                    'features'=>[
                        'Up to 10 Pages',
                        'E-commerce Functionality',
                        'Custom Backend & Database',
                        'User Authentication System',
                        'Advanced SEO Optimization',
                        'Blog/CMS Integration',
                        '5 Design Revisions',
                        '6 Months Free Support',
                        'Priority Queue',
                        'Weekly Progress Reports',
                        'Dedicated Project Manager',
                    ],
                    'missing'=>[],
                ],
                [
                    'name'=>'Business','price'=>'2,999','period'=>'per project','featured'=>false,'badge'=>'Best Value',
                    'desc'=>'Enterprise-grade solutions for complex, scalable business requirements.',
                    'features'=>[
                        'Unlimited Pages',
                        'Full Custom Web/Mobile App',
                        'Advanced API Integrations',
                        'Multi-role User Management',
                        'Payment Gateway (Stripe/PayPal)',
                        'Real-time Features (WebSockets)',
                        'Unlimited Revisions',
                        '12 Months Free Support',
                        '24/7 Priority Queue',
                        'Monthly Strategy Call',
                        'Dedicated Development Team',
                        'Source Code Ownership',
                    ],
                    'missing'=>[],
                ],
            ];
            foreach($plans as $i => $plan): ?>
            <div class="col-md-6 col-lg-4 reveal" data-delay="<?php echo $i * 120; ?>">
                <div class="pricing-card <?php echo $plan['featured'] ? 'featured' : ''; ?>" style="height:100%;">
                    <?php if ($plan['badge']): ?><div class="pricing-badge"><?php echo $plan['badge']; ?></div><?php endif; ?>
                    <div class="pricing-plan-name"><?php echo $plan['name']; ?></div>
                    <div class="pricing-price"><sup>$</sup><?php echo $plan['price']; ?></div>
                    <p class="pricing-period"><?php echo $plan['period']; ?></p>
                    <p style="font-size:0.85rem;<?php echo $plan['featured'] ? 'color:rgba(255,255,255,0.65);' : 'color:var(--text-muted);'; ?>margin-bottom:1.5rem;line-height:1.6;"><?php echo $plan['desc']; ?></p>
                    <hr class="pricing-divider">
                    <ul class="pricing-features">
                        <?php foreach($plan['features'] as $f): ?>
                        <li><i class="bi bi-check-circle-fill"></i> <?php echo $f; ?></li>
                        <?php endforeach; ?>
                        <?php foreach($plan['missing'] as $m): ?>
                        <li style="opacity:0.35;text-decoration:line-through;"><i class="bi bi-x-circle" style="color:var(--text-light)!important;"></i> <?php echo $m; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo baseUrl('contact'); ?>" class="btn-pricing mt-auto">Get Started</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Note -->
        <div class="text-center mt-4 reveal">
            <p style="color:var(--text-muted);font-size:0.88rem;">
                <i class="bi bi-info-circle me-1" style="color:var(--secondary);"></i>
                All prices are project-based estimates. Final pricing is determined after a free discovery call.
                <a href="<?php echo baseUrl('contact'); ?>" style="color:var(--secondary);font-weight:600;">Contact us</a> for a custom quote.
            </p>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section style="padding: 100px 0; background: var(--bg);">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-6 reveal">
                <span class="section-tag">Common Questions</span>
                <h2 class="section-title">Frequently Asked <span>Questions</span></h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8 reveal">
                <div class="accordion" id="pricingFAQ">
                    <?php
                    $faqs = [
                        ['q'=>'Do you offer payment plans?','a'=>'Yes! We offer flexible payment schedules: typically 40% upfront, 30% at design approval, and 30% at launch. For larger projects, we can split into monthly installments.'],
                        ['q'=>'What is included in the "support" period?','a'=>'Support includes bug fixes, security patches, minor content updates, and technical assistance via email and chat. It does not include new features or redesigns.'],
                        ['q'=>'Can I upgrade my plan later?','a'=>'Absolutely. You can start with the Starter plan and upgrade to Premium or Business at any time. We\'ll apply a pro-rated credit toward the upgrade cost.'],
                        ['q'=>'Do you sign NDAs and contracts?','a'=>'Yes. Every engagement includes a comprehensive project agreement covering scope, timelines, intellectual property (you own the code), confidentiality, and warranties.'],
                        ['q'=>'How long does a typical project take?','a'=>'Starter projects take 2-4 weeks, Premium projects 4-8 weeks, and Business projects 8-20 weeks depending on complexity. We always provide a detailed timeline in the discovery phase.'],
                        ['q'=>'What technologies do you use?','a'=>'We use modern, proven technologies: React, Next.js, Vue.js (frontend), PHP/Laravel, Node.js, Python/Django (backend), Flutter/React Native (mobile), MySQL, PostgreSQL, MongoDB (databases), and AWS/GCP/Vercel (hosting).'],
                    ];
                    foreach($faqs as $i => $faq): ?>
                    <div class="accordion-item" style="border:1px solid var(--border);border-radius:var(--radius-sm) !important;margin-bottom:0.75rem;overflow:hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?php echo $i > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo $i; ?>"
                                style="font-family:var(--font);font-weight:600;font-size:0.95rem;color:var(--primary);">
                                <?php echo $faq['q']; ?>
                            </button>
                        </h2>
                        <div id="faq<?php echo $i; ?>" class="accordion-collapse collapse <?php echo $i === 0 ? 'show' : ''; ?>" data-bs-parent="#pricingFAQ">
                            <div class="accordion-body" style="font-size:0.92rem;color:var(--text-muted);font-weight:300;line-height:1.75;">
                                <?php echo $faq['a']; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center reveal">
                <h2 class="section-title mb-4">Not Sure Which Plan to Choose?</h2>
                <p class="mb-4">Book a free 30-minute discovery call and we'll help you pick the perfect plan for your goals and budget.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="<?php echo baseUrl('contact'); ?>" class="btn-cta-white">
                        <i class="bi bi-calendar-check"></i> Book Free Discovery Call
                    </a>
                    <a href="<?php echo baseUrl('register'); ?>" class="btn-cta-outline-white">
                        <i class="bi bi-person-plus"></i> Join Our Team
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
