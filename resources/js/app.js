import './bootstrap';

import Alpine from 'alpinejs';

// Import reCAPTCHA utilities and make them globally available
import * as Recaptcha from './captcha';

window.Alpine = Alpine;
window.Recaptcha = Recaptcha;

Alpine.start();
