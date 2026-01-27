import './bootstrap';
import { initSupabaseRealtime } from './supabase-realtime.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.initSupabaseRealtime = initSupabaseRealtime;

Alpine.start();
