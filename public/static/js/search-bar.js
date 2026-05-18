// Ta klasa obsługuje cały mechanizm podpowiedzi w polu wyszukiwania.
class SearchSuggestions {
    // Konstruktor uruchamia się od razu po stworzeniu obiektu.
    constructor(form) {
        // Zapamiętujemy cały formularz wyszukiwania.
        this.form = form;
        // Szukamy inputa z frazą wyszukiwania.
        this.input = form.querySelector('[data-search-input]');
        // Szukamy pustego kontenera, do którego później wstawimy sugestie.
        this.panel = form.querySelector('[data-search-suggestions]');
        // Pobieramy adres endpointu z atrybutu data w HTML.
        this.endpoint = form.dataset.suggestionsEndpoint;
        // Ta zmienna przechowa identyfikator timera debounce.
        this.debounceTimer = null;
        // Ta zmienna pozwoli anulować poprzedni request fetch.
        this.abortController = null;
        // Tutaj będziemy trzymać wszystkie linki z sugestiami.
        this.items = [];
        // Tu zapisujemy numer aktualnie podświetlonej sugestii.
        this.activeIndex = -1;

        // Jeśli nie znaleźliśmy któregoś potrzebnego elementu, kończymy działanie.
        if (!this.input || !this.panel || !this.endpoint) {
            return;
        }

        // Gdy wszystko jest gotowe, podpinamy eventy.
        this.bindEvents();
    }

    // Ta metoda podpina wszystkie potrzebne nasłuchiwania zdarzeń.
    bindEvents() {
        // Reagujemy na każdą zmianę w inpucie.
        this.input.addEventListener('input', () => {
            // Jeśli użytkownik dalej pisze, kasujemy poprzedni timer.
            window.clearTimeout(this.debounceTimer);
            // Nowy timer odpali pobieranie sugestii po krótkiej pauzie.
            this.debounceTimer = window.setTimeout(() => this.handleInput(), 180);
        });

        // Reagujemy na klawisze takie jak strzałki, Enter i Escape.
        this.input.addEventListener('keydown', (event) => this.handleKeydown(event));

        // Gdy input odzyska fokus, możemy od razu odświeżyć sugestie.
        this.input.addEventListener('focus', () => {
            // Nie pobieramy sugestii dla bardzo krótkiej frazy.
            if (this.input.value.trim().length >= 2) {
                this.handleInput();
            }
        });

        // Nasłuchujemy kliknięć w całym dokumencie.
        document.addEventListener('click', (event) => {
            // Jeśli kliknięcie było poza formularzem, zamykamy dropdown.
            if (!this.form.contains(event.target)) {
                this.close();
            }
        });
    }

    // Ta metoda wysyła request do backendu i pobiera sugestie.
    async handleInput() {
        // Pobieramy tekst wpisany przez użytkownika i usuwamy zbędne spacje.
        const query = this.input.value.trim();

        // Jeśli fraza jest za krótka, czyścimy stan i nic więcej nie robimy.
        if (query.length < 2) {
            this.reset();
            this.close();
            return;
        }

        // Jeśli poprzedni request jeszcze się nie skończył, anulujemy go.
        if (this.abortController) {
            this.abortController.abort();
        }

        // Tworzymy nowy kontroler dla bieżącego requestu.
        this.abortController = new AbortController();

        // Komunikację z backendem obsługujemy wewnątrz try/catch.
        try {
            // Wysyłamy zapytanie GET z frazą q zakodowaną bezpiecznie do URL-a.
            const response = await fetch(`${this.endpoint}?q=${encodeURIComponent(query)}`, {
                // Prosimy backend o odpowiedź w formacie JSON.
                headers: {
                    Accept: 'application/json',
                },
                // Przekazujemy signal, żeby request można było anulować.
                signal: this.abortController.signal,
            });

            // Jeśli serwer zwróci błąd HTTP, przerywamy dalsze wykonanie.
            if (!response.ok) {
                throw new Error('Request failed');
            }

            // Zamieniamy odpowiedź HTTP na obiekt JavaScript.
            const payload = await response.json();
            // Upewniamy się, że suggestions to naprawdę tablica.
            const suggestions = Array.isArray(payload.suggestions) ? payload.suggestions : [];

            // Renderujemy dropdown na podstawie danych z backendu.
            this.render(suggestions, payload.resultsUrl || this.form.action);
        } catch (error) {
            // AbortError jest normalny przy szybkim pisaniu, więc go pomijamy.
            if (error.name !== 'AbortError') {
                // Przy realnym błędzie czyścimy dane i zamykamy panel.
                this.reset();
                this.close();
            }
        }
    }

    // Ta metoda obsługuje klawiaturę podczas wpisywania.
    handleKeydown(event) {
        // Jeśli nie ma żadnych sugestii, nie ma po czym nawigować.
        if (!this.items.length) {
            // Escape nadal może zamknąć panel.
            if (event.key === 'Escape') {
                this.close();
            }
            return;
        }

        // Strzałka w dół przechodzi do kolejnej sugestii.
        if (event.key === 'ArrowDown') {
            // Blokujemy domyślne zachowanie kursora w inpucie.
            event.preventDefault();
            // Przechodzimy do kolejnego elementu, a po końcu wracamy na początek.
            this.setActiveIndex((this.activeIndex + 1) % this.items.length);
            return;
        }

        // Strzałka w górę przechodzi do poprzedniej sugestii.
        if (event.key === 'ArrowUp') {
            event.preventDefault();
            // Dodajemy długość tablicy, żeby wynik nie wyszedł ujemny.
            this.setActiveIndex((this.activeIndex - 1 + this.items.length) % this.items.length);
            return;
        }

        // Enter otwiera aktualnie podświetloną sugestię.
        if (event.key === 'Enter' && this.activeIndex >= 0) {
            event.preventDefault();
            // Pobieramy aktywny link z tablicy elementów.
            const activeItem = this.items[this.activeIndex];
            // Sprawdzamy, czy link istnieje.
            if (activeItem) {
                // Przechodzimy na stronę produktu.
                window.location.href = activeItem.href;
            }
            return;
        }

        // Escape zamyka dropdown.
        if (event.key === 'Escape') {
            this.close();
        }
    }

    // Ta metoda buduje zawartość panelu z sugestiami.
    render(suggestions, resultsUrl) {
        // Czyścimy poprzednią zawartość dropdownu.
        this.panel.innerHTML = '';
        // Czyścimy tablicę linków.
        this.items = [];
        // Zerujemy aktywny indeks.
        this.activeIndex = -1;

        // Jeśli backend nic nie zwrócił, pokazujemy komunikat.
        if (!suggestions.length) {
            // Wstawiamy prosty stan pusty do środka panelu.
            this.panel.innerHTML = `
                <div class="search-suggestions__empty">
                    Brak podpowiedzi dla tej frazy.
                </div>
            `;
            // Pokazujemy panel, żeby użytkownik wiedział, że nic nie znaleziono.
            this.open();
            return;
        }

        // Tworzymy fragment dokumentu, żeby składać listę poza głównym DOM-em.
        const fragment = document.createDocumentFragment();

        // Przechodzimy po każdej sugestii zwróconej z backendu.
        suggestions.forEach((suggestion, index) => {
            // Tworzymy link prowadzący do strony produktu.
            const link = document.createElement('a');
            // Dodajemy klasę potrzebną do stylowania.
            link.className = 'search-suggestion-item';
            // Ustawiamy adres produktu.
            link.href = suggestion.url;
            // role="option" pomaga dostępności.
            link.setAttribute('role', 'option');
            // Nadajemy unikalne id, żeby powiązać aktywny element z inputem.
            link.id = `search-suggestion-${index}`;

            // Jeśli produkt ma obrazek, pokazujemy obrazek.
            // Jeśli nie ma, pokazujemy placeholder z ikoną.
            const image = suggestion.image
                ? `<img src="${this.escapeHtml(suggestion.image)}" alt="" class="search-suggestion-item__image" loading="lazy">`
                : `<span class="search-suggestion-item__placeholder"><i class="bi bi-search"></i></span>`;

            // Składamy wewnętrzny HTML pojedynczej sugestii.
            link.innerHTML = `
                ${image}
                <span class="search-suggestion-item__content">
                    <span class="search-suggestion-item__title">${this.escapeHtml(suggestion.name || '')}</span>
                    <span class="search-suggestion-item__meta">${this.escapeHtml(suggestion.price || 'Zobacz produkt')}</span>
                </span>
            `;

            // Najechanie myszką ustawia aktywną sugestię.
            link.addEventListener('mouseenter', () => this.setActiveIndex(index));
            // Dodajemy link do fragmentu dokumentu.
            fragment.appendChild(link);
            // Zapamiętujemy link w tablicy do obsługi klawiatury.
            this.items.push(link);
        });

        // Tworzymy dodatkowy link do pełnej listy wyników.
        const footerLink = document.createElement('a');
        // Dodajemy klasę do stylowania.
        footerLink.className = 'search-suggestions__results-link';
        // Ustawiamy link do pełnych wyników.
        footerLink.href = resultsUrl;
        // Ustawiamy tekst widoczny dla użytkownika.
        footerLink.textContent = `Zobacz wszystkie wyniki dla "${this.input.value.trim()}"`;
        // Doklejamy go pod właściwe sugestie.
        fragment.appendChild(footerLink);

        // Wstawiamy gotowy fragment do panelu.
        this.panel.appendChild(fragment);
        // Pokazujemy panel użytkownikowi.
        this.open();
    }

    // Ta metoda ustawia aktywną sugestię i aktualizuje jej wygląd.
    setActiveIndex(index) {
        // Zapisujemy numer aktywnej sugestii.
        this.activeIndex = index;

        // Przechodzimy po wszystkich elementach dropdownu.
        this.items.forEach((item, itemIndex) => {
            // Sprawdzamy, czy to właśnie ten aktywny element.
            const isActive = itemIndex === index;
            // Dodajemy albo usuwamy klasę podświetlenia.
            item.classList.toggle('is-active', isActive);
            // Dalsze akcje wykonujemy tylko dla aktywnego elementu.
            if (isActive) {
                // Aktualizujemy atrybut ARIA dla technologii asystujących.
                this.input.setAttribute('aria-activedescendant', item.id);
                // Jeśli aktywny element jest poza widokiem, przewijamy listę.
                item.scrollIntoView({ block: 'nearest' });
            }
        });
    }

    // Ta metoda pokazuje dropdown.
    open() {
        // Usuwamy klasę Bootstrap ukrywającą element.
        this.panel.classList.remove('d-none');
        // Informujemy, że lista jest rozwinięta.
        this.input.setAttribute('aria-expanded', 'true');
    }

    // Ta metoda chowa dropdown.
    close() {
        // Dodajemy klasę ukrywającą.
        this.panel.classList.add('d-none');
        // Informujemy, że lista jest zwinięta.
        this.input.setAttribute('aria-expanded', 'false');
        // Czyścimy informację o aktywnym elemencie.
        this.input.removeAttribute('aria-activedescendant');
        // Zerujemy indeks aktywnej sugestii.
        this.activeIndex = -1;
        // Usuwamy klasę aktywności ze wszystkich linków.
        this.items.forEach((item) => item.classList.remove('is-active'));
    }

    // Ta metoda czyści dane trzymane przez komponent.
    reset() {
        // Czyścimy HTML panelu.
        this.panel.innerHTML = '';
        // Czyścimy tablicę linków.
        this.items = [];
        // Zerujemy aktywny indeks.
        this.activeIndex = -1;
    }

    // Ta metoda zamienia niebezpieczne znaki na bezpieczne encje HTML.
    escapeHtml(value) {
        // Zamieniamy wartość na string i podmieniamy znaki specjalne.
        return String(value).replace(/[&<>\"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        }[char]));
    }
}

// Szukamy wszystkich formularzy oznaczonych atrybutem data-search-form.
document.querySelectorAll('[data-search-form]').forEach((form) => {
    // Dla każdego formularza tworzymy osobny komponent search suggestions.
    new SearchSuggestions(form);
});
