<?php
require_once "config.php";
$conn = DbConnect("tnasolut_sweet");

function describeTable($conn, $table) {
    echo "--- Table: $table ---\n";
    $res = $conn->query("DESCRIBE $table");
    if (!$res) {
        echo "Error: " . $conn->error . "\n";
        return;
    }
    while ($row = $res->fetch_assoc()) {
        echo "{$row['Field']} - {$row['Type']}\n";
    }
    echo "\n";
}

describeTable($conn, "cases");
describeTable($conn, "cases_cstm");
$conn->close();
?>
