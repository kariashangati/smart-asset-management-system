<script>
    window.appFlash = {
        success: @json(session('success')),
        error: @json(session('error')),
        warning: @json(session('warning')),
        info: @json(session('info')),
        status: @json(session('status')),
    };
</script>