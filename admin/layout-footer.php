        </div><!-- end admin-content -->
    </div><!-- end admin-main -->
</div><!-- end admin-wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Admin JS
document.addEventListener('DOMContentLoaded', function() {
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
