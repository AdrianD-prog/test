function togglePasswordVisibility(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + 'Icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    }
}

// Dodanie stylu dla przycisków podglądu
document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('[onclick^="togglePasswordVisibility"]');
    toggleButtons.forEach(btn => {
        btn.style.cursor = 'pointer';
        btn.style.fontSize = '1.2rem';
        btn.style.color = '#666';
        
        btn.addEventListener('mouseenter', function() {
            this.style.color = '#0066c0';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.color = '#666';
        });
    });
});