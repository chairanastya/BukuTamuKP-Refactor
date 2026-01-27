import './bootstrap';

import Alpine from 'alpinejs';
import { initSidebar, toggleSidebar, closeSidebar } from './sidebar';
import { initDropdown } from './dropdown.js';
import { DataTableManager } from './datatables-init.js';
import { initModals } from './modals.js';
import { ExcelExporter } from './excel-export';
import { initLoadingSpinner } from './loading-spinner';

window.ExcelExporter = ExcelExporter;
window.Alpine = Alpine;
window.toggleSidebar = toggleSidebar;
window.closeSidebar = closeSidebar;
window.initDropdown = initDropdown;
window.DataTableManager = DataTableManager;
window.initModals = initModals;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.createInlineSpinner = createInlineSpinner;

Alpine.start();

initSidebar();
initLoadingSpinner();
