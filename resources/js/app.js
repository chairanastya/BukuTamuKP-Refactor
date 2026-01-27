import './bootstrap';

import Alpine from 'alpinejs';
import { initLoadingSpinner } from './loading-spinner';

window.Alpine = Alpine;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.createInlineSpinner = createInlineSpinner;

Alpine.start();

initLoadingSpinner();
