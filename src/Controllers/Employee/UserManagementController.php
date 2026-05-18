<?php
declare(strict_types=1);

namespace App\Controllers\Employee;

use App\Controllers\Controller;
use App\Enums\RequestType;

class UserManagementController extends Controller
{
    private function canAccessManagerPanel(array $user): void
    {
        $allowedRoles = ['manager', 'admin', 'owner'];
        $role = strtolower((string)($user['role_name'] ?? ''));
        if (!in_array($role, $allowedRoles, true)) {
            $this->setToast('Brak uprawnień.', 'error');
            $this->redirect->to('/employee');
        }
    }

    public function index(): void
    {
        $this->canAccessManagerPanel($this->user);

        $users = $this->api->request(RequestType::GET, '/account/users');
        if ($users === null || isset($users['error'])) {
            $users = [];
        }

        $this->view->renderPage('account/employee/users/index', [
            'title' => 'Zarządzanie użytkownikami',
            'user' => $this->user,
            'cart' => $this->cart,
            'users' => $users,
            'styles' => [
                'static/css/account/dashboard.css',
                'static/css/account/products.css',
                'static/css/account/users.css'
            ]
        ]);
    }

    public function add(): void
    {
        $this->canAccessManagerPanel($this->user);

        $roles = $this->api->request(RequestType::GET, '/account/roles');

        $this->view->renderPage('account/employee/users/add', [
            'title' => 'Dodaj użytkownika',
            'user' => $this->user,
            'cart' => $this->cart,
            'roles' => $roles ?? [],
            'styles' => [
                'static/css/account/products.css',
                'static/css/account/users.css'
            ]
        ]);
    }

    public function store(): void
    {
        $this->canAccessManagerPanel($this->user);

        $roleId = (int)($_POST['role_id'] ?? 1);
        
        // Pobierz role, żeby sprawdzić czy ktoś nie próbuje dodać ownera
        $roles = $this->api->request(RequestType::GET, '/account/roles');
        $ownerRole = null;
        if ($roles) {
            foreach ($roles as $r) {
                if (strtolower($r['name']) === 'owner') {
                    $ownerRole = $r['id'];
                    break;
                }
            }
        }

        if ($ownerRole !== null && $roleId === (int)$ownerRole) {
            $this->setToast('Nie można przypisać rangi owner.', 'error');
            $this->redirect->to('/employee/users/add');
        }

        $payload = [
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'role_id' => $roleId
        ];

        $result = $this->api->request(RequestType::POST, '/account/users', $payload);

        if ($result && isset($result['success']) && $result['success'] === true) {
            $this->setToast('Użytkownik został dodany.', 'success');
            $this->redirect->to('/employee/users');
        }

        $this->setToast($result['error'] ?? 'Wystąpił błąd podczas dodawania użytkownika.', 'error');
        $this->redirect->to('/employee/users/add');
    }

    public function edit(array $matches): void
    {
        $this->canAccessManagerPanel($this->user);

        $userId = $matches['id'];
        $userToEdit = $this->api->request(RequestType::GET, "/account/users/$userId");

        if (!$userToEdit || isset($userToEdit['error'])) {
            $this->setToast('Nie znaleziono użytkownika.', 'error');
            $this->redirect->to('/employee/users');
        }

        $roles = $this->api->request(RequestType::GET, '/account/roles');

        $this->view->renderPage('account/employee/users/edit', [
            'title' => 'Edytuj użytkownika',
            'user' => $this->user,
            'cart' => $this->cart,
            'userToEdit' => $userToEdit,
            'roles' => $roles ?? [],
            'styles' => [
                'static/css/account/products.css',
                'static/css/account/users.css'
            ]
        ]);
    }

    public function update(array $matches): void
    {
        $this->canAccessManagerPanel($this->user);

        $userId = $matches['id'];

        // Blokada edycji ownera przez admina/managera
        $userToEdit = $this->api->request(RequestType::GET, "/account/users/$userId");
        if ($userToEdit && strtolower((string)($userToEdit['role_name'] ?? '')) === 'owner') {
            $this->setToast('Nie można edytować użytkownika o randze owner.', 'error');
            $this->redirect->to('/employee/users');
        }

        $payload = [
            'email' => trim($_POST['email'] ?? ''),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'role_id' => (int)($_POST['role_id'] ?? 1),
            'phone' => trim($_POST['phone'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'address' => trim($_POST['address'] ?? '')
        ];

        $result = $this->api->request(RequestType::PUT, "/account/users/$userId", $payload);

        if ($result && isset($result['success']) && $result['success'] === true) {
            $this->setToast('Użytkownik został zaktualizowany.', 'success');
            $this->redirect->to('/employee/users');
        }

        $this->setToast($result['error'] ?? 'Wystąpił błąd podczas aktualizacji użytkownika.', 'error');
        $this->redirect->to("/employee/users/edit/$userId");
    }

    public function delete(array $matches): void
    {
        $this->canAccessManagerPanel($this->user);

        $userId = $matches['id'];
        
        // Blokada usuwania ownera
        $userToDelete = $this->api->request(RequestType::GET, "/account/users/$userId");
        if ($userToDelete && strtolower((string)($userToDelete['role_name'] ?? '')) === 'owner') {
            $this->setToast('Nie można usunąć użytkownika o randze owner.', 'error');
            $this->redirect->to('/employee/users');
        }

        // Nie pozwól usunąć samego siebie
        if ((int)$userId === (int)$this->user['id']) {
            $this->setToast('Nie możesz usunąć własnego konta z tego poziomu.', 'error');
            $this->redirect->to('/employee/users');
        }

        $result = $this->api->request(RequestType::DELETE, "/account/users/$userId");

        if ($result && isset($result['success']) && $result['success'] === true) {
            $this->setToast('Użytkownik został usunięty.', 'success');
        } else {
            $this->setToast($result['error'] ?? 'Nie udało się usunąć użytkownika.', 'error');
        }

        $this->redirect->to('/employee/users');
    }
}
