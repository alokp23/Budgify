<?php
/**
 * Budgify Dashboard
 * Shows total income, total expenses, remaining balance, monthly summary, and recent transactions.
 */

require_once __DIR__ . '/includes/auth.php';
requireLogin();

$userId = (int)$_SESSION['user_id'];
$currentMonth = (int)date('n');
$currentYear = (int)date('Y');

// 1. All-time totals for this user
$stmt = $pdo->prepare(
    'SELECT
        COALESCE(SUM(CASE WHEN type = "income" THEN amount ELSE 0 END), 0) AS total_income,
        COALESCE(SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END), 0) AS total_expense
     FROM transactions
     WHERE user_id = ?'
);
$stmt->execute([$userId]);
$totals = $stmt->fetch();

$totalIncome = (float)$totals['total_income'];
$totalExpense = (float)$totals['total_expense'];
$remainingBalance = $totalIncome - $totalExpense;

// 2. Current month summary
$monthStmt = $pdo->prepare(
    'SELECT
        COALESCE(SUM(CASE WHEN type = "income" THEN amount ELSE 0 END), 0) AS month_income,
        COALESCE(SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END), 0) AS month_expense,
        COUNT(*) AS month_count
     FROM transactions
     WHERE user_id = ? AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?'
);
$monthStmt->execute([$userId, $currentMonth, $currentYear]);
$monthSummary = $monthStmt->fetch();

$monthIncome = (float)$monthSummary['month_income'];
$monthExpense = (float)$monthSummary['month_expense'];
$monthNet = $monthIncome - $monthExpense;

// 3. Category-wise expense breakdown for current month
$catStmt = $pdo->prepare(
    'SELECT c.name AS category_name, COALESCE(SUM(t.amount), 0) AS total_spent
     FROM transactions t
     INNER JOIN categories c ON t.category_id = c.id
     WHERE t.user_id = ? AND t.type = "expense"
       AND MONTH(t.transaction_date) = ? AND YEAR(t.transaction_date) = ?
     GROUP BY c.id, c.name
     ORDER BY total_spent DESC'
);
$catStmt->execute([$userId, $currentMonth, $currentYear]);
$categorySummary = $catStmt->fetchAll();

// 4. Recent 6 transactions
$recentStmt = $pdo->prepare(
    'SELECT t.id, t.title, t.amount, t.type, t.transaction_date, c.name AS category_name
     FROM transactions t
     INNER JOIN categories c ON t.category_id = c.id
     WHERE t.user_id = ?
     ORDER BY t.transaction_date DESC, t.id DESC
     LIMIT 6'
);
$recentStmt->execute([$userId]);
$recentTransactions = $recentStmt->fetchAll();

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-12 col-md-4">
    <div class="card p-3 h-100">
      <div class="d-flex justify-content-between align-items-center gap-2">
        <div>
          <div class="text-muted small fw-semibold">Total Income</div>
          <div class="fs-4 fw-bold sw-text-income mt-1"><?= e(formatMoney($totalIncome)) ?></div>
        </div>
        <span class="summary-icon summary-icon-income">
          <i class="bi bi-arrow-down-left-circle"></i>
        </span>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-4">
    <div class="card p-3 h-100">
      <div class="d-flex justify-content-between align-items-center gap-2">
        <div>
          <div class="text-muted small fw-semibold">Total Expenses</div>
          <div class="fs-4 fw-bold sw-text-expense mt-1"><?= e(formatMoney($totalExpense)) ?></div>
        </div>
        <span class="summary-icon summary-icon-expense">
          <i class="bi bi-arrow-up-right-circle"></i>
        </span>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-4">
    <div class="card p-3 h-100">
      <div class="d-flex justify-content-between align-items-center gap-2">
        <div>
          <div class="text-muted small fw-semibold">Remaining Balance</div>
          <div class="fs-4 fw-bold <?= $remainingBalance >= 0 ? 'text-primary' : 'sw-text-expense' ?> mt-1">
            <?= e(formatMoney($remainingBalance)) ?>
          </div>
        </div>
        <span class="summary-icon summary-icon-accent">
          <i class="bi bi-wallet2"></i>
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Monthly Summary & Category Breakdown -->
<div class="row g-3 mb-4">
  <div class="col-12 col-lg-5">
    <div class="card h-100">
      <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h6 class="mb-0 fw-bold">Monthly Summary (<?= e(date('F Y')) ?>)</h6>
        <a href="<?= e(url('reports.php')) ?>" class="btn btn-sm btn-outline-primary">Full Report</a>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted">Income This Month</span>
          <strong class="sw-text-income"><?= e(formatMoney($monthIncome)) ?></strong>
        </div>
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted">Expenses This Month</span>
          <strong class="sw-text-expense"><?= e(formatMoney($monthExpense)) ?></strong>
        </div>
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted">Net Monthly Balance</span>
          <strong class="<?= $monthNet >= 0 ? 'text-primary' : 'sw-text-expense' ?>"><?= e(formatMoney($monthNet)) ?></strong>
        </div>
        <div class="d-flex justify-content-between pt-2">
          <span class="text-muted">Transactions Recorded</span>
          <strong><?= (int)$monthSummary['month_count'] ?></strong>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="card h-100">
      <div class="card-header py-3">
        <h6 class="mb-0 fw-bold">This Month's Expenses by Category</h6>
      </div>
      <div class="card-body">
        <?php if (empty($categorySummary)): ?>
          <p class="text-muted small mb-0">No expenses recorded in <?= e(date('F Y')) ?> yet.</p>
        <?php else: ?>
          <?php foreach ($categorySummary as $cat): ?>
            <?php
              $catAmount = (float)$cat['total_spent'];
              $percent = $monthExpense > 0 ? round(($catAmount / $monthExpense) * 100) : 0;
            ?>
            <div class="mb-3">
              <div class="d-flex justify-content-between small mb-1 gap-2">
                <span class="fw-semibold"><?= e($cat['category_name']) ?></span>
                <span><?= e(formatMoney($catAmount)) ?> (<?= $percent ?>%)</span>
              </div>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-primary" style="width: <?= $percent ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Recent Transactions Table -->
<div class="card">
  <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h6 class="mb-0 fw-bold">Recent Transactions</h6>
    <a href="<?= e(url('transactions/index.php')) ?>" class="btn btn-sm btn-outline-secondary">View All</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>Title</th>
          <th>Category</th>
          <th>Type</th>
          <th>Date</th>
          <th class="text-end">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentTransactions)): ?>
          <tr>
            <td colspan="5" class="text-center py-4 text-muted">
              No transactions found. <a href="<?= e(url('transactions/add.php')) ?>" class="text-primary">Add your first transaction</a>.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($recentTransactions as $tx): ?>
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
