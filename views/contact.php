<!-- ================================================================
   CONTACT PAGE VIEW — NexSoft Hub
================================================================ -->

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-breadcrumb reveal">
            <a href="<?php echo baseUrl(); ?>">Home</a>
            <span><i class="bi bi-chevron-right"></i></span>
            <span class="active">Contact</span>
        </div>
        <h1 class="page-header-title reveal">Get In Touch</h1>
        <p class="page-header-subtitle reveal">We'd love to hear about your project. Let's build something great together.</p>
    </div>
</section>

<!-- Contact Section -->
<section class="form-section">
    <div class="container">
        <div class="row g-5 align-items-stretch">
            <!-- Contact Form -->
            <div class="col-lg-7 reveal">
                <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:3rem;box-shadow:var(--shadow);">
                    <h3 style="color:var(--primary);font-size:1.5rem;margin-bottom:0.5rem;">Send Us a Message</h3>
                    <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:2rem;">Fill out the form below and we'll get back to you within 24 hours.</p>

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

                    <form method="POST" action="<?php echo baseUrl('?route=contact'); ?>" class="nexsoft-form" id="contactForm" novalidate>
                        <div class="mb-3">
                            <label for="contact_name" class="form-label">Full Name <span style="color:var(--secondary);">*</span></label>
                            <input type="text" class="form-control" id="contact_name" name="name"
                                   placeholder="John Smith" required
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="contact_email" class="form-label">Email Address <span style="color:var(--secondary);">*</span></label>
                            <input type="email" class="form-control" id="contact_email" name="email"
                                   placeholder="john@company.com" required
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="contact_message" class="form-label">Message <span style="color:var(--secondary);">*</span></label>
                            <textarea class="form-control" id="contact_message" name="message"
                                      rows="6" placeholder="Tell us about your project, goals, and timeline..."
                                      required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-send-fill"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-5 reveal">
                <div class="contact-info-block">
                    <h3 style="color:white;margin-bottom:0.5rem;">Contact Information</h3>
                    <p style="color:rgba(255,255,255,0.6);font-size:0.9rem;margin-bottom:2rem;">Reach out through any channel — we're always ready to help.</p>

                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <div class="contact-info-label">Office Address</div>
                            <div class="contact-info-value">123 Innovation Drive, Tech City<br>California, CA 94105, USA</div>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <div class="contact-info-label">Phone Number</div>
                            <div class="contact-info-value">+1 (555) 234-5678</div>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <div class="contact-info-label">Email Address</div>
                            <div class="contact-info-value">hello@nexsofthub.com</div>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="bi bi-clock-fill"></i></div>
                        <div>
                            <div class="contact-info-label">Working Hours</div>
                            <div class="contact-info-value">Mon–Fri: 9:00 AM – 6:00 PM<br>Sat: 10:00 AM – 2:00 PM</div>
                        </div>
                    </div>

                    <div style="border-top:1px solid rgba(255,255,255,0.1);padding-top:1.5rem;margin-top:0.5rem;">
                        <p style="color:rgba(255,255,255,0.5);font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.75rem;font-weight:600;">Follow Us</p>
                        <div style="display:flex;gap:10px;">
                            <?php
                            $socials = [
                                ['icon'=>'bi-facebook','label'=>'Facebook'],
                                ['icon'=>'bi-twitter-x','label'=>'Twitter'],
                                ['icon'=>'bi-linkedin','label'=>'LinkedIn'],
                                ['icon'=>'bi-instagram','label'=>'Instagram'],
                                ['icon'=>'bi-github','label'=>'GitHub'],
                            ];
                            foreach($socials as $s): ?>
                            <a href="#" aria-label="<?php echo $s['label']; ?>" style="width:40px;height:40px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.6);font-size:1rem;transition:var(--transition);">
                                <i class="bi <?php echo $s['icon']; ?>"></i>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
