<?php
/**
 * @var array $user
 * @var array $orders
 * @var object $view
 * @var string $root
 */

$user = $user ?? [];
$orders = $orders ?? [];

$statusLabels = [
    'all' => 'Wszystkie',
    'new' => 'Nowe',
    'paid' => 'Oplacone',
    'shipped' => 'Wyslane',
    'delivered' => 'Dostarczone',
    'cancelled' => 'Anulowane',
];

$statusClasses = [
    'new' => 'status-new',
    'paid' => 'status-paid',
    'shipped' => 'status-shipped',
    'delivered' => 'status-delivered',
    'cancelled' => 'status-cancelled',
];

$statusCounts = array_fill_keys(array_keys($statusLabels), 0);
$statusCounts['all'] = count($orders);

foreach ($orders as $order) {
    $status = strtolower((string)($order['status'] ?? 'new'));
    if (isset($statusCounts[$status])) {
        $statusCounts[$status]++;
    }
}

$view->render('partials/header', ['user' => $user]);
?>

<div class="orders-page amazon-orders-theme">
    <div class="orders-shell">
        <div class="orders-breadcrumb">
            <a href="<?= $root ?>">Strona glowna</a>
            <span>/</span>
            <a href="<?= $root ?>/account">Twoje konto</a>
            <span>/</span>
            <span>Moje zamowienia</span>
        </div>

        <section class="orders-hero">
            <div class="orders-hero-copy">
                <p class="orders-eyebrow">amazonix orders</p>
                <h1>Twoje zamowienia</h1>
                <p class="orders-subtitle">
                    Przegladaj historie zakupow, filtruj statusy i szybko sprawdzaj najwazniejsze szczegoly kazdego zamowienia.
                </p>
            </div>
            <div class="orders-hero-actions">
                <a href="<?= $root ?>/products" class="orders-primary-btn">Wroc do zakupow</a>
            </div>
        </section>

        <?php if ($orders === []): ?>
            <section class="orders-empty">
                <h2>Nie masz jeszcze zadnych zamowien</h2>
                <p>Gdy tylko zlozysz pierwsze zamowienie, pojawi sie tutaj wraz ze statusem i podsumowaniem kosztow.</p>
                <a href="<?= $root ?>/products" class="orders-primary-btn">Przegladaj produkty</a>
            </section>
        <?php else: ?>
            <section class="orders-toolbar">
                <div class="orders-toolbar-top">
                    <div class="orders-toolbar-title">
                        <h2>Historia zamowien</h2>
                        <p><?= count($orders) ?> zamowien w historii konta</p>
                    </div>
                    <label class="orders-search">
                        <span>Szukaj po numerze zamowienia</span>
                        <input type="search" id="order-search" placeholder="Np. 152 lub 2025">
                    </label>
                </div>

                <div class="orders-filters" role="tablist" aria-label="Filtry zamowien">
                    <?php foreach ($statusLabels as $statusKey => $statusLabel): ?>
                        <button
                            type="button"
                            class="orders-filter-chip <?= $statusKey === 'all' ? 'is-active' : '' ?>"
                            data-filter="<?= htmlspecialchars($statusKey, ENT_QUOTES, 'UTF-8') ?>"
                        >
                            <span><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?></span>
                            <strong><?= (int)($statusCounts[$statusKey] ?? 0) ?></strong>
                        </button>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="orders-list" id="orders-list">
                <?php foreach ($orders as $order): ?>
                    <?php
                    $status = strtolower((string)($order['status'] ?? 'new'));
                    $statusLabel = $statusLabels[$status] ?? ucfirst($status);
                    $statusClass = $statusClasses[$status] ?? 'status-new';
                    $orderId = (string)($order['id'] ?? '0');
                    $orderDate = $order['order_date'] ?? null;
                    $formattedDate = $orderDate ? date('d.m.Y H:i', strtotime((string)$orderDate)) : 'Brak daty';
                    $shortDate = $orderDate ? date('d M Y', strtotime((string)$orderDate)) : 'Brak daty';
                    $itemsCount = (int)($order['items_count'] ?? 0);
                    $totalPrice = (float)($order['total_price'] ?? 0);
                    ?>
                    <article
                        class="amazon-order-card"
                        data-status="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"
                        data-order-id="<?= htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8') ?>"
                    >
                        <header class="amazon-order-meta">
                            <div class="amazon-order-meta-item">
                                <span>Zlozone</span>
                                <strong><?= htmlspecialchars($shortDate, ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                            <div class="amazon-order-meta-item">
                                <span>Suma</span>
                                <strong><?= number_format($totalPrice, 2) ?> zl</strong>
                            </div>
                            <div class="amazon-order-meta-item">
                                <span>Liczba sztuk</span>
                                <strong><?= $itemsCount ?></strong>
                            </div>
                            <div class="amazon-order-meta-item amazon-order-meta-id">
                                <span>Nr zamowienia</span>
                                <strong>#<?= htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                        </header>

                        <div class="amazon-order-body">
                            <div class="amazon-order-main">
                                <div class="amazon-order-status-row">
                                    <span class="order-status-badge <?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="amazon-order-timestamp"><?= htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>

                                <h3>Zamowienie #<?= htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="amazon-order-description">
                                    <?= $itemsCount ?> produkt<?= $itemsCount === 1 ? '' : ($itemsCount < 5 ? 'y' : 'ow') ?>
                                    w zamowieniu. Aktualny status: <?= htmlspecialchars(mb_strtolower($statusLabel), ENT_QUOTES, 'UTF-8') ?>.
                                </p>
                            </div>

                            <div class="amazon-order-actions">
                                <a href="<?= $root ?>/orders/checkout" class="orders-secondary-btn">Kup podobnie</a>
                                <button type="button" class="orders-secondary-btn orders-secondary-btn-muted" disabled>
                                    Szczegoly wkrotce
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>

            <div class="orders-no-results" id="orders-no-results" hidden>
                <h2>Brak wynikow dla wybranych filtrow</h2>
                <p>Sprobuj zmienic status albo wyczyscic wyszukiwanie numeru zamowienia.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($orders !== []): ?>
<script>
    (function() {
        const chips = Array.from(document.querySelectorAll('.orders-filter-chip'));
        const searchInput = document.getElementById('order-search');
        const cards = Array.from(document.querySelectorAll('.amazon-order-card'));
        const emptyState = document.getElementById('orders-no-results');
        let activeFilter = 'all';

        function applyFilters() {
            const query = (searchInput?.value || '').trim().toLowerCase();
            let visibleCount = 0;

            cards.forEach((card) => {
                const status = card.dataset.status || '';
                const orderId = (card.dataset.orderId || '').toLowerCase();
                const matchesFilter = activeFilter === 'all' || status === activeFilter;
                const matchesQuery = query === '' || orderId.includes(query);
                const visible = matchesFilter && matchesQuery;

                card.hidden = !visible;
                if (visible) {
                    visibleCount++;
                }
            });

            if (emptyState) {
                emptyState.hidden = visibleCount !== 0;
            }
        }

        chips.forEach((chip) => {
            chip.addEventListener('click', function() {
                activeFilter = this.dataset.filter || 'all';
                chips.forEach((node) => node.classList.remove('is-active'));
                this.classList.add('is-active');
                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

        applyFilters();
    })();
</script>
<?php endif; ?>

<?php $view->render('partials/footer'); ?>
