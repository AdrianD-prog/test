<?php
/**
 * @var array $userToEdit
 * @var array $roles
 * @var string $root
 * @var \App\View\View $view
 */
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
            <h2>Edytuj użytkownika: <?= htmlspecialchars($userToEdit['first_name'] . ' ' . $userToEdit['last_name']) ?></h2>
        </div>

        <div class="form-card">
            <form action="<?= htmlspecialchars($root) ?>/employee/users/update/<?= $userToEdit['id'] ?>" method="POST" class="amazon-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="first_name">Imię</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" value="<?= htmlspecialchars($userToEdit['first_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Nazwisko</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" value="<?= htmlspecialchars($userToEdit['last_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($userToEdit['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="role_id">Rola</label>
                        <select id="role_id" name="role_id" class="form-control" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" <?= $role['id'] == $userToEdit['role_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phone">Telefon</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($userToEdit['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="country">Kraj</label>
                        <input type="text" id="country" name="country" class="form-control" value="<?= htmlspecialchars($userToEdit['country'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="city">Miasto</label>
                        <input type="text" id="city" name="city" class="form-control" value="<?= htmlspecialchars($userToEdit['city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Adres</label>
                        <input type="text" id="address" name="address" class="form-control" value="<?= htmlspecialchars($userToEdit['address'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= htmlspecialchars($root) ?>/employee/users" class="btn-cancel">Anuluj</a>
                    <button type="submit" class="amazon-btn">Zapisz zmiany</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $view->render('partials/footer'); ?>
