<?php

// Enable error reporting for debugging (disable in production)
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');

// Database configuration
$dbHost = getenv('DB_HOST') ?: 'localhost'; // Change this to your database host
$dbUsername = getenv('DB_USERNAME') ?: 'gwedhahc_admin'; // Change this to your database username
$dbPassword = getenv('DB_PASSWORD') ?: 'Invest@Gwedhah2024!'; // Change this to your database password
$dbName = getenv('DB_NAME') ?: 'gwedhahc_loan-management-system'; // Change this to your database name

// Establish database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Set session timeout to 10 minutes
//ini_set('session.gc_maxlifetime', 1200);
//session_set_cookie_params(1200);
session_start();

?>
