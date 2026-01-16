{{-- ===== SWEETALERT GLOBAL ===== --}}

<script>
    /**
     * Hiển thị SweetAlert + xóa history state
     * => tránh Back trình duyệt hiện lại popup
     */
    function showAlertOnce(options) {
        Swal.fire(Object.assign({
            width: 600,                 //  TĂNG SIZE POPUP
            padding: '1.5rem',
            confirmButtonText: 'OK',
            confirmButtonColor: '#D70018'
        }, options));

        // FIX BACK BUTTON (BFCache)
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    }
</script>

{{-- ===== SUCCESS ===== --}}
@if (session('success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    showAlertOnce({
        icon: 'success',
        title: 'Thành công',
        text: @json(session('success'))
    });
});
</script>
@endif

{{-- ===== ERROR ===== --}}
@if (session('error'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    showAlertOnce({
        icon: 'error',
        title: 'Lỗi',
        text: @json(session('error'))
    });
});
</script>
@endif

{{-- ===== WARNING ===== --}}
@if (session('warning'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    showAlertOnce({
        icon: 'warning',
        title: 'Cảnh báo',
        text: @json(session('warning'))
    });
});
</script>
@endif

{{-- ===== INFO ===== --}}
@if (session('info'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    showAlertOnce({
        icon: 'info',
        title: 'Thông báo',
        text: @json(session('info'))
    });
});
</script>
@endif

{{-- ===== VALIDATION ERRORS ===== --}}
@if ($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function () {
    showAlertOnce({
        icon: 'error',
        title: 'Dữ liệu không hợp lệ',
        html: `{!! implode('<br>', $errors->all()) !!}`
    });
});
</script>
@endif
