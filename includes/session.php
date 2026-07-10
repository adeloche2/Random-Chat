<?php
function start_chat_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name('random_chat_session');
        session_start();
    }
}

function current_user_id(): ?int
{
    start_chat_session();
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function set_current_user(int $userId): void
{
    start_chat_session();
    $_SESSION['user_id'] = $userId;
}

function clear_current_user(): void
{
    start_chat_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
