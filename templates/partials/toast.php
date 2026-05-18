<?php
    /**
     * @var array $toast
     */
    $toastType = $toast['type'] ?? 'info';
    $toastClass = $toastType === 'success'
        ? 'bg-success text-white'
        : ($toastType === 'error' ? 'bg-danger text-white' : 'bg-secondary text-white');
?>
<?php if ($toast) : ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast " role="<?= htmlspecialchars($toastType)?>" aria-live="polite" aria-atomic="true">
        <div class="toast-header <?= $toastClass ?>">
            <strong class="me-auto">Amazonix</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <?= htmlspecialchars($toast['message']) ?>
        </div>
    </div>
</div>
<?php endif; ?>
