<?php
/**
 * Public Verification View
 */
require_once __DIR__ . '/../components/header.php';
?>

<section class="py-5" style="background: var(--bg); min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-800 mb-3">Verify <span class="text-secondary">Document</span></h1>
                    <p class="text-muted fs-5">Validate the authenticity of certificates and experience letters issued by NexSoft Hub.</p>
                </div>

                <div class="admin-card p-4 p-md-5 shadow-lg border-0" style="border-radius: 20px; background: white;">
                    <form method="GET" action="/NexSoft/">
                        <input type="hidden" name="route" value="verify">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Enter Document ID or Verification Code</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                                <span class="input-group-text bg-white border-0"><i class="bi bi-shield-check text-secondary"></i></span>
                                <input type="text" name="doc_id" class="form-control border-0 px-3" placeholder="e.g., NEX-C-20240318-0001" value="<?php echo htmlspecialchars($_GET['doc_id'] ?? ''); ?>" required>
                                <button class="btn btn-secondary px-4 fw-bold" type="submit">Verify Now</button>
                            </div>
                        </div>
                    </form>

                    <?php if (isset($result)): ?>
                        <?php if ($result): ?>
                            <div class="mt-5 p-4 bg-light border-start border-4 border-success" style="border-radius: 0 12px 12px 0;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                        <i class="bi bi-check-lg fs-4"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 fw-bold text-success">Verified Document</h4>
                                        <p class="text-muted small mb-0">This document is authentic and currently active.</p>
                                    </div>
                                </div>
                                <hr class="my-4 opacity-10">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="small text-muted text-uppercase fw-bold mb-1">Recipient Name</div>
                                        <div class="fw-bold fs-5"><?php echo htmlspecialchars($result['recipient_name']); ?></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="small text-muted text-uppercase fw-bold mb-1">Document Type</div>
                                        <div class="fw-bold fs-5 text-secondary"><?php echo ucfirst(str_replace('_', ' ', $result['type'])); ?></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="small text-muted text-uppercase fw-bold mb-1">Program/Course</div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($result['internship_title'] ?: 'General Program'); ?></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="small text-muted text-uppercase fw-bold mb-1">Issue Date</div>
                                        <div class="fw-bold"><?php echo date('F d, Y', strtotime($result['issue_date'])); ?></div>
                                    </div>
                                    <div class="col-12">
                                        <div class="small text-muted text-uppercase fw-bold mb-1">Document Status</div>
                                        <?php if ($result['status'] === 'active'): ?>
                                            <span class="badge-green">VALID & ACTIVE</span>
                                        <?php else: ?>
                                            <span class="badge-orange">REVOKED / INACTIVE</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($error): ?>
                            <div class="mt-5 p-4 bg-light border-start border-4 border-danger" style="border-radius: 0 12px 12px 0;">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                        <i class="bi bi-x-lg fs-4"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 fw-bold text-danger">Verification Failed</h4>
                                        <p class="text-muted small mb-0"><?php echo $error; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="mt-5 text-center px-lg-5">
                        <p class="small text-muted">NexSoft Hub uses a cryptographically secure auto-generated ID system. Each document is unique and can only be verified through our official website.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
