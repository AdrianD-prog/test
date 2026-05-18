<?php

    declare(strict_types=1);

    namespace App\Controllers;

    use App\Enums\RequestType;
    use App\Helpers\JWT;
    use App\Helpers\Mail;
    use App\Helpers\Redirect;
    use App\View\View;
    use App\Services\ApiService;
    use Michelf\MarkdownExtra;
    use Resend;

    /**
 * Bazowa klasa kontrolera.
 */
class Controller
{
    /** @var View Obiekt widoku */
    protected View $view;
    /** @var ApiService Serwis API */
    protected ApiService $api;
    /** @var JWT Pomocnik JWT */
    protected JWT $jwt;
    /** @var Redirect Pomocnik przekierowań */
    protected Redirect $redirect;
    protected MarkdownExtra $markdown;
    protected Resend\Client $resend;
    protected string $root;

    protected array $user = [];
    protected array $cart = [];

    /**
     * @param View $view
     * @param ApiService $api
     * @param JWT $jwt
     * @param Redirect $redirect
     * @param MarkdownExtra $markdown
     */
    public function __construct(View $view, ApiService $api, JWT $jwt, Redirect $redirect, MarkdownExtra $markdown)
    {
        $this->view = $view;
        $this->api = $api;
        $this->jwt = $jwt;
        $this->redirect = $redirect;
        $this->markdown = $markdown;
        $this->resend = Resend::client('re_h3L7P4P4_NnZrKsnUUoAs5DzoP6yeRaSa');

        $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http';
        $this->root = "$protocol://$_SERVER[SERVER_NAME]$_ENV[ROOT_DIR]";


        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->jwt->decode($_SESSION['JWT_TOKEN'] ?? '', $this->user);
        if(isset($this->user['id'])) {
            $userId = $this->user["id"];
            $this->getCartData($userId);
        } else {
            $this->cart = $_SESSION['guest_cart'] ?? [];
        }
    }

    /**
     * Inicjalizuje koszyk w sesji dla niezalogowanego użytkownika
     */
    protected function initGuestCart(): void
    {
        if (!isset($_SESSION['guest_cart'])) {
            $_SESSION['guest_cart'] = [
                'items' => [],
                'total' => 0,
                'count' => 0
            ];
        }
    }

    /**
     * Pobiera koszyk (dla zalogowanego z API, dla gościa z sesji)
     */
    protected function getCartData(int $userId): void
    {
        if ($userId !== 0) {
            $this->cart = $this->api->request(RequestType::GET, "/cart/$userId");

            if (!$this->cart) {
                $this->cart = ['items' => [], 'total' => 0, 'count' => 0];

            }
            if (isset($this->cart['items']) && is_array($this->cart['items'])) {
                foreach ($this->cart['items'] as &$item) {
                    if (!isset($item['total_price'])) {
                        $item['total_price'] = (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 0);
                    }
                    // Upewnij się, że product_id istnieje (może być jako 'product_id' lub 'id')
                    if (!isset($item['product_id']) && isset($item['id'])) {
                        $item['product_id'] = $item['id'];
                    }
                }
            }

            // Oblicz total jeśli brak
            if (!isset($this->cart['total']) && isset($this->cart['items'])) {
                $this->cart['total'] = array_sum(array_column($this->cart['items'], 'total_price'));
            }

            // Oblicz count jeśli brak
            if (!isset($this->cart['count']) && isset($this->cart['items'])) {
                $this->cart['count'] = array_sum(array_column($this->cart['items'], 'quantity'));
            }

        } else {
            $this->initGuestCart();
        }
    }

    /**
     * Ustawia toast do wyświetlenia po przekierowaniu.
     *
     * @param string $message
     * @param string $type
     * @return void
     */
    protected function setToast(string $message, string $type = 'info'): void
    {
        $_SESSION['toast'] = [
            'message' => $message,
            'type' => $type,
        ];
    }

    protected function send(
        array $reciver,
        string $subject,
        string $body,
        array $attachements = []
    ): void
    {
        $this->resend->emails->send([
            'from' => 'Amazonix <amazonix@amazonix.pl>',
            'to' => $reciver,
            'subject' => $subject,
            'html' => $body,
            'attachments' => $attachements
        ]);
    }
}
