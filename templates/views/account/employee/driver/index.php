<?php
/**
 * @var array $deliveries
 * @var string $root
 * @var View $view
 * @var array $user
 */

    use App\View\View;

    $view->render('partials/header', ['user' => $user, 'params' , 'cart' => $params['cart'] ?? null]);
?>

<div class="container account-container">
    <aside class="account-sidebar">
        <nav class="sidebar-nav">
            <a href="<?= htmlspecialchars($root)?>/employee" class="nav-item">
                <span class="nav-icon bi bi-grid-1x2-fill"></span>
                Przegląd panelu
            </a>
            <a href="<?= htmlspecialchars($root)?>/employee/driver" class="nav-item active">
                <span class="nav-icon bi bi-box-seam"></span>
                Moje dostawy
            </a>
        </nav>
    </aside>

    <div class="account-content">
        <div class="content-header">
            <h2>Panel kierowcy - dostawy</h2>
        </div>

        <div class="products-card">
            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                    <tr>
                        <th>ID dostawy</th>
                        <th>Zamówienie</th>
                        <th>Klient</th>
                        <th>Adres</th>
                        <th>Status</th>
                        <th>Akcja</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($deliveries)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Brak przypisanych dostaw.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($deliveries as $delivery): ?>
                            <?php $isDelivered = strtolower((string)($delivery['status'] ?? '')) === 'delivered'; ?>
                            <tr>
                                <td>#<?= (int)$delivery['id'] ?></td>
                                <td>#<?= (int)($delivery['order_id'] ?? 0) ?></td>
                                <td>
                                    <div class="product-name"><?= htmlspecialchars((string)($delivery['customer_name'] ?? '')) ?></div>
                                    <div class="product-sku"><?= htmlspecialchars((string)($delivery['customer_email'] ?? '')) ?></div>
                                </td>
                                <td>
                                    <?= htmlspecialchars((string)($delivery['city'] ?? '')) ?>
                                    <?= htmlspecialchars((string)($delivery['address'] ?? '')) ?>
                                </td>
                                <td>
                                    <?php if ($isDelivered): ?>
                                        <span class="status-badge status-active">Dostarczone</span>
                                    <?php else: ?>
                                        <span class="status-badge status-out">W trasie</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$isDelivered): ?>
                                        <form action="<?= htmlspecialchars($root) ?>/employee/driver/delivered/<?= (int)$delivery['id'] ?>" method="POST">
                                            <button type="submit" class="amazon-btn">
                                                Oznacz jako dostarczone
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="role-badge role-driver">Zakończono</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $view->render('partials/footer'); ?>
