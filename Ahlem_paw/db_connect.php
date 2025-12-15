<?php
// db_connect.php
//
// Provides a reusable function to get a PDO connection using config.php.

/**
 * Get a PDO database connection.
 *
 * @return PDO
 * @throws RuntimeException if connection fails
 */
function get_db_connection(): PDO
{
    $config = require __DIR__ . '/config.php';

    $host    = $config['host']     ?? 'localhost';
    $dbname  = $config['dbname']   ?? '';
    $user    = $config['username'] ?? '';
    $pass    = $config['password'] ?? '';
    $charset = $config['charset']  ?? 'utf8mb4';

    $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Optional logging to a file
        $logFile = __DIR__ . '/db_errors.log';
        $message = sprintf(
            "[%s] Database connection failed: %s%s",
            date('c'),
            $e->getMessage(),
            PHP_EOL
        );
        @file_put_contents($logFile, $message, FILE_APPEND);

        // Throw a cleaner exception for the caller
        throw new RuntimeException('Database connection failed. Please contact the administrator.');
    }
}


