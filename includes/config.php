<?php

define('DB_HOST', 'localhost');           // Set database host
define('DB_USER', 'root');                // Set database user
define('DB_PASS', '');                    // Set database password
define('DB_NAME', 'inventory_system');    // Set database name

try {
    // Create a new PDO instance
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set the PDO error mode to exception
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Catch and display any connection errors
    echo "Database connection failed: " . $e->getMessage();
    exit();
}

?>
