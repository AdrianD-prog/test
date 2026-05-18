<?php
/** @var array $user */
/** @var string $tableName */
/** @var array $tableData */
/** @var array $tableSchema */
/** @var App\View\View $view */
/** @var string $root */

$view->render('partials/header', ['user' => $user, 'params' , 'cart' => $params['cart'] ?? null]);

$primaryKey = 'id';
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
            <h1>Zarządzanie tabelą: <?= $tableName ?></h1>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="toggleAddForm()">
                    <span class="bi bi-plus-lg"></span> Dodaj rekord
                </button>
            </div>
        </header>

        <!-- Formularz dodawania -->
        <section id="add-form-section" class="admin-section" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h2>Dodaj nowy rekord</h2>
                </div>
                <form action="<?= htmlspecialchars($root)?>/employee/tables/table/add/<?= $tableName ?>" method="POST" class="form-grid">
                    <?php foreach ($tableSchema as $col): ?>
                        <?php if ($col['Extra'] === 'auto_increment') continue; ?>
                        <div class="form-group">
                            <label><?= $col['Field'] ?> <?= $col['Null'] === 'NO' ? '*' : '' ?></label>
                            <input type="text" name="<?= $col['Field'] ?>" class="form-control" <?= $col['Null'] === 'NO' ? 'required' : '' ?>>
                            <small><?= $col['Type'] ?></small>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">Zapisz</button>
                        <button type="button" class="btn btn-secondary" onclick="toggleAddForm()">Anuluj</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Tabela danych -->
        <section class="admin-section">
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <?php foreach ($tableSchema as $col): ?>
                                <th><?= $col['Field'] ?></th>
                            <?php endforeach; ?>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tableData)): ?>
                            <tr>
                                <td colspan="<?= count($tableSchema) + 1 ?>" class="text-center">Brak danych w tabeli.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tableData as $row): ?>
                                <tr>
                                    <?php foreach ($tableSchema as $col): ?>
                                        <td title="<?= htmlspecialchars((string)($row[$col['Field']] ?? '')) ?>">
                                            <?php 
                                                $val = $row[$col['Field']] ?? '';
                                                echo strlen((string)$val) > 50 ? mb_substr((string)$val, 0, 50) . '...' : htmlspecialchars((string)$val);
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="actions-cell">
                                        <button class="btn-icon" onclick='openEditModal(<?= json_encode($row) ?>)' title="Edytuj">
                                            <span class="bi bi-pencil"></span>
                                        </button>
                                        <form action="<?= htmlspecialchars($root)?>/employee/tables/table/delete/<?= $tableName ?>/<?= $row[$primaryKey] ?? 0 ?>" method="POST" style="display:inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć ten rekord?')">
                                            <button type="submit" class="btn-icon btn-delete" title="Usuń">
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
        </section>
    </main>
</div>

<!-- Modal Edycji -->
<div id="edit-modal" class="modal" style="display: none;">
    <div class="modal-content card">
        <div class="card-header">
            <h2>Edytuj rekord</h2>
            <button class="close-modal" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="edit-form" method="POST" class="form-grid">
            <div id="edit-fields-container">
                <!-- Pola zostaną wstawione przez JS -->
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Anuluj</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAddForm() {
    const section = document.getElementById('add-form-section');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

function openEditModal(rowData) {
    const modal = document.getElementById('edit-modal');
    const container = document.getElementById('edit-fields-container');
    const form = document.getElementById('edit-form');
    const tableName = "<?= $tableName ?>";
    const primaryKey = "<?= $primaryKey ?>";
    
    container.innerHTML = '';
    form.action = `<?= htmlspecialchars($root)?>/employee/tables/table/update/${tableName}/${rowData[primaryKey]}`;
    
    const schema = <?= json_encode($tableSchema) ?>;
    
    schema.forEach(col => {
        if (col.Extra === 'auto_increment') return;
        
        const div = document.createElement('div');
        div.className = 'form-group';
        
        const label = document.createElement('label');
        label.innerText = col.Field + (col.Null === 'NO' ? ' *' : '');
        
        const input = document.createElement('input');
        input.type = 'text';
        input.name = col.Field;
        input.className = 'form-control';
        input.value = rowData[col.Field] || '';
        if (col.Null === 'NO') input.required = true;
        
        const small = document.createElement('small');
        small.innerText = col.Type;
        
        div.appendChild(label);
        div.appendChild(input);
        div.appendChild(small);
        container.appendChild(div);
    });
    
    modal.style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

// Zamknij modal po kliknięciu poza nim
window.onclick = function(event) {
    const modal = document.getElementById('edit-modal');
    if (event.target == modal) {
        closeEditModal();
    }
}
</script>

<?php $view->render('partials/footer'); ?>
