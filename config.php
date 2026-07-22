<?php
/**
 * config.php
 * Database connection for the Mini Blog project.
 *
 * SETUP — change these 4 values to match your MySQL hosting:
 */
$DB_HOST = "localhost";
$DB_NAME = "mini_blog";
$DB_USER = "root";
$DB_PASS = "";

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed. Make sure you've created the " .
        "database and imported schema.sql. (" . $e->getMessage() . ")");
}
