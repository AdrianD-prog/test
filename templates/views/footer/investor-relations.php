<?php
    /**
     * @var View $view
     * @var array $params
     * @var string $root
     */

    use App\View\View;

?>

<?php $view->render('partials/header', ['params' => $params ?? null]); ?>


<div class="container py-5">

    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4">Relacje inwestorskie</h1>
            <p class="lead">Poznaj naszych strategicznych partnerów logistycznych</p>
        </div>
    </div>


    <div class="row mb-5 g-4">
        <?php foreach ($params['stats'] as $stat): ?>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h2 class="display-5 text-primary mb-0"><?= htmlspecialchars($stat['value']) ?></h2>
                    <p class="text-muted mb-0"><?= htmlspecialchars($stat['label']) ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>


    <div class="row g-4">
        <?php foreach ($params['partners'] as $partner): ?>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm text-center partner-card">
                <div class="card-body">
                    <div class=" mb-3">
                        <img src="<?= htmlspecialchars("$root/$partner[logo]") ?>"
                             alt="<?= htmlspecialchars($partner['name']) ?>"
                             class="img-fluid"
                             style="max-height: 80px; max-width: 150px;"
                             >
                    </div>

                    <h3 class="h4"><?= htmlspecialchars($partner['name']) ?></h3>
                    <p class="small text-muted">Partner od <?= htmlspecialchars($partner['since']) ?> roku</p>
                    <p class="small"><?= htmlspecialchars($partner['description']) ?></p>
                    <div class="mt-3">
                        <?php foreach ($partner['benefits'] as $benefit): ?>
                            <span class="badge bg-light text-dark me-1 mb-1">✓ <?= htmlspecialchars($benefit) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>


    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-success">
                <h5 class="mb-3"> Dlaczego warto w nas inwestować?</h5>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li>✓ Silna sieć partnerów logistycznych</li>
                            <li>✓ Rośnący rynek e-commerce w Polsce</li>
                            <li>✓ Nowoczesne rozwiązania technologiczne</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li>✓ Doświadczony zespół zarządzający</li>
                            <li>✓ Stabilny wzrost rok do roku</li>
                            <li>✓ Strategia ekspansji międzynarodowej</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="card bg-dark text-white">
                <div class="card-body py-5">
                    <h3 class="mb-3">Zainteresowany współpracą?</h3>
                    <p class="mb-4">Skontaktuj się z naszym działem relacji inwestorskich</p>
                    <a href="mailto:wspolpraca@amazonix.pl" class="btn btn-light btn-lg">wspolpraca@amazonix.pl</a>
                </div>
            </div>
        </div>
    </div>
</div>



<?php $view->render('partials/footer'); ?>