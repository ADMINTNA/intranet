<?php
require_once("config.php");
$conn = DbConnect("tnasolut_sweet");
$res = $conn->query("DESC cases");
echo "CASES:\n";
while($r = $res->fetch_assoc()) echo $r['Field'] . "\n";
echo "\nCASES_CSTM:\n";
$res = $conn->query("DESC cases_cstm");
while($r = $res->fetch_assoc()) echo $r['Field'] . "\n";
