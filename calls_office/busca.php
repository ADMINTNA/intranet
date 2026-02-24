<?php
//=====================================================
// /intranet/calls/busqueda.php
// Busca Notas de venta de BSale
// Autor: Mauricio Araneda
// Actualizado: 08-11-2025
//=====================================================

    // activo mostrar errores
//     error_reporting(E_ALL);
//     ini_set('display_errors', '1');
    if(isset($_POST['empresa']))    $empresa    = urlencode($_POST['empresa']);    
    if(isset($_POST['rut']))        $rut        = $_POST['rut'];    
    if(isset($_POST['nombre']))     $nombre     = urlencode($_POST['nombre']);    
    if(isset($_POST['apellido']))   $apellido   = urlencode($_POST['apellido']);    
    if(isset($_POST['ani']))        $ani        = $_POST['ani'];    
    if(isset($_POST['codserv']))    $codserv    = $_POST['codserv'];     
    if(isset($_POST['estserv']))    $estserv    = $_POST['estserv']; 
    if(isset($_POST['estcuenta']))  $estcuenta  = $_POST['estcuenta']; 
    if(isset($_POST['direccion']))  $direccion  = urlencode($_POST['direccion']); 
    if(!empty($ani)) {
        $url = 'window.open("index.php?ani='.$ani.'", "_self" )';
        ?> <script><?php echo $url; ?> </script> <?php 
        exit();
    }
    if(!empty($rut)) {
        $url = 'window.open("./index.php?rut='.$rut.'", "_self" )';
        ?> <script><?php echo $url; ?> </script> <?php 
        exit();
    }
    if(!empty($empresa)) {
        $url = 'window.open("./index.php?empresa='.urlencode($empresa).'", "_self" )';
        ?> <script><?php echo $url; ?> </script> <?php 
        exit();
    }
    if(!empty($nombre) or !empty($apellido)) {
        $url = 'window.open("index.php?nombre='.$nombre.'&apellido='.$apellido.'", "_self" )';
        ?> <script><?php echo $url; ?> </script> <?php 
        exit();
    }
    if(!empty($codserv)) {
        $url = 'window.open("index.php?codserv='.$codserv.'", "_self" )';
        ?> <script><?php echo $url; ?> </script> <?php 
        exit();
    }
 
    if(!empty($estcuenta)) {
        $url = 'window.open("index.php?estcuenta='.$estcuenta.'", "_self" )';
        ?> <script><?php echo $url; ?> </script> <?php 
        exit();
    }

    if(!empty($estserv)) {
        $url = 'window.open("index.php?estserv='.$estserv.'", "_self" )';
        ?> <script><?php echo $url; ?> </script> <?php 
        exit();
    }
    if(!empty($direccion)) {
         $url = 'window.open("index.php?direccion='.$direccion.'", "_self" )';
        //$url = 'window.open("index.php?rut='.$rut.'", "_self" )';
        ?> <script><?php echo $url; ?> </script> <?php 
        exit();
    }
    
    
    
?>
    
    
