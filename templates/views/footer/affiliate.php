<?php
    /**
     * @var View $view
     * @var array $params
     */

    use App\View\View;

?>

<?php $view->render('partials/header', ['params' => $params ?? null]); ?>



<div class="container py-5 affiliate-page">
    
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4">Program afiliacyjny</h1>
            <p class="lead">Zarabiaj z nami – polecaj i zyskuj nawet do 12% prowizji!</p>
            <a href="#" class="btn btn-primary btn-lg mt-3" onclick="alert('Rejestracja wkrótce dostępna!')">
                Dołącz do programu
            </a>
        </div>
    </div>

    
    <div class="row mb-5 g-4">
        <div class="col-12">
            <h2 class="text-center mb-4">Jak to działa?</h2>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="display-1 text-primary">1️⃣</div>
                    <h5>Zarejestruj się</h5>
                    <p class="text-muted">Wypełnij krótki formularz i dołącz do programu za darmo</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="display-1 text-primary">2️⃣</div>
                    <h5>Promuj</h5>
                    <p class="text-muted">Otrzymasz unikalny link afiliacyjny – umieść go na swojej stronie lub w social media</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="display-1 text-primary">3️⃣</div>
                    <h5>Zarabiaj</h5>
                    <p class="text-muted">Otrzymuj prowizję od każdej sprzedaży wygenerowanej przez Twój link</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">Wybierz swój poziom</h2>
        </div>
        <?php foreach ($params['levels'] as $level): ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-header bg-<?= $level['color'] ?> text-white">
                    <h3 class="mb-0"><?= htmlspecialchars($level['name']) ?></h3>
                </div>
                <div class="card-body">
                    <div class="display-4 text-<?= htmlspecialchars($level['color']) ?> fw-bold">
                        <?= htmlspecialchars($level['commission']) ?>
                    </div>
                    <p class="text-muted">prowizji</p>
                    <hr>
                    <p><strong>Wymagania:</strong><br><?= htmlspecialchars($level['requirements']) ?></p>
                    <ul class="list-unstyled text-start">
                        <?php foreach ($level['features'] as $feature): ?>
                        <li class="mb-2">✓ <?= htmlspecialchars($feature) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h3 class="text-center mb-3">Oblicz swoje potencjalne zarobki</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Miesięczna sprzedaż (zł)</label>
                            <input type="range" class="form-range" id="salesRange" min="0" max="50000" step="1000" value="10000">
                            <div class="text-center mt-2">
                                <span class="badge bg-primary fs-5" id="salesValue">10 000 zł</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Twój poziom</label>
                            <select class="form-select" id="levelSelect">
                                <option value="0.05">Starter (5%)</option>
                                <option value="0.08" selected>Pro (8%)</option>
                                <option value="0.12">Premium (12%)</option>
                            </select>
                            <div class="text-center mt-3">
                                <h4>Twoja prowizja:</h4>
                                <span class="badge bg-success fs-2" id="commissionAmount">800 zł</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">Najczęstsze pytania</h2>
            <div class="accordion" id="faqAccordion">
                <?php foreach ($params['faqs'] as $index => $faq): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $index ?>">
                            <?= htmlspecialchars($faq['q']) ?>
                        </button>
                    </h2>
                    <div id="faq<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <?= htmlspecialchars($faq['a']) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12 text-center">
            <div class="card bg-dark text-white">
                <div class="card-body py-5">
                    <h3 class="mb-3">Gotowy zacząć zarabiać?</h3>
                    <p class="mb-4">Dołącz do grona naszych partnerów już dziś!</p>
                    <a href="#" class="btn btn-light btn-lg" onclick="alert('Rejestracja wkrótce dostępna!')">
                        Zarejestruj się
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
     const inputRange = document.getElementById('salesRange');
    const wartoscSprzedazy = document.getElementById('salesValue');
    const rozwijanaLista = document.getElementById('levelSelect');
    const prowizjaPokaz = document.getElementById('commissionAmount');

    function aktualizujProwizje() {
        const sprzedaze = parseInt(inputRange.value);
        const wybor = parseFloat(rozwijanaLista.value);
        const prowizja = sprzedaze * wybor;
                    
        wartoscSprzedazy.textContent = sprzedaze.toLocaleString('pl-PL') + '    zł';
        prowizjaPokaz.textContent = Math.round(prowizja).toLocaleString ('pl-PL') + ' zł';
}

inputRange.addEventListener('input', aktualizujProwizje);
rozwijanaLista.addEventListener('change', aktualizujProwizje);
aktualizujProwizje();

</script>

<?php $view->render('partials/footer'); ?>
