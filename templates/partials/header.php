<?php
    /**
     * @var string $root
     */

    $user = $params['user'] ?? [];
    $cart = $params['cart'] ?? [];
?>

<header class="bg-dark border-bottom sticky-top">
    <div class="container-fluid py-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="<?= htmlspecialchars($root) ?>/" class="order-1 flex-shrink-0 text-decoration-none">
                <div class="bg-dark text-white px-3 py-2 rounded">
                    <span class="fs-4 fw-bold">Amazonix</span>
                </div>
            </a>

            <div class="order-last order-md-2 flex-grow-1">
                <div class="position-relative w-100">
                    <?php // To jest glowny formularz wyszukiwania widoczny w headerze. ?>
                    <form
                            <?php // search-form daje punkt odniesienia dla absolutnie pozycjonowanego dropdownu. ?>
                            class="d-flex search-form"
                            role="search"
                            action="<?= htmlspecialchars($root) ?>/products"
                            method="GET"
                            accept-charset="UTF-8"
                            data-search-form
                            data-suggestions-endpoint="<?= htmlspecialchars($root) ?>/products/suggestions"
                    >
                        <input
                                class="form-control me-2 shadow-none border"
                                type="search"
                                placeholder="Szukaj produktów..."
                                aria-label="Szukaj"
                                name="search"
                                value="<?= htmlspecialchars((string) ($_GET['search'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                autocomplete="off"
                                spellcheck="false"
                                aria-autocomplete="list"
                                aria-expanded="false"
                                aria-controls="search-suggestions-list"
                                data-search-input
                        >
                        <button type="submit" class="btn btn-primary">
                            <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                            >
                                <path d="m21 21-4.34-4.34"/><circle cx="11" cy="11" r="8"/>
                            </svg>
                        </button>
                        <?php // Dropdown jest wypelniany dynamicznie po odpowiedzi z endpointu suggestions. ?>
                        <div
                                class="search-suggestions shadow-lg d-none"
                                id="search-suggestions-list"
                                role="listbox"
                                aria-label="Podpowiedzi wyszukiwania"
                                data-search-suggestions
                        ></div>
                    </form>
                </div>
            </div>

            <div class="order-2 order-md-3 flex-shrink-0 ms-auto">
                <div class="d-flex align-items-center gap-4">
                    <a
                            href="<?= htmlspecialchars($root . (isset($user) && $user != [] ? "/account" : "/login")) ?>"
                            class="btn btn-link text-white text-decoration-none p-0 d-flex flex-column align-items-center"
                    >
                        <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                        >
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span style="font-size: 11px">
                            <?= htmlspecialchars(isset($user['first_name']) ? explode(' ', $user['first_name'])[0] : "Zaloguj się") ?>
                        </span>
                    </a>

                    <a href='<?= htmlspecialchars($root) ?>/favorites' class="btn btn-link text-white text-decoration-none p-0 d-flex flex-column align-items-center">
                        <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                        >
                            <path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/>
                        </svg>
                        <span style="font-size: 11px">Ulubione</span>
                    </a>

                    <a
                            href="<?= htmlspecialchars($root) ?>/cart"
                            class="btn btn-link text-white text-decoration-none p-0 d-flex flex-column align-items-center position-relative"
                    >
                        <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                        >
                            <circle cx="8" cy="21" r="1"/>
                            <circle cx="19" cy="21" r="1"/>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                        </svg>
                        <span style="font-size: 11px">Koszyk</span>
                        <?php if (isset($cart) && ($cart["count"] ?? 0) > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $cart["count"] ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-body-secondary">
        <div class="container-fluid">
            <nav class="d-flex gap-4 py-2 overflow-auto flex-nowrap">
                <a href="<?= htmlspecialchars($root)?>/products?inStock=on&category=1&priceMin=0&priceMax=20001&rating=0" class="btn btn-link text-dark text-decoration-none p-0 small text-nowrap">Słodycze</a>
                <a href="<?= htmlspecialchars($root)?>/products?inStock=on&category=2&priceMin=0&priceMax=20001&rating=0" class="btn btn-link text-dark text-decoration-none p-0 small text-nowrap">Bezpieczeństwo</a>
                <a href="<?= htmlspecialchars($root)?>/products?inStock=on&category=3&priceMin=0&priceMax=20001&rating=0" class="btn btn-link text-dark text-decoration-none p-0 small text-nowrap">Motoryzacja</a>
                <a href="<?= htmlspecialchars($root)?>/products?inStock=on&category=4&priceMin=0&priceMax=20001&rating=0" class="btn btn-link text-dark text-decoration-none p-0 small text-nowrap">Jedzenie</a>
                <a href="<?= htmlspecialchars($root)?>/products?inStock=on&category=5&priceMin=0&priceMax=20001&rating=0" class="btn btn-link text-dark text-decoration-none p-0 small text-nowrap">Elektronika</a>
                <a href="<?= htmlspecialchars($root)?>/products?inStock=on&category=6&priceMin=0&priceMax=20001&rating=0" class="btn btn-link text-dark text-decoration-none p-0 small text-nowrap">Odzież</a>
                <a href="<?= htmlspecialchars($root)?>/products?inStock=on&category=7&priceMin=0&priceMax=20001&rating=0" class="btn btn-link text-dark text-decoration-none p-0 small text-nowrap">Książki</a>
                <a href="<?= htmlspecialchars($root)?>/products?inStock=on&category=8&priceMin=0&priceMax=20001&rating=0" class="btn btn-link text-dark text-decoration-none p-0 small text-nowrap">AGD</a>
                <a href="<?= htmlspecialchars($root)?>/products?inStock=on&category=9&priceMin=0&priceMax=20001&rating=0" class="btn btn-link text-dark text-decoration-none p-0 small text-nowrap">Gaming</a>
                <a href="<?= htmlspecialchars($root)?>/products?inStock=on&category=10&priceMin=0&priceMax=20001&rating=0" class="btn btn-link text-dark text-decoration-none p-0 small text-nowrap">Sport</a>
            </nav>
        </div>
    </div>
</header>
