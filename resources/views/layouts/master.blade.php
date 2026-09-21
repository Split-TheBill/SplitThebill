<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Split TheBill membantu kamu berbagi biaya langganan premium secara lebih hemat, mudah, dan transparan.">
    <meta name="theme-color" content="#f5f3f6" data-theme-color>
    <title>@yield('title', 'Split TheBill')</title>
    <script>
        (() => {
            const storageKey = 'stb-theme';
            let theme;

            try {
                const savedTheme = window.localStorage.getItem(storageKey);
                theme = savedTheme === 'light' || savedTheme === 'dark'
                    ? savedTheme
                    : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            } catch (error) {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            document.documentElement.dataset.theme = theme;
            document.documentElement.classList.toggle('dark', theme === 'dark');
            document.documentElement.style.colorScheme = theme;
            document.querySelector('[data-theme-color]')?.setAttribute('content', theme === 'dark' ? '#110b18' : '#f5f3f6');
        })();
    </script>
    @stack('before-styles')
    <link href="{{asset('output.css')}}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('after-styles')
</head>
<body class="overflow-x-hidden">

    @yield('content')

    <button
        type="button"
        class="theme-toggle theme-toggle--floating"
        data-theme-toggle
        data-floating-theme-toggle
        aria-label="Aktifkan mode gelap"
        aria-pressed="false"
        title="Aktifkan mode gelap"
    >
        <svg class="theme-toggle__icon theme-toggle__icon--moon" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M20.2 15.3A8.4 8.4 0 0 1 8.7 3.8 8.5 8.5 0 1 0 20.2 15.3Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
        </svg>
        <svg class="theme-toggle__icon theme-toggle__icon--sun" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="12" r="3.7" fill="none" stroke="currentColor" stroke-width="1.8" />
            <path d="M12 2.3v2M12 19.7v2M4.3 12h-2M21.7 12h-2M5.1 5.1l1.4 1.4M17.5 17.5l1.4 1.4M18.9 5.1l-1.4 1.4M6.5 17.5l-1.4 1.4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
        </svg>
        <span class="sr-only" data-theme-toggle-label>Aktifkan mode gelap</span>
    </button>

    @stack('after-scripts')
</body>
</html>
