<?php

namespace capsweb\Helpers;

class Auth
{
    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return !empty($_SESSION['id']);
    }

    public static function requireLogin(string $redirect = '/acceso.php'): void
    {
        if (!self::check()) {
            header('Location: ' . $redirect);
            exit;
        }
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }
        session_destroy();
    }
}
