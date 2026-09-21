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
        menuButton.setAttribute('aria-expanded', 'false');
    };

    menuButton.addEventListener('click', () => {
        const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
        mobileMenu.classList.toggle('hidden', isOpen);
        menuButton.setAttribute('aria-expanded', String(!isOpen));
    });

    mobileMenu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            closeMenu();
        }
    });
}

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const revealSelectors = [
    '.reveal-on-scroll',
    '#main-content > header',
    '#main-content > form',
    '#main-content > section',
    '#main-content > aside',
    'main > header .site-shell > *',
    'main .section-kicker',
    'main .section-title',
    'main .surface-card',
    'main article',
    'main figure',
    'main details',
];

const revealElements = [...new Set(document.querySelectorAll(revealSelectors.join(',')))]
    .filter((element) => !element.closest('[hidden]'));

revealElements.forEach((element) => {
    if (!element.hasAttribute('data-reveal')) {
        element.dataset.reveal = element.matches('figure, details') ? 'scale' : 'up';
    }

    const explicitDelay = [1, 2, 3, 4].find((delay) => element.classList.contains(`reveal-delay-${delay}`));

    if (explicitDelay) {
        element.style.setProperty('--reveal-delay', `${explicitDelay * 90}ms`);
    } else {
        const siblings = [...element.parentElement.children].filter((sibling) => revealElements.includes(sibling));
        const siblingIndex = Math.max(0, siblings.indexOf(element));
        element.style.setProperty('--reveal-delay', `${Math.min(siblingIndex, 4) * 70}ms`);
    }
});

document.querySelectorAll('.btn-primary').forEach((button) => button.classList.add('motion-shimmer'));
document.querySelectorAll('main > header .inline-flex.rounded-full').forEach((badge) => badge.classList.add('motion-float'));

if (prefersReducedMotion || !('IntersectionObserver' in window)) {
    revealElements.forEach((element) => element.classList.add('is-visible'));
} else {
    root.classList.add('motion-ready');

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        rootMargin: '0px 0px -8% 0px',
        threshold: 0.08,
    });

    revealElements.forEach((element) => revealObserver.observe(element));
}
