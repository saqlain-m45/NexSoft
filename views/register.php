<!-- ================================================================
   REGISTER / JOIN US PAGE VIEW — NexSoft Hub
================================================================ -->

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-breadcrumb reveal">
            <a href="<?php echo baseUrl(); ?>">Home</a>
            <span><i class="bi bi-chevron-right"></i></span>
            <span class="active">Join Our Team</span>
        </div>
        <h1 class="page-header-title reveal">Join NexSoft Hub</h1>
        <p class="page-header-subtitle reveal">Are you a talented developer, designer, or creative? Apply to collaborate with us.</p>
    </div>
</section>

<!-- Register Section -->
<section class="register-section">
    <div class="container">
        <div class="row g-5 align-items-stretch">
            <!-- Info Sidebar -->
            <div class="col-lg-4 reveal">
                <div class="register-info">
                    <h3>Why Work With Us?</h3>
                    <p>Join a growing network of top-tier freelancers and specialists who collaborate with NexSoft Hub on exciting global projects.</p>

                    <div style="margin-top: 2rem;">
                        <?php
                        $perks = [
                            ['icon'=>'bi-cash-stack','text'=>'Competitive project-based compensation'],
                            ['icon'=>'bi-globe','text'=>'Work with international clients'],
                            ['icon'=>'bi-clock','text'=>'Flexible remote work hours'],
                            ['icon'=>'bi-graph-up-arrow','text'=>'Career growth & upskilling'],
                            ['icon'=>'bi-people','text'=>'Collaborative, supportive team'],
                            ['icon'=>'bi-award','text'=>'Build an impressive portfolio'],
                        ];
                        foreach($perks as $perk): ?>
                        <div class="register-perk">
                            <i class="bi <?php echo $perk['icon']; ?>"></i>
                            <span><?php echo $perk['text']; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin-top:2rem;background:rgba(14,165,164,0.1);border:1px solid rgba(14,165,164,0.2);border-radius:var(--radius-sm);padding:1.2rem;text-align:center;">
                        <i class="bi bi-envelope-paper-fill" style="font-size:1.8rem;color:var(--secondary);display:block;margin-bottom:0.5rem;"></i>
                        <p style="color:rgba(255,255,255,0.7);font-size:0.85rem;margin:0;">Questions? Email us at<br><strong style="color:var(--secondary);">careers@nexsofthub.com</strong></p>
                    </div>
                </div>
            </div>

            <!-- Application Form -->
            <div class="col-lg-8 reveal">
                <div class="register-card">
                    <h3 style="color:var(--primary);margin-bottom:0.4rem;">Submit Your Application</h3>
                    <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:2rem;">We review every application and get back to suitable candidates within 3-5 business days.</p>

                    <?php if (!empty($success)): ?>
                    <div class="alert-success-custom mb-3">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                    <div class="alert-error-custom mb-3">
                        <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo baseUrl('register'); ?>" class="nexsoft-form" id="registerForm" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="reg_name" class="form-label">Full Name <span style="color:var(--secondary);">*</span></label>
                                <input type="text" class="form-control" id="reg_name" name="name"
                                       placeholder="John Smith" required
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="reg_email" class="form-label">Email Address <span style="color:var(--secondary);">*</span></label>
                                <input type="email" class="form-control" id="reg_email" name="email"
                                       placeholder="john@example.com" required
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="reg_phone" class="form-label">Phone Number <span style="color:var(--secondary);">*</span></label>
                                <input type="tel" class="form-control" id="reg_phone" name="phone"
                                       placeholder="+1 555 234 5678" required
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="reg_portfolio" class="form-label">Portfolio / LinkedIn URL</label>
                                <input type="url" class="form-control" id="reg_portfolio" name="portfolio"
                                       placeholder="https://yourportfolio.com"
                                       value="<?php echo htmlspecialchars($_POST['portfolio'] ?? ''); ?>">
                            </div>
                            <div class="col-12">
                                <label for="reg_skills" class="form-label">Skills / Expertise <span style="color:var(--secondary);">*</span></label>
                                <input type="text" class="form-control" id="reg_skills" name="skills"
                                       placeholder="e.g. React, PHP, UI/UX Design, Flutter, WordPress..."
                                       required value="<?php echo htmlspecialchars($_POST['skills'] ?? ''); ?>">
                            </div>
                            <div class="col-12">
                                <label for="reg_message" class="form-label">Tell Us About Yourself <span style="color:var(--secondary);">*</span></label>
                                <textarea class="form-control" id="reg_message" name="message"
                                          rows="5" required
                                          placeholder="Describe your experience, what kind of projects you love, your availability, and anything else that would help us get to know you..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div style="margin-top:1.5rem;padding:1rem;background:var(--bg-alt);border-radius:var(--radius-sm);border:1px solid var(--border);">
                            <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">
                                <i class="bi bi-shield-check me-1" style="color:var(--secondary);"></i>
                                Your information is secure and will only be used for recruitment purposes. We do not share your data with third parties.
                            </p>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-send-fill"></i> Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
