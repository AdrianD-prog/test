function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    }
}

// Opcjonalnie: dodanie stylu dla przycisku podglądu hasła
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('togglePasswordBtn');
    if (toggleBtn) {
        toggleBtn.style.cursor = 'pointer';
        toggleBtn.style.fontSize = '1.2rem';
        toggleBtn.style.color = '#666';
    }
});