<?php
/**
 * Shared Page Footer
 */
$isAuthPage = $isAuthPage ?? false;
?>
<?php if (!$isAuthPage): ?>
    </main>
    <footer class="app-footer px-4 py-3 small d-flex justify-content-between flex-wrap gap-2">
      <span>Budgify &mdash; Personal Expense Tracker (PHP &amp; MySQL Mini Project)</span>
      <span>&copy; <?= date('Y') ?></span>
    </footer>
  </div><!-- /.main-content -->
</div><!-- /.app-wrapper -->
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(url('assets/js/script.js')) ?>"></script>
</body>
</html>

