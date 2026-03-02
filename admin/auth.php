<?php
/**
 * Session-based authentication with CSRF protection
 */

require_once __DIR__ . '/config.php';

/**
 * Start a secure session if not already active.
 */
function init_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/admin/',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'),
        'httponly'  => true,
        'samesite' => 'Strict',
    ]);

    session_start();

    // Expire session after lifetime
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_LIFETIME) {
        logout();
        return;
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Check whether the current session is authenticated.
 */
function is_logged_in(): bool
{
    init_session();
    return !empty($_SESSION['authenticated']);
}

/**
 * Redirect to login page if not authenticated.
 */
function require_auth(): void
{
    if (!is_logged_in()) {
        header('Location: index.php?action=login');
        exit;
    }
}

/**
 * Generate or return the current CSRF token.
 */
function csrf_token(): string
{
    init_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a submitted CSRF token against the session token.
 * Aborts with 403 on mismatch.
 */
function verify_csrf(string $token): void
{
    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        die('CSRF token mismatch');
    }
}

/**
 * Attempt to log in with the given credentials.
 * Returns true on success, false on failure.
 */
function login(string $user, string $pass): bool
{
    init_session();

    if ($user === AUTH_USER && password_verify($pass, AUTH_PASS_HASH)) {
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        $_SESSION['authenticated'] = true;
        $_SESSION['last_activity'] = time();
        // Reset CSRF token on login
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return true;
    }

    return false;
}

/**
 * Destroy the session and log out.
 */
function logout(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
