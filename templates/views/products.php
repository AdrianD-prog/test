<?php

    /**
     * @var View $view
     * @var array $params
     * */

    use App\View\View;

?>
<?php $view->render('partials/header', ['params' => $params ?? null]); ?>
<main>
    <div class="d-block d-lg-flex gap-5 mt-5 mt-lg-0">
        <div class="filter d-flex ms-lg-4 mt-lg-5 w-25 h-50 position-sticky justify-content-center" style="top: 11rem">
            <?php $view->render('partials/filters', ['categories' => $params['categories']]); ?>
        </div>
        <div class="container-fluid">
            <div class="row mb-2 p-2">
                <?php
                    $products_length = count($params['products']); ?>
                <p class="h5">1-<?= $products_length?> z <?= $products_length?> wyników</p>
            </div>
            <div class="row row-cols-5 column-gap-5 row-gap-4 align-items-center justify-content-center">
                <?php
                    if (empty($params['products'])) {
                        echo "Brak produktów";
                    } else {
                        foreach ($params['products'] as $product) {
                            $view->render('partials/product-card', ['product' => $product, 'user' => $params['user']]);
                        }
                    } ?>
            </div>
        </div>
    </div>
</main>
<?php $view->render('partials/footer'); ?>
