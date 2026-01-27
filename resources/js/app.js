import './bootstrap';

import Alpine from 'alpinejs';
import { clearOldImageStorages } from './image-storage';

window.Alpine = Alpine;
window.addEventListener('DOMContentLoaded', function() {
    clearOldImageStorages();
});


Alpine.start();