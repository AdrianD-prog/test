<?php
/**
 * @var array $user
 * @var array $cart
 * @var array $delivery_methods
 * @var object $view
 * @var string $root
 */

use App\View\View;

$user = $user ?? [];
$cart = $cart ?? [];
$items = $cart['items'] ?? [];
$deliveryMethods = $delivery_methods ?? [];

$errors = $_SESSION['error'] ?? [];
$previous = $_SESSION['previous_data'] ?? [];
$generalError = $errors['general'] ?? '';

$cartTotal = isset($cart['total']) ? (float)$cart['total'] : 0.0;
$taxAmount = round($cartTotal * 0.23, 2);

$selectedDeliveryId = (string)($previous['delivery_method'] ?? ($deliveryMethods[0]['id'] ?? ''));
$selectedPaymentMethod = (string)($previous['payment_method'] ?? 'card');

$selectedDelivery = null;
foreach ($deliveryMethods as $method) {
    if ((string)($method['id'] ?? '') === $selectedDeliveryId) {
        $selectedDelivery = $method;
        break;
    }
}

$deliveryCost = isset($selectedDelivery['price']) ? (float)$selectedDelivery['price'] : 0.0;
$grandTotal = round($cartTotal + $taxAmount + $deliveryCost, 2);

$view->render('partials/header', ['user' => $user]);
?>

<div class="amazon-checkout">
    <div class="checkout-header">
        <h1>Podsumowanie zamowienia</h1>
        <div class="checkout-steps">
            <span class="step active">Koszyk</span>
            <span class="step active">Podsumowanie</span>
            <span class="step">Zamowienie zlozone</span>
        </div>
    </div>

    <?php if ($generalError !== ''): ?>
        <div class="card" style="margin-bottom: 20px; border-left: 4px solid #c62828;">
            <p class="text-muted" style="margin: 0; color: #c62828;">
                <?= htmlspecialchars($generalError, ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="checkout-grid">
        <div class="checkout-left">
            <div class="delivery-section card">
                <h2>Adres dostawy</h2>
                <div class="address-preview">
                    <?php if (!empty($user['address']) && !empty($user['city']) && !empty($user['country'])): ?>
                        <p><strong><?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?></strong></p>
                        <p><?= nl2br(htmlspecialchars((string)$user['address'], ENT_QUOTES, 'UTF-8')) ?></p>
                        <p><?= htmlspecialchars((string)$user['city'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string)$user['country'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php else: ?>
                        <p class="text-muted">Brak zapisanego adresu. <a href="<?= $root ?>/account/edit">Uzupelnij dane adresowe</a></p>
                    <?php endif; ?>
                </div>

                <?php if (isset($errors['address'])): ?>
                    <div class="invalid-feedback" style="display: block; margin-top: 12px;">
                        <?= htmlspecialchars($errors['address'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <a href="<?= $root ?>/account/edit" class="edit-link">Zmien adres</a>
            </div>

            <form action="<?= $root ?>/orders/checkout" method="POST" id="checkout-form" novalidate>
                <div class="payment-section card">
                    <h2>Metoda dostawy</h2>
                    <?php if ($deliveryMethods !== []): ?>
                        <div class="delivery-options">
                            <?php foreach ($deliveryMethods as $method): ?>
                                <?php
                                $methodId = (string)($method['id'] ?? '');
                                $isChecked = $methodId === $selectedDeliveryId;
                                $price = isset($method['price']) ? (float)$method['price'] : 0.0;
                                ?>
                                <label class="delivery-option" for="delivery-<?= htmlspecialchars($methodId, ENT_QUOTES, 'UTF-8') ?>">
                                    <input
                                        type="radio"
                                        id="delivery-<?= htmlspecialchars($methodId, ENT_QUOTES, 'UTF-8') ?>"
                                        name="delivery_method"
                                        value="<?= htmlspecialchars($methodId, ENT_QUOTES, 'UTF-8') ?>"
                                        data-price="<?= htmlspecialchars(number_format($price, 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>"
                                        <?= $isChecked ? 'checked' : '' ?>
                                    >
                                    <span>
                                        <strong><?= htmlspecialchars((string)($method['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                                        <small><?= htmlspecialchars((string)($method['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small>
                                        <small>Szacowany czas: <?= htmlspecialchars((string)($method['eta'] ?? '2-4 dni'), ENT_QUOTES, 'UTF-8') ?></small>
                                    </span>
                                    <span><?= number_format($price, 2) ?> zl</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Metody dostawy sa chwilowo niedostepne.</p>
                    <?php endif; ?>

                    <?php if (isset($errors['delivery_method'])): ?>
                        <div class="invalid-feedback" style="display: block; margin-top: 12px;">
                            <?= htmlspecialchars($errors['delivery_method'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="payment-section card">
                    <h2>Metoda platnosci</h2>
                    <div class="delivery-options">
                        <label class="delivery-option" for="payment-card">
                            <input type="radio" id="payment-card" name="payment_method" value="card" <?= $selectedPaymentMethod === 'card' ? 'checked' : '' ?>>
                            <span>
                                <strong>Karta platnicza</strong>
                                <small>Symulacja platnosci karta Visa lub Mastercard</small>
                            </span>
                            <span>Mock</span>
                        </label>

                        <label class="delivery-option" for="payment-blik">
                            <input type="radio" id="payment-blik" name="payment_method" value="blik" <?= $selectedPaymentMethod === 'blik' ? 'checked' : '' ?>>
                            <span>
                                <strong>BLIK</strong>
                                <small>Wpisz 6-cyfrowy kod testowy</small>
                            </span>
                            <span>Mock</span>
                        </label>

                        <label class="delivery-option" for="payment-transfer">
                            <input type="radio" id="payment-transfer" name="payment_method" value="bank_transfer" <?= $selectedPaymentMethod === 'bank_transfer' ? 'checked' : '' ?>>
                            <span>
                                <strong>Szybki przelew</strong>
                                <small>Wybierz bank i finalizuj bez prawdziwej autoryzacji</small>
                            </span>
                            <span>Mock</span>
                        </label>
                    </div>

                    <?php if (isset($errors['payment_method'])): ?>
                        <div class="invalid-feedback" style="display: block; margin-top: 12px;">
                            <?= htmlspecialchars($errors['payment_method'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <div class="payment-method-panel" data-payment-panel="card" <?= $selectedPaymentMethod !== 'card' ? 'hidden' : '' ?>>
                        <div class="form-group">
                            <label for="card-number">Numer karty</label>
                            <input type="text" id="card-number" name="card_number" value="<?= htmlspecialchars((string)($previous['card_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="1234 5678 9012 3456" autocomplete="off" class="<?= isset($errors['card_number']) ? 'is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['card_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                        </div>

                        <div class="form-row">
                            <div class="form-group half">
                                <label for="expiry">Data waznosci (MM/RR)</label>
                                <input type="text" id="expiry" name="expiry" value="<?= htmlspecialchars((string)($previous['expiry'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="MM/RR" class="<?= isset($errors['expiry']) ? 'is-invalid' : '' ?>">
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['expiry'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            </div>

                            <div class="form-group half">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" value="<?= htmlspecialchars((string)($previous['cvv'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="123" class="<?= isset($errors['cvv']) ? 'is-invalid' : '' ?>">
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['cvv'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="card-name">Imie i nazwisko na karcie</label>
                            <input type="text" id="card-name" name="card_name" value="<?= htmlspecialchars((string)($previous['card_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Jan Kowalski" class="<?= isset($errors['card_name']) ? 'is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['card_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    </div>

                    <div class="payment-method-panel" data-payment-panel="blik" <?= $selectedPaymentMethod !== 'blik' ? 'hidden' : '' ?>>
                        <div class="form-group">
                            <label for="blik-code">Kod BLIK</label>
                            <input type="text" id="blik-code" name="blik_code" value="<?= htmlspecialchars((string)($previous['blik_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="123456" class="<?= isset($errors['blik_code']) ? 'is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['blik_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    </div>

                    <div class="payment-method-panel" data-payment-panel="bank_transfer" <?= $selectedPaymentMethod !== 'bank_transfer' ? 'hidden' : '' ?>>
                        <div class="form-group">
                            <label for="bank-name">Wybierz bank</label>
                            <select id="bank-name" name="bank_name" class="<?= isset($errors['bank_name']) ? 'is-invalid' : '' ?>">
                                <option value="">Wybierz bank</option>
                                <?php foreach (['mBank', 'PKO BP', 'Santander', 'ING', 'Alior Bank'] as $bank): ?>
                                    <option value="<?= htmlspecialchars($bank, ENT_QUOTES, 'UTF-8') ?>" <?= (($previous['bank_name'] ?? '') === $bank) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($bank, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['bank_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    </div>

                    <div class="form-group security-note">
                        <span>To jest mock platnosci. Dane nie sa przekazywane do operatora i nie sa zapisywane poza walidacja formularza.</span>
                    </div>

                    <button type="submit" class="btn-place-order" <?= empty($items) ? 'disabled' : '' ?>>Zloz zamowienie</button>
                </div>
            </form>
        </div>

        <div class="checkout-right">
            <div class="order-summary card">
                <h2>Podsumowanie koszyka</h2>
                <div class="cart-items">
                    <?php if (!empty($items) && is_array($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <div class="cart-item">
                                <div class="item-details">
                                    <div class="item-title"><?= htmlspecialchars((string)($item['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="item-quantity">Ilosc: <?= (int)($item['quantity'] ?? 0) ?></div>
                                </div>
                                <div class="item-price"><?= number_format(((float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 0)), 2) ?> zl</div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Twoj koszyk jest pusty.</p>
                    <?php endif; ?>
                </div>

                <div class="totals">
                    <div class="total-line">
                        <span>Suma produktow:</span>
                        <span><?= number_format($cartTotal, 2) ?> zl</span>
                    </div>
                    <div class="total-line">
                        <span>Dostawa:</span>
                        <span id="delivery-cost"><?= number_format($deliveryCost, 2) ?> zl</span>
                    </div>
                    <div class="total-line">
                        <span>Podatek (23%):</span>
                        <span><?= number_format($taxAmount, 2) ?> zl</span>
                    </div>
                    <div class="total-line grand-total">
                        <span>Do zaplaty:</span>
                        <span id="grand-total"><?= number_format($grandTotal, 2) ?> zl</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const paymentInputs = document.querySelectorAll('input[name="payment_method"]');
        const paymentPanels = document.querySelectorAll('[data-payment-panel]');
        const deliveryInputs = document.querySelectorAll('input[name="delivery_method"]');
        const deliveryCostNode = document.getElementById('delivery-cost');
        const grandTotalNode = document.getElementById('grand-total');
        const cartTotal = <?= json_encode($cartTotal) ?>;
        const taxAmount = <?= json_encode($taxAmount) ?>;

        function updatePaymentPanels() {
            const selected = document.querySelector('input[name="payment_method"]:checked');
            const selectedValue = selected ? selected.value : '';

            paymentPanels.forEach((panel) => {
                panel.hidden = panel.getAttribute('data-payment-panel') !== selectedValue;
            });
        }

        function updateSummary() {
            const selected = document.querySelector('input[name="delivery_method"]:checked');
            const deliveryPrice = selected ? parseFloat(selected.dataset.price || '0') : 0;
            const grandTotal = cartTotal + taxAmount + deliveryPrice;

            if (deliveryCostNode) {
                deliveryCostNode.textContent = deliveryPrice.toFixed(2) + ' zl';
            }

            if (grandTotalNode) {
                grandTotalNode.textContent = grandTotal.toFixed(2) + ' zl';
            }
        }

        paymentInputs.forEach((input) => {
            input.addEventListener('change', updatePaymentPanels);
        });

        deliveryInputs.forEach((input) => {
            input.addEventListener('change', updateSummary);
        });

        updatePaymentPanels();
        updateSummary();
    })();
</script>

<?php
$view->render('partials/footer');

unset($_SESSION['previous_data']);
unset($_SESSION['error']);
?>
