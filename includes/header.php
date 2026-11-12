<?php
/**
 * Shared Page Header & Sidebar Navigation
 */
$pageTitle = $pageTitle ?? 'Budgify';
$activePage = $activePage ?? '';
$isAuthPage = $isAuthPage ?? false;
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> — Budgify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= e(url('assets/css/style.css')) ?>" rel="stylesheet">
  <script>
    (function () {
      var savedTheme = localStorage.getItem('budgify_theme');
      if (savedTheme === 'dark' || savedTheme === 'light') {
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
      }
    })();
  </script>
</head>
<body>
<?php if (!$isAuthPage): ?>
<div class="app-wrapper">
  <div id="sidebarOverlay" class="sidebar-overlay"></div>

  <!-- Sidebar Navigation -->
  <aside id="appSidebar" class="sidebar">
    <a href="<?= e(url('dashboard.php')) ?>" class="sidebar-brand">
      <span class="sidebar-brand-icon"><i class="bi bi-wallet2"></i></span>
      <span>Budgify</span>
    </a>

    <nav class="sidebar-nav">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="<?= e(url('dashboard.php')) ?>" class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= e(url('transactions/index.php')) ?>" class="nav-link <?= $activePage === 'transactions' ? 'active' : '' ?>">
            <i class="bi bi-list-ul"></i>
            <span>All Transactions</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= e(url('transactions/add.php')) ?>" class="nav-link <?= $activePage === 'add_transaction' ? 'active' : '' ?>">
            <i class="bi bi-plus-circle"></i>
            <span>Add Transaction</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= e(url('reports.php')) ?>" class="nav-link <?= $activePage === 'reports' ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-line"></i>
            <span>Monthly Reports</span>
          </a>
        </li>
      </ul>
    </nav>

    <div class="sidebar-footer">
      <form method="POST" action="<?= e(url('auth/logout.php')) ?>" class="m-0">
        <?= csrfField() ?>
        <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <header class="topbar">
      <div class="d-flex align-items-center gap-2">
        <button type="button" id="sidebarToggleBtn" class="btn btn-outline-secondary btn-sm d-lg-none" aria-label="Toggle Menu">
          <i class="bi bi-list fs-5"></i>
        </button>
        <h5 class="mb-0 fw-semibold"><?= e($pageTitle) ?></h5>
      </div>

      <div class="d-flex align-items-center gap-2 gap-sm-3 flex-wrap">
        <span class="text-muted small d-none d-md-inline">
          <i class="bi bi-person-circle me-1"></i>
          <?= e($_SESSION['user_name'] ?? 'User') ?>
        </span>
        <button type="button" id="themeToggleBtn" class="btn btn-outline-secondary btn-sm" title="Switch Light/Dark Theme" aria-label="Toggle Theme">
          <i id="themeToggleIcon" class="bi bi-moon"></i>
        </button>
        <a href="<?= e(url('transactions/add.php')) ?>" class="btn btn-primary btn-sm text-nowrap">
          <i class="bi bi-plus-lg me-1"></i> Add New
        </a>
      </div>
    </header>

    <main class="content-area">
      <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
          <?= e($flash['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
<?php endif; ?>
