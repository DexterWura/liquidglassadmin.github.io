document.addEventListener('DOMContentLoaded', function () {
    const authForm = document.querySelector('.auth-form');
    if (!authForm) {
        return;
    }

    const toggleButtons = document.querySelectorAll('[data-toggle-password]');
    toggleButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = button.getAttribute('data-toggle-password');
            if (!targetId) {
                return;
            }

            const input = document.getElementById(targetId);
            const icon = button.querySelector('i');
            if (!input || !icon) {
                return;
            }

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
        });
    });

    authForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');

        if (password && confirmPassword && password.value !== confirmPassword.value) {
            alert('Passwords do not match!');
            return;
        }

        window.location.href = 'dashboard.html';
    });
});

