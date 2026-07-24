<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function isLoggedIn(): bool
{
    return !empty($_SESSION[ADMIN_SESSION_KEY]);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function attemptLogin(string $username, string $password): bool
{
    $settings = getSettings();

    if ($username !== ($settings['admin_user'] ?? '')) {
        return false;
    }

    if (!password_verify($password, $settings['admin_pass'] ?? '')) {
        return false;
    }

    $_SESSION[ADMIN_SESSION_KEY] = $username;
    session_regenerate_id(true);
    return true;
}

function logout(): void
{
    unset($_SESSION[ADMIN_SESSION_KEY]);
    session_regenerate_id(true);
}
