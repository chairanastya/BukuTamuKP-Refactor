import './bootstrap';
import { initPasswordToggle } from './password-toggle.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.initPasswordToggle = initPasswordToggle;

Alpine.start();
