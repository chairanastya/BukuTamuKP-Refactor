import './bootstrap';

import Alpine from 'alpinejs';
import { exportDataTablePDF, exportContentPDF } from './pdf-export.js';

window.Alpine = Alpine;
window.exportDataTablePDF = exportDataTablePDF;
window.exportContentPDF = exportContentPDF;

Alpine.start();
