import './bootstrap';

const THEME_STORAGE_KEY = 'stb-theme';
const root = document.documentElement;
const themeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
const themeColor = document.querySelector('[data-theme-color]');
const themeToggles = document.querySelectorAll('[data-theme-toggle]');
const navbarThemeToggle = document.querySelector('[data-navbar-theme-toggle]');
const floatingThemeToggle = document.querySelector('[data-floating-theme-toggle]');

const getSavedTheme = () => {
    try {
        const theme = window.localStorage.getItem(THEME_STORAGE_KEY);

        return theme === 'light' || theme === 'dark' ? theme : null;
    } catch (error) {
        return null;
    }
};

const applyTheme = (theme, animate = false) => {
    const isDark = theme === 'dark';
    const actionLabel = isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap';

    if (animate) {
        root.classList.add('theme-transition');
        window.setTimeout(() => root.classList.remove('theme-transition'), 350);
    }

    root.dataset.theme = theme;
    root.classList.toggle('dark', isDark);
    root.style.colorScheme = theme;

    if (themeColor) {
        themeColor.setAttribute('content', isDark ? '#110b18' : '#f5f3f6');
    }

    themeToggles.forEach((toggle) => {
        toggle.setAttribute('aria-pressed', String(isDark));
        toggle.setAttribute('aria-label', actionLabel);
        toggle.setAttribute('title', actionLabel);

        const label = toggle.querySelector('[data-theme-toggle-label]');
        if (label) {
            label.textContent = actionLabel;
        }
    });
};

applyTheme(root.dataset.theme || (themeMediaQuery.matches ? 'dark' : 'light'));

if (floatingThemeToggle && navbarThemeToggle) {
    floatingThemeToggle.hidden = true;
}

themeToggles.forEach((toggle) => {
    toggle.addEventListener('click', () => {
        const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';

        try {
            window.localStorage.setItem(THEME_STORAGE_KEY, nextTheme);
        } catch (error) {
            // The theme still changes for this page when storage is unavailable.
        }

        applyTheme(nextTheme, true);
    });
});

themeMediaQuery.addEventListener('change', (event) => {
    if (!getSavedTheme()) {
        applyTheme(event.matches ? 'dark' : 'light', true);
    }
});

const menuButton = document.querySelector('[data-mobile-menu-button]');
const mobileMenu = document.querySelector('[data-mobile-menu]');

if (menuButton && mobileMenu) {
    const closeMenu = () => {
        mobileMenu.classList.add('hidden');
        mobileMenu.classList.remove('mobile-navigation-enter');
        menuButton.setAttribute('aria-expanded', 'false');
        menuButton.setAttribute('aria-label', 'Buka menu navigasi');
    };

    menuButton.addEventListener('click', () => {
        const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
        mobileMenu.classList.toggle('hidden', isOpen);
        mobileMenu.classList.toggle('mobile-navigation-enter', !isOpen);
        menuButton.setAttribute('aria-expanded', String(!isOpen));
        menuButton.setAttribute('aria-label', isOpen ? 'Buka menu navigasi' : 'Tutup menu navigasi');
    });

    mobileMenu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menuButton.getAttribute('aria-expanded') === 'true') {
            closeMenu();
            menuButton.focus();
        }
    });

    document.addEventListener('pointerdown', (event) => {
        if (menuButton.getAttribute('aria-expanded') === 'true'
            && !menuButton.contains(event.target)
            && !mobileMenu.contains(event.target)) {
            closeMenu();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            closeMenu();
        }
    });
}

// The landing-page animation bundle is optional and never blocks checkout pages.
if (document.querySelector('[data-interactive-hero]')) {
    import('./motion').then(({ initMotion }) => initMotion()).catch((error) => {
        console.warn('Optional landing-page motion is unavailable.', error);
    });
}
