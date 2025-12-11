<?php
$hoy = date("Y-m-d H:i:s"); 
// control de errores
ini_set("error_log", "log/php_error.log");
ini_set("log_errors", TRUE);
//ini_set ('display_errors', TRUE);
ini_set ('display_startup_errors', TRUE);
//ini_set ('error_reporting', E_ALL);
error_log( "Nuevo comienzo: ".$hoy);
//$CFG->debug = 30719; // DEBUG_ALL, but that constant is not defined here.
//fin control errores
setlocale(LC_TIME,"es_ES");
date_default_timezone_set('America/Santiago');
$config['dbhost'] = "localhost";
$config['dbport'] = "3306";
$config['dbuser'] = "tnasolut_app";
$config['dbpass'] = "1Ngr3s0.,";
$config['dbname'] = "tnasolut_app";
$conn = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
/* comprobar la conexión */
if (mysqli_connect_errno()) {
    printf("Falló la conexión: %s\n", mysqli_connect_error());
    exit();
} else mysqli_set_charset($conn,"utf8");







?>




