<?php

    /**
     * @var array $params
     * @var View $view
     * @var array $product
     * @var array $reviews
     * @var string $root
     * @var HTMLDocument $description
     */

    use App\View\View;
    use Dom\HTMLDocument;

?>
<?php $view->render('partials/header', ['params' => $params ?? null]);?>
<div class="container py-4">

    <h1 class="product-title-main mb-2">
        <?= htmlspecialchars($product['name']) ?>
    </h1>

    <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
        <?php for ($i = 0; $i < $product['rating']; $i++) : ?>
            <i class="bi bi-star-fill text-warning"></i>
        <?php endfor;?>
        <?php for ($i = 0; $i < 5 - $product['rating']; $i++) : ?>
            <i class="bi bi-star text-warning"></i>
        <?php endfor;?>
        <i class="text-muted ms-2">
            <?= count($reviews)?>
            <?= (count($reviews) == 1) ? "opinia" : (count($reviews) < 5 ? "opinie" : "opinii") ?>
        </i>
    </div>

    <hr class="my-2"/>

    <div class="row g-4 mt-4">
        <div class="col-12 col-md-7 d-flex gap-2">
            <div class="main-img mb-2"><img class="img-fluid h-100" src="<?= htmlspecialchars("$root/static/images/products/$product[image].webp")?>" alt="<?= $product["name"]?>"></div>
        </div>

        <div class="col-12 col-md-5">
            <div class="buy-box">
                    <div class="price-box mb-2" style="font-size: 24px; font-weight: 400;">
                        <sup>zł</sup><span class="currency"><?= $product["price"]?></span>
                    </div>
                <div class="deliver-free mb-1">DARMOWA dostawa jutro</div>
                <div class="deliver-detail text-success fw-bold">
                    <?php
                        echo datefmt_format(
                                datefmt_create(
                                        'pl-PL',
                                        IntlDateFormatter::FULL,
                                        IntlDateFormatter::FULL,
                                        'Europe/Warsaw',
                                        IntlDateFormatter::GREGORIAN,
                                        'dd MMMM'
                                ),
                                strtotime("+1 day")
                        )
                    ?>
                </div>
                <div class="deliver-to mb-2">Dostawa do: Warszawa</div>
                <div class="in-stock-box mb-3">Na magazynie</div>

                <form action="<?= htmlspecialchars($root)?>/cart/add" method="post">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <label for="qty" style="font-size:14px; white-space:nowrap;">Ilość:</label>
                        <input type="number" name="quantity" id="qty" class="qty-select" value="1" min="1">
                    </div>
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id'])?>">
                    <button class="btn-add btn mb-2">Dodaj do koszyka</button>
                </form>
                <?php if(isset($params["user"]["id"])):?>
                    <button class="btn-fav btn mb-2" onclick="event.stopPropagation(); toggleFavorite(this, <?= $product["id"]?>, <?= $params["user"]["id"]?>)">Dodaj do ulubionych</button>
                <?php endif;?>

                <div class="text-center">
                    <a href="#" class="secure-link">Bezpieczna transakcja</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item" role="presentation">
                <button
                        id="opis-tab"
                        class="nav-link active d-flex"
                        data-bs-toggle="tab"
                        data-bs-target="#opis-pane"
                        type="button"
                        role="tab"
                        aria-controls="opis-pane"
                        aria-selected="true">
                    Opis
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                        id="opinie-tab"
                        class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#opinie-pane"
                        type="button"
                        role="tab"
                        aria-controls="opinie-pane"
                        aria-selected="false">
                    Opinie
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="opis-pane" role="tabpanel" aria-labelledby="opis-tab">
                <?= $product['description']?>
            </div>
            <div class="tab-pane fade" id="opinie-pane" role="tabpanel" aria-labelledby="opinie-tab">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-lg-4 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Dodaj opinię</h5>
                                    <?php if (!empty($params['user'])): ?>
                                        <form action="<?= htmlspecialchars($root) ?>/products/review" method="post">
                                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Twoja ocena</label>
                                                <div class="rating-input d-flex gap-2 fs-4">
                                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                                        <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" class="btn-check" required>
                                                        <label for="star<?= $i ?>" class="bi bi-star text-warning" style="cursor: pointer;"></label>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="comment" class="form-label">Komentarz</label>
                                                <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Co sądzisz o produkcie?"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">Opublikuj opinię</button>
                                        </form>
                                        <style>
                                            .rating-input { flex-direction: row-reverse; justify-content: flex-end; }
                                            .rating-input input:checked ~ label::before { content: "\f586" !important; }
                                            .rating-input label:hover::before,
                                            .rating-input label:hover ~ label::before { content: "\f586" !important; color: #ffc107 !important; }
                                            .rating-input label::before { content: "\f588"; font-family: "bootstrap-icons" !important; }
                                        </style>
                                    <?php else: ?>
                                        <p class="text-muted">Zaloguj się, aby wystawić opinię.</p>
                                        <a href="<?= htmlspecialchars($root) ?>/login" class="btn btn-outline-primary btn-sm">Zaloguj się</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-8">
                            <?php
                                if (empty($reviews)): ?>
                                    <p class="text-muted text-center py-4">Brak opinii dla tego produktu. Bądź pierwszy!</p>
                                <?php else:
                                    foreach ($reviews as $review)
                                        $view->render('partials/review', ["review" => $review, "user" => $params['user'] ?? null]);
                                endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Review Modal -->
<?php if (!empty($params['user'])): ?>
<div class="modal fade" id="editReviewModal" tabindex="-1" aria-labelledby="editReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= htmlspecialchars($root) ?>/products/review/edit" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReviewModalLabel">Edytuj opinię</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="review_id" id="edit-review-id">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Twoja ocena</label>
                        <div class="rating-input-edit d-flex gap-2 fs-4">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" value="<?= $i ?>" id="edit-star<?= $i ?>" class="btn-check" required>
                                <label for="edit-star<?= $i ?>" class="bi bi-star text-warning" style="cursor: pointer;"></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-comment" class="form-label">Komentarz</label>
                        <textarea name="comment" id="edit-comment" class="form-control" rows="3" placeholder="Co sądzisz o produkcie?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .rating-input-edit { flex-direction: row-reverse; justify-content: flex-end; }
    .rating-input-edit input:checked ~ label::before { content: "\f586" !important; }
    .rating-input-edit label:hover::before,
    .rating-input-edit label:hover ~ label::before { content: "\f586" !important; color: #ffc107 !important; }
    .rating-input-edit label::before { content: "\f588"; font-family: "bootstrap-icons" !important; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-review-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const reviewId = this.getAttribute('data-review-id');
                const rating = this.getAttribute('data-rating');
                const comment = this.getAttribute('data-comment');
                
                document.getElementById('edit-review-id').value = reviewId;
                document.getElementById('edit-comment').value = comment;
                
                const starInput = document.getElementById('edit-star' + rating);
                if (starInput) {
                    starInput.checked = true;
                }
            });
        });
    });
</script>
<?php endif; ?>

<?php $view->render('partials/footer');?>