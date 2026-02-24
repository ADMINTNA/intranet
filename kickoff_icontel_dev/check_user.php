<?php
require_once __DIR__ . '/session_core.php';
require_once __DIR__ . '/config.php';

$con = new mysqli("localhost", "tnasolut_app", "1Ngr3s0.,", "tnasolut_app");
$user = "mao";
$stmt = $con->prepare("SELECT * FROM clientes WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

echo "<h1>User DB Record (mao)</h1>";
echo "<pre>";
print_r($row);
echo "</pre>";

if ($row) {
    $conn_sweet = DbConnect("tnasolut_sweet");
    $sg_id = $row['sec_id'];
    $sql = "SELECT name FROM securitygroups WHERE id = '$sg_id'";
    $res_sg = $conn_sweet->query($sql);
    $row_sg = $res_sg->fetch_assoc();

    echo "<h2>Security Group for this ID ($sg_id)</h2>";
    echo "Name: " . ($row_sg['name'] ?? 'NOT FOUND');
} else {
    echo "<h2>User 'mao' not found in DB!</h2>";
}
?>
