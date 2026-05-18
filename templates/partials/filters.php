<?php
    /**
     * @var array $categories
     */
?>
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">

        <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
            <h6 class="mb-0 fw-bold fs-5">Filtry</h6>
            <form>
                <button type="submit" class="btn btn-link p-0 fw-semibold text-decoration-none text-brand" onclick="clearAll()">Wyczysc</button>
            </form>
        </div>

        <form id="filterForm" method="get">

            <div class="px-3 py-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between bg-light rounded-3 p-3">
                    <div class="d-flex align-items-center gap-2 fw-semibold">
            <span class="badge rounded-2 p-2" style="background:#c0392b;">
              <i class="bi bi-archive-fill"></i>
            </span>
                        Dostep
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="inStock" name="inStock" checked role="switch" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-3 py-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-3" data-bs-toggle="collapse" data-bs-target="#categoryCollapse" role="button">
                    <h6 class="mb-0 fw-bold">Kategoria</h6>
                    <span class="text-muted">&#8964;</span>
                </div>
                <div class="collapse show" id="categoryCollapse">
                    <div class="d-flex flex-wrap gap-2">
                        <input type="radio" class="btn-check" name="category" id="cat-all" value="all"  <?= isset($_GET['category']) && $_GET['category'] == "all" ? 'checked' : ''?>/>
                        <label class="btn btn-outline-secondary rounded-pill btn-sm px-3" for="cat-all">Wszystkie</label>
                        <?php foreach ($categories as $category): ?>
                            <input type="radio" class="btn-check" name="category" id="cat-<?= $category['name']?>" value=<?= $category['id']?>  <?= isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'checked' : ''?>/>
                            <label class="btn btn-outline-secondary rounded-pill btn-sm px-3" for="cat-<?= $category['name']?>"><?= $category['name']?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="px-3 py-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-3" data-bs-toggle="collapse" data-bs-target="#priceCollapse" role="button">
                    <h6 class="mb-0 fw-bold">Zakres cenowy</h6>
                    <span class="text-muted">&#8964;</span>
                </div>
                <div class="collapse show" id="priceCollapse">
                    <div class="dual-range mb-3 mx-1">
                        <div class="track"></div>
                        <div class="fill" id="rangeFill"></div>
                        <input type="range" id="priceMin" name="priceMin" min="0" max="20001" value="<?= $_GET['priceMin'] ?? 0?>"  oninput="updateDualRange()" />
                        <input type="range" id="priceMax" name="priceMax" min="0" max="20001" value="<?= $_GET['priceMax'] ?? 10000?>" oninput="updateDualRange()" />
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">PLN</span>
                            <input type="text" class="form-control text-center" id="minDisplay" value="0" readonly />
                        </div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">PLN</span>
                            <input type="text" class="form-control text-center" id="maxDisplay" value="20000+" readonly />
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-3 py-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-3" data-bs-toggle="collapse" data-bs-target="#ratingCollapse" role="button">
                    <h6 class="mb-0 fw-bold">Oceny</h6>
                    <span class="text-muted">&#8964;</span>
                </div>
                <div class="collapse show" id="ratingCollapse">
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rating" value="4" id="rating4" <?= isset($_GET['rating']) && $_GET['rating'] == '4' ? 'checked' : ''?> />
                            <label class="form-check-label d-flex align-items-center gap-1" for="rating4">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star text-warning"></i>
                                <span class="text-muted small ms-1">i wiecej</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rating" value="3" id="rating3" <?= isset($_GET['rating']) && $_GET['rating'] == '3' ? 'checked' : ''?>/>
                            <label class="form-check-label d-flex align-items-center gap-1" for="rating3">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star text-warning"></i>
                                <i class="bi bi-star text-warning"></i>
                                <span class="text-muted small ms-1">i wiecej</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rating" value="0" id="no-rating" <?= !isset($_GET['rating']) || $_GET['rating'] == '0' ? 'checked' : ''?>/>
                            <label class="form-check-label d-flex align-items-center gap-1" for="no-rating">
                                <span class="text-muted small ms-1">Każde</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-3 pt-3 pb-1">
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold fs-6">Pokaz produkty</button>
            </div>

        </form>
    </div>
</div>
