<?php
// Debug script to check what fields the stored procedure returns
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . "/includes/sb_config.php";
    
    $conn = DbConnect(DB_SWEET);
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p style='color: red;'>Failed to initialize: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Test with a known account ID - let's use the first one we can find
$sql = "SELECT id, name FROM accounts WHERE deleted = 0 LIMIT 1";
$result = $conn->query($sql);
$account = $result->fetch_assoc();

echo "<h2>Testing stored procedure for account: {$account['name']}</h2>";
echo "<p>Account ID: {$account['id']}</p>";

// Call the stored procedure
$sql = "CALL searchactiveservicesbyaccountid('{$account['id']}')";
echo "<p><strong>SQL:</strong> $sql</p>";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<h3>Fields returned by stored procedure:</h3>";
    $fields = $result->fetch_fields();
    echo "<ul>";
    foreach ($fields as $field) {
        echo "<li><strong>{$field->name}</strong> ({$field->type})</li>";
    }
    echo "</ul>";
    
    echo "<h3>First 3 rows of data:</h3>";
    $result->data_seek(0); // Reset pointer
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        if ($count >= 3) break;
        echo "<pre>";
        print_r($row);
        echo "</pre>";
        echo "<hr>";
        $count++;
    }
} else {
    echo "<p style='color: red;'>No results or error: " . $conn->error . "</p>";
}

// Clear multiple result sets
while ($conn->more_results() && $conn->next_result()) {
    if ($r = $conn->store_result()) {
        $r->free();
    }
}

$conn->close();
?>
