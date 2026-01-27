import './bootstrap';

import Alpine from 'alpinejs';
import { initModals } from './modals.js';

window.Alpine = Alpine;
window.initModals = initModals;

Alpine.start();
