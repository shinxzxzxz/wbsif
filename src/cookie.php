<?php

namespace App;

class Cookie
{
    /**
     * Set a cookie.
     *
     * @param string $name Name of the cookie
     * @param string $value Value of the cookie
     * @param int $expires Expiration time (in seconds)
     * @param string $path Path on the server in which the cookie will be available
     * @param string $domain Domain that the cookie is available to
     * @param bool $secure Indicates whether the cookie should only be transmitted over a secure HTTPS connection
     * @param bool $httpOnly When true, the cookie will be made accessible only through the HTTP protocol
     * @return void
     */
    public static function set(string $name, string $value, int $expires = 3600, string $path = "/", string $domain = "", bool $secure = false, bool $httpOnly = true): void
    {
        setcookie($name, $value, time() + $expires, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Get a cookie value.
     *
     * @param string $name Name of the cookie
     * @return string|null Returns the value of the cookie or null if it does not exist
     */
    public static function get(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Delete a cookie.
     *
     * @param string $name Name of the cookie
     * @param string $path Path on the server in which the cookie will be available
     * @param string $domain Domain that the cookie is available to
     * @return void
     */
    public static function delete(string $name, string $path = "/", string $domain = ""): void
    {
        // Set the cookie's expiration date to the past
        setcookie($name, "", time() - 3600, $path, $domain);
        unset($_COOKIE[$name]); // Remove from the superglobal array
    }

    /**
     * Check if a cookie exists.
     *
     * @param string $name Name of the cookie
     * @return bool True if the cookie exists, false otherwise
     */
    public static function exists(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }
}