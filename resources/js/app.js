import './bootstrap';

import Alpine from 'alpinejs';
import { createAutocomplete } from './autocomplete';

window.Alpine = Alpine;
window.createAutocomplete = createAutocomplete;

Alpine.start();
