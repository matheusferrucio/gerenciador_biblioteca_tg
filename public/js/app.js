/**
 * Biblioteca TG — Application JavaScript
 * Form validation, UI toggles, and utilities
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar Toggle (Mobile) ──
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
    }

    // ── Flash Message Auto-Dismiss ──
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        setTimeout(function () {
            flashMessage.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            flashMessage.style.opacity = '0';
            flashMessage.style.transform = 'translateY(-10px)';
            setTimeout(function () {
                flashMessage.remove();
            }, 400);
        }, 4000);
    }

    // ── Delete / Confirm Dialogs ──
    const confirmButtons = document.querySelectorAll('.btn-confirm');
    confirmButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const message = this.getAttribute('data-confirm') || 'Tem certeza que deseja continuar?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ── Form Validation ──
    const forms = document.querySelectorAll('form.form, #loginForm');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');

            // Clear previous error styles
            form.querySelectorAll('.input-error').forEach(function (el) {
                el.classList.remove('input-error');
            });

            requiredFields.forEach(function (field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('input-error');
                    field.style.borderColor = '#dc2626';
                    field.style.boxShadow = '0 0 0 3px rgba(220, 38, 38, 0.12)';
                }
            });

            // Email validation
            const emailField = form.querySelector('input[type="email"]');
            if (emailField && emailField.value.trim()) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value.trim())) {
                    isValid = false;
                    emailField.classList.add('input-error');
                    emailField.style.borderColor = '#dc2626';
                    emailField.style.boxShadow = '0 0 0 3px rgba(220, 38, 38, 0.12)';
                }
            }

            // Password minimum length
            const passwordField = form.querySelector('input[type="password"][required]');
            if (passwordField && passwordField.value.length > 0 && passwordField.value.length < 6) {
                isValid = false;
                passwordField.classList.add('input-error');
                passwordField.style.borderColor = '#dc2626';
                passwordField.style.boxShadow = '0 0 0 3px rgba(220, 38, 38, 0.12)';
            }

            if (!isValid) {
                e.preventDefault();
                // Focus first invalid field
                const firstError = form.querySelector('.input-error');
                if (firstError) firstError.focus();
            }
        });
    });

    // ── Clear error style on input ──
    document.querySelectorAll('input, select, textarea').forEach(function (field) {
        field.addEventListener('input', function () {
            this.classList.remove('input-error');
            this.style.borderColor = '';
            this.style.boxShadow = '';
        });
    });

    // ── Number input min enforcement ──
    document.querySelectorAll('input[type="number"][min]').forEach(function (input) {
        input.addEventListener('change', function () {
            const min = parseInt(this.getAttribute('min'));
            if (parseInt(this.value) < min) {
                this.value = min;
            }
        });
    });
});
