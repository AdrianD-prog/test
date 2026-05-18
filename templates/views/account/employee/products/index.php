<?php $view->render('partials/header', ['params' => $params ?? null]); ?>
<div class="container account-container">
    <aside class="account-sidebar">
        <nav class="sidebar-nav">
            <a href="<?= htmlspecialchars($root)?>/employee" class="nav-item">
                <span class="nav-icon bi bi-grid-1x2-fill"></span>
                Przegląd panelu
            </a>
            <a href="<?= htmlspecialchars($root)?>/employee/products" class="nav-item active">
                <span class="nav-icon bi bi-box-seam"></span>
                Zarządzanie produktami
            </a>
            <a href="<?= htmlspecialchars($root)?>/employee/users" class="nav-item">
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
            <h2>Zarządzanie produktami</h2>
            <a href="<?= htmlspecialchars($root) ?>/employee/products/add" class="amazon-btn">
                <span class="bi bi-plus-lg"></span> Dodaj produkt
            </a>
        </div>

        <div class="products-table-container">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Obraz</th>
                        <th>Nazwa</th>
                        <th>Cena</th>
                        <th>Stan</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Brak produktów do wyświetlenia.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$product['id']) ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($root . "/static/images/products/" . htmlspecialchars($product["image"] ?? "placeholder") . ".webp") ?>"
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         class="product-thumb">
                                </td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= number_format((float)$product['price'], 2, ',', ' ') ?> zł</td>
                                <td><?= htmlspecialchars((string)$product['stock_quantity']) ?></td>
                                <td>
                                    <a href="<?= htmlspecialchars($root) ?>/employee/products/edit/<?= $product['id'] ?>" 
                                       class="btn-edit" title="Edytuj">
                                        <span class="bi bi-pencil"></span>
                                    </a>
                                    <form action="<?= htmlspecialchars($root) ?>/employee/products/delete/<?= $product['id'] ?>" 
                                          method="POST" 
                                          onsubmit="return confirm('Czy na pewno chcesz usunąć ten produkt?');"
                                          style="display: inline;">
                                        <button type="submit" class="btn-delete" title="Usuń">
                                            <span class="bi bi-trash"></span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $view->render('partials/footer'); ?>
