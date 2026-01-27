export function isRecaptchaLoaded() {
    return typeof grecaptcha !== 'undefined' && typeof grecaptcha.getResponse === 'function';
}

export function resetRecaptcha() {
    if (isRecaptchaLoaded()) {
        try {
            grecaptcha.reset();
            console.log('reCAPTCHA reset successfully');
        } catch (error) {
            console.error('Error resetting reCAPTCHA:', error);
        }
    }
}

export function getRecaptchaResponse() {
    if (!isRecaptchaLoaded()) {
        console.warn('reCAPTCHA is not loaded yet');
        return '';
    }
    
    try {
        return grecaptcha.getResponse();
    } catch (error) {
        console.error('Error getting reCAPTCHA response:', error);
        return '';
    }
}

export function validateRecaptcha(event, options = {}) {
    const {
        alertMessage = 'Silakan verifikasi bahwa Anda bukan robot',
        logResponse = false
    } = options;

    const recaptchaResponse = getRecaptchaResponse();
    
    if (logResponse) {
        console.log('reCAPTCHA Token Length:', recaptchaResponse.length);
        if (recaptchaResponse.length > 0) {
            console.log('reCAPTCHA Token Preview:', recaptchaResponse.substring(0, 50) + '...');
        }
    }
    
    if (!recaptchaResponse) {
        if (event) {
            event.preventDefault();
        }
        alert(alertMessage);
        return false;
    }
    
    return true;
}

export function initRecaptchaValidation(formSelector, options = {}) {
    const {
        beforeValidate = null,
        onError = null,
        resetOnError = true,
        logResponse = false,
        alertMessage = 'Silakan verifikasi bahwa Anda bukan robot'
    } = options;

    document.addEventListener('DOMContentLoaded', function () {
        const form = typeof formSelector === 'string' 
            ? document.querySelector(formSelector) 
            : formSelector;

        if (!form) {
            console.error('Form not found:', formSelector);
            return;
        }

        // reset recaptcha if error
        const hasRecaptchaError = document.querySelector('.recaptcha-error') 
            || document.querySelector('[data-recaptcha-error]');
        
        if (hasRecaptchaError) {
            resetRecaptcha();
        }

        form.addEventListener('submit', function (e) {
            let hasError = false;
            
            if (beforeValidate && typeof beforeValidate === 'function') {
                hasError = beforeValidate(e, form);
            }

            const isValid = validateRecaptcha(e, { alertMessage, logResponse });
            
            if (!isValid) {
                if (resetOnError) {
                    resetRecaptcha();
                }
                
                if (onError && typeof onError === 'function') {
                    onError(e, form);
                }
                
                return false;
            }

            if (hasError && resetOnError) {
                resetRecaptcha();
                return false;
            }

            return true;
        });
    });
}

export function addRecaptchaToForm(formSelector, options = {}) {
    initRecaptchaValidation(formSelector, options);
}