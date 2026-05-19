import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

document.addEventListener('DOMContentLoaded', () => {
    const flash = window.appFlash || {};

    const showFlash = (icon, title, text) => {
        if (!text) {
            return;
        }

        Swal.fire({
            icon,
            title,
            text,
            confirmButtonText: 'Okay',
            timer: icon === 'success' ? 2600 : undefined,
            timerProgressBar: icon === 'success',
        });
    };

    showFlash('success', 'Success', flash.success);
    showFlash('error', 'Error', flash.error);
    showFlash('warning', 'Warning', flash.warning);
    showFlash('info', 'Information', flash.info);

    if (flash.status) {
        showFlash('success', 'Done', flash.status);
    }

    document.querySelectorAll('.js-confirm-delete').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const title = form.dataset.title || 'Are you sure?';
            const text = form.dataset.text || 'This action cannot be undone.';
            const confirmButtonText = form.dataset.confirmText || 'Yes, continue';

            const result = await Swal.fire({
                icon: 'warning',
                title,
                text,
                showCancelButton: true,
                confirmButtonText,
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true,
            });

            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});