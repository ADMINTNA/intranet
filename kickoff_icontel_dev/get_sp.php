<?php
require_once("config.php");
$conn = DbConnect("tnasolut_sweet");
$res = $conn->query("SHOW CREATE PROCEDURE Kick_Off_Operaciones_Abiertos");
if ($res) {
    $row = $res->fetch_assoc();
    echo $row['Create Procedure'];
} else {
    echo "Error: " . $conn->error;
}
