{{-- ===== SWEETALERT GLOBAL FOR ADMIN ===== --}}

<script>
    /**
     * Hiển thị SweetAlert + xóa history state
     * => tránh Back trình duyệt hiện lại popup
     */
    function showAlertOnce(options) {
        Swal.fire(options);

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
        text: @json(session('success')),
        confirmButtonText: 'OK'
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
        title: 'Không được phép',
        text: @json(session('error')),
        confirmButtonText: 'OK'
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
        text: @json(session('warning')),
        confirmButtonText: 'OK'
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
        text: @json(session('info')),
        confirmButtonText: 'OK'
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
        html: `{!! implode('<br>', $errors->all()) !!}`,
        confirmButtonText: 'OK'
    });
});
</script>
@endif
