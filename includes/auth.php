<?php
/**
 * Authentication & Helper Functions
 * Handles PHP sessions, login protection, CSRF tokens, and XSS escaping.
 */

require_once __DIR__ . '/../config/database.php';

define('CURRENCY_SYMBOL', '₹');

// Start PHP session safely if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate a CSRF token for the active session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Escape output to prevent XSS attacks.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Build an application URL relative to BASE_URL.
 */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Format a numeric amount in Indian Rupees (INR), e.g., ₹1,50,000.00.
 */
function formatMoney(float $amount): string
{
    $prefix = $amount < 0 ? '-' : '';
    $formatted = number_format(abs($amount), 2, '.', '');
    [$intPart, $decPart] = explode('.', $formatted);

    if (strlen($intPart) > 3) {
        $lastThree = substr($intPart, -3);
        $remaining = substr($intPart, 0, -3);
        $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
        $intPart = $remaining . ',' . $lastThree;
    }

    return $prefix . CURRENCY_SYMBOL . $intPart . '.' . $decPart;
}

/**
 * Return hidden CSRF input field for HTML forms.
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e($_SESSION['csrf_token']) . '">';
}

/**
 * Verify submitted CSRF token on POST requests.
 */
function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die('Invalid security token. Please go back, refresh the page, and try again.');
    }
}

/**
 * Check whether a user is logged in.
 */
function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

/**
 * Protect private pages: redirect guests to login page.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . url('auth/login.php'));
        exit;
    }
}

/**
 * Set and retrieve one-time flash messages across redirects.
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

