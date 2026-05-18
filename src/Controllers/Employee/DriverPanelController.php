<?php
declare(strict_types=1);

namespace App\Controllers\Employee;

use App\Controllers\Controller;
use App\Enums\RequestType;

class DriverPanelController extends Controller
{
    private function canAccessDriverPanel(): bool
    {
        return strtolower((string)($this->user['role_name'] ?? '')) === 'driver';
    }

    public function index(): void
    {
        if (!$this->canAccessDriverPanel()) {
            $this->setToast('Brak uprawnień do panelu kierowcy.', 'error');
            $this->redirect->to('/employee');
        }

        $driverId = (int)($this->user['id'] ?? 0);
        $response = $this->api->request(RequestType::GET, "/driver/deliveries/$driverId");
        $deliveries = [];
        if (is_array($response) && isset($response['success']) && $response['success'] === true) {
            $deliveries = $response['deliveries'] ?? [];
        }

        $this->view->renderPage('account/employee/driver/index', [
            'title' => 'Panel kierowcy',
            'user' => $this->user,
            'cart' => $this->cart,
            'deliveries' => $deliveries,
            'styles' => [
                'static/css/account/dashboard.css',
                'static/css/account/products.css',
                'static/css/account/users.css'
            ]
        ]);
    }

    public function markDelivered(array $matches): void
    {
        if (!$this->canAccessDriverPanel()) {
            $this->setToast('Brak uprawnień do panelu kierowcy.', 'error');
            $this->redirect->to('/employee');
        }

        $deliveryId = (int)($matches['id'] ?? 0);
        $driverId = (int)($this->user['id'] ?? 0);

        $result = $this->api->request(RequestType::POST, "/driver/deliveries/$deliveryId/delivered", [
            'driver_id' => $driverId
        ]);

        if ($result && isset($result['success']) && $result['success'] === true) {
            $this->setToast('Dostawa została oznaczona jako dostarczona.', 'success');
        } else {
            $this->setToast($result['error'] ?? 'Nie udało się oznaczyć dostawy.', 'error');
        }

        $this->redirect->to('/employee/driver');
    }
}
