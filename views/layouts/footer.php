    </div>
</main>

<!-- Footer -->
<footer class="bg-white border-top py-4 mt-auto no-print">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <span class="fw-bold text-dark">HUIT Schedule Hub</span> &copy; <?= date('Y') ?> - Hệ thống Quản lý và Phân lịch Giảng dạy Đa cơ sở Thông minh.
                <div class="text-muted small mt-1">
                    Đồ án Mã Nguồn Mở | Đại học Công Thương TP. Hồ Chí Minh (HUIT)
                </div>
            </div>
            <div class="col-md-6 text-center text-md-end text-muted small">
                <span class="me-3"><i class="fa-solid fa-code-branch me-1"></i> Phiên bản 1.0.0</span>
                <span class="me-3"><i class="fa-solid fa-scale-balanced me-1"></i> MIT License</span>
                <a href="README.md" target="_blank" class="text-decoration-none text-muted">
                    <i class="fa-solid fa-book me-1"></i> Tài liệu
                </a>
            </div>
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
    // Global Confirmation Helper using SweetAlert2
    function confirmDelete(formOrUrl, message = 'Bạn có chắc chắn muốn xóa mục này? Thao tác này không thể hoàn tác.') {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Xóa ngay',
            cancelButtonText: 'Hủy bỏ'
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
