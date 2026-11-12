<?php
/**
 * Edit Existing Transaction
 * Only allows editing transactions owned by the logged-in user.
 */

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$userId = (int)$_SESSION['user_id'];
$txId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Fetch transaction and verify ownership
$stmt = $pdo->prepare(
    'SELECT id, category_id, title, amount, type, transaction_date, description
     FROM transactions
     WHERE id = ? AND user_id = ?
     LIMIT 1'
);
$stmt->execute([$txId, $userId]);
$transaction = $stmt->fetch();

if (!$transaction) {
    setFlash('danger', 'Transaction not found.');
    header('Location: ' . url('transactions/index.php'));
    exit;
}

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY id ASC')->fetchAll();

$errors = [];
$title = $transaction['title'];
$amount = $transaction['amount'];
$type = $transaction['type'];
$categoryId = (int)$transaction['category_id'];
$transactionDate = $transaction['transaction_date'];
$description = $transaction['description'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $title = trim($_POST['title'] ?? '');
    $amount = $_POST['amount'] ?? '';
    $type = $_POST['type'] ?? 'expense';
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $transactionDate = trim($_POST['transaction_date'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (strlen($title) < 2) {
        $errors[] = 'Title must be at least 2 characters.';
    }
    if (!is_numeric($amount) || (float)$amount <= 0) {
        $errors[] = 'Amount must be a number greater than zero.';
    }
    if (!in_array($type, ['income', 'expense'], true)) {
        $errors[] = 'Invalid transaction type.';
    }
    if ($categoryId <= 0) {
        $errors[] = 'Please select a valid category.';
    }
    $dt = DateTime::createFromFormat('Y-m-d', $transactionDate);
    if (!$dt || $dt->format('Y-m-d') !== $transactionDate) {
        $errors[] = 'Please select a valid date.';
    }

    if (empty($errors)) {
        $catCheck = $pdo->prepare('SELECT id FROM categories WHERE id = ? LIMIT 1');
        $catCheck->execute([$categoryId]);
        if (!$catCheck->fetch()) {
            $errors[] = 'Selected category does not exist.';
        } else {
            $updateStmt = $pdo->prepare(
                'UPDATE transactions
                 SET category_id = ?, title = ?, amount = ?, type = ?, transaction_date = ?, description = ?
                 WHERE id = ? AND user_id = ?'
            );
            $updateStmt->execute([
                $categoryId,
                $title,
                round((float)$amount, 2),
                $type,
                $transactionDate,
                $description !== '' ? $description : null,
                $txId,
                $userId,
            ]);

            setFlash('success', 'Transaction updated successfully.');
            header('Location: ' . url('transactions/index.php'));
            exit;
        }
    }
}

$pageTitle = 'Edit Transaction';
$activePage = 'transactions';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-12 col-lg-7">
    <div class="card">
      <div class="card-header py-3">
        <h6 class="mb-0 fw-bold">Edit Transaction</h6>
      </div>
      <div class="card-body p-4">
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger small">
            <ul class="mb-0 ps-3">
              <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form id="transactionForm" method="POST" action="">
          <?= csrfField() ?>
          <input type="hidden" name="id" value="<?= (int)$txId ?>">

          <div class="mb-3">
            <label class="form-label small fw-semibold">Transaction Type</label>
            <div class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="typeExpense" value="expense" <?= $type === 'expense' ? 'checked' : '' ?>>
                <label class="form-check-label sw-text-expense fw-semibold" for="typeExpense">Expense</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="typeIncome" value="income" <?= $type === 'income' ? 'checked' : '' ?>>
                <label class="form-check-label sw-text-income fw-semibold" for="typeIncome">Income</label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="title" class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
            <input type="text" id="title" name="title" class="form-control" value="<?= e($title) ?>" required>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <label for="amount" class="form-label small fw-semibold">Amount (<?= e(CURRENCY_SYMBOL) ?>) <span class="text-danger">*</span></label>
              <input type="number" id="amount" name="amount" class="form-control" step="0.01" min="0.01" value="<?= e((string)$amount) ?>" required>
            </div>
            <div class="col-sm-6">
              <label for="transaction_date" class="form-label small fw-semibold">Date <span class="text-danger">*</span></label>
              <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="<?= e($transactionDate) ?>" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="category_id" class="form-label small fw-semibold">Category <span class="text-danger">*</span></label>
            <select id="category_id" name="category_id" class="form-select" required>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= $categoryId === (int)$cat['id'] ? 'selected' : '' ?>>
                  <?= e($cat['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-4">
            <label for="description" class="form-label small fw-semibold">Description (Optional)</label>
            <textarea id="description" name="description" class="form-control" rows="3"><?= e($description) ?></textarea>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">Update Transaction</button>
            <a href="<?= e(url('transactions/index.php')) ?>" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

