import './bootstrap';

import Alpine from 'alpinejs';
import { initSidebar, toggleSidebar, closeSidebar } from './sidebar';
import { initDropdown } from './dropdown.js';

window.Alpine = Alpine;
window.toggleSidebar = toggleSidebar;
window.closeSidebar = closeSidebar;
window.initDropdown = initDropdown;

Alpine.start();

initSidebar();