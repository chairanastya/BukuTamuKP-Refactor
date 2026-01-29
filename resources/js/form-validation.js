// Form validation functions
export function validateNama(namaInput, namaErrorId = 'nama_karyawan_error') {
    const namaError = document.getElementById(namaErrorId);
    if (namaInput.value.trim() === '') {
        namaError.classList.add('show');
        return false;
    } else {
        namaError.classList.remove('show');
        return true;
    }
}

export function validateEmail(emailInput, emailErrorId = 'email_karyawan_error') {
    const emailError = document.getElementById(emailErrorId);
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailInput.value.trim())) {
        emailError.classList.add('show');
        return false;
    } else {
        emailError.classList.remove('show');
        return true;
    }
}

// Setup function to be called with specific elements
export function setupFormValidation(form, namaInput, emailInput, namaErrorId = 'nama_karyawan_error', emailErrorId = 'email_karyawan_error') {
    if (!form || !namaInput || !emailInput) return;
    
    namaInput.addEventListener('blur', () => validateNama(namaInput, namaErrorId));
    emailInput.addEventListener('blur', () => validateEmail(emailInput, emailErrorId));

    form.addEventListener('submit', function (e) {
        const isNamaValid = validateNama(namaInput, namaErrorId);
        const isEmailValid = validateEmail(emailInput, emailErrorId);

        if (!isNamaValid || !isEmailValid) {
            e.preventDefault();
        }
    });
}