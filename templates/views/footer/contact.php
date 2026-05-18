<?php

    use App\View\View;
    /**
     * @var View $view
     * @var array $params
     */
?>

<?php $view->render('partials/header', ['params' => $params ?? null]); ?>


<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4">Skontaktuj się z nami</h1>
            <p class="lead">Jesteśmy tutaj, aby Ci pomóc</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h3 class="mb-4"> Nasze dane</h3>
                    
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                📍
                            </div>
                            <div>
                                <strong>Adres</strong><br>
                                <span class="text-muted">ul. Kazimierza Jagiellończyka 3, 39-300 Mielec</span>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                📧
                            </div>
                            <div>
                                <strong>Email</strong><br>
                                <a href="mailto:kontakt@amazonix.pl" class="text-decoration-none">kontakt@amazonix.pl</a>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                📞
                            </div>
                            <div>
                                <strong>Telefon</strong><br>
                                <span class="text-muted">+48 123 456 789</span>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                ⏰
                            </div>
                            <div>
                                <strong>Godziny otwarcia</strong><br>
                                <span class="text-muted">Pon-Pt: 9:00 - 17:00</span><br>
                                <span class="text-muted">Sob: 10:00 - 14:00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="mb-4"> Napisz do nas</h3>
                    
                    <form action="#" method="POST" onsubmit="alert('Dziękujemy! Twoja wiadomość została wysłana. Odpowiemy najszybciej jak to możliwe.'); return false">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Imię i nazwisko *</label>
                                <input type="text" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Temat *</label>
                                <select class="form-select" required>
                                    <option value="">Wybierz temat</option>
                                    <option>Zapytanie o produkt</option>
                                    <option>Reklamacja</option>
                                    <option>Zwrot</option>
                                    <option>Współpraca</option>
                                    <option>Inne</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Wiadomość *</label>
                                <label>
                                    <textarea class="form-control" rows="5" required placeholder="Treść wiadomości..."></textarea>
                                </label>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                     Wyślij wiadomość
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $view->render('partials/footer'); ?>