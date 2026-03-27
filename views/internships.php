<?php require_once __DIR__ . '/../components/header.php'; ?>

<section class="py-5" style="background: var(--bg); min-height: 80vh;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-3 fw-900 mb-3">Careers & <span class="text-secondary">Internships</span></h1>
            <p class="text-muted fs-5 mx-auto" style="max-width: 700px;">Join NexSoft Hub's elite training programs. We offer hands-on experience in cutting-edge technologies to help you kickstart your professional career.</p>
        </div>

        <div class="row g-4 mt-4">
            <?php if (empty($internships)): ?>
                <div class="col-12 text-center py-5">
                    <div class="bg-white p-5 rounded-4 shadow-sm border border-light">
                        <i class="bi bi-calendar-x display-1 text-muted mb-4"></i>
                        <h3 class="fw-bold">No Active Openings</h3>
                        <p class="text-muted">We currently don't have any open internships. Please check back later or contact us for future opportunities.</p>
                        <a href="<?php echo baseUrl('contact'); ?>" class="btn btn-secondary px-4 py-2 mt-3" style="border-radius: 50px;">Get in Touch</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($internships as $item): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-up" style="border-radius: 20px; transition: all 0.3s ease; overflow: hidden; background: white;">
                            <div class="p-4">
                                <span class="badge mb-3 <?php echo $item['category'] === 'internship' ? 'bg-primary' : 'bg-success'; ?> bg-opacity-10 <?php echo $item['category'] === 'internship' ? 'text-primary' : 'text-success'; ?> px-3 py-2" style="border-radius: 50px; font-size: 0.75rem; font-weight: 700;">
                                    <?php echo strtoupper($item['category']); ?>
                                </span>
                                <h4 class="fw-800 mb-3" style="color: #0B1F3B;"><?php echo htmlspecialchars($item['title']); ?></h4>
                                <div class="d-flex align-items-center mb-4 text-muted small">
                                    <i class="bi bi-clock me-2 text-secondary"></i>
                                    <span>Duration: <?php echo htmlspecialchars($item['duration'] ?: 'Not Specified'); ?></span>
                                </div>
                                <p class="text-muted small mb-4" style="line-height: 1.6;">
                                    <?php echo htmlspecialchars(mb_strimwidth($item['description'] ?? '', 0, 120, '...')); ?>
                                </p>
                                <a href="<?php echo baseUrl('courses'); ?>" class="btn btn-outline-secondary w-100 fw-bold py-3" style="border-radius: 12px; transition: all 0.3s ease;">Apply Now</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    .hover-up:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    }
    .hover-up:hover .btn-outline-secondary {
        background: var(--secondary) !important;
        color: white !important;
        border-color: var(--secondary) !important;
    }
</style>

<?php require_once __DIR__ . '/../components/footer.php'; ?>