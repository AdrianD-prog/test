<?php
declare(strict_types=1);

namespace App\Controllers\Account;

use App\Controllers\Controller;
use App\Enums\RequestType;
use DateTime;

/**
 * Kontroler obslugujacy zamowienia.
 */
class OrderController extends Controller
{
    private const DEFAULT_DELIVERY_METHODS = [
        [
            'id' => 'courier_standard',
            'name' => 'Kurier standard',
            'description' => 'Dostawa w 2-4 dni robocze',
            'price' => 14.99,
            'eta' => '2-4 dni',
        ],
        [
            'id' => 'parcel_locker',
            'name' => 'Paczkomat',
            'description' => 'Odbior o dowolnej porze',
            'price' => 9.99,
            'eta' => '1-2 dni',
        ],
        [
            'id' => 'courier_express',
            'name' => 'Kurier express',
            'description' => 'Priorytetowa realizacja zamowienia',
            'price' => 24.99,
            'eta' => '24h',
        ],
    ];

    private const PAYMENT_METHODS = [
        'card' => 'Karta platnicza',
        'blik' => 'BLIK',
        'bank_transfer' => 'Szybki przelew',
    ];


    public function index(): void
    {
        if ($this->user === []) {
            $this->redirect->to('/login');
        }

        $ordersResponse = $this->api->request(RequestType::GET, '/orders/' . $this->user['id']);
        $orders = [];
        if (is_array($ordersResponse) && isset($ordersResponse['orders']) && is_array($ordersResponse['orders'])) {
            $orders = $ordersResponse['orders'];
        }

        $this->view->renderPage('orders/index', [
            'title' => 'Moje zamowienia',
            'orders' => $orders,
            'user' => $this->user,
            'scripts' => [],
            'styles' => [
                'static/css/orders.css',
            ],
        ]);
    }

    public function checkout(): void
    {

        $this->view->renderPage('orders/checkout', [
            'title' => 'Finalizacja zamowienia',
            'user' => $this->user,
            'cart' => $this->cart,
            'delivery_methods' => $this->getDeliveryMethods(),
            'scripts' => [],
            'styles' => [
                'static/css/orders/checkout.css',
            ],
        ]);
    }

    public function processCheckout(): void
    {

        if (empty($this->cart['items'])) {
            $this->setToast('Koszyk jest pusty. Dodaj produkty przed finalizacja zamowienia.', 'error');
            $this->redirect->to('/cart');
        }

        $_SESSION['error'] = [];
        $_SESSION['previous_data'] = [
            'delivery_method' => trim((string)($_POST['delivery_method'] ?? '')),
            'payment_method' => trim((string)($_POST['payment_method'] ?? '')),
            'card_number' => trim((string)($_POST['card_number'] ?? '')),
            'expiry' => trim((string)($_POST['expiry'] ?? '')),
            'cvv' => trim((string)($_POST['cvv'] ?? '')),
            'card_name' => trim((string)($_POST['card_name'] ?? '')),
            'blik_code' => trim((string)($_POST['blik_code'] ?? '')),
            'bank_name' => trim((string)($_POST['bank_name'] ?? '')),
        ];

        $deliveryMethods = $this->getDeliveryMethods();
        $deliveryMap = [];
        foreach ($deliveryMethods as $method) {
            $deliveryMap[(string)$method['id']] = $method;
        }

        $deliveryId = $_SESSION['previous_data']['delivery_method'];
        $paymentMethod = $_SESSION['previous_data']['payment_method'];

        if ($deliveryId === '' || !isset($deliveryMap[$deliveryId])) {
            $_SESSION['error']['delivery_method'] = 'Wybierz metode dostawy.';
        }

        if ($paymentMethod === '' || !isset(self::PAYMENT_METHODS[$paymentMethod])) {
            $_SESSION['error']['payment_method'] = 'Wybierz metode platnosci.';
        }

        if (
            empty(trim((string)($this->user['address'] ?? ''))) ||
            empty(trim((string)($this->user['city'] ?? ''))) ||
            empty(trim((string)($this->user['country'] ?? '')))
        ) {
            $_SESSION['error']['address'] = 'Uzupelnij adres dostawy w ustawieniach konta.';
        }

        $this->validatePaymentDetails($paymentMethod);

        if (!empty($_SESSION['error'])) {
            $this->redirect->to('/orders/checkout');
        }

        $result = $this->api->request(RequestType::POST, '/orders', [
            'user_id' => (int)$this->user['id'],
        ]);

        if (!is_array($result) || isset($result['error'])) {
            $_SESSION['error']['general'] = $result['error'] ?? 'Nie udalo sie utworzyc zamowienia.';
            $this->redirect->to('/orders/checkout');
        }

        unset($_SESSION['error'], $_SESSION['previous_data']);

        $deliveryName = $deliveryMap[$deliveryId]['name'] ?? 'wybrana dostawa';
        $paymentName = self::PAYMENT_METHODS[$paymentMethod] ?? 'wybrana platnosc';
        $orderId = $result['order_id'] ?? null;

        $message = 'Zamowienie zostalo zlozone';
        if ($orderId !== null) {
            $message .= ' #' . $orderId;
        }
        $message .= '. Dostawa: ' . $deliveryName . '. Platnosc: ' . $paymentName . '.';

        $this->setToast($message, 'success');
        $this->redirect->to('/orders');
    }

    private function getDeliveryMethods(): array
    {
        $response = $this->api->request(RequestType::GET, '/delivers');
        if (!is_array($response) || empty($response['delivers']) || !is_array($response['delivers'])) {
            return self::DEFAULT_DELIVERY_METHODS;
        }

        $methods = [];
        foreach ($response['delivers'] as $deliver) {
            $id = trim((string)($deliver['id'] ?? ''));
            $name = trim((string)($deliver['name'] ?? ''));
            if ($id === '' || $name === '') {
                continue;
            }

            $methods[] = [
                'id' => $id,
                'name' => $name,
                'description' => (string)($deliver['description'] ?? 'Dostawa kurierem'),
                'price' => isset($deliver['price']) ? (float)$deliver['price'] : 0.0,
                'eta' => (string)($deliver['eta'] ?? '2-4 dni'),
            ];
        }

        return $methods !== [] ? $methods : self::DEFAULT_DELIVERY_METHODS;
    }

    private function validatePaymentDetails(string $paymentMethod): void
    {
        if ($paymentMethod === 'card') {
            $cardNumber = preg_replace('/\D+/', '', (string)($_POST['card_number'] ?? ''));
            $expiry = trim((string)($_POST['expiry'] ?? ''));
            $cvv = preg_replace('/\D+/', '', (string)($_POST['cvv'] ?? ''));
            $cardName = trim((string)($_POST['card_name'] ?? ''));

            if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
                $_SESSION['error']['card_number'] = 'Podaj poprawny numer karty.';
            }

            if (!$this->isValidExpiry($expiry)) {
                $_SESSION['error']['expiry'] = 'Podaj poprawna date waznosci w formacie MM/RR.';
            }

            if (!preg_match('/^\d{3,4}$/', $cvv)) {
                $_SESSION['error']['cvv'] = 'Kod CVV musi miec 3 lub 4 cyfry.';
            }

            if ($cardName === '' || mb_strlen($cardName) < 3) {
                $_SESSION['error']['card_name'] = 'Podaj imie i nazwisko z karty.';
            }
        }

        if ($paymentMethod === 'blik') {
            $blikCode = preg_replace('/\D+/', '', (string)($_POST['blik_code'] ?? ''));
            if (!preg_match('/^\d{6}$/', $blikCode)) {
                $_SESSION['error']['blik_code'] = 'Kod BLIK musi miec 6 cyfr.';
            }
        }

        if ($paymentMethod === 'bank_transfer') {
            $bankName = trim((string)($_POST['bank_name'] ?? ''));
            if ($bankName === '') {
                $_SESSION['error']['bank_name'] = 'Wybierz bank do szybkiego przelewu.';
            }
        }
    }

    private function isValidExpiry(string $expiry): bool
    {
        if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $expiry, $matches)) {
            return false;
        }

        $month = (int)$matches[1];
        $year = 2000 + (int)$matches[2];
        $expiryDate = DateTime::createFromFormat('Y-n-j H:i:s', sprintf('%d-%d-1 23:59:59', $year, $month));
        if (!$expiryDate instanceof DateTime) {
            return false;
        }

        $expiryDate->modify('last day of this month');
        return $expiryDate >= new DateTime('now');
    }
}
