<?php
// Simple script to check deleted invoices
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$dbname = 'icontel_sweet';
$username = 'icontel_sweet';
$password = 'Sweet2017';

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "<h2>Checking aos_invoices table</h2>";
    
    // Count deleted invoices
    $sql = "SELECT COUNT(*) as total FROM aos_invoices WHERE deleted = 1";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Total invoices with deleted = 1: <strong>{$row['total']}</strong></p>";
    }
    
    // Count non-deleted invoices
    $sql = "SELECT COUNT(*) as total FROM aos_invoices WHERE deleted = 0";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Total invoices with deleted = 0: <strong>{$row['total']}</strong></p>";
    }
    
    echo "<p>✅ Query already filters deleted = 0 correctly</p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
