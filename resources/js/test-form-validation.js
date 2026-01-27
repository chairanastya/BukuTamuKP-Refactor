import { validateNama, validateEmail, setupFormValidation } from './form-validation.js';

// Setup form validation for the test page
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('test_form');
    const namaInput = document.getElementById('nama_karyawan');
    const emailInput = document.getElementById('email_karyawan');

    if (form && namaInput && emailInput) {
        setupFormValidation(form, namaInput, emailInput);
    }
});