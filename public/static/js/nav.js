/**
 * Klasa obsługująca chowanie i pokazywanie paska nawigacji podczas przewijania strony.
 */
class NavScroll{
    /**
     * @param {string} navSelector - Selektor CSS elementu nawigacji
     */
    constructor( navSelector ) {
        /** @type {string} */
        this.navSelector = navSelector;
        this.init()
    }

    /**
     * Inicjalizuje komponent, pobiera element DOM i ustawia początkowe wartości.
     * @returns {void}
     */
    init(){
       /** @type {HTMLElement|null} */
       this.navElement = document.querySelector(this.navSelector);
       /** @type {number} Pozycja skrolla z poprzedniego kroku */
       this.lastScrollTop = 0;
       this.scroll()
    }

    /**
     * Podpina zdarzenie scroll do okna przeglądarki.
     * @returns {void}
     */
    scroll(){
        window.addEventListener("scroll", () => {
            /** @type {number} Aktualna pozycja skrolla */
            this.scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            this.show();
            })
    }

    /**
     * Decyduje o pokazaniu lub ukryciu paska nawigacji na podstawie kierunku przewijania.
     * @returns {void}
     */
    show(){
        // Jeśli zmiana pozycji skrolla jest większa niż 3 piksele
        if(Math.abs(this.scrollTop - this.lastScrollTop) > 3){
            // Jeśli przewijamy w dół i jesteśmy poniżej 50px - ukryj nawigację
            if(this.scrollTop > this.lastScrollTop && this.scrollTop > 50){
                this.navElement.classList.add('nav-hidden');
            }
            // W przeciwnym razie (przewijanie w górę) - pokaż nawigację
            else{
                this.navElement.classList.remove('nav-hidden');
            }
        }

        this.lastScrollTop = this.scrollTop;
    }
}

// Utworzenie instancji dla nawigacji o ID #nav
new NavScroll('#nav')