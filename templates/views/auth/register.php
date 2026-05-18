<?php

/**
 * @var View $view
 * @var array $params
 * @var string $root
 */

use App\View\View;

$error = $_SESSION['register_error'] ?? '';
$success = $_SESSION['register_success'] ?? '';
$generalError = $_SESSION['error']['general'] ?? '';
unset($_SESSION['register_error'], $_SESSION['register_success'], $_SESSION['error']['general']);
?>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= htmlspecialchars($root) ?>/" class="text-decoration-none">
                    <i class="bi bi-house-door"></i> Strona główna
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-person-plus"></i> Rejestracja
            </li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="mb-3">
                <a href="<?= htmlspecialchars($root) ?>/" class="text-decoration-none" style="color: #0066c0;">
                    <i class="bi bi-arrow-left"></i> Powrót
                </a>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <a href="<?= htmlspecialchars($root) ?>/" class="text-decoration-none">
                            <span class="display-6 fw-bold text-dark">
                                Amazonix
                            </span>
                        </a>
                    </div>
                    
                    <h1 class="h3 fw-normal mb-3">
                        <i class="bi bi-person-plus"></i> Utwórz konto
                    </h1>
                    
                    <?php if ($generalError) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <strong> Błąd!</strong> <?= htmlspecialchars($generalError) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            <strong> Sukces!</strong> <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form 
                        id="registerForm" 
                        method="POST" 
                        action="<?= htmlspecialchars($root) ?>/register"
                        novalidate
                    >
                        <!-- Imię i Nazwisko -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-semibold">
                                    <i class="bi bi-person"></i> Imię
                                </label>
                                <input 
                                    type="text"
                                    class="form-control form-control-lg <?= isset($_SESSION['error']['first_name']) ? 'is-invalid' : '' ?>" 
                                    id="first_name" 
                                    name="first_name"
                                    placeholder="Jan"
                                    value="<?= htmlspecialchars($_SESSION['previous_data']['first_name'] ?? '') ?>"  
                                    required
                                    style="background-color: #fff; border: 1px solid #d9d9d9; border-radius: 8px;"
                                >
                                <div id="first_name_error" class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle"></i>
                                    <?= htmlspecialchars($_SESSION['error']['first_name'] ?? '') ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="last_name" class="form-label fw-semibold">
                                    <i class="bi bi-person"></i> Nazwisko
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-lg <?= isset($_SESSION['error']['last_name']) ? 'is-invalid' : '' ?>" 
                                    id="last_name" 
                                    name="last_name" 
                                    placeholder="Kowalski"
                                    value="<?= htmlspecialchars($_SESSION['previous_data']['last_name'] ?? '') ?>"  
                                    required
                                    style="background-color: #fff; border: 1px solid #d9d9d9; border-radius: 8px;"
                                >
                                <div id="last_name_error" class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle"></i>
                                    <?= htmlspecialchars($_SESSION['error']['last_name'] ?? '') ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="bi bi-envelope"></i> Adres e-mail
                            </label>
                            <input 
                                type="email"
                                class="form-control form-control-lg <?= isset($_SESSION['error']['email']) ? 'is-invalid' : '' ?>" 
                                id="email" 
                                name="email" 
                                placeholder="jan.kowalski@example.com"
                                value="<?= htmlspecialchars($_SESSION['previous_data']['email'] ?? '') ?>"  
                                required
                                style="background-color: #fff; border: 1px solid #d9d9d9; border-radius: 8px;"
                            >
                            <div id="email_error" class="invalid-feedback">
                                <i class="bi bi-exclamation-circle"></i>
                                <?= htmlspecialchars($_SESSION['error']['email'] ?? '') ?>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Podaj aktywny adres e-mail
                            </small>
                        </div>
                        
                        <!-- Telefon -->
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold">
                                <i class="bi bi-telephone"></i> Numer telefonu
                            </label>
                            <input 
                                type="tel" 
                                class="form-control form-control-lg <?= isset($_SESSION['error']['phone']) ? 'is-invalid' : '' ?>" 
                                id="phone" 
                                name="phone"
                                placeholder="123 456 789"
                                value="<?= htmlspecialchars($_SESSION['previous_data']['phone'] ?? '') ?>"
                                style="background-color: #fff; border: 1px solid #d9d9d9; border-radius: 8px;"
                            >
                            <div id="phone_error" class="invalid-feedback">
                                <i class="bi bi-exclamation-circle"></i>
                                <?= htmlspecialchars($_SESSION['error']['phone'] ?? '') ?>
                            </div>
                        </div>
                        
                        <!-- Adres (Kraj, Miasto, Ulica) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-geo-alt"></i> Adres zamieszkania
                            </label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input 
                                        type="text" 
                                        class="form-control <?= isset($_SESSION['error']['country']) ? 'is-invalid' : '' ?>" 
                                        id="country" 
                                        name="country"
                                        placeholder="Kraj"
                                        value="<?= htmlspecialchars($_SESSION['previous_data']['country'] ?? '') ?>"
                                        style="background-color: #fff; border: 1px solid #d9d9d9; border-radius: 8px;"
                                    >
                                    <div id="country_error" class="invalid-feedback">
                                        <?= htmlspecialchars($_SESSION['error']['country'] ?? '') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <input 
                                        type="text" 
                                        class="form-control <?= isset($_SESSION['error']['city']) ? 'is-invalid' : '' ?>" 
                                        id="city" 
                                        name="city"
                                        placeholder="Miasto"
                                        value="<?= htmlspecialchars($_SESSION['previous_data']['city'] ?? '') ?>"
                                        style="background-color: #fff; border: 1px solid #d9d9d9; border-radius: 8px;"
                                    >
                                    <div id="city_error" class="invalid-feedback">
                                        <?= htmlspecialchars($_SESSION['error']['city'] ?? '') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <input 
                                        type="text" 
                                        class="form-control <?= isset($_SESSION['error']['address']) ? 'is-invalid' : '' ?>" 
                                        id="address" 
                                        name="address"
                                        placeholder="Ulica i numer"
                                        value="<?= htmlspecialchars($_SESSION['previous_data']['address'] ?? '') ?>"
                                        style="background-color: #fff; border: 1px solid #d9d9d9; border-radius: 8px;"
                                    >
                                    <div id="address_error" class="invalid-feedback">
                                        <?= htmlspecialchars($_SESSION['error']['address'] ?? '') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hasło z podglądem -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-lock"></i> Hasło
                            </label>
                            <div class="position-relative">
                                <input 
                                    type="password" 
                                    class="form-control form-control-lg <?= isset($_SESSION['error']['password']) ? 'is-invalid' : '' ?>" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Utwórz silne hasło"
                                    minlength="6"
                                    value="<?= htmlspecialchars($_SESSION['previous_data']['password'] ?? '') ?>" 
                                    required
                                    style="background-color: #fff; border: 1px solid #d9d9d9; border-radius: 8px; padding-right: 50px;"
                                >
                                <button 
                                    type="button" 
                                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y"
                                    style="text-decoration: none; padding: 0; margin-right: 10px; background: transparent; border: none;"
                                    onclick="togglePasswordVisibility('password')"
                                >
                                    <i id="passwordIcon" class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div id="password_error" class="invalid-feedback">
                                <i class="bi bi-exclamation-circle"></i>
                                <?= htmlspecialchars($_SESSION['error']['password'] ?? '') ?>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-shield-check"></i> Hasło musi mieć co najmniej 6 znaków
                            </small>
                        </div>
                        
                        <!-- Powtórz hasło z podglądem -->
                        <div class="mb-4">
                            <label for="repassword" class="form-label fw-semibold">
                                <i class="bi bi-lock-fill"></i> Potwierdź hasło
                            </label>
                            <div class="position-relative">
                                <input 
                                    type="password" 
                                    class="form-control form-control-lg <?= isset($_SESSION['error']['repassword']) ? 'is-invalid' : '' ?>" 
                                    id="repassword" 
                                    name="repassword" 
                                    placeholder="Powtórz hasło"
                                    minlength="6"
                                    value="<?= htmlspecialchars($_SESSION['previous_data']['repassword'] ?? '') ?>"
                                    required
                                    style="background-color: #fff; border: 1px solid #d9d9d9; border-radius: 8px; padding-right: 50px;"
                                >
                                <button 
                                    type="button" 
                                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y"
                                    style="text-decoration: none; padding: 0; margin-right: 10px; background: transparent; border: none;"
                                    onclick="togglePasswordVisibility('repassword')"
                                >
                                    <i id="repasswordIcon" class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div id="repassword_error" class="invalid-feedback">
                                <i class="bi bi-exclamation-circle"></i>
                                <?= htmlspecialchars($_SESSION['error']['repassword'] ?? '') ?>
                            </div>
                        </div>
                        
                        <!-- Przycisk rejestracji -->
                        <button type="submit" class="btn w-100 py-2 mb-3 fw-bold" id="registerBtn" style="background-color: #ffd814; border-color: #fcd200; border-radius: 20px; color: #0F1111;">
                            <i class="bi bi-person-plus"></i> Załóż konto
                        </button>
                        
                        <!-- Link do logowania -->
                        <div class="text-center">
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-box-arrow-in-right"></i> Masz już konto?
                            </p>
                            <a href="<?= htmlspecialchars($root)?>/login" class="text-decoration-none fw-semibold" style="color: #0066c0;">
                                Zaloguj się <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <!-- Informacja o bezpieczeństwie -->
                    <div class="text-center text-muted small">
                        <p class="mb-1">
                            <i class="bi bi-shield-lock"></i> Twoje dane są bezpieczne
                        </p>
                        <p class="mb-0">
                            Rejestrując się akceptujesz nasz 
                            <a href="#" class="text-decoration-none" style="color: #0066c0;">regulamin</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript do podglądu hasła -->
<script>

</script>



<?php
// Usunięcie danych z buforu sesji
unset($_SESSION['previous_data']);
unset($_SESSION['error']);
?>