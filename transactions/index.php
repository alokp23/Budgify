<?php
/**
 * View All Transactions
 * Supports optional filtering by type and category.
 */

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$userId = (int)$_SESSION['user_id'];

// Read optional filters from GET query string
$filterType = $_GET['type'] ?? '';
$filterCategory = (int)($_GET['category_id'] ?? 0);

// Fetch categories for filter dropdown
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY id ASC')->fetchAll();

// Build prepared query scoped to current user
$sql = 'SELECT t.id, t.title, t.amount, t.type, t.transaction_date, t.description, c.name AS category_name
        FROM transactions t
        INNER JOIN categories c ON t.category_id = c.id
        WHERE t.user_id = ?';
$params = [$userId];

if (in_array($filterType, ['income', 'expense'], true)) {
    $sql .= ' AND t.type = ?';
    $params[] = $filterType;
}
if ($filterCategory > 0) {
    $sql .= ' AND t.category_id = ?';
    $params[] = $filterCategory;
}

$sql .= ' ORDER BY t.transaction_date DESC, t.id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

$pageTitle = 'All Transactions';
$activePage = 'transactions';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <p class="text-muted mb-0 small">Showing <?= count($transactions) ?> transaction(s)</p>
  <a href="<?= e(url('transactions/add.php')) ?>" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg me-1"></i> Add Transaction
  </a>
</div>

<!-- Simple Filter Bar -->
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" action="" class="row g-3 align-items-end">
      <div class="col-12 col-sm-6 col-md-4">
        <label for="type" class="form-label small fw-semibold mb-1">Type</label>
        <select id="type" name="type" class="form-select">
          <option value="">All Types</option>
          <option value="income" <?= $filterType === 'income' ? 'selected' : '' ?>>Income</option>
          <option value="expense" <?= $filterType === 'expense' ? 'selected' : '' ?>>Expense</option>
        </select>
      </div>

      <div class="col-12 col-sm-6 col-md-4">
        <label for="category_id" class="form-label small fw-semibold mb-1">Category</label>
        <select id="category_id" name="category_id" class="form-select">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= $filterCategory === (int)$cat['id'] ? 'selected' : '' ?>>
              <?= e($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-md-4 d-flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary px-3">Filter</button>
        <a href="<?= e(url('transactions/index.php')) ?>" class="btn btn-outline-secondary px-3">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- Transactions Table -->
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>Title</th>
          <th>Category</th>
          <th>Type</th>
          <th>Date</th>
          <th>Description</th>
          <th class="text-end">Amount</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($transactions)): ?>
          <tr>
            <td colspan="7" class="text-center py-4 text-muted">
              No transactions found. <a href="<?= e(url('transactions/add.php')) ?>" class="text-primary">Add a transaction</a>.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($transactions as $tx): ?>
            <tr>
              <td class="fw-semibold"><?= e($tx['title']) ?></td>
              <td><span class="badge sw-badge-neutral"><?= e($tx['category_name']) ?></span></td>
              <td>
                <?php if ($tx['type'] === 'income'): ?>
                  <span class="badge sw-badge-income">Income</span>
                <?php else: ?>
                  <span class="badge sw-badge-expense">Expense</span>
                <?php endif; ?>
              </td>
              <td class="text-muted small text-nowrap"><?= e(date('d M Y', strtotime($tx['transaction_date']))) ?></td>
              <td class="text-muted small"><?= e($tx['description'] ?: '—') ?></td>
              <td class="text-end fw-bold text-nowrap <?= $tx['type'] === 'income' ? 'sw-text-income' : 'sw-text-expense' ?>">
                <?= $tx['type'] === 'income' ? '+' : '-' ?><?= e(formatMoney((float)$tx['amount'])) ?>
              </td>
              <td class="text-end text-nowrap">
                <a href="<?= e(url('transactions/edit.php?id=' . (int)$tx['id'])) ?>" class="btn btn-outline-secondary btn-sm" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="<?= e(url('transactions/delete.php')) ?>" class="d-inline js-delete-form" data-title="<?= e($tx['title']) ?>">
                  <?= csrfField() ?>
                  <input type="hidden" name="id" value="<?= (int)$tx['id'] ?>">
                  <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
