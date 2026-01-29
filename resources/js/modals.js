/**
 * Modal Management Component
 * Handles showing/closing modals with support for:
 * - Success/Error modals with dynamic messages
 * - Karyawan modal management
 * - Backdrop click to close
 * - ESC key to close
 */

export function initModals() {
    // Success Modal Functions
    window.showSuccessModal = function(message) {
        const successModal = document.getElementById('successModal');
        const successMessage = document.getElementById('successMessage');
        
        if (successModal && successMessage) {
            successMessage.textContent = message;
            successModal.classList.add('show');
        } else {
            console.warn('[showSuccessModal] Elements not found - successModal or successMessage');
        }
    };

    window.closeSuccessModal = function() {
        const successModal = document.getElementById('successModal');
        if (successModal) {
            successModal.classList.remove('show');
        }
    };

    // Error Modal Functions
    window.showErrorModal = function(message) {
        const errorModal = document.getElementById('errorModal');
        const errorMessage = document.getElementById('errorMessage');
        
        if (errorModal && errorMessage) {
            errorMessage.textContent = message;
            errorModal.classList.add('show');
        } else {
            console.warn('[showErrorModal] Elements not found - errorModal or errorMessage');
        }
    };

    window.closeErrorModal = function() {
        const errorModal = document.getElementById('errorModal');
        if (errorModal) {
            errorModal.classList.remove('show');
        }
    };

    // Karyawan Modal Functions
    window.openKaryawanModal = function(modalId = 'karyawan_modal') {
        const karyawanModal = document.getElementById(modalId);
        if (karyawanModal) {
            karyawanModal.classList.add('show');
        } else {
            console.warn(`[openKaryawanModal] Modal with ID "${modalId}" not found`);
        }
    };

    window.closeKaryawanModal = function(modalId = 'karyawan_modal') {
        const karyawanModal = document.getElementById(modalId);
        if (karyawanModal) {
            karyawanModal.classList.remove('show');
        }
    };

    // Generic Modal Functions (reusable)
    window.showModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
        }
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
        }
    };

    // Setup backdrop click handlers for all modals
    setupBackdropClickHandlers();

    // Setup ESC key close for all modals
    setupESCKeyHandler();
}

/**
 * Setup backdrop click to close modal
 */
function setupBackdropClickHandlers() {
    // Success Modal
    const successModal = document.getElementById('successModal');
    if (successModal) {
        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                closeSuccessModal();
            }
        });
    }

    // Error Modal
    const errorModal = document.getElementById('errorModal');
    if (errorModal) {
        errorModal.addEventListener('click', function(e) {
            if (e.target === errorModal) {
                closeErrorModal();
            }
        });
    }

    // Karyawan Modal
    const karyawanModal = document.getElementById('karyawan_modal');
    if (karyawanModal) {
        karyawanModal.addEventListener('click', function(e) {
            if (e.target === karyawanModal) {
                closeKaryawanModal();
            }
        });
    }
}

/**
 * Setup ESC key to close modals
 */
function setupESCKeyHandler() {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close success modal if open
            const successModal = document.getElementById('successModal');
            if (successModal && successModal.classList.contains('show')) {
                closeSuccessModal();
                return;
            }

            // Close error modal if open
            const errorModal = document.getElementById('errorModal');
            if (errorModal && errorModal.classList.contains('show')) {
                closeErrorModal();
                return;
            }

            // Close karyawan modal if open
            const karyawanModal = document.getElementById('karyawan_modal');
            if (karyawanModal && karyawanModal.classList.contains('show')) {
                closeKaryawanModal();
                return;
            }
        }
    });
}

export default initModals;
