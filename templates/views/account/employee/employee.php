<?php
/**
 * @var View $view
 * @var string $root
 * @var array $user
 */

use App\View\View;

$view->render('partials/header', ['user' => $params['user'] ?? null]);

$user = $params['user'] ?? [];
$role = strtolower((string)($user['role_name'] ?? 'customer'));
$firstName = $user['first_name'] ?? 'Użytkowniku';
$lastName = $user['last_name'] ?? '';
$displayName = trim($firstName . ' ' . $lastName) ?: 'Użytkownik';
$initials = strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName ?: 'X', 0, 1));

$roleLabels = [
    'driver' => 'Kierowca',
    'manager' => 'Menedżer',
    'admin' => 'Administrator',
    'warehouse_worker' => 'Magazynier',
];

$roleTaglines = [
    'driver' => 'Monitoruj trasę dnia, dostawy i najważniejsze zadania operacyjne.',
    'manager' => 'Pilnuj zespołu, wyników i bieżących priorytetów operacyjnych.',
    'admin' => 'Zarządzaj systemem, użytkownikami i stabilnością platformy.',
    'warehouse_worker' => 'Pilnuj kompletacji, pakowania i obsługi przesyłek.',
];

$roleIcons = [
    'driver' => 'bi-truck',
    'manager' => 'bi-clipboard-data',
    'admin' => 'bi-shield-lock',
    'warehouse_worker' => 'bi-box-seam',
];

$roleConfigs = [
    'driver' => [
        'accent' => 'driver',
        'headline' => 'Panel kierowcy',
        'description' => 'Operacyjny podgląd dostaw, zadań i priorytetów na trasie.',
        'stats' => [
            ['value' => '18', 'label' => 'Dostaw na trasie'],
            ['value' => '5', 'label' => 'Pilnych paczek'],
            ['value' => '2', 'label' => 'Punkty odbioru'],
        ],
        'actions' => [
            ['title' => 'Moje dostawy', 'desc' => 'Lista kursów i kolejność realizacji.', 'href' => $root . '/employee/driver', 'status' => 'Dostępne'],
            ['title' => 'Status dostaw', 'desc' => 'Aktualizacja dostarczeń i problemów.', 'href' => null, 'status' => 'Do wdrożenia'],
        ]
    ],
    'manager' => [
        'accent' => 'manager',
        'headline' => 'Panel menedżera',
        'description' => 'Jedno miejsce do nadzoru zespołu, zamówień i wyników operacyjnych.',
        'stats' => [
            ['value' => '24', 'label' => 'Otwarte zamówienia'],
            ['value' => '9', 'label' => 'Osób na zmianie'],
            ['value' => '3', 'label' => 'Alerty operacyjne'],
        ],
        'actions' => [
            ['title' => 'Zespół', 'desc' => 'Podgląd pracowników, zmian i obciążenia.', 'href' => null, 'status' => 'Do wdrożenia'],
            ['title' => 'Zamówienia', 'desc' => 'Nadzór nad realizacją i opóźnieniami.', 'href' => null, 'status' => 'Do wdrożenia'],
            ['title' => 'Raporty', 'desc' => 'Wyniki operacyjne i wydajność procesów.', 'href' => null, 'status' => 'Do wdrożenia'],
            ['title' => 'Produkty', 'desc' => 'Zarządzanie katalogiem produktów.', 'href' => $root . '/account/products', 'status' => 'Dostępne'],
            ['title' => 'Użytkownicy', 'desc' => 'Zarządzanie kontami użytkowników.', 'href' => $root . '/account/users', 'status' => 'Dostępne']
        ]
    ],
    'warehouse_worker' => [
        'accent' => 'warehouse',
        'headline' => 'Panel magazyniera',
        'description' => 'Przejrzysty pulpit do kompletacji, pakowania i kontroli przesyłek.',
        'stats' => [
            ['value' => '31', 'label' => 'Paczek do spakowania'],
            ['value' => '8', 'label' => 'Pilnych kompletacji'],
            ['value' => '4', 'label' => 'Zwrotów do sprawdzenia'],
        ],
        'actions' => [
            ['title' => 'Lista kompletacji', 'desc' => 'Zamówienia oczekujące na zebranie.', 'href' => null, 'status' => 'Do wdrożenia'],
            ['title' => 'Stany magazynowe', 'desc' => 'Kontrola braków i ruchu produktów.', 'href' => null, 'status' => 'Do wdrożenia'],
        ]
    ],
    'admin' => [
        'accent' => 'admin',
        'headline' => 'Panel administratora',
        'description' => 'Centralny panel do zarządzania platformą i bezpieczeństwem systemu.',
        'stats' => [
            ['value' => '156', 'label' => 'Użytkowników online'],
            ['value' => '6', 'label' => 'Otwartych zgłoszeń'],
            ['value' => '99.9%', 'label' => 'Dostępności systemu'],
        ],
        'actions' => [
            ['title' => 'Użytkownicy', 'desc' => 'Zarządzanie rolami i uprawnieniami.', 'href' => $root . '/account/users'],
            ['title' => 'Produkty', 'desc' => 'Zarządzanie katalogiem produktów.', 'href' => $root . '/account/products'],
            ['title' => 'Edycja danych', 'desc' => 'Zarządzanie tabelami bazy danych.', 'href' => $root . '/account/admin'],
        ]
    ],
];

$dashboard = $roleConfigs[$role] ?? [
    'accent' => 'default',
    'headline' => 'Panel pracownika',
    'description' => 'Ten widok jest przeznaczony dla ról operacyjnych i administracyjnych.',
    'stats' => [
        ['value' => '12', 'label' => 'Otwartych spraw'],
        ['value' => '4', 'label' => 'Priorytetów na dziś'],
        ['value' => '98%', 'label' => 'Gotowości profilu'],
    ],
    'actions' => [
        ['title' => 'Profil pracownika', 'desc' => 'Przegląd danych i podstawowych informacji.', 'href' => $root . '/account', 'status' => 'Dostępne'],
        ['title' => 'Edytuj profil', 'desc' => 'Aktualizacja danych użytkownika.', 'href' => $root . '/account/edit', 'status' => 'Dostępne'],
    ],
    'focus' => [
        'Szybki dostęp do danych pracownika.',
        'Miejsce gotowe pod rozbudowę modułów operacyjnych.',
        'Widok wspólny dla ról bez osobnej konfiguracji.',
    ],
];

$displayRole = $roleLabels[$role] ?? 'Pracownik';
$roleIcon = $roleIcons[$role] ?? 'bi-grid-1x2';
?>

<div class="container amazon-account-container dashboard-account-container">
    <div class="amazon-breadcrumb">
        <a href="<?= htmlspecialchars($root)?>/">Strona główna</a>
        <span> › </span>
        <a href="<?= htmlspecialchars($root)?>/account">Twoje konto</a>
        <span> › </span>
        <span>Panel pracownika</span>
    </div>

    <div class="welcome-banner dashboard-welcome dashboard-welcome-<?= htmlspecialchars($dashboard['accent']) ?>">
        <div class="welcome-title">
            <?= htmlspecialchars($dashboard['headline']) ?>
        </div>
        <div class="welcome-subtitle">
            <?= htmlspecialchars($dashboard['description']) ?>
        </div>
    </div>

    <div class="account-grid">
        <div class="account-sidebar">
            <div class="sidebar-header">
                <i class="nav-icon bi <?= htmlspecialchars($roleIcon) ?>"></i> Panel pracownika
            </div>
            <ul class="sidebar-nav">
                <li>
                    <a href="<?= htmlspecialchars($root) ?>/employee/" class="active">
                        <span class="nav-icon bi bi-grid-1x2-fill"></span>
                        Przegląd panelu
                    </a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($root) ?>/account">
                        <span class="nav-icon bi bi-person"></span>
                        Moje konto
                    </a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($root) ?>/account/edit">
                        <span class="nav-icon bi bi-pencil-square"></span>
                        Dane profilu
                    </a>
                </li>
                <?php if (in_array($user['role_name'], ['manager', 'admin', 'owner'])): ?>
                <li>
                    <a href="<?= htmlspecialchars($root) ?>/employee/products">
                        <span class="nav-icon bi bi-box-seam"></span>
                        Zarządzanie produktami
                    </a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($root) ?>/employee/users">
                        <span class="nav-icon bi bi-people"></span>
                        Zarządzanie użytkownikami
                    </a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($root) ?>/employee/tables">
                        <span class="nav-icon bi bi-shield-lock"></span>
                        Edycja danych
                    </a>
                </li>
                <?php endif; ?>
                <?php if ($user['role_name'] === 'driver'): ?>
                <li>
                    <a href="<?= htmlspecialchars($root) ?>/employee/driver">
                        <span class="nav-icon bi bi-truck"></span>
                        Moje dostawy
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a href="<?= htmlspecialchars($root) ?>/logout">
                        <span class="nav-icon bi bi-box-arrow-right"></span>
                        Wyloguj
                    </a>
                </li>
            </ul>
        </div>

        <div class="account-content">
            <div class="content-header">
                <h2><?= htmlspecialchars($displayRole) ?> - centrum pracy</h2>
            </div>

            <div class="profile-card">
                <div class="profile-row">
                    <div class="profile-avatar">
                        <div class="avatar-circle">
                            <div class="avatar-placeholder text-primary bg-primary bg-opacity-10">
                                <?= htmlspecialchars($initials) ?>
                            </div>
                        </div>
                    </div>

                    <div class="profile-info">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Pracownik</div>
                                <div class="info-value"><?= htmlspecialchars($displayName) ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Stanowisko</div>
                                <div class="info-value"><?= htmlspecialchars($displayRole) ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value"><?= htmlspecialchars($user['email'] ?? 'Brak') ?></div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a href="<?= htmlspecialchars($root)?>/account/edit" class="amazon-btn">
                            Edytuj profil
                        </a>
                    </div>
                </div>

                <div class="recent-orders">
                    <div class="section-title">Szybkie akcje</div>
                    <div class="admin-quick-actions" style="margin-bottom: 20px;">
                        <?php foreach ($dashboard['actions'] as $action): ?>
                            <?php if ($action['href']): ?>
                                <a href="<?= htmlspecialchars($action['href']) ?>" class="amazon-btn" style="text-decoration: none;">
                                    <?= htmlspecialchars($action['title']) ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="stats-grid">
                    <?php foreach ($dashboard['stats'] as $stat): ?>
                        <div class="stat-card">
                            <div class="stat-number"><?= htmlspecialchars($stat['value']) ?></div>
                            <div class="stat-label"><?= htmlspecialchars($stat['label']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $view->render('partials/footer'); ?>
