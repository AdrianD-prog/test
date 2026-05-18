<?php $view->render('partials/header', ['user' => $params['user'] ?? null]); ?>


<div class="container py-5">
    <h1 class="mb-4 display-1 text-center"> Ulubione</h1>
    
    <?php if (!empty($params['favorites'])): ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($params['favorites'] as $product): ?>
        <div class="col">
            <?php $view->render('partials/product-card', ['product' => $product, 'user' => $params['user']]); ?>
        </div>
    <?php endforeach; ?>
    </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="display-1">SADGE</div>
            <h3>Nie masz jeszcze ulubionych produktów</h3>
            <p class="text-muted">Kliknij serduszko  przy produkcie, aby dodać go do ulubionych</p>
            <a href="<?= $root ?>/products" class="btn btn-primary mt-3">Przeglądaj produkty</a>
        </div>
    <?php endif; ?>
</div>

<?php $view->render('partials/footer'); ?>