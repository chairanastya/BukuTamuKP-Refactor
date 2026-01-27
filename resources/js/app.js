import './bootstrap';

import Alpine from 'alpinejs';
import { exportDataTablePDF, exportContentPDF } from './pdf-export.js';
import { initSidebar, toggleSidebar, closeSidebar } from './sidebar';
import { initDropdown } from './dropdown.js';
import { DataTableManager } from './datatables-init.js';
import { initModals } from './modals.js';
import { ExcelExporter } from './excel-export';
import { initLoadingSpinner } from './loading-spinner';
import { createStatusFilter } from './status-filter.js';
import { initDatatableFilter } from './datatables-filters.js';

window.Alpine = Alpine;
window.exportDataTablePDF = exportDataTablePDF;
window.exportContentPDF = exportContentPDF;
window.toggleSidebar = toggleSidebar;
window.closeSidebar = closeSidebar;
window.initDropdown = initDropdown;
window.DataTableManager = DataTableManager;
window.initModals = initModals;
window.ExcelExporter = ExcelExporter;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.createInlineSpinner = createInlineSpinner;
window.createStatusFilter = createStatusFilter;
window.initDatatableFilter = initDatatableFilter;

Alpine.start();

initSidebar();
initLoadingSpinner();
