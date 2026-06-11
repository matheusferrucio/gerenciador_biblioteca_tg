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

    // ── Password Validation (User Registration) ──
    initPasswordValidation();

    // ── Phone Mask (Brazilian format) ──
    initPhoneMask();

    // ── Book Filters ──
    initBookFilters();

    // ── Extend Loan Modal ──
    initExtendModal();

    // ── Confirm Return Modal ──
    initReturnModal();
});

/**
 * Logic for the custom Return Confirmation Modal
 */
function initReturnModal() {
    const modal = document.getElementById('confirmReturnModal');
    if (!modal) return;

    const returnButtons = document.querySelectorAll('.btn-return');
    const closeButtons  = modal.querySelectorAll('.modal-close, .modal-close-btn');
    const confirmBtn    = document.getElementById('confirmReturnBtn');
    const bookTitleSpan = document.getElementById('returnBookTitle');

    function openModal(e) {
        e.preventDefault(); // Stop immediate redirection
        
        const btn = e.currentTarget;
        const title = btn.getAttribute('data-title') || 'este livro';
        const url = btn.getAttribute('href');

        bookTitleSpan.textContent = title;
        confirmBtn.setAttribute('href', url);

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    returnButtons.forEach(btn => btn.addEventListener('click', openModal));
    closeButtons.forEach(btn => btn.addEventListener('click', closeModal));

    // Close on overlay click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
}

/**
 * Logic for the Loan Extension (Prorrogação) Modal
 */
function initExtendModal() {
    const modal = document.getElementById('extendModal');
    if (!modal) return;

    const extendButtons = document.querySelectorAll('.btn-extend');
    const closeButtons  = modal.querySelectorAll('.modal-close, .modal-close-btn');
    const form          = document.getElementById('extendForm');
    const loanIdInput   = document.getElementById('extendLoanId');
    const bookTitleSpan = document.getElementById('extendBookTitle');
    const dateInput     = document.getElementById('new_due_date');
    const quickOpts     = modal.querySelectorAll('.btn-extend-opt');

    let originalDueDate = '';

    function openModal(e) {
        const btn = e.currentTarget;
        const id = btn.getAttribute('data-id');
        const due = btn.getAttribute('data-due');
        const title = btn.getAttribute('data-title');

        originalDueDate = due;
        loanIdInput.value = id;
        bookTitleSpan.textContent = title;
        
        // Update form action URL structure if necessary, 
        // but here we use a hidden input and static action /loans/extend
        form.action = form.action.split('?')[0] + '/' + id;

        // Set min date to original due date
        dateInput.min = due;
        dateInput.value = due;

        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scroll
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Quick add days logic
    quickOpts.forEach(opt => {
        opt.addEventListener('click', () => {
            const daysToAdd = parseInt(opt.getAttribute('data-days'));
            const baseDate = new Date(originalDueDate + 'T12:00:00'); // Use original as base
            baseDate.setDate(baseDate.getDate() + daysToAdd);
            
            // Format to YYYY-MM-DD
            const newDateStr = baseDate.toISOString().split('T')[0];
            dateInput.value = newDateStr;
        });
    });

    extendButtons.forEach(btn => btn.addEventListener('click', openModal));
    closeButtons.forEach(btn => btn.addEventListener('click', closeModal));

    // Close on overlay click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
}

/**
 * Initialize client-side book filtering
 */
function initBookFilters() {
    var table = document.getElementById('booksFilterTable');
    var filterBar = document.getElementById('bookFilterBar');
    if (!table || !filterBar) return;

    var searchInput = document.getElementById('bookSearch');
    var catSelect   = document.getElementById('bookCategory');
    var availSelect = document.getElementById('bookAvailability');
    var clearBtn    = document.getElementById('clearBookFilters');
    var countEl     = document.getElementById('bookFilterCount');

    var rows = table.querySelectorAll('tbody tr');
    var totalRows = rows.length;

    function applyFilters() {
        var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        var cat   = catSelect ? catSelect.value : '';
        var avail = availSelect ? availSelect.value : '';

        var visible = 0;

        rows.forEach(function (row) {
            var title    = row.getAttribute('data-title') || '';
            var author   = row.getAttribute('data-author') || '';
            var isbn     = row.getAttribute('data-isbn') || '';
            var rowCat   = row.getAttribute('data-cat') || '';
            var rowAvail = row.getAttribute('data-available') || '';

            var show = true;

            // Search by text (Title, Author or ISBN)
            if (query && !title.includes(query) && !author.includes(query) && !isbn.includes(query)) {
                show = false;
            }

            // Filter by Category
            if (show && cat && rowCat !== cat) {
                show = false;
            }

            // Filter by Availability
            if (show && avail && rowAvail !== avail) {
                show = false;
            }

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Update count
        if (countEl) {
            var hasFilter = query || cat || avail;
            if (hasFilter) {
                countEl.innerHTML = '<strong>' + visible + '</strong> de ' + totalRows + ' livro(s) encontrado(s)';
            } else {
                countEl.innerHTML = '';
            }
        }
    }

    // Bind events
    [searchInput, catSelect, availSelect].forEach(function (el) {
        if (el) {
            el.addEventListener('input', applyFilters);
            el.addEventListener('change', applyFilters);
        }
    });

    // Clear filters
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (catSelect) catSelect.value = '';
            if (availSelect) availSelect.value = '';
            applyFilters();
        });
    }
}

/**
 * Applies a Brazilian phone mask (00) 00000-0000 to #phone inputs
 */
function initPhoneMask() {
    const phoneInputs = document.querySelectorAll('#phone');
    
    phoneInputs.forEach(input => {
        input.addEventListener('input', e => {
            let value = e.target.value.replace(/\D/g, ""); // Remove non-digits
            
            // Limit to 11 digits (2 for area code + 9 for number)
            if (value.length > 11) value = value.slice(0, 11);

            // Apply mask
            if (value.length > 10) {
                // (00) 00000-0000
                value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
            } else if (value.length > 5) {
                // (00) 0000-0000 (standard for 8-digit landlines if needed)
                value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
            } else if (value.length > 2) {
                // (00) 000
                value = value.replace(/^(\d{2})(\d{0,5})/, "($1) $2");
            } else if (value.length > 0) {
                // (00
                value = value.replace(/^(\d{0,2})/, "($1");
            }

            e.target.value = value;
        });
    });
}

/**
 * Validates password requirements in real-time
 */
function initPasswordValidation() {
    const passwordInput = document.getElementById('password');
    const requirementsList = document.getElementById('passwordRequirements');
    const reqContainer = document.getElementById('passwordReqContainer');
    const submitBtn = document.getElementById('btnSubmitUser');
    
    if (!passwordInput || !requirementsList) return;

    const requirements = {
        length:  { el: document.getElementById('req-length'), regex: /.{8,}/ },
        upper:   { el: document.getElementById('req-upper'),  regex: /[A-Z]/ },
        lower:   { el: document.getElementById('req-lower'),  regex: /[a-z]/ },
        number:  { el: document.getElementById('req-number'), regex: /[0-9]/ },
        special: { el: document.getElementById('req-special'), regex: /[^A-Za-z0-9]/ }
    };

    function validate() {
        const val = passwordInput.value;
        let allValid = true;

        // Show container if hidden (used in Edit form)
        if (reqContainer && val.length > 0) {
            reqContainer.style.display = 'block';
        } else if (reqContainer && val.length === 0) {
            reqContainer.style.display = 'none';
        }

        for (const key in requirements) {
            const req = requirements[key];
            const isValid = req.regex.test(val);
            
            if (isValid) {
                req.el.classList.add('valid');
                req.el.classList.remove('invalid');
            } else {
                req.el.classList.remove('valid');
                if (val.length > 0) {
                    req.el.classList.add('invalid');
                    allValid = false;
                } else {
                    req.el.classList.remove('invalid');
                    allValid = false;
                }
            }
        }

        // Apply visual feedback to input
        if (val.length > 0) {
            if (allValid) {
                passwordInput.classList.add('input-success');
                passwordInput.classList.remove('input-error');
            } else {
                passwordInput.classList.add('input-error');
                passwordInput.classList.remove('input-success');
            }
        } else {
            passwordInput.classList.remove('input-success', 'input-error');
        }

        return allValid;
    }

    passwordInput.addEventListener('input', validate);
    
    // Prevent submission if password exists but is invalid
    const form = passwordInput.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const val = passwordInput.value;
            // On create, it's required. On edit, if typed, must be valid.
            if (passwordInput.hasAttribute('required') || val.length > 0) {
                if (!validate()) {
                    e.preventDefault();
                    passwordInput.focus();
                }
            }
        });
    }
}

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
