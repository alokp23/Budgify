<?php
/**
 * Monthly Reports Page
 * Filters transactions by selected month/year and displays total income and expenses.
 */

require_once __DIR__ . '/includes/auth.php';
requireLogin();

$userId = (int)$_SESSION['user_id'];
$selectedMonth = (int)($_GET['month'] ?? date('n'));
$selectedYear = (int)($_GET['year'] ?? date('Y'));

if ($selectedMonth < 1 || $selectedMonth > 12) {
    $selectedMonth = (int)date('n');
}
if ($selectedYear < 2000 || $selectedYear > 2100) {
    $selectedYear = (int)date('Y');
}

// 1. Fetch total income and expenses for selected month
$sumStmt = $pdo->prepare(
    'SELECT
        COALESCE(SUM(CASE WHEN type = "income" THEN amount ELSE 0 END), 0) AS total_income,
        COALESCE(SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END), 0) AS total_expense
     FROM transactions
     WHERE user_id = ? AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?'
);
$sumStmt->execute([$userId, $selectedMonth, $selectedYear]);
$summary = $sumStmt->fetch();

$monthIncome = (float)$summary['total_income'];
$monthExpense = (float)$summary['total_expense'];
$monthBalance = $monthIncome - $monthExpense;

// 2. Fetch all transactions for selected month
$txStmt = $pdo->prepare(
    'SELECT t.id, t.title, t.amount, t.type, t.transaction_date, t.description, c.name AS category_name
     FROM transactions t
     INNER JOIN categories c ON t.category_id = c.id
     WHERE t.user_id = ? AND MONTH(t.transaction_date) = ? AND YEAR(t.transaction_date) = ?
     ORDER BY t.transaction_date DESC, t.id DESC'
);
$txStmt->execute([$userId, $selectedMonth, $selectedYear]);
$transactions = $txStmt->fetchAll();

$periodLabel = date('F Y', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear));

$pageTitle = 'Monthly Reports';
$activePage = 'reports';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Month Selector Card -->
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" action="" class="row g-3 align-items-end">
      <div class="col-12 col-sm-6 col-md-4">
        <label for="month" class="form-label small fw-semibold mb-1">Select Month</label>
        <select id="month" name="month" class="form-select">
          <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= $m === $selectedMonth ? 'selected' : '' ?>>
              <?= e(date('F', mktime(0, 0, 0, $m, 1))) ?>
            </option>
          <?php endfor; ?>
        </select>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <label for="year" class="form-label small fw-semibold mb-1">Select Year</label>
        <select id="year" name="year" class="form-select">
          <?php for ($y = (int)date('Y') - 3; $y <= (int)date('Y') + 1; $y++): ?>
            <option value="<?= $y ?>" <?= $y === $selectedYear ? 'selected' : '' ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>

      <div class="col-12 col-md-5 d-flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary px-4">View Report</button>
        <a href="<?= e(url('reports.php')) ?>" class="btn btn-outline-secondary px-3">Current Month</a>
      </div>
    </form>
  </div>
</div>

<!-- Monthly Totals Summary -->
<div class="row g-3 mb-4">
  <div class="col-12 col-md-4">
    <div class="card p-3 h-100">
      <div class="text-muted small fw-semibold">Income (<?= e($periodLabel) ?>)</div>
      <div class="fs-4 fw-bold sw-text-income mt-1"><?= e(formatMoney($monthIncome)) ?></div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card p-3 h-100">
      <div class="text-muted small fw-semibold">Expenses (<?= e($periodLabel) ?>)</div>
      <div class="fs-4 fw-bold sw-text-expense mt-1"><?= e(formatMoney($monthExpense)) ?></div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card p-3 h-100">
      <div class="text-muted small fw-semibold">Net Savings (<?= e($periodLabel) ?>)</div>
      <div class="fs-4 fw-bold <?= $monthBalance >= 0 ? 'text-primary' : 'sw-text-expense' ?> mt-1">
        <?= e(formatMoney($monthBalance)) ?>
      </div>
    </div>
  </div>
</div>

<!-- Filtered Transactions Table -->
<div class="card">
  <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h6 class="mb-0 fw-bold">Transactions in <?= e($periodLabel) ?></h6>
    <span class="badge sw-badge-neutral"><?= count($transactions) ?> records</span>
  </div>
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
        </tr>
      </thead>
      <tbody>
        <?php if (empty($transactions)): ?>
          <tr>
            <td colspan="6" class="text-center py-4 text-muted">
              No transactions recorded for <?= e($periodLabel) ?>.
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
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
