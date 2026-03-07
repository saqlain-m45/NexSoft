</div><!-- end admin-content -->
</div><!-- end admin-main -->
</div><!-- end admin-wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Admin JS
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = <?php echo json_encode(adminCsrfToken()); ?>;
        document.querySelectorAll('form[method="POST"], form[method="post"]').forEach(function (form) {
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
            menuToggle.addEventListener('click', function () {
                if (!mobileMq.matches) return;
                const isOpen = sidebar.classList.contains('open');
                if (isOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            backdrop.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });

            window.addEventListener('resize', function () {
                if (!mobileMq.matches) {
                    closeSidebar();
                }
            });
        }

        // Confirm delete
        document.querySelectorAll('.confirm-delete').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (!confirm('Are you sure you want to delete this? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });

        // --- Real-time Notifications (Ting Tong) ---
        const notificationSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');

        function checkNotifications() {
            fetch('ajax-notifications.php')
                .then(response => response.json())
                .then(data => {
                    if (data.new_registrations && data.new_registrations.length > 0) {
                        // Play Sound
                        notificationSound.play().catch(e => console.log('Audio play blocked by browser:', e));

                        // Show Browser/UI notification
                        data.new_registrations.forEach(reg => {
                            showToast(`New Registration!`, `${reg.name} applied for ${reg.course_title}`);
                        });
                    }
                })
                .catch(err => console.error('Notification check failed:', err));
        }

        function showToast(title, message) {
            // Simple alert if no toast system exists, or injecting a basic toast
            const toastHtml = `
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
                <div class="toast show border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px; border-left: 5px solid #0EA5A4 !important;">
                    <div class="toast-header border-0 bg-white">
                        <i class="bi bi-bell-fill text-primary me-2"></i>
                        <strong class="me-auto">${title}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body bg-white py-3">
                        ${message}
                    </div>
                </div>
            </div>`;
            const div = document.createElement('div');
            div.innerHTML = toastHtml;
            document.body.appendChild(div);
            setTimeout(() => div.remove(), 8000);
        }

        // Start polling every 15 seconds
        setInterval(checkNotifications, 15000);
        // Initial check after page load
        setTimeout(checkNotifications, 2000);
    });
</script>
</body>

</html>