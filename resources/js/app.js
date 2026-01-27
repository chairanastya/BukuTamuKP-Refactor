import './bootstrap';

import Alpine from 'alpinejs';
import { updateInputBackground, initInputBackgrounds } from './input-background.js';

window.Alpine = Alpine;
window.updateInputBackground = updateInputBackground;
window.initInputBackgrounds = initInputBackgrounds;

Alpine.start();
