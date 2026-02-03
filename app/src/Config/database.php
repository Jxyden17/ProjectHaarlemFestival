<?php

$AppEnvDir = '/app';

if (is_readable($AppEnvDir . DIRECTORY_SEPARATOR . '.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable($AppEnvDir);
    $dotenv->load();
}

$host = $_ENV['host'] ?? getenv('host');
$dbname = $_ENV['dbname'] ?? getenv('dbname');
$user = $_ENV['user'] ?? getenv('user');
$password = $_ENV['password'] ?? getenv('password');
$charset = $_ENV['charset'] ?? getenv('charset');

return [
    'host' => $host,
    'dbname' => $dbname,
    'user' => $user,
    'password' => $password,
    'charset' => $charset ?: 'utf8mb4'
];
