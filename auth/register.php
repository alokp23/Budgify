<?php
/**
 * User Registration Page
 */

require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . url('dashboard.php'));
    exit;
}

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($name) < 2) {
        $errors[] = 'Name must be at least 2 characters long.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        // Check if email already exists
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->execute([$email]);

        if ($check->fetch()) {
            $errors[] = 'An account with this email already exists.';
        } else {
            // Hash password securely before saving
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hashedPassword]);

            header('Location: ' . url('auth/login.php?registered=1'));
            exit;
        }
    }
}

$pageTitle = 'Register';
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
      <h4 class="fw-bold mb-1">Create Account</h4>
      <p class="text-muted small mb-0">Start tracking your income and expenses with Budgify</p>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger small py-2">
        <ul class="mb-0 ps-3">
          <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <?= csrfField() ?>

      <div class="mb-3">
        <label for="name" class="form-label small fw-semibold">Full Name</label>
        <input type="text" id="name" name="name" class="form-control" value="<?= e($name) ?>" placeholder="Rahul Sharma" required autofocus>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label small fw-semibold">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= e($email) ?>" placeholder="you@example.com" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label small fw-semibold">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 6 characters" minlength="6" required>
      </div>

      <div class="mb-3">
        <label for="confirm_password" class="form-label small fw-semibold">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" minlength="6" required>
      </div>

      <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>
    </form>

    <p class="text-center small text-muted mb-0">
      Already have an account? <a href="<?= e(url('auth/login.php')) ?>" class="text-decoration-none fw-semibold">Sign in</a>
    </p>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

