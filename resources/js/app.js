import './bootstrap';
import './multi-step-form';
import './components/editor';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.deferLoadingAlpine = function (callback) { callback(); };

Alpine.start();
