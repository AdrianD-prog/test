<?php

declare(strict_types=1);

namespace App\Controllers\Account;

use App\Controllers\Controller;
use App\Enums\RequestType;

/**
 * Kontroler obsługujący koszyk zakupowy użytkownika.
 */
class CartController extends Controller
{
    /**
     * Zapisuje koszyk gościa do sesji
     */
    private function saveGuestCart(array $cart): void
    {
        $_SESSION['guest_cart'] = $cart;
    }

    /**
     * Wyświetla zawartość koszyka użytkownika.
     *
     * @return void
     */
    public function cart(): void
    {
        if ($this->user !== [] && isset($_SESSION['guest_cart']) && !empty($_SESSION['guest_cart']['items']))
        {
            $this->mergeGuestCart();
            $this->redirect->to("/cart");
        }

        // Sformatowanie danych do 2 miejsc po przecinku
        if (isset($this->cart['total']))
        {
            $this->cart['total'] = round((float)$this->cart['total'], 2);
        }

        if (!empty($this->cart['items']))
        {
            foreach ($this->cart['items'] as &$item)
            {
                $item['price'] = round((float)$item['price'] ?? 0, 2);
                $item['total_price'] = round((float)$item['total_price'] ?? 0, 2);
            }
        }

        $this->view->renderPage('account/cart', [
            'title' => 'Twój koszyk',

            'user' => $this->user,
            'cart' => $this->cart,

            'scripts' => [
                'static/js/cart.js'
            ],
        ]);
    }

    /**
     * Dodaje produkt do koszyka.
     */
    public function add(): void
    {
        $productId = (int) ($_POST['product_id'] ?? -1);
        $quantity = (int) ($_POST['quantity'] ?? 1);

        if ($productId === -1) {
            $this->setToast('Nieprawidłowy produkt', 'error');
            $this->redirect->to('/');
        }
        if ($quantity < 1) {
            $this->setToast('Nieprawidłowa ilość', 'error');
            $this->redirect->to('/');
        }
        
        // ZALOGOWANY UŻYTKOWNIK
        if ($this->user !== [] || isset($_SESSION["JWT_TOKEN"])) {
            $result = $this->api->request(RequestType::POST, '/cart', [
                'user_id' => $this->user["id"],
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
            
            if ($result && isset($result['success']) && $result['success'] === true) {
                $this->setToast('Produkt dodany do koszyka!', 'success');
            } else {
                $this->setToast($result['error'] ?? 'Błąd dodawania do koszyka', 'error');
            }
        } 
        // NIEZALOGOWANY UŻYTKOWNIK
        else {
            // Pobierz dane produktu (z wygenerowanym SKU jeśli brak)
            $product = $this->getProductData($productId);
            
            if (!$product) {
                $this->setToast('Nie można pobrać danych produktu', 'error');
                $this->redirect->back();
            }
            
            $this->initGuestCart();
            $cart = $_SESSION['guest_cart'];
            
            // Sprawdź czy produkt już jest w koszyku
            $found = false;
            foreach ($cart['items'] as &$item) {
                if ($item['product_id'] === $productId) {
                    $item['quantity'] += $quantity;
                    $item['total_price'] = $item['price'] * $item['quantity'];
                    $found = true;
                    break;
                }
            }
            
            // Jeśli nie ma, dodaj nowy z PEŁNYMI danymi
            if (!$found) {
                $cart['items'][] = [
                    'id' => $product['id'] ?? $productId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'name' => $product['name'] ?? 'Produkt #' . $productId,
                    'sku' => $product['sku'], // Teraz na pewno istnieje
                    'price' => (float) ($product['price'] ?? 0),
                    'total_price' => ((float) ($product['price'] ?? 0)) * $quantity,
                    'thumbnail' => $product['thumbnail'] ?? null,
                    'slug' => $product['slug'] ?? 'product-' . $productId,
                    'weight' => (float) ($product['weight'] ?? 0),
                    'stock' => (int) ($product['stock'] ?? 0)
                ];
            }
            
            // Przelicz sumy całkowite
            $cart['total'] = array_sum(array_column($cart['items'], 'total_price'));
            $cart['count'] = array_sum(array_column($cart['items'], 'quantity'));
            
            $this->saveGuestCart($cart);
            $this->setToast('Produkt dodany do koszyka!', 'success');
        }
        
        $this->redirect->back();
    }

    /**
    * Pobiera pełne dane produktu z API
    */
    private function getProductData(int $productId): ?array
    {
        $response = $this->api->request(RequestType::GET, "/produkty/$productId");
        
        // API zwraca tablicę z jednym produktem, wyciągamy go
        if (is_array($response) && isset($response[0])) {
            $product = $response[0];
        } else {
            $product = $response;
        }
        
        // Jeśli nie ma danych, zwróć null
        if (!$product || isset($product['error'])) {
            return null;
        }
        
        // DODAJEMY sztuczne SKU (bo API go nie zwraca)
        $product['sku'] = 'PROD-' . str_pad((string)$productId, 6, '0', STR_PAD_LEFT);
        
        // DODAJEMY slug jeśli go nie ma
        if (empty($product['slug'])) {
            $product['slug'] = 'product-' . $productId;
        }
        
        return $product;
    }

    /**
     * Aktualizuje ilość produktu w koszyku.
     */
    public function update(): void
    {
        // Dla niezalogowanego użytkownika
        if ($this->user == []) {
            $this->updateGuestCart();
            return;
        }
        
        // Dla zalogowanego użytkownika
        if (!isset($_SESSION['JWT_TOKEN'])) {
            $this->redirect->to('/login');
        }

        $itemId = (int) ($_POST['item_id'] ?? 0);
        $currentValue = (int) ($_POST['current_quantity'] ?? 0);
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'decrease':
                $currentValue--;
                break;
            case 'increase':
                $currentValue++;
                break;
            default:
                $this->redirect->to('/cart');
        }

        if ($currentValue > 0) {
            $result = $this->api->request(RequestType::PUT, "/cart/$itemId", [
                'user_id' => $this->user["id"],
                'quantity' => $currentValue
            ]);

            if ($result && isset($result['success'])) {
                $this->setToast('Koszyk zaktualizowany', 'success');
            } else {
                $this->setToast($result['error'] ?? 'Błąd aktualizacji', 'error');
            }
        } else {
            $this->remove();
            return;
        }
        
        $this->redirect->to('/cart');
    }

    /**
     * Aktualizuje koszyk gościa (niezalogowanego)
     */
    private function updateGuestCart(): void
    {
        $this->initGuestCart();
        $cart = $_SESSION['guest_cart'];
        
        $productId = (int) ($_POST['product_id'] ?? 0);
        $currentValue = (int) ($_POST['current_quantity'] ?? 0);
        $action = $_POST['action'] ?? '';
        
        if (!$productId) {
            $this->setToast('Nieprawidłowy produkt', 'error');
            $this->redirect->to('/cart');
        }
        
        switch ($action) {
            case 'decrease':
                $currentValue--;
                break;
            case 'increase':
                $currentValue++;
                break;
            default:
                $this->redirect->to('/cart');
        }
        
        // Znajdź i zaktualizuj produkt
        foreach ($cart['items'] as &$item) {
            if ($item['product_id'] === $productId) {
                if ($currentValue > 0) {
                    $item['quantity'] = $currentValue;
                    $item['total_price'] = $item['price'] * $currentValue;
                } else {
                    // Usuń produkt jeśli ilość = 0
                    $cart['items'] = array_values(array_filter($cart['items'], function($i) use ($productId) {
                        return $i['product_id'] !== $productId;
                    }));
                }
                break;
            }
        }
        
        // Przelicz sumy całkowite
        $cart['total'] = array_sum(array_column($cart['items'], 'total_price'));
        $cart['count'] = array_sum(array_column($cart['items'], 'quantity'));
        
        $this->saveGuestCart($cart);
        $this->setToast('Koszyk zaktualizowany', 'success');
        $this->redirect->to('/cart');
    }

    /**
     * Usuwa produkt z koszyka.
     */
    public function remove(): void
    {

        // Dla niezalogowanego
        if ($this->user === []) {
            $this->initGuestCart();
            $cart = $_SESSION['guest_cart'];
            $productId = (int) ($_POST['product_id'] ?? 0);
            
            if (!$productId) {
                $this->setToast('Nieprawidłowy produkt', 'error');
                $this->redirect->to('/cart');
            }
            
            // Usuń produkt
            $cart['items'] = array_values(array_filter($cart['items'], function($item) use ($productId) {
                return $item['product_id'] !== $productId;
            }));
            
            // Przelicz sumy
            $cart['total'] = array_sum(array_column($cart['items'], 'total_price'));
            $cart['count'] = array_sum(array_column($cart['items'], 'quantity'));
            
            $this->saveGuestCart($cart);
            $this->setToast('Produkt usunięty z koszyka', 'success');
            $this->redirect->to('/cart');
        }
        
        // Dla zalogowanego (istniejący kod)
        if (!isset($_SESSION['JWT_TOKEN'])) {
            $this->redirect->to('/login');
        }

        $itemId = (int) ($_POST['item_id'] ?? -1);

        if ($itemId === -1) {
            $this->setToast('Nieprawidłowy produkt', 'error');
            $this->redirect->to('/cart');
        }

        $result = $this->api->request(RequestType::DELETE, "/cart/$itemId", [
            'user_id' => $this->user["id"]
        ]);

        if ($result && isset($result['success'])) {
            $this->setToast('Produkt usunięty z koszyka', 'success');
        } else {
            $this->setToast($result['error'] ?? 'Błąd usuwania', 'error');
        }

        $this->redirect->to('/cart');
    }

    
    /**
     * Prywatna metoda scalająca koszyk gościa z kontem użytkownika
     */
    private function mergeGuestCart(): void
    {
        if (!isset($_SESSION['JWT_TOKEN'])) {
            return;
        }
        
        $userId = $this->user['id'];

        // Sprawdź czy istnieje koszyk gościa
        if (!isset($_SESSION['guest_cart']) || empty($_SESSION['guest_cart']['items'])) {
            return;
        }
        
        $guestCart = $_SESSION['guest_cart'];
        
        // Pobierz obecny koszyk użytkownika
        $currentCart = $this->api->request(RequestType::GET, "/cart/$userId");
        $currentItems = [];
        
        if ($currentCart && isset($currentCart['items'])) {
            foreach ($currentCart['items'] as $item) {
                $currentItems[$item['product_id']] = [
                    'id' => $item['id'],
                    'quantity' => $item['quantity']
                ];
            }
        }
        
        // Przenieś każdy produkt z koszyka gościa
        foreach ($guestCart['items'] as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];
            
            // Jeśli produkt już istnieje w koszyku, dodaj ilość
            if (isset($currentItems[$productId])) {
                $newQuantity = $currentItems[$productId]['quantity'] + $quantity;
                $this->api->request(RequestType::PUT, "/cart/{$currentItems[$productId]['id']}", [
                    'user_id' => $userId,
                    'quantity' => $newQuantity
                ]);
            } else {
                // Dodaj nowy produkt
                $this->api->request(RequestType::POST, '/cart', [
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity
                ]);
            }
        }
        
        // Wyczyść koszyk gościa
        unset($_SESSION['guest_cart']);

        $this->setToast('Koszyk gościa został przeniesiony do Twojego konta!', 'success');
    }
}