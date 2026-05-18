<?php
    /**
     * @var View $view
     */

    use App\View\View;
?>
<?php $view->render('partials/header', ['params' => $params ?? null]); ?>

<div class="container py-5 careers-page">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="display-4">Dołącz do naszego zespołu!</h1>
            <p class="lead">Szukamy ambitnych osób, które chcą rozwijać się razem z nami.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"> Aktualne oferty pracy</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Stanowisko</th>
                                    <th>Wynagrodzenie</th>
                                    <th>Akcja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($params['offers']) && is_array($params['offers'])): ?>
                                    <?php foreach ($params['offers'] as $offer): ?>
                                        <?php if (is_array($offer)): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($offer['nazwa'] ?? 'Brak nazwy') ?></strong>
                                            </td>
                                            <td>
                                                <?php if (!empty($offer['minPensja']) && $offer['minPensja'] !== null): ?>
                                                    <span class="badge bg-success"><?= number_format((float)$offer['minPensja'], 2, ',', ' ') ?> zł</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Do negocjacji</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="alert(' Wyślij CV na wojan@shopp.pl')">
                                                    Aplikuj ✉️
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4">
                                            <p class="mb-0">Aktualnie nie mamy otwartych rekrutacji. Sprawdź wkrótce!</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-info">
                <h5> Jak aplikować?</h5>
                <p class="mb-0">Wyślij swoje CV na adres: <strong>wojan@shopp.pl</strong> z tytułem "Aplikacja - [stanowisko]".</p>
            </div>
        </div>
    </div>
</div>

<?php $view->render('partials/footer'); ?>
