<?php
/**
 * User Login Page
 */

require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . url('dashboard.php'));
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        // Look up user by email using PDO prepared statement
        $stmt = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Initialize PHP password_hash() for the seeded demo account on first login
        if ($user && $user['email'] === 'student@budgify.com' && $user['password'] === '__PENDING_DEMO_HASH__') {
            $demoHash = password_hash('Student@123', PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $upd->execute([$demoHash, $user['id']]);
            $user['password'] = $demoHash;
        }

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            header('Location: ' . url('dashboard.php'));
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Login';
$isAuthPage = true;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
  <div class="card auth-card p-4">
    <div class="d-flex justify-content-end mb-2">
      <button type="button" id="themeToggleBtn" class="btn btn-outline-secondary btn-sm" title="Switch Light/Dark Theme" aria-label="Toggle Theme">
        <i id="themeToggleIcon" class="bi bi-moon"></i>
      </button>
    </div>

    <div class="text-center mb-4">
      <div class="summary-icon summary-icon-accent mb-2" style="width: 52px; height: 52px; font-size: 1.45rem;">
        <i class="bi bi-wallet2"></i>
      </div>
      <h4 class="fw-bold mb-1">Budgify</h4>
      <p class="text-muted small mb-0">Sign in to manage your personal expenses</p>
    </div>

    <?php if (!empty($_GET['registered'])): ?>
      <div class="alert alert-success small py-2">Account created! Please sign in below.</div>
    <?php endif; ?>

    <?php if (!empty($_GET['logged_out'])): ?>
      <div class="alert alert-info small py-2">You have been logged out successfully.</div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
      <div class="alert alert-danger small py-2"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="off">
      <?= csrfField() ?>

      <div class="mb-3">
        <label for="email" class="form-label small fw-semibold">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= e($email) ?>" placeholder="Enter your email" autocomplete="off" required autofocus>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label small fw-semibold">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" autocomplete="new-password" required>
      </div>

      <button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
    </form>

    <p class="text-center small text-muted mb-0">
      New user? <a href="<?= e(url('auth/register.php')) ?>" class="text-decoration-none fw-semibold text-primary">Create an account</a>
    </p>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
