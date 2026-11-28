(() => {
    const themeStorageKey = 'cccc-theme';
    const root = document.documentElement;
    const storedTheme = window.localStorage.getItem(themeStorageKey);

    const updateToggleButtons = () => {
        const isDark = root.dataset.theme === 'dark';

        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            button.setAttribute('aria-pressed', String(isDark));
            button.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
            button.setAttribute('title', isDark ? 'Switch to light mode' : 'Switch to dark mode');
        });
    };

    const applyTheme = (theme) => {
        const isDark = theme === 'dark';
        root.dataset.theme = isDark ? 'dark' : 'light';
        updateToggleButtons();

        const themeColor = document.querySelector('meta[name="theme-color"]');
        if (themeColor) {
            themeColor.setAttribute('content', isDark ? '#262624' : '#faf9f5');
        }
    };

    applyTheme(storedTheme === 'dark' ? 'dark' : 'light');
    document.addEventListener('DOMContentLoaded', updateToggleButtons, { once: true });

    document.addEventListener('click', (event) => {
        const target = event.target;
        const button = target instanceof Element ? target.closest('[data-theme-toggle]') : null;
        if (!button) {
            return;
        }

        const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        window.localStorage.setItem(themeStorageKey, nextTheme);
        applyTheme(nextTheme);
    });

    window.addEventListener('storage', (event) => {
        if (event.key === themeStorageKey) {
            applyTheme(event.newValue === 'dark' ? 'dark' : 'light');
        }
    });
})();
