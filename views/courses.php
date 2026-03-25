<?php require_once __DIR__ . '/../components/header.php'; ?>

<!-- ── Hero Section ────────────────────────────────────────────── -->
<section class="page-hero"
    style="background: linear-gradient(135deg, #0B1F3B 0%, #162d4f 100%); padding: 80px 0 50px; position: relative; overflow: hidden;">
    <div class="hero-shape"
        style="position: absolute; top: -50%; right: -10%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(14,165,164,0.1) 0%, transparent 70%); border-radius: 50%;">
    </div>
    <div class="container text-center" style="position: relative; z-index: 2;">
        <span class="badge"
            style="background: rgba(14,165,164,0.1); color: #0EA5A4; padding: 6px 16px; border-radius: 50px; font-weight: 700; margin-bottom: 15px; display: inline-block; border: 1px solid rgba(14,165,164,0.2); font-size: 0.8rem;">Skill
            Up with NexSoft</span>
        <h1 style="color: white; font-size: 2.8rem; font-weight: 900; margin-bottom: 15px; letter-spacing: -1px;">
            Premium <span style="color: #0EA5A4;">Tech Courses</span></h1>
        <p style="color: rgba(255,255,255,0.7); font-size: 1rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
            Master the most in-demand skills with our expert-led, project-based courses.</p>
    </div>
</section>

<!-- ── Courses Listing ─────────────────────────────────────────── -->
<section style="padding: 60px 0; background: #f8fafc;">
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success border-0 shadow-sm mb-5"
            style="border-radius: 12px; background: white; border-left: 5px solid #22c55e !important;">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>

        <?php if (empty($courses)): ?>
        <div class="text-center py-5">
            <i class="bi bi-calendar2-x"
                style="font-size: 3rem; color: #cbd5e1; display: block; margin-bottom: 15px;"></i>
            <h3 style="color: #1e293b;">No active registrations at the moment.</h3>
            <p style="color: #64748b;">Please check back later or contact us for inquiries.</p>
        </div>
        <?php else: ?>
        <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
            <?php foreach ($courses as $course): ?>
            <div class="col d-flex">
                <div class="course-card d-flex flex-column w-100"
                    style="background: white; border-radius: 16px; padding: 25px; border: 1px solid #e2e8f0; transition: all 0.3s ease; position: relative; overflow: hidden; height: 100%;">
                    <div
                        style="position: absolute; top: 0; right: 0; padding: 8px 15px; background: rgba(14,165,164,0.1); color: #0EA5A4; font-weight: 800; font-size: 0.6rem; border-bottom-left-radius: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <?php echo htmlspecialchars($course['category']); ?>
                    </div>

                    <div class="course-icon mb-3"
                        style="width: 45px; height: 45px; background: rgba(14,165,164,0.05); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #0EA5A4; font-size: 1.3rem;">
                        <i class="bi <?php 
                                    echo match($course['category']) {
                                        'Web Dev' => 'bi-code-slash',
                                        'WordPress' => 'bi-wordpress',
                                        'SEO' => 'bi-search',
                                        'App Dev' => 'bi-phone',
                                        default => 'bi-mortarboard'
                                    };
                                ?>"></i>
                    </div>

                    <div class="flex-grow-1">
                        <h4
                            style="font-weight: 800; color: #0B1F3B; margin-bottom: 10px; font-size: 1.25rem; line-height: 1.3;">
                            <?php echo htmlspecialchars($course['title']); ?>
                        </h4>
                        <p style="color: #64748b; line-height: 1.5; margin-bottom: 20px; font-size: 0.85rem;">
                            <?php echo htmlspecialchars($course['description']); ?>
                        </p>
                    </div>

                    <button type="button" class="btn btn-primary w-100 open-reg-modal"
                        data-course-id="<?php echo $course['id']; ?>"
                        data-course-title="<?php echo htmlspecialchars($course['title']); ?>"
                        style="background: #0EA5A4; border: none; padding: 10px; border-radius: 8px; font-weight: 700; transition: 0.3s ease; font-size: 0.85rem;">
                        Register Now
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ── Registration Modal ────────────────────────────────────────── -->
<div class="modal fade" id="registrationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"
            style="border-radius: 20px; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
            <div class="modal-header border-0 pb-0" style="padding: 25px 25px 10px;">
                <div>
                    <h5 class="modal-title" style="font-weight: 800; color: #0B1F3B; margin-bottom: 2px;">Course
                        Registration</h5>
                    <p id="modalCourseTitle" style="color: #0EA5A4; font-weight: 700; font-size: 0.85rem; margin: 0;">
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="background-color: #f1f5f9; border-radius: 50%; padding: 8px; opacity: 1;"></button>
            </div>
            <div class="modal-body" style="padding: 25px;">
                <form action="<?php echo baseUrl('?route=courses'); ?>" method="POST" id="modalRegForm">
                    <input type="hidden" name="course_id" id="modalCourseId">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; font-size: 0.8rem; color: #1e293b;">Full
                            Name</label>
                        <input type="text" name="name" class="form-control premium-input" placeholder="Your name"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; font-size: 0.8rem; color: #1e293b;">Email
                            Address</label>
                        <input type="email" name="email" class="form-control premium-input"
                            placeholder="email@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; font-size: 0.8rem; color: #1e293b;">Phone
                            Number</label>
                        <input type="text" name="phone" class="form-control premium-input"
                            placeholder="+1 (555) 000-0000">
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="font-weight: 600; font-size: 0.8rem; color: #1e293b;">Notes
                            (Optional)</label>
                        <textarea name="message" class="form-control premium-input" rows="2"
                            placeholder="Tell us more..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2"
                        style="background: #0EA5A4; border: none; border-radius: 10px; font-weight: 700;">
                        Submit Registration
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        border-color: #0EA5A4 !important;
    }

    .premium-input {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.9rem;
    }

    .premium-input:focus {
        border-color: #0EA5A4;
        box-shadow: 0 0 0 3px rgba(14, 165, 164, 0.1);
        background: white;
    }

    .modal.fade .modal-dialog {
        transform: scale(0.95);
        transition: transform 0.2s ease-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const regModal = new bootstrap.Modal(document.getElementById('registrationModal'));
        const modalCourseTitle = document.getElementById('modalCourseTitle');
        const modalCourseId = document.getElementById('modalCourseId');

        document.querySelectorAll('.open-reg-modal').forEach(button => {
            button.addEventListener('click', function () {
                modalCourseTitle.textContent = this.getAttribute('data-course-title');
                modalCourseId.value = this.getAttribute('data-course-id');
                regModal.show();
            });
        });
    });
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>