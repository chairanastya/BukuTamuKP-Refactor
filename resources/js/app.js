import './bootstrap';

import Alpine from 'alpinejs';
import { initSidebar, toggleSidebar, closeSidebar } from './sidebar';

window.Alpine = Alpine;
window.toggleSidebar = toggleSidebar;
window.closeSidebar = closeSidebar;

Alpine.start();

initSidebar();
