// SweetAlert2 Helper Functions
window.showSuccess = function(message, title = 'Berhasil!') {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#10B981',
        timer: 3000,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        customClass: {
            popup: 'swal2-toast',
            title: 'swal2-toast-title',
            content: 'swal2-toast-content'
        }
    });
};

window.showError = function(message, title = 'Error!') {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#EF4444',
        customClass: {
            popup: 'swal2-error-popup',
            title: 'swal2-error-title',
            content: 'swal2-error-content'
        }
    });
};

window.showWarning = function(message, title = 'Peringatan!') {
    return Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#F59E0B',
        customClass: {
            popup: 'swal2-warning-popup',
            title: 'swal2-warning-title',
            content: 'swal2-warning-content'
        }
    });
};

window.showInfo = function(message, title = 'Informasi') {
    return Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#3B82F6',
        customClass: {
            popup: 'swal2-info-popup',
            title: 'swal2-info-title',
            content: 'swal2-info-content'
        }
    });
};

window.showConfirm = function(message, title = 'Konfirmasi', confirmText = 'Ya', cancelText = 'Tidak') {
    return Swal.fire({
        icon: 'question',
        title: title,
        text: message,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        reverseButtons: true,
        customClass: {
            popup: 'swal2-confirm-popup',
            title: 'swal2-confirm-title',
            content: 'swal2-confirm-content',
            confirmButton: 'swal2-confirm-button',
            cancelButton: 'swal2-cancel-button'
        }
    });
};

window.showLoading = function(message = 'Memproses...') {
    return Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
        customClass: {
            popup: 'swal2-loading-popup',
            title: 'swal2-loading-title'
        }
    });
};

// Override native alert and confirm
window.alert = function(message) {
    return showInfo(message);
};

window.confirm = function(message) {
    return showConfirm(message).then((result) => {
        return result.isConfirmed;
    });
};

// Custom SweetAlert2 styles
const style = document.createElement('style');
style.textContent = `
    .swal2-toast {
        background: #10B981 !important;
        color: white !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3) !important;
    }
    
    .swal2-error-popup {
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(239, 68, 68, 0.2) !important;
    }
    
    .swal2-warning-popup {
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.2) !important;
    }
    
    .swal2-info-popup {
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2) !important;
    }
    
    .swal2-confirm-popup {
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }
    
    .swal2-loading-popup {
        border-radius: 12px !important;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px) !important;
    }
    
    .swal2-confirm-button {
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 12px 24px !important;
        transition: all 0.2s ease !important;
    }
    
    .swal2-confirm-button:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3) !important;
    }
    
    .swal2-cancel-button {
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 12px 24px !important;
        transition: all 0.2s ease !important;
    }
    
    .swal2-cancel-button:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3) !important;
    }
`;
document.head.appendChild(style); 