<?php
/**
 * @var View $view
 * @var string $root
 * @var bool $success
 * @var string $message
 */

use App\View\View;
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 text-center">
            <div class="card border-0 shadow-sm p-4">
                <div class="card-body">
                    <div class="mb-4">
                        <a href="<?= htmlspecialchars($root) ?>/" class="text-decoration-none">
                            <span class="display-6 fw-bold text-dark">Amazonix</span>
                        </a>
                    </div>

                    <?php if ($success) : ?>
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h1 class="h3 mb-3">Email zweryfikowany!</h1>
                        <p class="text-muted mb-4">
                            <?= htmlspecialchars($message ?: 'Twoje konto zostało pomyślnie aktywowane. Możesz teraz w pełni korzystać z serwisu.') ?>
                        </p>
                        <a href="<?= htmlspecialchars($root) ?>/" class="btn w-100 py-2 fw-bold" style="background-color: #ffd814; border-color: #fcd200; border-radius: 20px; color: #0F1111;">
                            Wróć do strony głównej
                        </a>
                    <?php else : ?>
                        <div class="mb-4">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h1 class="h3 mb-3">Weryfikacja nieudana</h1>
                        <p class="text-muted mb-4">
                            <?= htmlspecialchars($message ?: 'Wystąpił błąd podczas weryfikacji Twojego adresu e-mail. Link może być nieaktywny lub wygasł.') ?>
                        </p>
                        <div class="d-grid gap-2">
                            <a href="<?= htmlspecialchars($root) ?>/" class="btn btn-outline-secondary py-2 fw-bold" style="border-radius: 20px;">
                                Wróć do strony głównej
                            </a>
                            <a href="<?= htmlspecialchars($root) ?>/help" class="btn btn-link text-decoration-none small">
                                Potrzebujesz pomocy?
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
