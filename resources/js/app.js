import './bootstrap';
import { initWebcam } from './webcam.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.initWebcam = initWebcam;

Alpine.start();
