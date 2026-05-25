<script>
    window.appFlash = {
        success: @json(session('success')),
        error:   @json(session('error')),
        warning: @json(session('warning')),
        info:    @json(session('info')),
        status:  @json(session('status')),
    };

    @if(session('_modal'))
        window.defaultOpenModal = @json(session('_modal'));
    @elseif($errors->any() && old('_modal'))
        window.defaultOpenModal = @json(old('_modal'));
    @endif
</script>
