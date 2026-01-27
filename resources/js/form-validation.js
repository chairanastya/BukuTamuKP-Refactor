// Form validation functions
function validateNama() {
    const namaError = document.getElementById('nama_karyawan_error');
    if (namaInput.value.trim() === '') {
        namaError.classList.add('show');
        return false;
    } else {
        namaError.classList.remove('show');
        return true;
    }
}

function validateEmail() {
    const emailError = document.getElementById('email_karyawan_error');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailInput.value.trim())) {
        emailError.classList.add('show');
        return false;
    } else {
        emailError.classList.remove('show');
        return true;
    }
}

// Make functions global
window.validateNama = validateNama;
window.validateEmail = validateEmail;

// Event listeners (assuming global variables are defined in the view)
document.addEventListener('DOMContentLoaded', function () {
    if (typeof namaInput !== 'undefined') {
        namaInput.addEventListener('blur', validateNama);
    }
    if (typeof emailInput !== 'undefined') {
        emailInput.addEventListener('blur', validateEmail);
    }
    if (typeof form !== 'undefined') {
        form.addEventListener('submit', function (e) {
            const isNamaValid = validateNama();
            const isEmailValid = validateEmail();

            if (!isNamaValid || !isEmailValid) {
                e.preventDefault();
            }
        });
    }
});