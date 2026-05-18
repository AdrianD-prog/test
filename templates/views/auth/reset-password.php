<?php
/**
 * @var View $view
 * @var string $root
 * @var string $token
 */

use App\View\View;

$error = $_SESSION['reset_error'] ?? '';
unset($_SESSION['reset_error']);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <a href="<?= htmlspecialchars($root) ?>/" class="text-decoration-none">
                            <span class="display-6 fw-bold text-dark">Amazonix</span>
                        </a>
                    </div>
                    
                    <h1 class="h3 fw-normal mb-3">Utwórz nowe hasło</h1>
                    
                    <?php if ($error) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= htmlspecialchars($root) ?>/reset-password">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Nowe hasło</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password"  
                                required
                                minlength="8"
                                style="border-radius: 8px;"
                            >
                            <small class="text-muted">Hasło musi mieć co najmniej 8 znaków.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label fw-semibold">Ponownie wpisz hasło</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="confirm_password" 
                                name="confirm_password"  
                                required
                                style="border-radius: 8px;"
                            >
                        </div>
                        
                        <button type="submit" class="btn w-100 py-2 mb-3 fw-bold" style="background-color: #ffd814; border-color: #fcd200; border-radius: 20px; color: #0F1111;">
                            Zapisz zmiany i zaloguj się
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
