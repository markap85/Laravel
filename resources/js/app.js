import './bootstrap';

// Toast Notification System
class Toast {
    constructor() {
        this.container = this.createContainer();
    }

    createContainer() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '11';
            document.body.appendChild(container);
        }
        return container;
    }

    show(message, type = 'success') {
        const toastId = `toast-${Date.now()}`;
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-exclamation-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };

        const bgColors = {
            success: 'bg-success',
            error: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-info'
        };

        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgColors[type]} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icons[type]} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        this.container.insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }
}

window.toast = new Toast();

// Confirmation Modal System
window.confirmDelete = function(message, formId) {
    if (confirm(message)) {
        document.getElementById(formId).submit();
    }
    return false;
};

// Sortable Tables
document.addEventListener('DOMContentLoaded', function() {
    const sortableTables = document.querySelectorAll('.sortable-table');
    
    sortableTables.forEach(table => {
        const headers = table.querySelectorAll('th[data-sort]');
        
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                const sortKey = this.dataset.sort;
                const currentUrl = new URL(window.location);
                const currentSort = currentUrl.searchParams.get('sort');
                const currentDirection = currentUrl.searchParams.get('direction');
                
                let newDirection = 'asc';
                if (currentSort === sortKey && currentDirection === 'asc') {
                    newDirection = 'desc';
                }
                
                currentUrl.searchParams.set('sort', sortKey);
                currentUrl.searchParams.set('direction', newDirection);
                window.location = currentUrl.toString();
            });
        });
    });

    // Show flash messages as toasts
    const successMessage = document.querySelector('[data-success-message]');
    const errorMessage = document.querySelector('[data-error-message]');
    
    if (successMessage) {
        window.toast.show(successMessage.dataset.successMessage, 'success');
    }
    
    if (errorMessage) {
        window.toast.show(errorMessage.dataset.errorMessage, 'error');
    }
});
