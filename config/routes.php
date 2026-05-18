<?php

    use App\Controllers\Account\AccountController;
    use App\Controllers\Account\AuthController;
    use App\Controllers\Account\CartController;
    use App\Controllers\Account\OrderController;
    use App\Controllers\Employee\ProductManagementController;
    use App\Controllers\Employee\TablesPanelController;
    use App\Controllers\Employee\UserManagementController;
    use App\Controllers\Employee\DriverPanelController;
    use App\Controllers\FooterController;
    use App\Controllers\MainController;

    /**
     * Definicja tras
     *
     * @var App\Http\Router $router
     */

        $router->add("GET", "/", [MainController::class, 'home']);


        $router->group("/products", function ($router) {
            $router->add("GET", "/", [MainController::class, 'products']);
            $router->add("GET", "/{id:int}", [MainController::class, 'product']);
            $router->add("GET", "/suggestions", [MainController::class, 'suggestions']);
            $router->add("POST", "/review", [MainController::class, 'addReview']);
            $router->add("POST", "/review/edit", [MainController::class, 'editReview']);
        });

        $router->group('/cart', function ($router) {
            $router->add("GET", "/", [CartController::class, 'cart']);
            $router->add("POST", "/add", [CartController::class, 'add']);
            $router->add("POST", "/update", [CartController::class, 'update']);
            $router->add("POST", "/remove", [CartController::class, 'remove']);
        });


        $router->group("/register", function ($router) {
            $router->add("GET", "/", [AuthController::class, 'showRegisterForm']);
            $router->add("POST", "/", [AuthController::class, 'register']);
        });

        $router->group("/login", function ($router) {
            $router->add("GET", "/", [AuthController::class, 'showLoginForm']);
            $router->add("POST", "/", [AuthController::class, 'login']);
        });

        $router->add("GET", "/logout", [AuthController::class, 'logout']);


        $router->add("GET", "/verify", [AuthController::class, 'verifyEmail']);

        $router->add("GET", "/forgot-password", [AuthController::class, 'showForgotPasswordForm']);
        $router->add("POST", "/forgot-password", [AuthController::class, 'forgotPassword']);
        $router->add("GET", "/reset-password", [AuthController::class, 'showResetPasswordForm']);
        $router->add("POST", "/reset-password", [AuthController::class, 'resetPassword']);


        $router->add("GET","/about-us", [FooterController::class, 'aboutUs']);
        $router->add("GET", "/careers", [FooterController::class, 'careers']);
        $router->add("GET", "/investor-relations", [FooterController::class, 'investorRelations']);
        $router->add("GET", "/affiliate", [FooterController::class, 'affiliate']);
        $router->add("GET", "/help", [FooterController::class, 'help']);
        $router->add("GET", "/returns", [FooterController::class, 'returns']);
        $router->add("GET","/contact",[FooterController::class, 'contact']);

        $router->group("/favorites", function ($router) {
            $router->add("GET", '/', [MainController::class, 'favorites']);
            $router->add("POST", '/toggle', [MainController::class, 'toggleFavorite']);
        });

        $router->group("/orders", function ($router) {
            $router->add("GET", "/", [OrderController::class, 'index']);
            $router->add("GET", "/checkout", [OrderController::class, 'checkout']);
            $router->add("POST", "/checkout", [OrderController::class, 'processCheckout']);
        });

        $router->group("/account", function ($router) {
            $router->add("GET", "/", [AccountController::class, 'showAccount']);
            $router->add("GET", "/edit", [AccountController::class, 'editAccount']);
            $router->add("POST", "/update", [AccountController::class, 'updateAccount']);
            $router->add("GET", "/settings", [AccountController::class, 'settingsAccount']);
        });


        $router->group("/employee", function ($router) {
            $router->add("GET",'/',[AccountController::class, 'showEmployee']);
            $router->group("/driver", function ($router) {
                $router->add("GET", "/", [DriverPanelController::class, 'index']);
                $router->add("POST", "/delivered/{id:int}", [DriverPanelController::class, 'markDelivered']);
            });

            $router->group("/products", function ($router) {
                $router->add("GET", "/", [ProductManagementController::class, 'index']);
                $router->add("GET", "/add", [ProductManagementController::class, 'add']);
                $router->add("POST", "/add", [ProductManagementController::class, 'store']);
                $router->add("GET", "/edit/{id:int}", [ProductManagementController::class, 'edit']);
                $router->add("POST", "/update/{id:int}", [ProductManagementController::class, 'update']);
                $router->add("POST", "/delete/{id:int}", [ProductManagementController::class, 'delete']);
            });

            $router->group("/users", function ($router) {
                $router->add("GET", "/", [UserManagementController::class, 'index']);
                $router->add("GET", "/add", [UserManagementController::class, 'add']);
                $router->add("POST", "/add", [UserManagementController::class, 'store']);
                $router->add("GET", "/edit/{id:int}", [UserManagementController::class, 'edit']);
                $router->add("POST", "/update/{id:int}", [UserManagementController::class, 'update']);
                $router->add("POST", "/delete/{id:int}", [UserManagementController::class, 'delete']);
            });

            $router->group("/tables", function ($router) {
                $router->add("GET", "/", [TablesPanelController::class, 'index']);
                $router->add("GET", "/table/{table}", [TablesPanelController::class, 'manageTable']);
                $router->add("POST", "/table/add/{table:string}", [TablesPanelController::class, 'addRow']);
                $router->add("POST", "/table/update/{table:string}/{id:int}", [TablesPanelController::class, 'updateRow']);
                $router->add("POST", "/table/delete/{table:string}/{id:int}", [TablesPanelController::class, 'deleteRow']);
            });
        });

    return $router;
