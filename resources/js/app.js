import './bootstrap';

import Alpine from 'alpinejs';
import * as Recaptcha from './captcha';

window.Alpine = Alpine;
window.Recaptcha = Recaptcha;

Alpine.start();
