/**
 * Budgify Basic JavaScript Helpers
 * Handles Light/Dark theme toggle, mobile sidebar toggle, delete confirmation, and validation.
 */

document.addEventListener('DOMContentLoaded', function () {
  // 1. Light / Dark Theme Toggle
  var themeToggleBtn = document.getElementById('themeToggleBtn');
  var themeIcon = document.getElementById('themeToggleIcon');

  function updateThemeIcon(theme) {
    if (!themeIcon) return;
    themeIcon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
  }

  var currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
  updateThemeIcon(currentTheme);

  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', function () {
      var active = document.documentElement.getAttribute('data-bs-theme') || 'light';
      var next = active === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-bs-theme', next);
      localStorage.setItem('budgify_theme', next);
      updateThemeIcon(next);
    });
  }

  // 2. Mobile Sidebar Toggle
  var sidebar = document.getElementById('appSidebar');
  var overlay = document.getElementById('sidebarOverlay');
  var toggleBtn = document.getElementById('sidebarToggleBtn');

  if (toggleBtn && sidebar && overlay) {
    toggleBtn.addEventListener('click', function () {
      sidebar.classList.toggle('show');
      overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', function () {
      sidebar.classList.remove('show');
      overlay.classList.remove('show');
    });
  }

  // 3. Delete Confirmation on Delete Forms
  var deleteForms = document.querySelectorAll('.js-delete-form');
  deleteForms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var title = form.getAttribute('data-title') || 'this transaction';
      if (!window.confirm('Are you sure you want to delete "' + title + '"?')) {
        e.preventDefault();
      }
    });
  });

  // 4. Basic Client-Side Transaction Amount Validation
  var txForm = document.getElementById('transactionForm');
  if (txForm) {
    txForm.addEventListener('submit', function (e) {
      var amountInput = document.getElementById('amount');
      var titleInput = document.getElementById('title');

      if (titleInput && titleInput.value.trim().length < 2) {
        alert('Please enter a valid title (at least 2 characters).');
        titleInput.focus();
        e.preventDefault();
        return;
      }

      if (amountInput && (isNaN(amountInput.value) || Number(amountInput.value) <= 0)) {
        alert('Please enter an amount greater than 0.');
        amountInput.focus();
        e.preventDefault();
      }
    });
  }
});
