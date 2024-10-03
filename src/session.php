<?php

namespace App;

class Session
{
    // Static property to track whether the session has already started
    private static bool $isStarted = false;

    // Constructor ensures session is started only once
    public function __construct()
    {
        if (!self::$isStarted) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
                self::$isStarted = true;
            }
        }
    }
    
    // Set session data
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    // Get session data by key
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    // Check if a session key exists
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    // Remove a session key
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    // Regenerate session ID for security
    public function regenerate(bool $deleteOldSession = false): void
    {
        session_regenerate_id($deleteOldSession);
    }

    // Destroy the session
    public function destroy(): void
    {
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session
        self::$isStarted = false; // Mark session as not started
    }

    // Get all session data
    public function all(): array
    {
        return $_SESSION;
    }
}