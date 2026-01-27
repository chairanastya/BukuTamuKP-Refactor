import './bootstrap';

import Alpine from 'alpinejs';
import * as KaryawanRowManager from './karyawan-row-manager.js';

window.Alpine = Alpine;
window.addKaryawanRow = KaryawanRowManager.addKaryawanRow;
window.removeKaryawanRow = KaryawanRowManager.removeKaryawanRow;
window.setupRowListeners = KaryawanRowManager.setupRowListeners;
window.searchKaryawan = KaryawanRowManager.searchKaryawan;
window.displayAutocomplete = KaryawanRowManager.displayAutocomplete;
window.selectKaryawan = KaryawanRowManager.selectKaryawan;
window.renderKaryawanCard = KaryawanRowManager.renderKaryawanCard;
window.updateHiddenInput = KaryawanRowManager.updateHiddenInput;
window.resetKaryawanRow = KaryawanRowManager.resetKaryawanRow;
window.updateMinusButtonsVisibility = KaryawanRowManager.updateMinusButtonsVisibility;
window.setSearchKaryawanRoute = KaryawanRowManager.setSearchKaryawanRoute;
window.setEscapeHtmlFn = KaryawanRowManager.setEscapeHtmlFn;

Alpine.start();
