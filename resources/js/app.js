import './bootstrap';

import Alpine from 'alpinejs';
import { initLoadingSpinner } from './loading-spinner';

window.Alpine = Alpine;

Alpine.start();

initLoadingSpinner();
