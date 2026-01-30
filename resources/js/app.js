import './bootstrap';

import Alpine from 'alpinejs';
import { exportDataTablePDF, exportContentPDF } from './pdf-export.js';
import { exportNotulensiPDF } from './notulensi-export.js';
import { initSidebar, toggleSidebar, closeSidebar } from './sidebar';
import { initDropdown } from './dropdown.js';
import { DataTableManager } from './datatables-init.js';
import { initModals } from './modals.js';
import { renderKaryawanListModal, renderDetailModal, renderKtpModal } from './modal-content.js';
import { ExcelExporter } from './excel-export';
import { initLoadingSpinner, showLoading, hideLoading, createInlineSpinner } from './loading-spinner';
import { createStatusFilter } from './status-filter.js';
import { initDatatableFilter } from './datatables-filters.js';
import { DatatableMultiFilter } from './datatable-multi-filter.js';
import { updateInputBackground, initInputBackgrounds } from './input-background.js';
import { initPasswordToggle } from './password-toggle.js';
import * as KaryawanRowManager from './karyawan-row-manager.js';
import { initWebcam } from './webcam.js';
import { createImageStorage, clearOldImageStorages } from './image-storage';
import { initSupabaseRealtime } from './supabase-realtime.js';
import * as Recaptcha from './captcha';
import { setupFormValidation, validateNama, validateEmail } from './form-validation';
import { createAutocomplete } from './autocomplete';

window.Alpine = Alpine;
window.exportDataTablePDF = exportDataTablePDF;
window.exportContentPDF = exportContentPDF;
window.exportNotulensiPDF = exportNotulensiPDF;
window.toggleSidebar = toggleSidebar;
window.closeSidebar = closeSidebar;
window.DataTableManager = DataTableManager;
window.initModals = initModals;
window.renderKaryawanListModal = renderKaryawanListModal;
window.renderDetailModal = renderDetailModal;
window.renderKtpModal = renderKtpModal;
window.ExcelExporter = ExcelExporter;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.createInlineSpinner = createInlineSpinner;
window.createStatusFilter = createStatusFilter;
window.initDatatableFilter = initDatatableFilter;
window.DatatableMultiFilter = DatatableMultiFilter;
window.updateInputBackground = updateInputBackground;
window.initInputBackgrounds = initInputBackgrounds;
window.initPasswordToggle = initPasswordToggle;
window.addKaryawanRow = KaryawanRowManager.addKaryawanRow;
window.removeKaryawanRow = KaryawanRowManager.removeKaryawanRow;
window.setupRowListeners = KaryawanRowManager.setupRowListeners;
window.displayAutocomplete = KaryawanRowManager.displayAutocomplete;
window.selectKaryawan = KaryawanRowManager.selectKaryawan;
window.renderKaryawanCard = KaryawanRowManager.renderKaryawanCard;
window.updateHiddenInput = KaryawanRowManager.updateHiddenInput;
window.resetKaryawanRow = KaryawanRowManager.resetKaryawanRow;
window.updateMinusButtonsVisibility = KaryawanRowManager.updateMinusButtonsVisibility;
window.setSearchKaryawanRoute = KaryawanRowManager.setSearchKaryawanRoute;
window.setEscapeHtmlFn = KaryawanRowManager.setEscapeHtmlFn;
window.getSelectedKaryawan = KaryawanRowManager.getSelectedKaryawan;
window.preloadKaryawanData = KaryawanRowManager.preloadKaryawanData;
window.initWebcam = initWebcam;
window.createImageStorage = createImageStorage;
window.initSupabaseRealtime = initSupabaseRealtime;
window.Recaptcha = Recaptcha;
window.setupFormValidation = setupFormValidation;
window.validateNama = validateNama;
window.validateEmail = validateEmail;
window.createAutocomplete = createAutocomplete;

Alpine.start();

document.addEventListener('DOMContentLoaded', function() {
    clearOldImageStorages();
    if (document.getElementById('dropdown')) {
        initDropdown('dropdown');
    }
    initSidebar();
    initLoadingSpinner();
    initModals();
    initInputBackgrounds();
    initPasswordToggle();
    if (
        document.getElementById('webcam_video') &&
        document.getElementById('capture_canvas') &&
        document.getElementById('webcam_modal')
    ) {
        initWebcam();
    }
    initSupabaseRealtime();
    if (window.dataTableInstance) {
        initDatatableFilter({ tableInstance: window.dataTableInstance });
    }
});
