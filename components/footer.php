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
                            <a href="#" class="social-link" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="social-link" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                            <a href="#" class="social-link" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                            <a href="#" class="social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="social-link" aria-label="GitHub"><i class="bi bi-github"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Services Column -->
                <div class="col-6 col-lg-2">
                    <h5 class="footer-heading">Services</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo baseUrl('?route=services'); ?>">Web Development</a></li>
                        <li><a href="<?php echo baseUrl('?route=services'); ?>">App Development</a></li>
                        <li><a href="<?php echo baseUrl('?route=services'); ?>">WordPress Dev</a></li>
                        <li><a href="<?php echo baseUrl('?route=services'); ?>">UI/UX Design</a></li>
                        <li><a href="<?php echo baseUrl('?route=services'); ?>">Content Writing</a></li>
                        <li><a href="<?php echo baseUrl('?route=services'); ?>">Video Editing</a></li>
                    </ul>
                </div>

                <!-- Company Column -->
                <div class="col-6 col-lg-2">
                    <h5 class="footer-heading">Company</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo baseUrl('?route=about'); ?>">About Us</a></li>
                        <li><a href="<?php echo baseUrl('?route=blog'); ?>">Blog</a></li>
                        <li><a href="<?php echo baseUrl('?route=pricing'); ?>">Pricing</a></li>
                        <li><a href="<?php echo baseUrl('?route=contact'); ?>">Contact</a></li>
                        <li><a href="<?php echo baseUrl('?route=register'); ?>">Join Our Team</a></li>
                        <li><a href="<?php echo baseUrl('admin/'); ?>">Admin</a></li>
                    </ul>
                </div>

                <!-- Contact Column -->
                <div class="col-lg-4">
                    <h5 class="footer-heading">Get In Touch</h5>
                    <ul class="footer-contact">
                        <li>
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>123 Innovation Drive, Tech City, CA 94105</span>
                        </li>
                        <li>
                            <i class="bi bi-telephone-fill"></i>
                            <span>+1 (555) 234-5678</span>
                        </li>
                        <li>
                            <i class="bi bi-envelope-fill"></i>
                            <span>hello@nexsofthub.com</span>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?php echo baseUrl('assets/js/main.js'); ?>"></script>
</body>
</html>
