document.addEventListener('DOMContentLoaded', () => {
    const openModal = (modalId) => {
        const modal = document.getElementById(modalId);

        if (!modal) {
            return;
        }

        modal.classList.add('is-open');
        document.body.classList.add('modal-open');
    };

    const closeModal = (modal) => {
        modal.classList.remove('is-open');

        const anyModalOpen = document.querySelector('.app-modal.is-open');
        if (!anyModalOpen) {
            document.body.classList.remove('modal-open');
        }
    };

    document.querySelectorAll('[data-modal-open]').forEach((button) => {
        button.addEventListener('click', () => {
            openModal(button.dataset.modalOpen);
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', () => {
            const modal = button.closest('.app-modal');

            if (modal) {
                closeModal(modal);
            }
        });
    });

    document.querySelectorAll('.app-modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.app-modal.is-open').forEach((modal) => {
                closeModal(modal);
            });
        }
    });

    if (window.defaultOpenModal) {
        openModal(window.defaultOpenModal);
    }
});