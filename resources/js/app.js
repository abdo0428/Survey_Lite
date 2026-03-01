import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const applyTheme = (theme) => {
    const resolved = theme === 'dark' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', resolved);
    document.documentElement.setAttribute('data-bs-theme', resolved);
    localStorage.setItem('survey-lite-theme', resolved);
};

window.surveyLiteToast = (icon, title) => {
    if (!window.Swal) {
        return;
    }

    window.Swal.fire({
        toast: true,
        position: 'top-end',
        timer: 2200,
        timerProgressBar: true,
        showConfirmButton: false,
        icon,
        title,
    });
};

document.addEventListener('DOMContentLoaded', () => {
    applyTheme(localStorage.getItem('survey-lite-theme') || 'light');

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
        });
    });
});
