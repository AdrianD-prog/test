<?php
    /**
     * @var View $view
     */

    use App\View\View;

?>

<?php $view->render('partials/header', ['params' => $params ?? null]); ?>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4">Zwroty i reklamacje</h1>
            <p class="lead">Prosty proces zwrotu – bez zbędnego stresu</p>
        </div>
    </div>

    <div class="row mb-5 g-4">
        <div class="col-12">
            <h2 class="text-center mb-4">Jak zwrócić produkt?</h2>
        </div>
        
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="display-1">1️⃣</div>
                    <h4>Zgłoś zwrot</h4>
                    <p>Zaloguj się do panelu klienta i wypełnij formularz zwrotu</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="display-1">2️⃣</div>
                    <h4>Zapakuj produkt</h4>
                    <p>Umieść produkt w oryginalnym opakowaniu z formularzem</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="display-1">3️⃣</div>
                    <h4>Wyślij paczkę</h4>
                    <p>Nadaj paczkę w punkcie kurierskim lub paczkomacie</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="mb-0"> Ważne informacje</h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="fs-1 me-3">⏰</div>
                                <div>
                                    <strong>14 dni na zwrot</strong>
                                    <p class="mb-0 text-muted">Od daty otrzymania przesyłki</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="fs-1 me-3">💰</div>
                                <div>
                                    <strong>Zwrot pieniędzy</strong>
                                    <p class="mb-0 text-muted">Do 14 dni od otrzymania zwrotu</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="fs-1 me-3">📦</div>
                                <div>
                                    <strong>Produkt nieużywany</strong>
                                    <p class="mb-0 text-muted">W oryginalnym opakowaniu</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="fs-1 me-3">🎫</div>
                                <div>
                                    <strong>Dowód zakupu</strong>
                                    <p class="mb-0 text-muted">Paragon lub faktura</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="mb-0">⚠️ Reklamacje</h3>
                </div>
                <div class="card-body">
                    <p>Produkt jest uszkodzony lub nie działa? Złóż reklamację!</p>
                    
                    <div class="alert alert-light">
                        <strong>📝 Jak złożyć reklamację?</strong>
                        <ol class="mt-2">
                            <li>Wyślij zdjęcia uszkodzonego produktu na <strong>wojann@shop.pl</strong></li>
                            <li>Podaj numer zamówienia i opis problemu</li>
                            <li>Odpowiemy w ciągu 48h</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php $view->render('partials/footer'); ?>