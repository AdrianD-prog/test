<?php
declare(strict_types=1);

namespace App\Controllers\Employee;

use App\Controllers\Controller;
use App\Enums\RequestType;

class ProductManagementController extends Controller
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

        $products = $this->api->request(RequestType::GET, '/produkty');
        if ($products === null || isset($products['error'])) {
            $products = [];
        }

        $this->view->renderPage('account/employee/products/index', [
            'title' => 'Zarządzanie produktami',
            'user' => $this->user,
            'cart' => $this->cart,
            'products' => $products,
            'styles' => [
                'static/css/account/dashboard.css',
                'static/css/account/products.css',
            ]
        ]);
    }

    public function add(): void
    {
        $this->canAccessManagerPanel($this->user);

        $categories = $this->api->request(RequestType::GET, '/kategorie');

        $this->view->renderPage('account/employee/products/add', [
            'title' => 'Dodaj nowy produkt',
            'user' => $this->user,
            'cart' => $this->cart,
            'categories' => $categories ?? [],
            'styles' => [
                'static/css/account/dashboard.css',
                'static/css/account/products.css'
            ]
        ]);
    }

    public function store(): void
    {
        $this->canAccessManagerPanel($this->user);

        $payload = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'image_url' => trim($_POST['image_url'] ?? '')
        ];

        $result = $this->api->request(RequestType::POST, '/produkty', $payload);

        if ($result && isset($result['success']) && $result['success'] === true) {
            $this->setToast('Produkt został dodany.', 'success');
            $this->redirect->to('/employee/products');
        }

        $this->setToast($result['error'] ?? 'Wystąpił błąd podczas dodawania produktu.', 'error');
        $this->redirect->to('/employee/products/add');
    }

    public function edit(array $matches): void
    {
        $this->canAccessManagerPanel($this->user);

        $productId = $matches['id'];
        $productResponse = $this->api->request(RequestType::GET, "/produkty/$productId");
        
        $product = is_array($productResponse) && isset($productResponse[0]) ? $productResponse[0] : $productResponse;

        if (!$product || isset($product['error'])) {
            $this->setToast('Nie znaleziono produktu.', 'error');
            $this->redirect->to('/employee/products');
        }

        $categories = $this->api->request(RequestType::GET, '/kategorie');

        $this->view->renderPage('account/employee/products/edit', [
            'title' => 'Edytuj produkt',
            'user' => $this->user,
            'cart' => $this->cart,
            'product' => $product,
            'categories' => $categories ?? [],
            'styles' => [
                'static/css/account/dashboard.css',
                'static/css/account/products.css'
            ]
        ]);
    }

    public function update(array $matches): void
    {
        $this->canAccessManagerPanel($this->user);

        $productId = $matches['id'];
        $payload = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'image_url' => trim($_POST['image_url'] ?? '')
        ];

        $result = $this->api->request(RequestType::PUT, "/produkty/$productId", $payload);

        if ($result && isset($result['success']) && $result['success'] === true) {
            $this->setToast('Produkt został zaktualizowany.', 'success');
            $this->redirect->to('/employee/products');
        }

        $this->setToast($result['error'] ?? 'Wystąpił błąd podczas aktualizacji produktu.', 'error');
        $this->redirect->to("/employee/products/edit/$productId");
    }

    public function delete(array $matches): void
    {
        $this->canAccessManagerPanel($this->user);

        $productId = $matches['id'];
        $result = $this->api->request(RequestType::DELETE, "/produkty/$productId");

        if ($result && isset($result['success']) && $result['success'] === true) {
            $this->setToast('Produkt został usunięty.', 'success');
        } else {
            $this->setToast($result['error'] ?? 'Nie udało się usunąć produktu.', 'error');
        }

        $this->redirect->to('/employee/products');
    }
}
