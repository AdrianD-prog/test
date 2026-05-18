<?php
/**
 * @var array $users
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
            <a href="<?= htmlspecialchars($root)?>/employee/products" class="nav-item">
                <span class="nav-icon bi bi-box-seam"></span>
                Zarządzanie produktami
            </a>
            <a href="<?= htmlspecialchars($root)?>/employee/users" class="nav-item active">
                <span class="nav-icon bi bi-people"></span>
                Zarządzanie użytkownikami
            </a>
            <a href="<?= htmlspecialchars($root)?>/employee/tables" class="nav-item">
                <span class="nav-icon bi bi-shield-lock"></span>
                Edycja wszystkich danych
            </a>
        </nav>
    </aside>

    <div class="account-content">
        <div class="content-header">
            <h2>Zarządzanie użytkownikami</h2>
            <a href="<?= htmlspecialchars($root) ?>/employee/users/add" class="amazon-btn">
                <i class="bi bi-person-plus"></i> Dodaj użytkownika
            </a>
        </div>

        <div class="products-card">
            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Użytkownik</th>
                            <th>Rola</th>
                            <th>Status</th>
                            <th>Data rejestracji</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td>#<?= htmlspecialchars((string)$u['id']) ?></td>
                            <td>
                                <div class="product-info">
                                    <div class="product-details">
                                        <div class="product-name">
                                            <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?>
                                        </div>
                                        <div class="product-sku" style="font-size: 0.8rem; color: var(--theme-text-muted);">
                                            <?= htmlspecialchars($u['email']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge role-<?= htmlspecialchars(strtolower($u['role_name'])) ?>">
                                    <?= htmlspecialchars($u['role_name']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($u['verified']): ?>
                                    <span class="status-badge status-active">Zweryfikowany</span>
                                <?php else: ?>
                                    <span class="status-badge status-out">Niezweryfikowany</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars(date('d.m.Y', strtotime($u['created_at']))) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= htmlspecialchars($root) ?>/employee/users/edit/<?= $u['id'] ?>" class="btn-edit" title="Edytuj">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ((int)$u['id'] !== (int)$user['id']): ?>
                                    <form action="<?= htmlspecialchars($root) ?>/employee/users/delete/<?= $u['id'] ?>" method="POST" style="display:inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć tego użytkownika?')">
                                        <button type="submit" class="btn-delete" title="Usuń">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Brak użytkowników do wyświetlenia.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $view->render('partials/footer'); ?>
