import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import Sortable from 'sortablejs';

window.Alpine = Alpine;
window.Chart = Chart;
window.Sortable = Sortable;

/* ------------------------------- tema claro/oscuro ------------------------------ */
const aplicarTema = (oscuro) => {
    document.documentElement.classList.toggle('dark', oscuro);
    localStorage.setItem('fieldops-tema', oscuro ? 'oscuro' : 'claro');
};

window.toggleTema = () => aplicarTema(!document.documentElement.classList.contains('dark'));

// Se aplica antes del primer paint desde el layout para evitar el flash.
if (localStorage.getItem('fieldops-tema') === 'oscuro') {
    document.documentElement.classList.add('dark');
}

/* --------------------------------- miscelaneos -------------------------------- */
window.confirmarAccion = (mensaje) => window.confirm(mensaje);

// Auto-cierre de los avisos (toasts) a los 5 segundos.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-toast]').forEach((el) => {
        setTimeout(() => el.classList.add('opacity-0', 'translate-y-1'), 5000);
        setTimeout(() => el.remove(), 5400);
    });
});

Alpine.start();
