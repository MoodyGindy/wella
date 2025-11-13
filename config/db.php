<?php

declare(strict_types=1);

$DB_HOST = 'localhost';
$DB_NAME = 'wellaresin';
$DB_USER = 'wellaresin';
$DB_PASS = 'wellaresin';

/**
 * Returns a shared PDO instance.
 *
 * @throws PDOException when the connection fails.
 */
function get_db_connection(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $DB_HOST, $DB_NAME);

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $connection = new PDO($dsn, $DB_USER, $DB_PASS, $options);

    return $connection;
}


