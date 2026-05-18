<?php
    /**
     * @var View $view
     */

    use App\View\View;

?>

<?php $view->render('partials/header', ['params' => $params ?? null]); ?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
        <p class="display-1 text-center">Jak możemy Ci pomóc?</p>
        </div>
    </div>
    <div class="row mb-5 g-4">
        <div class="col-12">
            <h2 class="text-center mb-4">Wybierz kategorię</h2>
        </div>
    </div>
    <div class="row g-4 mt-5">
    <div class="col-md-4">
        <div class="card text-center h-100 shadow-sm">
            <a href="mailto:pomoc@amazonix.pl" style="text-decoration: none;">
            <div class="card-body">
                <div class="display-1">📦</div>
                <h4>Zamówienia</h4>
                <p>Jak złożyć zamówienie, zmienić adres, anulować</p>
            </div>
            </a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center h-100 shadow-sm">
            <a href="mailto:pomoc@amazonix.pl" style="text-decoration: none;">
            <div class="card-body">
                <div class="display-1">🚚</div>
                <h4>Dostawa</h4>
                <p>Czas realizacji, koszty wysyłki, śledzenie paczki</p>
            </div>
            </a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center h-100 shadow-sm">
            <a href="mailto:pomoc@amazonix.pl" style="text-decoration: none;">
            <div class="card-body">
                <div class="display-1">🔄</div>
                <h4>Zwroty</h4>
                <p>Jak zwrócić produkt, reklamacja, czas zwrotu pieniędzy</p>
            </div>
            </a>
        </div>
    </div>
    <div class="row g-4">
    
    <div class="col-md-6">
        <div class="card text-center h-100 shadow-sm">
            <a href="mailto:pomoc@amazonix.pl" style="text-decoration: none;">
            <div class="card-body">
                <div class="display-1">💳</div>
                <h4>Płatności</h4>
                <p>Metody płatności, faktury, odroczone płatności</p>
            </div>
            </a>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card text-center h-100 shadow-sm">
            <a href="mailto:pomoc@amazonix.pl" style="text-decoration: none;">
            <div class="card-body">
                <div class="display-1">👤</div>
                <h4>Konto</h4>
                <p>Rejestracja, logowanie, dane osobowe</p>
            </div>
            </a>
        </div>
    </div>
</div>
</div>
<div class="row mb-5 mt-5">
        <div class="col-12">
            <h2 class="text-center mb-4">Najczęściej zadawane pytania</h2>
            
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Jak długo trwa dostawa?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Dostawa standardowa trwa 2-3 dni robocze. Dostawa ekspresowa to 1 dzień roboczy. Paczkomaty InPost to zazwyczaj 24h.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Czy mogę zwrócić towar?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Tak! Masz 14 dni na zwrot towaru bez podawania przyczyny. Wystarczy wypełnić formularz zwrotu w panelu klienta.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center">
            <div class="card bg-primary text-white">
                <div class="card-body py-5">
                    <h3>Nadal potrzebujesz pomocy?</h3>
                    <p>Skontaktuj się z nami!</p>
                    <a href="mailto:pomoc@amazonix.pl" class="btn btn-light btn-lg">pomoc@amazonix.pl</a>
                </div>
            </div>
        </div>
    </div>
    
</div>

<?php $view->render('partials/footer'); ?>