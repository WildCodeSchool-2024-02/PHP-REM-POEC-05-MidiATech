<?php

namespace App\Model;

class SessionManager
{
    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function remove(array $keys): void
    {
        foreach ($keys as $key) {
            unset($_SESSION[$key]);
        }
    }

    public function isset(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
}
