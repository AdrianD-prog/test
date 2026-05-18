/**
 * Inicjalizacja i wyświetlenie komunikatu Toast (Bootstrap) na stronie.
 */
const toastElement = document.getElementById('liveToast');
if (toastElement) {
    const toastBootstrap = new bootstrap.Toast(toastElement);
    toastBootstrap.show();
}

// Dark mode
(function() {
    // Sprawdź czy użytkownik ma zapisane preferencje
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    // Ustaw początkowy motyw
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.body.classList.add('dark-mode');
    }

    // Stwórz przycisk przełącznika DOPIERO po zdefiniowaniu funkcji
    function getToggleIcon(isDark) {
        if (isDark) {
            // Ikona słońca (tryb jasny)
            return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>`;
        } else {
            // Ikona księżyca (tryb ciemny)
            return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>`;
        }
    }

    function updateToggleIcon(isDark) {
        const toggleButton = document.querySelector('.theme-toggle');
        if (toggleButton) {
            toggleButton.innerHTML = getToggleIcon(isDark);
        }
    }

    // Utwórz przycisk
    const toggleButton = document.createElement('button');
    toggleButton.className = 'theme-toggle';
    toggleButton.setAttribute('aria-label', 'Przełącz tryb ciemny');
    toggleButton.innerHTML = getToggleIcon(document.body.classList.contains('dark-mode'));
    document.body.appendChild(toggleButton);

    // Obsługa kliknięcia
    toggleButton.addEventListener('click', function() {
        // Dodaj klasę blokującą przejścia
        document.body.classList.add('no-transition');
        
        const isDark = document.body.classList.toggle('dark-mode');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateToggleIcon(isDark);

        // Usuń klasę blokującą przejścia po krótkiej chwili
        // Używamy setTimeout, aby przeglądarka zdążyła przeliczyć style bez przejść
        setTimeout(() => {
            document.body.classList.remove('no-transition');
        }, 100);
    });
})();

// Potwierdzanie przekierowania na inną dommene
document.querySelectorAll('a[href]').forEach(link => {
    link.addEventListener('click', function(e) {
        if (link.hostname !== window.location.hostname) {
            e.preventDefault();
            const confirmed = confirm('Zaraz opuścisz tą stronę. Czy na pewno chcesz to zrobić?');
            if (confirmed) {
                window.location.href = this.href;
            }
        }
    });
});

// Animacja pojawiania się produktów przy przewijaniu
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Funkcja inicjalizująca obserwację
    function initReveals() {
        const reveals = document.querySelectorAll('.reveal:not(.active)');
        reveals.forEach(reveal => {
            revealObserver.observe(reveal);
        });
    }

    initReveals();

    // Re-inicjalizacja przy zmianach w DOM (np. jeśli produkty są doładowywane dynamicznie)
    const mutationObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.addedNodes.length) {
                initReveals();
            }
        });
    });

    mutationObserver.observe(document.body, { childList: true, subtree: true });
});
