<?php

    /**
     * @var array $product
     * @var string $root
     * */
    $favoriteIds = $params['favorite_ids'] ?? $_SESSION['favorite_ids'] ?? [];
    $isFavorite = in_array($product['id'], $favoriteIds);
?>

<div class="col card shadow-sm border text-decoration-none reveal" style="width: 20rem; cursor: pointer;" onclick="window.location.href='<?= htmlspecialchars("$root/products/$product[id]") ?>'">

    <!-- Image area -->
    <div class="bg-light position-relative d-flex justify-content-center align-items-center p-3" style="min-height:200px;">
        <img src="<?= htmlspecialchars("$root/static/images/products/$product[image].webp") ?>" class="img-fluid" style="height:15rem;" alt="<?= $product["name"]?>">
        <?php if(!empty($user)): ?>
            <div class="position-absolute top-0 end-0 d-flex gap-1 p-2">
                <button class="favorite-btn btn btn-sm border bg-white rounded-circle"
                        data-id="<?= $product['id'] ?>"
                        onclick="event.stopPropagation(); toggleFavorite(this, <?= $product['id'] ?>,<?= $user['id'] ?>)"
                        style="width: 35px; height: 35px;">
                    <i class="bi <?= $isFavorite ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
                </button>
            </div>
            <?php endif; ?>
    </div>

    <div class="px-3 pt-2 pb-3">

        <p class="fw-semibold mb-2" style="font-size:14px;"><?= $product["name"]?></p>

        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <span class="text-warning" style="font-size:13px; letter-spacing:-1px;">★</span>
            <span class="text-secondary fw-semibold" style="font-size:12px;"><?= $product["rating"] ?? "0.00"?></span>
            <span class="text-secondary" style="font-size:12px;">(<?= $product["rating_count"]?>)</span>
        </div>

        <div class="d-flex align-items-baseline gap-2 mb-1">
            <span class="fw-bold" style="font-size:26px;"><?= $product["price"]?> zł</span>
        </div>

        <div class="d-flex align-items-center gap-1 mb-1">
            <i class="bi bi-circle-fill text-success" style="font-size:10px;"></i>
            <span class="fw-semibold" style="font-size:13px;">dostawa jutro</span>
            <i class="bi bi-info-circle text-secondary" style="font-size:11px;"></i>
        </div>

        <div class="d-grid gap-2">
            <form action="<?= htmlspecialchars($root)?>/cart/add" method="post">
                <input type="hidden" name="quantity" id="qty" class="qty-select" value="1">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id'])?>">
                <button class="btn fw-bold text-white bg-primary" style="letter-spacing:1px;">Dodaj do koszyka</button>
            </form>
        </div>
    </div>
</div>
