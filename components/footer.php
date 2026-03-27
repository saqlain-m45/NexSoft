<!-- ===================== FOOTER ===================== -->
<footer class="nexsoft-footer">
    <div class="footer-top">
        <div class="container">
            <div class="row g-5">
                <!-- Brand Column -->
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <a href="<?php echo baseUrl(); ?>" class="footer-logo">
                            <span class="brand-icon"><i class="bi bi-hexagon-fill"></i></span>
                            <span class="brand-text">NexSoft <span class="brand-accent">Hub</span></span>
                        </a>
                        <p class="footer-tagline">We craft premium digital experiences that drive business growth. Your vision, our expertise.</p>
                        <div class="social-links">
                            <?php if ($fb = getSetting('facebook_link')): ?>
                            <a href="<?php echo htmlspecialchars($fb); ?>" class="social-link" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                            <?php endif; ?>
                            <?php if ($tw = getSetting('twitter_link')): ?>
                            <a href="<?php echo htmlspecialchars($tw); ?>" class="social-link" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                            <?php endif; ?>
                            <?php if ($li = getSetting('linkedin_link')): ?>
                            <a href="<?php echo htmlspecialchars($li); ?>" class="social-link" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                            <?php endif; ?>
                            <?php if ($ig = getSetting('instagram_link')): ?>
                            <a href="<?php echo htmlspecialchars($ig); ?>" class="social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                            <?php endif; ?>
                            <?php if ($gh = getSetting('github_link')): ?>
                            <a href="<?php echo htmlspecialchars($gh); ?>" class="social-link" aria-label="GitHub"><i class="bi bi-github"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Services Column -->
                <div class="col-6 col-lg-2">
                    <h5 class="footer-heading">Services</h5>
                    <ul class="footer-links">
                        <?php 
                        $footerServices = getDB()->query("SELECT title FROM services ORDER BY order_no ASC LIMIT 6")->fetchAll();
                        if ($footerServices): 
                            foreach($footerServices as $s): ?>
                                <li><a href="<?php echo baseUrl('services'); ?>"><?php echo h($s['title']); ?></a></li>
                            <?php endforeach; 
                        else: ?>
                            <li><a href="<?php echo baseUrl('services'); ?>">Web Development</a></li>
                            <li><a href="<?php echo baseUrl('services'); ?>">App Development</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Contact Column -->
                <div class="col-lg-4">
                    <h5 class="footer-heading">Get In Touch</h5>
                    <ul class="footer-contact">
                        <li>
                            <i class="bi bi-geo-alt-fill"></i>
                            <span><?php echo getSetting('site_address', '123 Innovation Drive, Tech City, CA 94105'); ?></span>
                        </li>
                        <li>
                            <i class="bi bi-telephone-fill"></i>
                            <span><?php echo getSetting('site_phone', '+1 (555) 234-5678'); ?></span>
                        </li>
                        <li>
                            <i class="bi bi-envelope-fill"></i>
                            <span><?php echo getSetting('site_email', 'hello@nexsofthub.com'); ?></span>
                        </li>
                    </ul>
                    <div class="footer-newsletter mt-3">
                        <p class="small mb-2" style="color: var(--text-muted);">Subscribe for updates</p>
                        <div class="newsletter-form">
                            <input type="email" class="form-control newsletter-input" placeholder="Your email address">
                            <button class="btn newsletter-btn"><i class="bi bi-send-fill"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <strong>NexSoft Hub</strong>. All rights reserved.</p>
                <p class="mb-0 footer-bottom-links">
                    <a href="#">Privacy Policy</a> &bull;
                    <a href="#">Terms of Service</a> &bull;
                    <a href="#">Cookie Policy</a>
                </p>
            </div>
        </div>
    </div>
</footer>
<!-- =================== END FOOTER =================== -->

<?php if (getSetting('custom_cursor_enabled', '1') == '1'): ?>
<!-- Custom Cursor Elements -->
<div class="cursor-outer"></div>
<div class="cursor-inner"></div>
<?php endif; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?php echo baseUrl('assets/js/main.js'); ?>"></script>

<!-- Custom Footer Scripts -->
<?php echo getSetting('custom_footer_scripts'); ?>
</body>
</html>
