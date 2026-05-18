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
            <h2>Edytuj produkt: #<?= htmlspecialchars((string)$product['id']) ?></h2>
            <a href="<?= htmlspecialchars($root) ?>/employee/products" class="btn-back">
                <span class="bi bi-arrow-left"></span> Powrót do listy
            </a>
        </div>

        <div class="form-container">
            <form action="<?= htmlspecialchars($root) ?>/employee/products/update/<?= $product['id'] ?>" method="POST" class="amazon-form">
                <div class="form-group">
                    <label for="name">Nazwa produktu</label>
                    <input type="text" id="name" name="name" required class="form-control" 
                           value="<?= htmlspecialchars($product['name']) ?>">
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="price">Cena (zł)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required class="form-control"
                               value="<?= (float)$product['price'] ?>">
                    </div>
                    <div class="form-group col">
                        <label for="stock_quantity">Ilość w magazynie</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0" required class="form-control"
                               value="<?= (int)$product['stock_quantity'] ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="category_id">Kategoria</label>
                    <select id="category_id" name="category_id" required class="form-control">
                        <option value="">Wybierz kategorię...</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= $category['id'] == ($product['category_id'] ?? '') ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image_url">URL zdjęcia</label>
                    <input type="url" id="image_url" name="image_url" class="form-control" 
                           value="<?= htmlspecialchars($product['image'] ?? '') ?>"
                           placeholder="https://example.com/image.jpg">
                </div>

                <div class="form-group">
                    <label for="description">Opis (obsługuje Markdown)</label>
                    <textarea id="description" name="description" rows="10" class="form-control"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="amazon-btn">Zaktualizuj produkt</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $view->render('partials/footer'); ?>
