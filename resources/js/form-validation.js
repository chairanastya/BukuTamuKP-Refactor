export function showError(input, errorElement, timeout = 5000) {
    if (errorElement) {
        errorElement.classList.add('show');
    }

    const wrapper = input.closest('.input-wrapper');
    if (wrapper) {
        wrapper.classList.add('error');
    }

    setTimeout(() => {
        if (errorElement) {
            errorElement.classList.remove('show');
        }
        if (wrapper) {
            wrapper.classList.remove('error');
        }
    }, timeout);
}

export function validateTextField(input, errorElement) {
    if (!input || !input.value?.trim()) {
        if (errorElement) {
            showError(input, errorElement);
        }
        return false;
    }
    return true;
}

export function validateEmail(emailInput, emailErrorElement) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailInput || !emailInput.value?.trim() || !emailRegex.test(emailInput.value.trim())) {
        if (emailErrorElement) {
            showError(emailInput, emailErrorElement);
        }
        return false;
    }
    return true;
}

export function validateKaryawanSelection(getKaryawanFn, errorElement, containerElement, timeout = 5000) {
    const selectedKaryawan = getKaryawanFn ? getKaryawanFn() : [];
    
    if (selectedKaryawan.length === 0) {
        if (errorElement) {
            errorElement.classList.add('show');
            setTimeout(() => {
                errorElement.classList.remove('show');
            }, timeout);
        }
        
        if (containerElement) {
            const firstRow = containerElement.querySelector('.karyawan-search-container');
            if (firstRow) {
                const inputWrapper = firstRow.querySelector('.border-2');
                if (inputWrapper) {
                    inputWrapper.classList.add('border-red-600');
                    inputWrapper.classList.remove('border-[#084E8F]');
                    setTimeout(() => {
                        inputWrapper.classList.remove('border-red-600');
                        inputWrapper.classList.add('border-[#084E8F]');
                    }, timeout);
                }
            }
        }
        return false;
    }
    return true;
}

export function validatePhoto(photoInput, errorElement, areaElement, timeout = 5000) {
    if (!photoInput || !photoInput.value?.trim()) {
        if (errorElement) {
            errorElement.classList.add('show');
            setTimeout(() => {
                errorElement.classList.remove('show');
            }, timeout);
        }
        
        if (areaElement) {
            areaElement.classList.add('error');
            setTimeout(() => {
                areaElement.classList.remove('error');
            }, timeout);
        }
        return false;
    }
    return true;
}

export function validateNama(namaInput, namaErrorId = 'nama_karyawan_error') {
    const namaError = document.getElementById(namaErrorId);
    return validateTextField(namaInput, namaError);
}

export function setupFormValidation(options = {}) {
    const {
        formSelector = 'form',
        fields = {},
        onSubmit = null,
        scrollToError = true,
        preventDoubleSubmit = true,
        submitButtonSelector = 'button[type="submit"]',
        submitButtonLoadingText = '<span class="animate-pulse">Mengirim...</span>',
        timeout = 10000
    } = options;

    const form = typeof formSelector === 'string' 
        ? document.querySelector(formSelector) 
        : formSelector;

    if (!form) {
        console.error('[setupFormValidation] Form not found:', formSelector);
        return;
    }

    const submitButton = form.querySelector(submitButtonSelector);
    let isSubmitting = false;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (isSubmitting && preventDoubleSubmit) {
            return false;
        }

        let hasError = false;
        let firstErrorElement = null;

        Object.keys(fields).forEach(key => {
            const field = fields[key];
            const { input, error, validator, extraElement } = field;

            if (!input) return;

            let isValid = false;

            if (validator && typeof validator === 'function') {
                isValid = validator(input, error, extraElement);
            } else if (field.type === 'email') {
                isValid = validateEmail(input, error);
            } else if (field.type === 'karyawan') {
                isValid = validateKaryawanSelection(field.getKaryawanFn, error, extraElement);
            } else if (field.type === 'photo') {
                isValid = validatePhoto(input, error, extraElement);
            } else {
                isValid = validateTextField(input, error);
            }

            if (!isValid) {
                hasError = true;
                if (!firstErrorElement) {
                    firstErrorElement = extraElement || input;
                }
            }
        });

        if (hasError) {
            if (scrollToError && firstErrorElement) {
                firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }

        if (onSubmit && typeof onSubmit === 'function') {
            const shouldContinue = onSubmit(e, form);
            if (shouldContinue === false) {
                return false;
            }
        }

        if (preventDoubleSubmit) {
            isSubmitting = true;
            if (submitButton) {
                submitButton.disabled = true;
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = submitButtonLoadingText;

                setTimeout(() => {
                    if (isSubmitting) {
                        isSubmitting = false;
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                }, timeout);
            }
        }

        e.target.submit();
    });

    return {
        form,
        reset: () => {
            isSubmitting = false;
            if (submitButton) {
                submitButton.disabled = false;
            }
        }
    };
}

export function setupFormValidationLegacy(form, namaInput, emailInput, namaErrorId = 'nama_karyawan_error', emailErrorId = 'email_karyawan_error') {
    if (!form || !namaInput || !emailInput) return;

    const namaError = document.getElementById(namaErrorId);
    const emailError = document.getElementById(emailErrorId);

    namaInput.addEventListener('blur', () => validateTextField(namaInput, namaError));
    emailInput.addEventListener('blur', () => validateEmail(emailInput, emailError));

    form.addEventListener('submit', function (e) {
        const isNamaValid = validateTextField(namaInput, namaError);
        const isEmailValid = validateEmail(emailInput, emailError);

        if (!isNamaValid || !isEmailValid) {
            e.preventDefault();
        }
    });
}