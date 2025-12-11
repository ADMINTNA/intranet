<?php
require_once __DIR__ . '/session_config.php';
// ... tu código de la página ...
include "config.php";
$columnName = $_POST['columnName'];
$sort = $_POST['sort'];
$query = $_SESSION["query"]." order by ".$columnName." ".$sort." ";
include_once("tabla_datos.php");
?>

	
