<?php
// ==========================================================
// KickOff Office V2 - Versión AJAX
// /intranet/kickoff_office/index.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2026-01-06
// Codificación: UTF-8 sin BOM
// ==========================================================
mb_internal_encoding("UTF-8");

// -------------------------
// CONFIGURACIÓN DE SESIÓN
// -------------------------
// -------------------------
// CONFIGURACIÓN DE SESIÓN
// -------------------------
require_once __DIR__ . '/session_core.php';

// -------------------------
// VALIDACIÓN DE SESIÓN
// -------------------------
if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirigir suavemente si no hay sesión, pero sin session_destroy preventivo aquí
    header("Location: https://intranet.icontel.cl");
    exit;
}

// -------------------------
// MODO DEBUG
// -------------------------
$DEBUG_MODE = false;

if (!empty($_SESSION['debug']) && ($_SESSION['debug'] === true)) {
    $DEBUG_MODE = true;
}

if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    $DEBUG_MODE = true;
    $_SESSION['debug'] = true;
}

if ($DEBUG_MODE && !isset($_GET['ok'])) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Debug - KickOff Office V2</title>
<style>
body { font-family: Arial; background:#EEE; padding:40px; }
button { padding:10px 20px; background:#27304A; color:#FFF; border:none; border-radius:5px; cursor:pointer; font-size:16px; }
button:hover { background:#1F1D3E; }
pre { background:white; padding:20px; border:1px solid #CCC; overflow:auto; }
</style>
</head>
<body>
<h2>🔍 Debug de Sesión — KickOff Office V2</h2>
<p>Estado de <strong>$_SESSION</strong>:</p>
<pre><?php var_dump($_SESSION); ?></pre>
<form method="get">
    <input type="hidden" name="ok" value="1">
    <?php if ($DEBUG_MODE): ?>
        <input type="hidden" name="debug" value="1">
    <?php endif; ?>
    <button type="submit">Continuar</button>
</form>
</body>
</html>
<?php
    exit;
}

// -------------------------
// CONFIGURACIÓN Y VARIABLES
// -------------------------
include_once("config.php");
include_once("security_groups.php");

// Manejo de grupo seleccionado
if (isset($_POST['sg'])) {
    $sg_id = $_POST['sg'];
    $_SESSION['sg_id'] = $sg_id;
    $_SESSION['sec_id_office'] = $sg_id; // Sincronizar
}

if (isset($_GET['sg'])) {
    $sg_id = $_GET['sg'];
    $_SESSION['sg_id'] = $sg_id;
    $_SESSION['sec_id_office'] = $sg_id; // Sincronizar
}

if (!isset($sg_id)) {
    if (isset($_SESSION['sec_id_office'])) {
        $sg_id = $_SESSION['sec_id_office'];
    } else {
        $sg_id = "8226b570-5bdb-66e9-8399-69224778d1da"; // soporte
        $_SESSION['sec_id_office'] = "8226b570-5bdb-66e9-8399-69224778d1da";
    }
}

// Obtener nombre del grupo
$sg_name = '';
foreach ($grupos as $grupo) {
    if ($grupo['id'] == $sg_id) {
        $sg_name = $grupo['name'];
        break;
    }
}

// Guardar en sesión para que los módulos AJAX puedan acceder
$_SESSION['sg_name'] = $sg_name;

// Exponer variables JS
echo "<script>
var sg_id = '$sg_id';
var sg_name = '$sg_name';
console.log('🔧 KickOff Office V2 - sg_id:', sg_id, 'sg_name:', sg_name);
</script>";

// Metadatos
include_once("meta_data/meta_data_kickoff.html");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>KickOff Office V2 - AJAX</title>
    <meta charset="UTF-8">
    
    <!-- Favicon específico de KickOff -->
    <link rel="icon" type="image/png" sizes="96x96" href="favicon/favicon-96x96.png?v=3">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png?v=3">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png?v=3">
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico?v=3">
    
    <!-- JS del KickOff original -->
    <script src="js/kickoff.js"></script>
    
    <!-- NUEVO JS DEL MODO AJAX -->
    <script src="js/kickoff_ajax.js?v=4"></script>
    
    <!-- COLUMNAS REDIMENSIONABLES UNIVERSAL -->
    <script src="js/cm_resizable_columns_universal.js"></script>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/kickoff.css">
    <link rel="stylesheet" href="css/rebote.css">
    
    <?php
    // Auto-refresh controlado
    $refreshHabilitado = false;
    if (!isset($_SESSION['debug']) || $_SESSION['debug'] === false) {
        if (!empty($_SESSION['auto_refresh'])) {
            $refreshHabilitado = true;
        }
    }
    if ($refreshHabilitado) {
        echo '<meta http-equiv="refresh" content="300">';
    }
    ?>
    
    <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background-color: #FFFFFF;
      font-family: Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
      overflow-y: auto;
    }
    
    #page {
      display: flex;
      flex-direction: column;
      min-height: calc(100vh - 40px);
    }
    
    #header {
      height: auto;
      background-color: #64C2C8;
      color: #fff;
      padding: 0;
      flex-shrink: 0;
      position: relative;
      z-index: 1;
    }
    
    #content {
      margin-top: 0px;
      flex: 1;
      overflow-y: auto;
      padding: 0px;
    }
    
    #modulo-contenedor {
      margin-top: 0px;
      padding: 0px 15px;
    }
    
    footer {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background-color: black;
      color: white;
      text-align: center;
      font-size: 12px;
      border-top: 2px solid #512554;
      height: 25px;
      line-height: 25px;
      z-index: 9000;
      box-shadow: 0 -1px 3px rgba(0,0,0,0.1);
    }
    
    /* Loader moderno */
    .cargando {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
      padding: 20px;
    }
    
    .cargando.ocultar {
      display: none !important;
    }
    
    .loader {
      width: 48px;
      height: 48px;
      border: 5px solid #512554;
      border-bottom-color: transparent;
      border-radius: 50%;
      display: inline-block;
      box-sizing: border-box;
      animation: rotation 1s linear infinite;
    }
    
    @keyframes rotation {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style>
    
</head>

<body onload="BodyOnLoad()">

<div id="page">

    <div id="header">
        <?php include_once("cm_header.php"); ?>
    </div>

    <div id="content">

        <!-- Touch Bar AJAX con Badges -->
        <?php include_once("menu_modulos.php"); ?>

        <!-- Capas ocultas para compatibilidad -->
        <!--
        <div hidden id="capa_casos">
            <?php // include_once("cm_casos_abiertos.php"); ?>
        </div>
        -->
        <div hidden id="capa_iconos">
            <iframe src="../app/menu.php"></iframe>
        </div>
        <div hidden id="capa_buscadores">
            <iframe src="../kickoff_icontel/buscadores/index.php" style="width:100%; height:100%; border:none;"></iframe>
        </div>

        <!-- CONTENEDOR PARA LOS MÓDULOS AJAX -->
        <div id="modulo-contenedor">
            <div class="cargando"><span class="loader"></span></div>
        </div>
    </div>
</div>

<?php include_once("footer/footer_oscuro.php"); ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // 1. Cargar módulo inicial → TAREAS PENDIENTES
    loadModulo('cm_tareas_pendientes.php');

    // 2. Marcar botón como activo visualmente
    const btn = document.getElementById('btn-def-tareas');
    if (btn) btn.classList.add('active');
    
    // 3. Activar sort en tablas si existe la función
    if (typeof activarSortEnTablas === "function") {
        activarSortEnTablas();
    }
    
    // 4. Ocultar loaders globales
    const loaders = document.querySelectorAll('.cargando');
    loaders.forEach(el => el.classList.add('ocultar'));
});
</script>

<!-- ========================================================== -->
<!-- 🔍 OVERLAY BUSCADOR (Fuera de cualquier contenedor con z-index) -->
<!-- ========================================================== -->
<div id="overlay-buscador" onclick="cerrarBuscador(event)">
    <div id="modal-buscador" onclick="event.stopPropagation()">
        <button id="cerrar-buscador" onclick="cerrarBuscador()">&times;</button>
        <div id="contenido-buscador">
            <!-- El contenido de busca.html se cargará aquí -->
        </div>
    </div>
</div>
    
</body>
</html>
