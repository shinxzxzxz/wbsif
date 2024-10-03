<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

function env($key, $default = null) {
    return isset($_ENV[$key]) ? $_ENV[$key] : $default;
}