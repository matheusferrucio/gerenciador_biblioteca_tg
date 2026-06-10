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

    // ── Loan Filters (Dashboard + Loans Index) ──
    initLoanFilters();
});

/**
 * Inicializa os filtros de empréstimos
 * Funciona em toda página que tem #loansFilterTable e os inputs de filtro
 * 
 * A função é chamado no final dentro do DOMContentLoaded
 */
function initLoanFilters() {
    var table = document.getElementById('loansFilterTable');
    var filterBar = document.getElementById('loanFilterBar');
    if (!table || !filterBar) return;

    var statusSelect = document.getElementById('filterStatus');
    var loanFrom = document.getElementById('filterLoanFrom');
    var loanTo = document.getElementById('filterLoanTo');
    var dueFrom = document.getElementById('filterDueFrom');
    var dueTo = document.getElementById('filterDueTo');
    var clearBtn = document.getElementById('clearFilters');
    var countEl = document.getElementById('filterCount');

    var rows = table.querySelectorAll('tbody tr');
    var totalRows = rows.length;

    function applyFilters() {
        var status = statusSelect ? statusSelect.value : '';
        var lfrom = loanFrom ? loanFrom.value : '';
        var lto = loanTo ? loanTo.value : '';
        var dfrom = dueFrom ? dueFrom.value : '';
        var dto = dueTo ? dueTo.value : '';

        var visible = 0;

        rows.forEach(function (row) {
            var rowStatus = row.getAttribute('data-status') || '';
            var rowLoanDate = row.getAttribute('data-loan-date') || '';
            var rowDueDate = row.getAttribute('data-due-date') || '';

            var show = true;

            // Filter by status
            if (status && rowStatus !== status) {
                show = false;
            }

            // Filter by loan date range
            if (show && lfrom && rowLoanDate < lfrom) {
                show = false;
            }
            if (show && lto && rowLoanDate > lto) {
                show = false;
            }

            // Filter by due date range
            if (show && dfrom && rowDueDate < dfrom) {
                show = false;
            }
            if (show && dto && rowDueDate > dto) {
                show = false;
            }

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Update count
        if (countEl) {
            var hasFilter = status || lfrom || lto || dfrom || dto;
            if (hasFilter) {
                countEl.innerHTML = '<strong>' + visible + '</strong> de ' + totalRows + ' empréstimo(s) encontrado(s)';
            } else {
                countEl.innerHTML = '';
            }
        }
    }

    // Bind change events to all filter inputs
    [statusSelect, loanFrom, loanTo, dueFrom, dueTo].forEach(function (el) {
        if (el) {
            el.addEventListener('change', applyFilters);
            el.addEventListener('input', applyFilters);
        }
    });

    // Clear filters button
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (statusSelect) statusSelect.value = '';
            if (loanFrom) loanFrom.value = '';
            if (loanTo) loanTo.value = '';
            if (dueFrom) dueFrom.value = '';
            if (dueTo) dueTo.value = '';
            applyFilters();
        });
    }
}
