        </div><!-- end admin-content -->
    </div><!-- end admin-main -->
</div><!-- end admin-wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Admin JS
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = <?php echo json_encode(adminCsrfToken()); ?>;
    document.querySelectorAll('form[method="POST"], form[method="post"]').forEach(function(form) {
        if (form.querySelector('input[name="csrf_token"]')) {
            return;
        }
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'csrf_token';
        input.value = csrfToken;
        form.appendChild(input);
    });

    const sidebar = document.getElementById('adminSidebar');
    const menuToggle = document.getElementById('adminMenuToggle');
    const backdrop = document.getElementById('adminSidebarBackdrop');
    const mobileMq = window.matchMedia('(max-width: 991px)');

    function closeSidebar() {
        if (!sidebar || !backdrop || !menuToggle) return;
        sidebar.classList.remove('open');
        backdrop.classList.remove('show');
        menuToggle.setAttribute('aria-expanded', 'false');
    }

    function openSidebar() {
        if (!sidebar || !backdrop || !menuToggle) return;
        sidebar.classList.add('open');
        backdrop.classList.add('show');
        menuToggle.setAttribute('aria-expanded', 'true');
    }

    if (menuToggle && sidebar && backdrop) {
        menuToggle.addEventListener('click', function() {
            if (!mobileMq.matches) return;
            const isOpen = sidebar.classList.contains('open');
            if (isOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        backdrop.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });

        window.addEventListener('resize', function() {
            if (!mobileMq.matches) {
                closeSidebar();
            }
        });
    }

    // Confirm delete
    document.querySelectorAll('.confirm-delete').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
</script>
</body>
</html>
