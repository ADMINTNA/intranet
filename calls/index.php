<?php 
//=====================================================
// /intranet/calls/index.php
// Recibe parámetros y carga los informes CALLS
// Autor: Mauricio Araneda
// Actualizado: 12-11-2025
//=====================================================

// Mostrar errores en desarrollo
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Inicializar variables
$ani = $rut = $empresa = $codserv = $nombre = $apellido = $direccion = $estserv = $estcuenta = "";
$iframe = "";

// Obtener variables desde GET/POST
foreach ($_REQUEST as $key => $value) {
    $$key = trim($value);
}

// Definir iframe según parámetros recibidos
if (!empty($ani)) {
    $aniNum = intval(preg_replace('/[^0-9]/', '', $ani));
    $iframe = "./includes/calls.php?ani={$aniNum}";
}
if (!empty($rut))         $iframe = "./includes/rut.php?rut={$rut}";
if (!empty($codserv))     $iframe = "./includes/codserv.php?codserv={$codserv}";
if (!empty($empresa))     $iframe = "./includes/empresa.php?empresa={$empresa}";
if (!empty($nombre))      $iframe = "./includes/contacto.php?nombre=" . urlencode($nombre) . "&apellido=" . urlencode($apellido);
if (!empty($apellido))    $iframe = "./includes/contacto.php?nombre=" . urlencode($nombre) . "&apellido=" . urlencode($apellido);
if (!empty($direccion))   $iframe = "./includes/direccion.php?direccion=" . urlencode($direccion);
if (!empty($estserv))     $iframe = "./includes/estserv.php?estserv=" . urlencode($estserv);
if (!empty($estcuenta))   $iframe = "./includes/estcuenta.php?estserv=" . urlencode($estcuenta);
// echo $iframe;

?>
<!DOCTYPE html>
<html>
<head>
    <!-- Favicon específico del módulo CALLS -->
    <link rel="icon" type="image/x-icon" href="./favicon/favicon.ico?v=4">
    <link rel="icon" type="image/png" sizes="16x16" href="./favicon/favicon-16x16.png?v=4">
    <link rel="icon" type="image/png" sizes="32x32" href="./favicon/favicon-32x32.png?v=4">
    <link rel="icon" type="image/png" sizes="96x96" href="./favicon/favicon-96x96.png?v=4">
    <link rel="apple-touch-icon" sizes="180x180" href="./favicon/apple-icon-180x180.png?v=4">
    
    <?php include_once("meta_data_calls.html"); ?>
    <title>Datos Empresa - CALLS</title>
    <link rel="stylesheet" href="./css/index.css">
</head>

<body>

<!-- LOADER -->
<div class="cargando">
  <span class="texto">iConTel</span>
</div>

<!-- CONTENIDO PRINCIPAL -->
<div class="contenido">  
  <iframe src="<?= htmlspecialchars($iframe) ?>"></iframe>
  <?PHP include_once("../footer/footer.php"); ?>   
</div>

<script>
// Mostrar contenido y ocultar loader suavemente
document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('.cargando').classList.add('ocultar');
  document.querySelector('.contenido').style.opacity = "1";
});
document.getElementById("Year").textContent = new Date().getFullYear();
</script>

</body>
</html>