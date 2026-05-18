<?php
/** @var array $user */
/** @var array $tables */
/** @var App\View\View $view */
/** @var string $root */

$view->render('partials/header', ['user' => $user, 'params' , 'cart' => $params['cart'] ?? null]);
?>
<div class="container account-container">
    <aside class="account-sidebar">
        <nav class="sidebar-nav">
            <a href="<?= htmlspecialchars($root)?>/employee" class="nav-item">
                <span class="nav-icon bi bi-grid-1x2-fill"></span>
                Przegląd panelu
            </a>
            <a href="<?= htmlspecialchars($root)?>/employee/products" class="nav-item">
                <span class="nav-icon bi bi-box-seam"></span>
                Zarządzanie produktami
            </a>
            <a href="<?= htmlspecialchars($root)?>/employee/users" class="nav-item">
                <span class="nav-icon bi bi-people"></span>
                Zarządzanie użytkownikami
            </a>
            <a href="<?= htmlspecialchars($root)?>/employee/tables" class="nav-item active">
                <span class="nav-icon bi bi-shield-lock"></span>
                Edycja wszystkich danych
            </a>
        </nav>
    </aside>

    <main class="account-content">
        <header class="content-header">
            <h1>Edycja wszystkich danych</h1>
            <p>Zarządzaj wszystkimi tabelami w systemie</p>
        </header>

        <div class="admin-grid">
            <?php foreach ($tables as $table): ?>
                <a href="<?= htmlspecialchars($root)?>/employee/tables/table/<?= $table ?>" class="admin-card">
                    <div class="card-icon">
                        <i class="fas fa-table"></i>
                    </div>
                    <div class="card-info">
                        <h3><?= ucfirst(str_replace('_', ' ', $table)) ?></h3>
                        <span>Tabela: <?= $table ?></span>
                    </div>
                    <div class="card-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
</div>
<?php $view->render('partials/footer'); ?>
