<?php
/**
 * @var View $view
 * @var string $root
 * @var array $user
 */

use App\View\View;

$view->render('partials/header', ['params' => $params ?? null]);

$previous = $_SESSION['previous_data'] ?? [];
$error = $_SESSION['error'] ?? [];
?>
<div class="container amazon-edit-container">
    <!-- Breadcrumb -->
    <div class="amazon-breadcrumb">
        <a href="<?= htmlspecialchars($root)?>/">Strona główna</a>
        <span>›</span>
        <a href="<?= htmlspecialchars($root)?>/account">Twoje konto</a>
        <span>›</span>
        <span>Edytuj profil</span>
    </div>

    <!-- Edit Card -->
    <div class="amazon-edit-card">
        <div class="card-header">
            <h2>Edytuj profil</h2>
            <p>Zaktualizuj swoje dane osobowe i informacje kontaktowe</p>
        </div>

        <div class="card-body">
            <!-- Edit Form -->
            <form id="editForm" action="<?= htmlspecialchars($root)?>/account/update" method="POST" novalidate>
                
                <!-- Personal Information Section -->
                <div class="form-section">
                    <div class="section-title">
                        <span class="section-icon"></span>
                        <span>Dane osobowe</span>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name" class="form-label">
                                Imię <span class="required-star">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="first_name" 
                                id="first_name" 
                                value="<?= htmlspecialchars($previous['first_name'] ?? $user['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                class="form-control <?= isset($error['first_name']) ? 'is-invalid' : '' ?>"
                                required
                                maxlength="50"
                                autofocus
                            >
                            <div class="invalid-feedback"><?= htmlspecialchars($error['first_name'] ?? '') ?></div>
                        </div>

                        <div class="form-group">
                            <label for="last_name" class="form-label">
                                Nazwisko <span class="required-star">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="last_name" 
                                id="last_name" 
                                value="<?= htmlspecialchars($previous['last_name'] ?? $user['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                class="form-control <?= isset($error['last_name']) ? 'is-invalid' : '' ?>"
                                required
                                maxlength="50"
                            >
                            <div class="invalid-feedback"><?= htmlspecialchars($error['last_name'] ?? '') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section">
                    <div class="section-title">
                        <span class="section-icon"></span>
                        <span>Dane kontaktowe</span>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group form-group-full">
                            <label for="email" class="form-label">
                                Adres email <span class="required-star">*</span>
                                <span class="tooltip-icon" title="Twój email będzie używany do logowania i powiadomień">?</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="<?= htmlspecialchars($previous['email'] ?? $user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                class="form-control <?= isset($error['email']) ? 'is-invalid' : '' ?>"
                                required
                            >
                            <div class="form-text">Email będzie używany do logowania i otrzymywania powiadomień</div>
                            <div class="invalid-feedback"><?= htmlspecialchars($error['email'] ?? '') ?></div>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Numer telefonu</label>
                            <input 
                                type="tel" 
                                name="phone" 
                                id="phone" 
                                value="<?= htmlspecialchars($previous['phone'] ?? $user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                class="form-control <?= isset($error['phone']) ? 'is-invalid' : '' ?>"
                                placeholder="+48 123 456 789"
                            >
                            <div class="form-text">Format: +48 XXX XXX XXX</div>
                            <div class="invalid-feedback"><?= htmlspecialchars($error['phone'] ?? '') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Address Information Section -->
                <div class="form-section">
                    <div class="section-title">
                        <span class="section-icon"></span>
                        <span>Adres zamieszkania</span>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="country" class="form-label">Kraj</label>
                            <input 
                                type="text" 
                                name="country" 
                                id="country" 
                                value="<?= htmlspecialchars($previous['country'] ?? $user['country'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                class="form-control <?= isset($error['country']) ? 'is-invalid' : '' ?>"
                                placeholder="Np. Polska"
                            >
                            <div class="invalid-feedback"><?= htmlspecialchars($error['country'] ?? '') ?></div>
                        </div>

                        <div class="form-group">
                            <label for="city" class="form-label">Miasto</label>
                            <input 
                                type="text" 
                                name="city" 
                                id="city" 
                                value="<?= htmlspecialchars($previous['city'] ?? $user['city'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                class="form-control <?= isset($error['city']) ? 'is-invalid' : '' ?>"
                                placeholder="Np. Warszawa"
                            >
                            <div class="invalid-feedback"><?= htmlspecialchars($error['city'] ?? '') ?></div>
                        </div>

                        <div class="form-group form-group-full">
                            <label for="address" class="form-label">Adres</label>
                            <input 
                                type="text" 
                                name="address" 
                                id="address" 
                                value="<?= htmlspecialchars($previous['address'] ?? $user['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                class="form-control <?= isset($error['address']) ? 'is-invalid' : '' ?>"
                                placeholder="Ulica, numer domu/mieszkania"
                            >
                            <div class="form-text">Ulica, numer domu/mieszkania, kod pocztowy</div>
                            <div class="invalid-feedback"><?= htmlspecialchars($error['address'] ?? '') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Account Information Section -->
                <div class="form-section">
                    <div class="section-title">
                        <span class="section-icon"></span>
                        <span>Informacje o koncie</span>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Rola</label>
                            <input 
                                type="text" 
                                value="<?= htmlspecialchars($user['role_name'] ?? 'Klient', ENT_QUOTES, 'UTF-8') ?>" 
                                class="form-control" 
                                disabled
                            >
                            <div class="form-text">Rola nie może być zmieniona</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Data dołączenia</label>
                            <input 
                                type="text" 
                                value="<?= isset($user['created_at']) ? date('d.m.Y', strtotime($user['created_at'])) : 'N/A' ?>" 
                                class="form-control" 
                                disabled
                            >
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="<?= htmlspecialchars($root) ?>/account" class="btn-secondary">Anuluj</a>
                    <button type="submit" class="btn-save" id="submitBtn">Zapisz zmiany</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Form validation and submission
    const form = document.getElementById('editForm');
    const submitBtn = document.getElementById('submitBtn');

    // Validation functions
    function validateEmail(email) {
        const re = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
        return re.test(email);
    }

    function validatePhone(phone) {
        if (!phone) return true;
        const re = /^[+\d\s\-()]{9,}$/;
        return re.test(phone);
    }

    function validateRequired(value) {
        return value.trim().length > 0;
    }

    // Real-time validation
    const inputs = ['first_name', 'last_name', 'email', 'phone'];
    
    inputs.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            input.addEventListener('blur', function() {
                validateField(field);
            });
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(field);
                }
            });
        }
    });

    function validateField(field) {
        const input = document.getElementById(field);
        const feedback = input.nextElementSibling;
        
        switch(field) {
            case 'first_name':
            case 'last_name':
                if (!validateRequired(input.value)) {
                    setInvalid(input, feedback, 'To pole jest wymagane');
                    return false;
                }
                break;
            case 'email':
                if (!validateRequired(input.value)) {
                    setInvalid(input, feedback, 'Email jest wymagany');
                    return false;
                }
                if (!validateEmail(input.value)) {
                    setInvalid(input, feedback, 'Podaj poprawny adres email');
                    return false;
                }
                break;
            case 'phone':
                if (input.value && !validatePhone(input.value)) {
                    setInvalid(input, feedback, 'Podaj poprawny numer telefonu');
                    return false;
                }
                break;
        }
        
        setValid(input, feedback);
        return true;
    }

    function setInvalid(input, feedback, message) {
        input.classList.add('is-invalid');
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = message;
        }
    }

    function setValid(input, feedback) {
        input.classList.remove('is-invalid');
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = '';
        }
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate all fields
        inputs.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }
        
        // Show loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
    });

    // Auto-save warning
    let formChanged = false;
    form.addEventListener('input', function() {
        if (!formChanged) {
            formChanged = true;
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = 'Masz niezapisane zmiany. Czy na pewno chcesz opuścić stronę?';
                    return e.returnValue;
                }
            });
        }
    });

    // Remove warning on form submit
    form.addEventListener('submit', function() {
        formChanged = false;
    });
</script>

<?php
$view->render('partials/footer');

unset($_SESSION['previous_data']);
unset($_SESSION['error']);
?>