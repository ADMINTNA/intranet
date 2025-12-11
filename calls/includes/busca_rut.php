<?php 
// Inicializar variables
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    include_once("config.php");    
    date_default_timezone_set("America/Santiago");
    if (isset($_POST['rut'])) { $ani = $_POST['rut']; }
    if (isset( $_GET['rut'])) { $rut =  $_GET['rut']; }
    if(empty($rut)) exit();
    $datos_completos = busca_datos($rut);
 //   include_once("informe.php");    



