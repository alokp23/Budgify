<?php
/**
 * Application Entry Point
 * Redirects logged-in users to Dashboard, or guests to Login page.
 */

require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . url('dashboard.php'));
} else {
    header('Location: ' . url('auth/login.php'));
}
exit;

