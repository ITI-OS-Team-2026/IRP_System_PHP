<?php

class Database {
    private static $connection = null;

    /**
     * Get the MySQLi database connection
     * @return mysqli
     */
    public static function getConnection() {
        if (self::$connection === null) {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $port = $_ENV['DB_PORT'] ?? 3306;
            $db   = $_ENV['DB_DATABASE'] ?? 'defaultdb';
            $user = $_ENV['DB_USERNAME'] ?? 'root';
            $pass = $_ENV['DB_PASSWORD'] ?? '';
            $sslMode = $_ENV['DB_SSL_MODE'] ?? 'REQUIRED';

            // Initialize MySQLi
            $mysqli = mysqli_init();

            if (!$mysqli) {
                die("mysqli_init failed");
            }

            // Handle SSL Configuration for Aiven
            if (strtoupper($sslMode) === 'REQUIRED') {
                // To strictly verify the server, you would download ca.pem from Aiven and use:
                // $mysqli->ssl_set(NULL, NULL, __DIR__ . '/../ca.pem', NULL, NULL);
                
                // For a basic SSL connection without providing a CA file:
                // We just pass the MYSQLI_CLIENT_SSL flag in real_connect.
            }

            // Establish the connection
            $success = $mysqli->real_connect(
                $host,
                $user,
                $pass,
                $db,
                (int)$port,
                null,
                MYSQLI_CLIENT_SSL
            );

            if (!$success) {
                die("Database Connection failed: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
            }

            // Set charset to utf8mb4
            $mysqli->set_charset("utf8mb4");

            self::$connection = $mysqli;
        }

        return self::$connection;
    }
}
