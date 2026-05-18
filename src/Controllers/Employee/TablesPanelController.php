<?php
declare(strict_types=1);

namespace App\Controllers\Employee;

use App\Controllers\Controller;
use App\Enums\RequestType;

class TablesPanelController extends Controller
{
    private function isAdmin(): bool
    {
        return in_array(strtolower((string)($this->user['role_name'] ?? '')), ['admin', 'owner', 'manager'], true);
    }

    public function index(): void
    {
        if (!$this->isAdmin()) {
            $this->setToast('Brak uprawnień administratora.', 'error');
            $this->redirect->to('/employee');
        }

        $tables = $this->api->request(RequestType::GET, '/tables');

        $this->view->renderPage('account/employee/tables/index', [
            'title' => 'Panel Administratora',
            'user' => $this->user,
            'cart' => $this->cart,
            'tables' => $tables ?? [],
            'styles' => [
                'static/css/account/dashboard.css',
                'static/css/account/products.css',
                'static/css/account/tables.css'
            ]
        ]);
    }

    public function manageTable(array $matches): void
    {
        if (!$this->isAdmin()) {
            $this->redirect->to('/employee');
        }

        $tableName = $matches['table'];
        $data = $this->api->request(RequestType::GET, "/tables/$tableName");
        $schema = $this->api->request(RequestType::GET, "/tables/$tableName/schema");

        $this->view->renderPage('account/employee/tables/table', [
            'title' => "Zarządzanie tabelą: $tableName",
            'user' => $this->user,
            'cart' => $this->cart,
            'tableName' => $tableName,
            'tableData' => $data ?? [],
            'tableSchema' => $schema ?? [],
            'styles' => [
                'static/css/account/dashboard.css',
                'static/css/account/products.css',
                'static/css/account/tables.css'
            ]
        ]);
    }

    public function addRow(array $matches): void
    {
        if (!$this->isAdmin()) {
            $this->redirect->to('/employee');
        }

        $tableName = $matches['table'];
        $payload = $_POST;

        // Basic protection for roles
        if ($tableName === 'users' && isset($payload['role_id'])) {
             // Check if trying to set owner role (assuming 3 is owner based on logic, but better to check by name in API)
             // Python API already handles owner protection by ID/Role name.
        }

        $result = $this->api->request(RequestType::POST, "/tables/tables/$tableName", $payload);

        if ($result && isset($result['success']) && $result['success'] === true) {
            $this->setToast('Rekord został dodany.', 'success');
        } else {
            $this->setToast($result['error'] ?? 'Wystąpił błąd.', 'error');
        }

        $this->redirect->to("/employee/tables/table/$tableName");
    }

    public function updateRow(array $matches): void
    {
        if (!$this->isAdmin()) {
            $this->redirect->to('/employee');
        }

        $tableName = $matches['table'];
        $rowId = $matches['id'];
        $payload = $_POST;

        $result = $this->api->request(RequestType::PUT, "/tables/tables/$tableName/$rowId", $payload);

        if ($result && isset($result['success']) && $result['success'] === true) {
            $this->setToast('Rekord został zaktualizowany.', 'success');
        } else {
            $this->setToast($result['error'] ?? 'Wystąpił błąd.', 'error');
        }

        $this->redirect->to("/employee/tables/table/$tableName");
    }

    public function deleteRow(array $matches): void
    {
        if (!$this->isAdmin()) {
            $this->redirect->to('/employee');
        }

        $tableName = $matches['table'];
        $rowId = $matches['id'];

        $result = $this->api->request(RequestType::DELETE, "/tables/tables/$tableName/$rowId");

        if ($result && isset($result['success']) && $result['success'] === true) {
            $this->setToast('Rekord został usunięty.', 'success');
        } else {
            $this->setToast($result['error'] ?? 'Wystąpił błąd.', 'error');
        }

        $this->redirect->to("/employee/tables/table/$tableName");
    }
}
