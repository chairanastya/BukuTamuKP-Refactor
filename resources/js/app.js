import './bootstrap';

import Alpine from 'alpinejs';

import { setupFormValidation, validateNama, validateEmail } from './form-validation';

window.Alpine = Alpine;

// Expose form validation helpers to global scope so blade views can call them
window.setupFormValidation = setupFormValidation;
window.validateNama = validateNama;
window.validateEmail = validateEmail;

Alpine.start();
