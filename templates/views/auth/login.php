<?php

/**
 * @var View $view
 * @var string $root
 */

use App\View\View;

// Pobierz ewentualne błędy z sesji
$error = $_SESSION['login_error'] ?? '';
$success = $_SESSION['login_success'] ?? '';
unset($_SESSION['login_error'], $_SESSION['login_success']);
?>

<div class="container py-4">
    <!-- Breadcrumb - opcja powrotu -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= htmlspecialchars($root) ?>/" class="text-decoration-none">Strona główna</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Logowanie
            </li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <!-- Przycisk powrotu (dodatkowa opcja) -->
            <div class="mb-3">
                <a href="<?= htmlspecialchars($root) ?>/" class="text-decoration-none" style="color: #0066c0;">
                    ← Powrót
                </a>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <a href="<?= htmlspecialchars($root) ?>/" class="text-decoration-none">
                            <span class="display-6 fw-bold text-dark">Amazonix</span>
                        </a>
                    </div>
                    
                    <h1 class="h3 fw-normal mb-3">Zaloguj się</h1>
                    
                    <?php if ($error) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>⚠️ Błąd!</strong> <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>✓ Sukces!</strong> <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form 
                        method="POST" 
                        action="<?= htmlspecialchars($root) ?>/login"
                        novalidate
                    >
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Adres e-mail</label>
                            <input 
                                type="email" 
                                class="form-control form-control-lg <?= isset($_SESSION['error']['email']) || isset($_SESSION['error']['login']) ? 'is-invalid' : '' ?>" 
                                id="email" 
                                name="email"  
                                required
                                value="<?= htmlspecialchars($_SESSION['previous_data']['email'] ?? '') ?>"
                                style="background-color: #fff; border: 1px solid #d9d9d9; border-radius: 8px;"
                            >
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($_SESSION['error']['email'] ?? '') ?>
                                <?= htmlspecialchars($_SESSION['error']['login'] ?? '') ?>
                            </div>
                        </div>
                        
                        <!-- Hasło -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label fw-semibold mb-0">Hasło</label>
                                <a href="<?= htmlspecialchars($root) ?>/forgot-password" class="text-decoration-none small" style="color: #0066c0;">Zapomniałeś hasła?</a>
                            </div>
                            <div class="position-relative">
                                <input 
                                    type="password" 
                                    class="form-control form-control-lg <?= isset($_SESSION['error']['password']) || isset($_SESSION['error']['login']) ? 'is-invalid' : '' ?>" 
                                    id="password" 
                                    name="password" 
                                    placeholder="••••••••" 
                                    required
                                    value="<?= htmlspecialchars($_SESSION['previous_data']['password'] ?? '') ?>"
                                    style="background-color: #fff; border: 1px solid #d9d9d9; border-radius: 8px; padding-right: 50px;"
                                >
                                <button 
                                    type="button" 
                                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y"
                                    style="text-decoration: none; padding: 0; margin-right: 10px; background: transparent; border: none;"
                                    onclick="togglePasswordVisibility()"
                                    id="togglePasswordBtn"
                                >
                                    <i id="toggleIcon" class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($_SESSION['error']['password'] ?? '') ?>
                                <?= htmlspecialchars($_SESSION['error']['login'] ?? '') ?>
                            </div>
                            <small class="text-muted">Hasło powinno mieć co najmniej 8 znaków</small>
                        </div>
                        
                        <!-- Zapamiętaj mnie -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Zapamiętaj mnie</label>
                        </div>
                        
                        <!-- Przycisk logowania -->
                        <button type="submit" class="btn w-100 py-2 mb-3 fw-bold" style="background-color: #ffd814; border-color: #fcd200; border-radius: 20px; color: #0F1111;">
                            Zaloguj się
                        </button>
                        
                        <!-- Link do rejestracji -->
                        <div class="text-center">
                            <p class="mb-0 text-muted small">Nie masz konta?</p>
                            <a href="<?= htmlspecialchars($root)?>/register" class="text-decoration-none fw-semibold" style="color: #0066c0;">
                                Zarejestruj się
                            </a>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <!-- Dane testowe -->
                    <div class="text-center text-muted small">
                        <p>Dane testowe:</p>
                        <p class="mb-1"><strong>Email:</strong> ludmila.czerwinski52@o2.pl</p>
                        <p><strong>Hasło:</strong> LudmiłaCzerwiński48544</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

</script>


<?php 
unset($error, $success);
unset($_SESSION['error']);
unset($_SESSION['previous_data']);
?>
