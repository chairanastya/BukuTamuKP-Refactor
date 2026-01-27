import './bootstrap';

import Alpine from 'alpinejs';
import { createStatusFilter } from './status-filter.js';

window.Alpine = Alpine;
window.createStatusFilter = createStatusFilter;

Alpine.start();
