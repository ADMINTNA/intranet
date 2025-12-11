<?php
include "config.php";
$columnName = $_POST['columnName'];
$sort = $_POST['sort'];
session_start();
$query = $_SESSION["query_contactos"]." order by ".$columnName." ".$sort." ";
include_once("tabla_datos.php");
?>

	
