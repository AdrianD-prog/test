<?php
    declare(strict_types=1);

    namespace App\Controllers\Account;

    use App\Controllers\Controller;
    use App\Enums\RequestType;
    use App\Helpers\Validation;

    class AccountController extends Controller
    {
        private function canAccessEmployeeDashboard(array $user): bool
        {
            $allowedRoles = ['driver', 'warehouse_worker', 'manager', 'admin', 'owner'];
            $role = strtolower((string)($user['role_name'] ?? ''));

            return in_array($role, $allowedRoles, true);
        }

        public function showAccount(): void
        {
            if($this->user == [])
            {
                $this->redirect->to('/');
            }

            $orders = $this->api->request(RequestType::GET, '/orders/' . $this->user['id']);
            $reviews = $this->api->request(RequestType::GET, '/opinie/' . $this->user['id']);
            $favorites = $this->api->request(RequestType::GET, '/favorites?user_id=' . $this->user['id']);
            $user = $this->api->request(RequestType::GET, '/account/users/' . $this->user['id']);
            
            if(isset($user['error'])) {
                $this->redirect->to('/logout');
            }

            $this->view->renderPage('account/account', [
                'title' => 'Moje konto',

                'user' => $user,
                'cart' => $this->cart,
                'orders_count' => count($orders ?? []),
                'reviews_count' => count($reviews ?? []),
                'favorites_count' => count($favorites ?? []),

                'scripts' => [
                    'static/js/account.js',
                ],
                'styles' => [
                    'static/css/account/nav.css',
                    'static/css/account/main.css'
                ]
            ]);
        }

        public function editAccount(): void
        {
            if($this->user == [])
            {
                $this->redirect->to('/');
            }


            $this->view->renderPage('account/edit',
            [
                'title' => 'Edytuj konto',

                'user' => $this->user,
                'cart' => $this->cart,

                'styles' => [
                    'static/css/account/edit.css',
                ]

            ]
            );
        }

        public function updateAccount(): void
        {
            if($this->user == [])
            {
                $this->redirect->to('/');
            }

            $userId = $this->user['id'] ?? null;
            if (!$userId) {
                $this->setToast('Brak identyfikatora użytkownika. Zaloguj się ponownie.', 'error');
                $this->redirect->to('/login');
            }

            $payload = [
                'user_id' => $userId,
                'email' => trim($_POST['email'] ?? ''),
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'country' => trim($_POST['country'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
            ];

            if(Validation::validateUserData($payload))
            {
                $this->setToast('Walidacja danych rejestracji nie powiodła się!', 'error');
                foreach ($payload as $key => $value)
                {
                    $_SESSION['previous_data'][$key] = $value;
                }
                $this->redirect->to("/account/edit");
            }

            $result = $this->api->request(
                RequestType::POST,
                '/account/update',
                $payload
            );

            if ($result && isset($result['success']) && $result['success'] === true) {
                if (isset($result['jwt_token'])) {
                    $_SESSION['JWT_TOKEN'] = $result['jwt_token'];
                }

                $this->setToast('Dane konta zostały zaktualizowane.', 'success');
                $this->redirect->to('/account');
            }

            $this->setToast($result['error'] ?? 'Nie udało się zaktualizować danych konta.', 'error');
            $this->redirect->to('/account/edit');
        }

        public function settingsAccount(): void 
        {
            if($this->user == [])
            {
                $this->redirect->to('/');
            }


            $this->view->renderPage('account/edit',
            [
                'title' => 'Ustawienia',

                'user' => $this->user,
                'cart' => $this->cart,

                'styles' => [
                    'static/css/style.css',
                ]
                
            ]
            );
        }
        public function showEmployee(): void
        {
            if (!$this->canAccessEmployeeDashboard($this->user)) {
                $this->setToast('Ten panel jest dostępny tylko dla pracowników.', 'error');
                $this->redirect->to('/account');
            }


            $this->view->renderPage('account/employee/employee',
            [
                'title' => 'Panel',

                'user' => $this->user,
                'cart' => $this->cart,

                'styles' => [
                    'static/css/account/main.css',
                    'static/css/account/dashboard.css',
                ]

            ]
            );
        }

    }
