<?php
$host     = "localhost";
$user     = "tnasolut_data_studio";
$password = "P3rf3ct0.,";
$dbname   = "tnasolut_sweet";
date_default_timezone_set('UTC'); // hora de la base de datos
$userTimeZone = new DateTimeZone('America/Santiago');  // hora de chile
$hoy = date("d-m-Y H:i:s");


function DbConnect($dbname){
    $server   = "localhost";
    $user     = "tnasolut_data_studio";
    $password = "P3rf3ct0.,";
    // me conecto a la Base de Datos
    $conn = new mysqli($server, $user, $password);
    if ($conn->connect_error) { die("No me pude conectar a servidor localhost: " . $conn->connect_error); }
    $dummy = mysqli_set_charset ($conn, "utf8");    
    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) { die ('No se puede usar '.$dbname.' : ' . mysql_error()); }
    return($conn);
}

function horacl($date) {
    global $userTimeZone;
    $dateNeeded = new DateTime($date); 
    $dateNeeded->setTimeZone($userTimeZone);
    return($dateNeeded->format('d-m-Y H:i:s')) ;
}
function fechacl($fechaUTC) {
    if (empty($fechaUTC)) return null;

    $dt = new DateTime($fechaUTC, new DateTimeZone('UTC'));
    $dt->setTimezone(new DateTimeZone('America/Santiago'));
    return $dt->format('d-m-Y');
}