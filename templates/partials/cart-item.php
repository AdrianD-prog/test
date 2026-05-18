<?php
/**
 * @var array $item
 * @var array $params
 * @var string $root
 */

// Przygotuj dane dla niezalogowanego
$isGuest = ($params['user']['id'] ?? 0) === 0;
$identifier = $isGuest ? 'product_id' : 'item_id';
$idValue = $isGuest ? $item['product_id'] : $item['id'];
?>

<div class="card mb-3 shadow-sm">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
                <p class="card-text text-muted small"><?= htmlspecialchars($item['sku']) ?></p>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center justify-content-between">
                    <!-- Formularz aktualizacji ilości -->
                    <form method="POST" action="<?= htmlspecialchars($root) ?>/cart/update" class="d-flex align-items-center">
                        <input type="hidden" name="<?= $identifier ?>" value="<?= $idValue ?>">
                        <input type="hidden" name="current_quantity" value="<?= $item['quantity'] ?>">
                        
                        <?php if (!$isGuest): ?>
                            <input type="hidden" name="user_id" value="<?= $params['user']['id'] ?>">
                        <?php endif; ?>
                        <button type="submit" name="action" value="decrease"
                                class="btn btn-outline-<?= $item['quantity'] <= 1 ? 'danger' : 'secondary' ?> btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>

                        <span class="mx-2"><?= $item['quantity'] ?></span>

                        <button type="submit" name="action" value="increase" class="btn btn-outline-secondary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>
                    </form>

                    <!-- Cena z formatowaniem do 2 miejsc po przecinku -->
                    <span class="h5 mb-0 text-primary">
                        <?= number_format($item['price'] * $item['quantity'], 2, ',', ' ') ?> zł
                    </span>

                    <!-- Formularz usuwania -->
                    <form method="POST" action="<?= htmlspecialchars($root) ?>/cart/remove" onsubmit="return confirm('Usunąć produkt z koszyka?')">
                        <input type="hidden" name="<?= $identifier ?>" value="<?= $idValue ?>">
                        <?php if (!$isGuest): ?>
                            <input type="hidden" name="user_id" value="<?= $params['user']['id'] ?>">
                        <?php endif; ?>
                        <button type="submit" class="btn btn-link text-danger p-0" title="Usuń">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                <path d="M8 4V3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>