    </div>
</main>

<!-- Modern Minimalist Footer -->
<footer class="bg-white border-top py-3 mt-auto no-print">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-muted">
        <div>
            <strong class="text-dark">HUIT Schedule Hub</strong> &copy; <?= date('Y') ?> • Đại học Công Thương TP.HCM
        </div>
        <div class="d-flex align-items-center gap-3">
            <span>v2.0</span>
            <span>MIT License</span>
            <a href="README.md" target="_blank" class="text-decoration-none text-muted">Tài liệu</a>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmDelete(formOrUrl, message = 'Bạn có chắc chắn muốn xóa mục này?') {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Xóa ngay',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof formOrUrl === 'string') {
                    window.location.href = formOrUrl;
                } else if (formOrUrl instanceof HTMLFormElement) {
                    formOrUrl.submit();
                }
            }
        });
        return false;
    }
</script>

</body>
</html>
