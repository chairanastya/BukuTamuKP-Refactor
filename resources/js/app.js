import './bootstrap';

import Alpine from 'alpinejs';
import { ExcelExporter } from './excel-export';

window.Alpine = Alpine;
window.ExcelExporter = ExcelExporter;

Alpine.start();
