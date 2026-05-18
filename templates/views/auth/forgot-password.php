<?php
/**
 * @var View $view
 * @var string $root
 */

use App\View\View;

$error = $_SESSION['forgot_error'] ?? '';
$success = $_SESSION['forgot_success'] ?? '';
unset($_SESSION['forgot_error'], $_SESSION['forgot_success']);
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
                    
                    <h1 class="h3 fw-normal mb-3">Pomoc z hasłem</h1>
                    <p class="text-muted small mb-4">Wpisz adres e-mail powiązany z Twoim kontem Amazonix.</p>
                    
                    <?php if ($error) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success) : ?>
                        <div class="alert alert-success" role="alert">
                            <?= $success ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= htmlspecialchars($root) ?>/forgot-password">
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Adres e-mail</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email"  
                                required
                                style="border-radius: 8px;"
                            >
                        </div>
                        
                        <button type="submit" class="btn w-100 py-2 mb-3 fw-bold" style="background-color: #ffd814; border-color: #fcd200; border-radius: 20px; color: #0F1111;">
                            Kontynuuj
                        </button>
                    </form>
                    
                    <div class="mt-4">
                        <p class="fw-bold mb-1 small">Czy zmienił się Twój adres e-mail?</p>
                        <p class="small text-muted">Jeśli nie korzystasz już z adresu e-mail powiązanego z Twoim kontem Amazonix, możesz skontaktować się z <a href="<?= htmlspecialchars($root) ?>/contact" style="color: #0066c0;">Obsługą Klienta</a>, aby uzyskać pomoc w odzyskaniu dostępu do konta.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
