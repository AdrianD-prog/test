<?php

/**
 * @var View $view
 * @var array $params
 * @var string $root
 */

use App\View\View;

$cart = $params['cart'];
$items = $cart['items'] ?? [];
$total = $cart['total'] ?? 0;
?>

<?php $view->render('partials/header', ['params' => $params ?? null]); ?>
<div class="container py-5" style="min-height: 49.5vh">
    <h1 class="mb-4">Twój koszyk</h1>

    <?php if (empty($items)) : ?>
        <div class="text-center py-5">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="text-muted mb-3">
                <circle cx="8" cy="21" r="1"/>
                <circle cx="19" cy="21" r="1"/>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
            </svg>
            <h3>Twój koszyk jest pusty</h3>
            <p class="text-muted">Dodaj produkty, aby zobaczyć je tutaj</p>
            <a href="<?= htmlspecialchars($root) ?>/products" class="btn btn-primary">Przejdź do sklepu</a>
        </div>
    <?php else : ?>
        <div class="row">
            <div class="col-lg-8">
                <?php
                foreach ($items as $item) {
                    $view->render('partials/cart-item', ['item' => $item, 'user' => $params['user'] ?? null, 'root' => $root]);
                }
                ?>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Podsumowanie</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Liczba artykułów:</span>
                            <span class="fw-bold"><?= $cart['count'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Wartość koszyka:</span>
                            <span class="h4 text-primary"><?= htmlspecialchars($total) ?> zł</span>
                        </div>
                        <hr>
                        <a class="btn btn-success w-100 mb-2" href="<?= $root?>/orders/checkout">Przejdź do kasy</a>
                        <a href="<?= htmlspecialchars($root) ?>/products" class="btn btn-outline-secondary w-100">Kontynuuj zakupy</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $view->render('partials/footer')?>