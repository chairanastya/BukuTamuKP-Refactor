import './bootstrap';

import Alpine from 'alpinejs';
import { DataTableManager } from './datatables-init.js';

window.Alpine = Alpine;
window.DataTableManager = DataTableManager;

Alpine.start();
