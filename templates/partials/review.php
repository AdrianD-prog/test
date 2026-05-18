<?php
    /**
     * @var array $review
     */
?>
<div class="d-flex flex-column mb-3">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">

            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-semibold flex-shrink-0"
                     style="width:48px;height:48px;font-size:15px;">
                    <?= mb_substr($review['first_name'], 0, 1) . mb_substr($review['last_name'], 0, 1)?>
                </div>
                <div>
                    <p class="mb-0 fw-semibold text-dark"><?= htmlspecialchars("$review[first_name] $review[last_name]") ?></p>
                </div>
                <?php if (!empty($params['user']) && $params['user']['id'] == $review['user_id']): ?>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill edit-review-btn" 
                                data-bs-toggle="modal" data-bs-target="#editReviewModal"
                                data-review-id="<?= $review['id'] ?>"
                                data-rating="<?= $review['rating'] ?>"
                                data-comment="<?= htmlspecialchars($review['comment']) ?>">
                            <i class="bi bi-pencil-square me-1"></i> Edytuj
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="text-warning fs-5">
                    <?php for ($i = 0; $i < $review['rating']; $i++) : ?>
                        <i class="bi bi-star-fill text-warning"></i>
                    <?php endfor;?>
                    <?php for ($i = 0; $i < 5 - $review['rating']; $i++) : ?>
                        <i class="bi bi-star text-warning"></i>
                    <?php endfor;?>
                </span>
                <span class="text-muted small"><?= $review["rating"]?> / 5</span>
            </div>

            <p class="text-secondary mb-3" style="line-height:1.7;font-size:14px;">
                <?= htmlspecialchars($review['comment']) ?>
            </p>
        </div>
    </div>
</div>