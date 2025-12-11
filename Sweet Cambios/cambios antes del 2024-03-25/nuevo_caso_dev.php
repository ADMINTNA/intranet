<?php
    global $db;
    global $current_user;
    $default_assigned_user_id = "1";
    $script="";
    $usuario = $current_user->first_name." ".$current_user->last_name;
    $hoy = date("d-m-Y H:i:s");
    $fecha_est_Resolucion = strtotime ( '+4 hour' , strtotime ($hoy) );
    $fecha_est_Resolucion = date ( 'Y-m-d' , $fecha_est_Resolucion);
    $hora = date("H:i", strtotime($hoy));
    $dia  = date('N', strtotime($hoy));
    if($dia >=1 and $dia <=5) { if($hora >= "09:00" and $hora <= "18:00") $habil =1; } else $habil = 0;
     include_once("includes/funciones.php");
    if($_SERVER['REQUEST_METHOD']==='POST'){
        include_once("includes/post.php"); 
    } elseif(!empty($_GET['account_id'])){
        include_once("includes/get.php");
    } else {
        header('Location: ../../index.php');
        exit;
    }
?>