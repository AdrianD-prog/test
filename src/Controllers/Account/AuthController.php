<?php

    declare(strict_types=1);

    namespace App\Controllers\Account;

    use App\Controllers\Controller;
    use App\Enums\RequestType;
    use App\Helpers\Validation;


    /**
 * Kontroler obsługujący autentykację użytkowników (logowanie, rejestracja, wylogowanie).
 */
class AuthController extends Controller
{
    /**
     * Wyświetla formularz logowania.
     *
     * @return void
     */
    public function showLoginForm(): void
    {
        if (isset($_SESSION['JWT_TOKEN'])) {
            $this->redirect->to('/');
        }

        $this->view->renderPage('auth/login', [
            'title' => 'Logowanie - Amazonix',

            'scripts' => [
                'static/js/login.js'
            ],
            'styles' => [
                'static/css/auth.css'
            ]
        ]);
    }

    /**
     * Wyświetla formularz rejestracji.
     *
     * @return void
     */
    public function showRegisterForm(): void
    {
        if (isset($_SESSION['JWT_TOKEN'])) {
            $this->redirect->to('/');
        }

        $this->view->renderPage('auth/register', [
            'title' => 'Rejestracja - Amazonix',

            'scripts' => [
                'static/js/register.js'
            ],
            'styles' => [
                'static/css/auth.css'
            ]
        ]);
    }

    private function performLogin(string $email, string $password): array
    {
        $result = $this->api->request(RequestType::POST, '/auth/login',
        [
            'email' => $email,
            'password' => $password
        ]);

        if ($result && isset($result['success']) && $result['success'] === true) {
            try {
                $_SESSION['JWT_TOKEN'] = $result['jwt_token'];
                if (isset($_SESSION['guest_cart']) && !empty($_SESSION['guest_cart']['items']))
                {
                    $this->mergeGuestCart();
                }
            } catch (\InvalidArgumentException $e) {
                $this->setToast($result['error'] ?? 'Błąd logowania', 'error');
            }
        }

        return $result;
    }

    private function validateLogin(array $data): bool
    {
        $validationError = false;
        if (empty($data['email'])) {
            $_SESSION['error']['email'] = 'Pole [Email] jest wymagany.';
            $validationError = true;
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error']['email'] = 'Nieprawidłowy format email.';
            $validationError = true;
        }
        if (empty($data['password'])) {
            $_SESSION['error']['password'] = 'Pole [Hasło] jest wymagany.';
            $validationError = true;
        }
        if (strlen($data['password']) < 8) {
            $_SESSION['error']['password'] = 'Hasło musi mieć co najmniej 8 znaków.';
            $validationError = true;
        }

        if($validationError)
        {
            $this->rememberPreviousData($data);
        }
        return $validationError;
    }

    /**
     * Obsługuje proces logowania użytkownika.
     *
     * @return void
     */
    public function login(): void
    {
        $data = [
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? ''
        ];

        $loginError = $this->validateLogin($data);
        if ($loginError) {
            $_SESSION['error']['login'] = 'Nieprawidlowe dane logowania!';
            $this->setToast('Nieprawidlowe dane logowania!', 'error');
            $this->redirect->to('/login');
        }

        $loginResult = $this->performLogin($data['email'], $data['password']);

        if ($loginResult['success']) {
            $this->setToast('Pomyślnie zalogowano!', 'success');
            $this->redirect->to('/');
        } else {
            $this->rememberPreviousData($data);
            $_SESSION['error']['login'] = $loginResult['error'];
            $this->setToast($loginResult['error'] ?? 'Bledny email lub haslo!', 'error');

            $this->redirect->to("/login");
        }
    }

    private function rememberPreviousData(array $data): void
    {
        foreach ($data as $key => $value)
        {
            $_SESSION['previous_data'][$key] = $value;
        }
    }

    /**
     * Obsługuje proces rejestracji nowego użytkownika.
     *
     * @return void
     */
    public function register(): void
    {
        $userData = [
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'repassword' => $_POST['repassword'] ?? '',
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'phone' => str_replace(' ', '', trim($_POST['phone'] ?? '')),
            'country' => trim($_POST['country'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'address' => trim($_POST['address'] ?? '')
        ];

        $validationError = Validation::validateUserData($userData);
        if ($validationError) {
            $this->setToast('Walidacja danych rejestracji nie powiodła się!', 'error');
            $this->rememberPreviousData($userData);
            $this->redirect->to('/register');
        }

        unset($userData['repassword']);

        $result = $this->api->request(RequestType::POST, '/auth/register', $userData);

        if ($result && isset($result['success']) && $result['success'] === true) {

            $loginResult = $this->performLogin($userData['email'], $userData['password']);

            $token = $result['token'];

            $this->resend->emails->send([
                'from' => 'Amazonix no-reply@amazonix.pl',
                'to' => [$userData['email']],
                'subject' => 'Aktywacja konta',
                'template' => [
                    'id' => 'email-confirmation',
                    'variables' => [
                        'first_name' => $userData['first_name'],
                        'confirmation_url' => "$this->root/verify?token=$token"
                    ],
                ],
            ]);

            if ($loginResult && isset($loginResult['success']) && $loginResult['success'] === true) {
                $_SESSION['JWT_TOKEN'] = $loginResult['jwt_token'];
                $this->redirect->to('/');
            } else {
                $this->setToast('Rejestracja udana! Możesz się zalogować.', 'success');

                $this->redirect->to('/login');
            }
        } else {
            $_SESSION['error']['general'] = $result['error'] ?? 'Błąd rejestracji';
            $this->setToast($result['error'] ?? 'Błąd rejestracji', 'error');
            $this->rememberPreviousData($userData);
            $this->redirect->to('/register');
        }
    }

    /**
     * Wyświetla formularz zapomnianego hasła.
     */
    public function showForgotPasswordForm(): void
    {
        $this->view->renderPage('auth/forgot-password', [
            'title' => 'Zapomniałeś hasła?',
            'root' => $_ENV['ROOT_DIR'],
            'styles' => ['static/css/auth.css']
        ]);
    }

    /**
     * Obsługuje żądanie resetowania hasła.
     */
    public function forgotPassword(): void
    {
        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            $_SESSION['forgot_error'] = 'Email jest wymagany';
            $this->redirect->to('/forgot-password');
        }

        $response = $this->api->request(RequestType::POST, '/auth/forgot-password', ['email' => $email]);

        if ($response && $response['success']) {
            $_SESSION['forgot_success'] = $response['message'];
            // W celach demonstracyjnych pokazujemy token, jeśli wrócił z API
            if (isset($response['token'])) {
                $_SESSION['forgot_success'] .= " (Token testowy: " . $response['token'] . ")";
            }

            $this->resend->emails->send([
                'from' => 'Amazonix no-reply@amazonix.pl',
                'to' => [$email],
                'subject' => 'Reset hasła',
                'template' => [
                    'id' => 'password-reset',
                    'variables' => [
                        'first_name' => $email,
                        'password_reset_url' => "$this->root/reset-password?token=" . $response['token']
                    ],
                ],
            ]);

        } else {
            $_SESSION['forgot_error'] = $response['error'] ?? 'Wystąpił błąd podczas wysyłania prośby.';
        }

        $this->redirect->to('/forgot-password');
    }

    /**
     * Wyświetla formularz resetowania hasła.
     */
    public function showResetPasswordForm(): void
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $this->redirect->to('/login');
        }

        $this->view->renderPage('auth/reset-password', [
            'title' => 'Resetowanie hasła',
            'root' => $_ENV['ROOT_DIR'],
            'token' => $token,
            'styles' => ['static/css/auth.css']
        ]);
    }

    /**
     * Obsługuje faktyczną zmianę hasła.
     */
    public function resetPassword(): void
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($password) || empty($confirm_password)) {
            $_SESSION['reset_error'] = 'Wszystkie pola są wymagane';
            $this->redirect->to("/reset-password?token=$token");
            return;
        }

        if ($password !== $confirm_password) {
            $_SESSION['reset_error'] = 'Hasła nie są identyczne';
            $this->redirect->to("/reset-password?token=$token");
            return;
        }

        if (strlen($password) < 8) {
            $_SESSION['reset_error'] = 'Hasło musi mieć co najmniej 8 znaków';
            $this->redirect->to("/reset-password?token=$token");
            return;
        }

        $response = $this->api->request(RequestType::POST, '/auth/reset-password', [
            'token' => $token,
            'password' => $password
        ]);

        if ($response && $response['success']) {
            $_SESSION['login_success'] = 'Hasło zostało zmienione. Możesz się zalogować.';
            $this->redirect->to('/login');
        } else {
            $_SESSION['reset_error'] = $response['error'] ?? 'Wystąpił błąd podczas zmiany hasła.';
            $this->redirect->to("/reset-password?token=$token");
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        $this->redirect->to('/');
    }

    /**
     * Dodaje do koszyka dane przed logowaniem
     */
    private function mergeGuestCart(): void 
    {
        $this->redirect->to('/cart');
    }


    /**
     * Weryfikuje adres e-mail użytkownika za pomocą tokena.
     *
     * @return void
     */
    public function verifyEmail(): void
    {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $this->view->renderPage('auth/verify', [
                'title' => 'Weryfikacja e-mail - Błąd',
                'success' => false,
                'message' => 'Brak tokena weryfikacyjnego.'
            ]);
            return;
        }


        if (!$this->user) {
            $this->view->renderPage('auth/verify', [
                'title' => 'Weryfikacja e-mail - Błąd',
                'success' => false,
                'message' => 'Nieprawidłowy lub wygasły token weryfikacyjny.'
            ]);
            return;
        }

        $userId = $this->user["id"];

        $result = $this->api->request(RequestType::POST, "/auth/verify/$userId/$token");

        $success = $result && isset($result['success']) && $result['success'] === true;
        $message = $result['message'] ?? ($success ? 'Twój adres e-mail został pomyślnie zweryfikowany.' : 'Wystąpił błąd podczas weryfikacji adresu e-mail.');

        $this->view->renderPage('auth/verify', [
            'title' => 'Weryfikacja e-mail',

            'success' => $success,
            'message' => $message
        ]);
    }
}
