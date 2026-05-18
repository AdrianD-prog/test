<?php
/**
 * @var View $view
 * @var string $root
 * @var array $user
 */

use App\View\View;

$view->render('partials/header', ['params' => $params ?? null]);
?>

<div class="container amazon-account-container">
    <!-- Breadcrumb -->
    <div class="amazon-breadcrumb">
        <a href="<?= htmlspecialchars($root)?>/">Strona główna</a>
        <span> › </span>
        <span>Twoje konto</span>
    </div>

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="welcome-title">
            Witaj, <?= htmlspecialchars($user['first_name'] ?? 'Kliencie') ?>!
        </div>
        <div class="welcome-subtitle">
            Zarządzaj swoim kontem, sprawdzaj zamówienia i personalizuj ustawienia
        </div>
    </div>

    <!-- Main Account Grid -->
    <div class="account-grid">
        <!-- Sidebar Navigation -->
        <div class="account-sidebar">
            <div class="sidebar-header">
                <i class="nav-icon"></i> Twoje konto
            </div>
            <ul class="sidebar-nav">
                <li>
                    <a href="#" class="active">
                        <span class="nav-icon bi bi-grid-1x2-fill"></span>
                        Przegląd konta
                    </a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($root) ?>/orders">
                        <span class="nav-icon bi bi-cart"></span>
                        Moje zamówienia
                    </a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($root) ?>/account/edit">
                        <span class="nav-icon bi bi-pencil-square"></span>
                        Edytuj profil
                    </a>
                </li>
                
                <li>
                    <a href="<?= htmlspecialchars($root) ?>/favorites">
                        <span class="nav-icon bi bi-heart"></span>
                        Ulubione
                    </a>
                </li>
                <?php if (isset($user['role_name']) && $user['role_name'] !== 'customer'): ?>
                    <li>
                        <a href="<?= htmlspecialchars($root) ?>/employee">
                            <span class="nav-icon bi bi-person-workspace"></span>
                            Panel Pracownika
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="<?= $root ?>/logout">
                        <span class="nav-icon bi bi-box-arrow-right"></span>
                        Wyloguj
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="account-content">
            <div class="content-header">
                <h2>Przegląd konta</h2>
            </div>

            <div class="profile-card">
                <!-- Profile Row -->
                <div class="profile-row">
                    <!-- Avatar -->
                    <div class="profile-avatar">
                        <div class="avatar-circle">
                                <div class="avatar-placeholder text-primary bg-primary bg-opacity-10">
                                    <?= strtoupper(mb_substr($user['first_name'] ?? 'U', 0, 1)) ?><?= strtoupper(mb_substr($user['last_name'] ?? 'S', 0, 1)) ?>
                                </div>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="profile-info">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Pełne imię</div>
                                <div class="info-value">
                                    <?= htmlspecialchars($user['first_name'] ?? 'N/A') ?>
                                    <?= htmlspecialchars($user['last_name'] ?? '') ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">
                                    <?= htmlspecialchars($user['email'] ?? 'N/A') ?>
                                    <?php if (!isset($user['verified']) || !$user['verified']): ?>
                                        <small>(niezweryfikowany)</small>
                                    <?php else: ?>
                                        <small>(zweryfikowany)</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Telefon</div>
                                <div class="info-value"><?= htmlspecialchars($user['phone'] ?? 'Brak') ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Lokalizacja</div>
                                <div class="info-value">
                                    <?= htmlspecialchars($user['country'] ?? '') ?>
                                    <?= htmlspecialchars($user['city'] ?? '') ?><br>
                                    <?= htmlspecialchars($user['address'] ?? '') ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Data dołączenia</div>
                                <div class="info-value">
                                    <?php
                                    $createdAt = $user['created_at'] ?? null;
                                    if ($createdAt) {
                                        try {
                                            $date = new DateTime($createdAt);
                                            echo $date->format('d.m.Y');
                                        } catch (Exception $e) {
                                            echo 'N/A';
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="profile-actions">
                        <a href="<?= htmlspecialchars($root)?>/account/edit" class="amazon-btn">
                         Edytuj profil
                        </a>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= $params['orders_count'] ?? '0' ?></div>
                        <div class="stat-label">Złożonych zamówień</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $params['favorites_count'] ?? '0' ?></div>
                        <div class="stat-label">Produktów w ulubionych</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $params['reviews_count'] ?? '0' ?></div>
                        <div class="stat-label">Wystawionych opinii</div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <?php if (!empty($params['recent_orders'])): ?>
                <div class="recent-orders">
                    <div class="section-title">Ostatnie zamówienia</div>
                    <?php foreach (array_slice($params['recent_orders'], 0, 3) as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <span class="order-id">Zamówienie #<?= htmlspecialchars($order['id'] ?? 'N/A') ?></span>
                                <span class="order-date"><?= date('d.m.Y', strtotime($order['created_at'] ?? 'now')) ?></span>
                                <span class="order-status status-<?= strtolower($order['status'] ?? 'processing') ?>">
                                    <?php
                                    $status = $order['status'] ?? 'processing';
                                    $statusLabels = [
                                        'delivered' => 'Dostarczono',
                                        'shipped' => 'Wysłano',
                                        'processing' => 'W realizacji'
                                    ];
                                    echo $statusLabels[$status] ?? $status;
                                    ?>
                                </span>
                            </div>
                            <div class="order-total">
                                <strong>Kwota:</strong> <?= number_format($order['total'] ?? 0, 2) ?> zł
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="view-all-link">
                        <a href="<?= htmlspecialchars($root)?>/account/orders">Zobacz wszystkie zamówienia ›</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$view->render('partials/footer');
?>