<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Enums\RequestType;


/**
 * Główny kontroler aplikacji obsługujący widoki publiczne.
 */
class MainController extends Controller
{
    /**
     * Wyświetla stronę główną.
     *
     * @return void
     */
    public function home(): void
    {
        $products = $this->api->request(RequestType::GET, '/produkty');

        if ($products === null || isset($products['error'])) {
            $products = [];
        }


        $this->view->renderPage('home', [
            'title' => 'Strona główna',

            'user' => $this->user,
            'cart' => $this->cart,

            'products' => $products,

            'scripts' => [
                'static/js/search-bar.js'
            ]
        ]);
    }

    /**
     * Zwraca lekką odpowiedz JSON z podpowiedziami do globalnej wyszukiwarki.
     */
    public function suggestions(): void
    {
        // Ustawiamy typ odpowiedzi, aby frontend wiedzial, ze dostanie JSON.
        header('Content-Type: application/json; charset=UTF-8');

        // Pobieramy fraze z parametru q i usuwamy spacje z poczatku oraz konca.
        $query = trim((string) ($_GET['q'] ?? ''));

        // Pomijamy bardzo krótkie frazy, zeby nie generowac zbednych zapytan.
        if ($query === '' || mb_strlen($query) < 2) {
            echo json_encode(['suggestions' => []], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Korzystamy z istniejacego endpointu produktow, wiec suggestions zawsze
        // opieraja sie na tych samych danych co lista wynikow.
        $products = $this->api->request(
            RequestType::GET,
            '/produkty?' . http_build_query(['search' => $query])
        );

        // Jesli backend zwroci blad, oddajemy pusta liste podpowiedzi.
        if ($products === null || isset($products['error']) || isset($products['success'])) {
            echo json_encode(['suggestions' => []], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Front potrzebuje tylko kilku pol do renderu dropdownu.
        $suggestions = array_map(function (array $product): array {
            // Odczytujemy nazwe produktu.
            $name = (string) ($product['name'] ?? '');
            // Odczytujemy nazwe pliku obrazka.
            $image = (string) ($product['image'] ?? '');
            // Odczytujemy identyfikator produktu.
            $productId = (int) ($product['id'] ?? 0);

            return [
                'id' => $productId,
                'name' => $name,
                // Formatujemy cene tak, by od razu nadawala sie do wyswietlenia.
                'price' => isset($product['price']) ? number_format((float) $product['price'], 2, ',', ' ') . ' zl' : null,
                // Budujemy pelny adres URL do obrazka produktu.
                'image' => $image !== '' ? $this->root . '/static/images/products/' . rawurlencode($image) . '.webp' : null,
                // Budujemy pelny adres URL do strony produktu.
                'url' => $this->root . '/products/' . $productId,
            ];
        }, array_slice($products, 0, 6));

        // Oprocz samych sugestii zwracamy tez gotowy link do pelnych wynikow.
        echo json_encode([
            'suggestions' => $suggestions,
            'query' => $query,
            'resultsUrl' => $this->root . '/products?' . http_build_query(['search' => $query]),
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Wyświetla listę produktów z uwzględnieniem filtrów.
     *
     * @return void
     */
    public function products(): void
    {
        $favoriteIds = [];
        if ($this->user) {
            $favorites = $this->api->request(RequestType::GET, '/favorites?user_id=' . $this->user['id']);
            $favoriteIds = array_column($favorites ?? [], 'id');
            $_SESSION['favorite_ids'] = $favoriteIds;
        }

        $args = http_build_query($_GET);

        $products = $this->api->request(RequestType::GET, "/produkty?$args");
        $categories = $this->api->request(RequestType::GET, '/kategorie');

        if ($products === null || isset($products['error'])) {
            $products = [];
        }

        if ($categories === null || isset($categories['error'])) {
            $categories = [];
        }

        $this->view->renderPage('products', [
            'title' => 'Produkty',

            'user' => $this->user,
            'cart' => $this->cart,

            'products' => $products,
            'categories' => $categories,

            'scripts' => [
                'static/js/filters.js',
            ],
            'styles' => [
                'static/css/filters.css'
            ],
            'favorite_ids' => $favoriteIds
        ]);
    }

    /**
     * Wyświetla stronę pojedynczego produktu.
     *
     * @param array $matches Tablica dopasowań z routera (zawiera 'id')
     * @return void
     */
    public function product(array $matches): void
    {

        $product = $this->api->request(RequestType::GET, '/produkty/' . $matches['id'])[0];
        $reviews = $this->api->request(RequestType::GET, "/opinie/" . $matches['id']);

        $product['description'] = $this->markdown->transform($product['description'] ?? 'Brak opisu');

        $this->view->renderPage('single-product', [
            'title' => 'Produkt',

            'user' => $this->user,
            'cart' => $this->cart,

            'product' => $product,
            'reviews' => $reviews,

            'styles' => [
                "static/css/product-page.css"
            ]
        ]);
    }

    /**
     * Obsługuje dodawanie opinii o produkcie.
     *
     * @return void
     */
    public function addReview(): void
    {
        if (empty($this->user)) {
            $this->setToast('Zaloguj się, aby dodać opinię.', 'danger');
            $this->redirect->to('/login');
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        if ($productId <= 0 || $rating < 1 || $rating > 5) {
            $this->setToast('Nieprawidłowe dane opinii.', 'danger');
            $this->redirect->to('/');
        }

        $response = $this->api->request(RequestType::POST, '/opinie', [
            'product_id' => $productId,
            'user_id' => $this->user['id'],
            'rating' => $rating,
            'comment' => $comment
        ]);
        if (isset($response['success']) && $response['success']) {
            $this->setToast('Dziękujemy za wystawienie opinii!', 'success');
        } else {
            $error = $response['error'] ?? 'Wystąpił błąd podczas dodawania opinii.';
            $this->setToast($error, 'danger');
        }

        $this->redirect->to("/products/$productId");
    }

    /**
     * Obsługuje edycję opinii o produkcie.
     *
     * @return void
     */
    public function editReview(): void
    {
        if (empty($this->user)) {
            $this->setToast('Zaloguj się, aby edytować opinię.', 'danger');
            $this->redirect->to('/login');
        }

        $reviewId = (int)($_POST['review_id'] ?? 0);
        $productId = (int)($_POST['product_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($reviewId <= 0 || $productId <= 0 || $rating < 1 || $rating > 5) {
            $this->setToast('Nieprawidłowe dane opinii.', 'danger');
            $this->redirect->to('/');
        }

        $response = $this->api->request(RequestType::PUT, "/opinie/$reviewId", [
            'user_id' => $this->user['id'],
            'rating' => $rating,
            'comment' => $comment
        ]);

        if (isset($response['success']) && $response['success']) {
            $this->setToast('Twoja opinia została zaktualizowana!', 'success');
        } else {
            $error = $response['error'] ?? 'Wystąpił błąd podczas edycji opinii.';
            $this->setToast($error, 'danger');
        }

        $this->redirect->to("/products/$productId");
    }
    public function toggleFavorite(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? null;

        $result = $this->api->request(RequestType::POST, '/favorites', [
            'user_id' => $this->user['id'],
            'product_id' => $productId
        ]);
        echo json_encode($result);
    }
    public function favorites(): void
    {
        if(!$this->user){
            $this->redirect->to('/login');
        }
        $favoriteIds = [];
        $favorites = [];
        if ($this->user) {
            $favorites = $this->api->request(RequestType::GET, '/favorites?user_id=' . $this->user['id']);
            $favoriteIds = array_column($favorites ?? [], 'id');
            $_SESSION['favorite_ids'] = $favoriteIds;
        }

        $this->view->renderPage('favorites', [
            'title' => 'Ulubione',
            'user' => $this->user,
            'favorites' => $favorites,
            'favorite_ids' => $favoriteIds
        ]);

    }
}
