<?php

    /**
     * @var View $view
     * @var array $params
     * @var string $root
     * */

    use App\View\View;

?>

<?php
    $view->render('partials/header', ['params' => $params ?? null]);

    $cartMessage = $_SESSION['cart_message'] ?? '';
    $cartError = $_SESSION['cart_error'] ?? '';
    unset($_SESSION['cart_message'], $_SESSION['cart_error']);
?>

<?php if ($cartMessage) : ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($cartMessage) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($cartError) : ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($cartError) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<main>
    <section class="py-5 text-white" style="background:#1f2a37;">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="text-uppercase small opacity-75 mb-2">Wyprzedaż Nowego Sezonu</div>
                    <h1 class="display-5 fw-bold mb-3">Do 70% Zniżki na Wybrane Produkty</h1>
                    <p class="opacity-75 mb-4">
                        Odkryj niesamowite okazje na elektronikę, modę, artykuły domowe i wiele więcej.
                        Darmowa dostawa przy zamówieniach powyżej 35PLN.
                    </p>
                    <a href="<?= htmlspecialchars($root) ?>/products" class="btn btn-warning fw-semibold px-4">
                        Kup Teraz
                        <span class="ms-1">→</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 pb-5 bg-white">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h4 fw-bold mb-0">Polecane Produkty</h2>
                <a class="small text-decoration-none" href="<?= htmlspecialchars($root) ?>/products">Zobacz Wszystkie →</a>
            </div>
            
            <div class="row row-cols-6 column-gap-2 row-gap-4 align-items-center justify-content-center">
                <?php if (empty($params['products'])) : ?>
                    <div class="col-12 text-center py-5">
                        <p>Brak produktów w sklepie</p>
                    </div>
                <?php else :
                    shuffle($params['products']);
                    foreach (array_slice($params['products'], 0, 4) as $product) {
                        $view->render('partials/product-card', ['product' => $product, 'user' => $params['user']]);
                    }
                endif;?>
            </div>
        </div>
    </section>

    <section class="pb-5 bg-white">
        <div class="container">
            <h2 class="h4 fw-bold mb-3">Dzisiejsze Okazje</h2>

            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <div class="p-4 rounded-3 text-white" style="background:#111827;">
                        <span class="badge bg-warning text-dark mb-2">Do 40% taniej</span>
                        <div class="h5 fw-bold">Wyprzedaż Elektroniki</div>
                        <div class="small opacity-75">Oszczędzaj na laptopach, tabletach i nie tylko</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-4 rounded-3 text-dark" style="background:#f59e0b;">
                        <span class="badge bg-dark mb-2">Oferta Dnia</span>
                        <div class="h5 fw-bold">Błyskawiczna Wyprzedaż</div>
                        <div class="small opacity-75">Oferty czasowe wkrótce wygasają</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-4 rounded-3 text-white" style="background:#111827;">
                        <span class="badge bg-warning text-dark mb-2">Darmowa Dostawa</span>
                        <div class="h5 fw-bold">Niezbędniki Modowe</div>
                        <div class="small opacity-75">Odzież, obuwie i akcesoria</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php $view->render('partials/footer'); ?>
