import './masks';
import './nav';

const themeKey = 'cdu-theme';
const setTheme = (theme) => {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    document.documentElement.dataset.theme = theme;
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', String(theme === 'dark'));
        button.setAttribute('title', theme === 'dark' ? 'Usar tema claro' : 'Usar tema escuro');
        const label = button.querySelector('[data-theme-label]');
        if (label) label.textContent = theme === 'dark' ? 'Tema claro' : 'Tema escuro';
    });
};

setTheme(localStorage.getItem(themeKey) || 'light');
document.addEventListener('livewire:navigated', () => {
    setTheme(localStorage.getItem(themeKey) || 'light');
});

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-toggle]');
    if (!button) return;
    const theme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
    localStorage.setItem(themeKey, theme);
    setTheme(theme);
});
