<?php
// =============================================================================
// ARCHIVO: /intranet/kickoff_icontel/icontel_sweet.php
// =============================================================================
// KickOff AJAX – Con Autenticación SuiteCRM
// Versión de Kickoff que requiere sesión activa de SuiteCRM para acceder
// =============================================================================

// -------------------------
// SESIÓN UNIFICADA
// -------------------------
require_once __DIR__ . '/session_core.php';

// =======================================================
// VERIFICAR SESIÓN DE SUITECRM
// =======================================================
require_once('../includes/sweet_session_check.php');

// Verificar si hay sesión activa de SuiteCRM
if (!SweetSessionCheck::isLoggedIn()) {
    // NO hay sesión de Sweet → redirigir a Sweet para login
    session_unset();
    session_destroy();
    
    $return_url = urlencode("https://intranet.icontel.cl/kickoff_icontel/icontel_sweet.php");
    header("Location: https://sweet.icontel.cl/index.php?return_url=" . $return_url);
    exit;
}

// Obtener datos del usuario desde Sweet
$sweet_user_id = SweetSessionCheck::getUserId();
$sweet_user_data = SweetSessionCheck::getUserData();
$sweet_security_groups = SweetSessionCheck::getSecurityGroups($sweet_user_id);

// Guardar en sesión de Kickoff para compatibilidad
$_SESSION['loggedin'] = true;
$_SESSION['sweet_user_id'] = $sweet_user_id;
$_SESSION['name'] = $sweet_user_data['user_name'] ?? 'Usuario';
$_SESSION['cliente'] = $sweet_user_data['full_name'] ?? $sweet_user_data['user_name'];

// Security Groups: usar el primero como principal
if (!empty($sweet_security_groups)) {
    $_SESSION['sg_id'] = $sweet_security_groups[0]['id'];
    $_SESSION['sg_name'] = $sweet_security_groups[0]['name'];
} else {
    // Usuario sin grupo → asignar grupo por defecto
    // Puedes cambiar esto para denegar acceso si prefieres
    $_SESSION['sg_id'] = 'a03a40e8-bda8-0f1b-b447-58dcfb6f5c19'; // Grupo por defecto
    $_SESSION['sg_name'] = 'Sin Grupo Asignado';
}

// -----------------------------------------------------
// 🔍 MODO DEBUG
// -----------------------------------------------------
$DEBUG_MODE = (!empty($_SESSION['debug']) && $_SESSION['debug'] === true);

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
<title>Cuadro de Mando</title>
<style>
body { font-family: Arial; background:#EEE; padding:40px; }
button {
    padding:10px 20px; background:#27304A; color:#FFF;
    border:none; border-radius:5px; cursor:pointer;
    font-size:16px;
}
button:hover { background:#1F1D3E; }
pre {
    background:white; padding:20px; border:1px solid #CCC;
    overflow:auto;
}
#content {
    padding-top: 65px !important;  /* espacio para el menú fijo */
}
</style>
</head>
<body>

<h2>🔍 Debug de Sesión — KickOff con SuiteCRM</h2>

<h3>✅ Autenticado vía SuiteCRM</h3>
<p><strong>User ID:</strong> <?= htmlspecialchars($sweet_user_id) ?></p>
<p><strong>Usuario:</strong> <?= htmlspecialchars($sweet_user_data['user_name'] ?? 'N/A') ?></p>
<p><strong>Nombre:</strong> <?= htmlspecialchars($sweet_user_data['full_name'] ?? 'N/A') ?></p>
<p><strong>Security Group:</strong> <?= htmlspecialchars($_SESSION['sg_name']) ?> (<?= htmlspecialchars($_SESSION['sg_id']) ?>)</p>

<h3>📋 Variables de Sesión</h3>
<pre><?php var_dump($_SESSION); ?></pre>

<h3>🔐 Security Groups del Usuario</h3>
<pre><?php print_r($sweet_security_groups); ?></pre>

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

// -----------------------------------------------------
// VARIABLES KICKOFF
// -----------------------------------------------------
include_once("config.php");
include_once("security_groups.php");

// -----------------------------------------------------
// GRUPOS DEFINIDOS – NECESARIOS PARA menu_modulos.php
// -----------------------------------------------------
$ventas      = "/ Ventas / -..Ghislaine / -..MAO / -..Natalia / -..Monica / -..Raquel";
$operaciones = "/ -..Bryan / Operaciones / -..Alex /";
$sac         = "/ Servicio al Cliente / -..Maria José / -..DAM /";
$admin       = "/ -..MAM / -..MAO ";
$proveedores = "/ -..MAO";
$mao_mam     = "/ -..MAO / -..MAM";

// Manejo de grupo
if (isset($_POST['sg'])) $_SESSION['sg_id'] = $_POST['sg'];
if (isset($_GET['sg']))  $_SESSION['sg_id'] = $_GET['sg'];

$sg_id = $_SESSION['sg_id'] ?? "a03a40e8-bda8-0f1b-b447-58dcfb6f5c19";

// Obtener nombre del grupo
$sg_name = '';
foreach ($grupos as $grupo) {
    if ($grupo['id'] == $sg_id) {
        $sg_name = $grupo['name'];
        break;
    }
}

// Guardar en sesión
$_SESSION['sg_name'] = $sg_name;

// Exponer variables a JavaScript
echo "<script>
var sg_id = '$sg_id'; 
var sg_name = '$sg_name';
// Configurar ruta base para módulos AJAX
window.KICKOFF_BASE_PATH = window.KICKOFF_BASE_PATH || '';
console.log('🔧 KICKOFF_BASE_PATH configurado:', window.KICKOFF_BASE_PATH || '(relativo)');
console.log('✅ Autenticado vía SuiteCRM - User ID: $sweet_user_id');
</script>";




// Metadatos estándar
include_once("meta_data/meta_data.html");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>KickOff AJAX - SuiteCRM Auth</title>
    <meta charset="UTF-8">

    <!-- JS del KickOff original -->
    <script src="js/kickoff.js"></script>

    <!-- NUEVO JS DEL MODO AJAX -->
    <script src="js/kickoff_ajax.js"></script>
    
    <script src="js/sort_global.js?v=1"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="./css/kickoff.css">
    <link rel="stylesheet" href="./css/rebote.css">

    <?php
    if (!isset($_SESSION['debug']) || $_SESSION['debug'] === false) {
        if (!empty($_SESSION['auto_refresh'])) {
            echo '<meta http-equiv="refresh" content="300">';
        }
    }
    ?>
<style>
html, body {
    height: auto !important;
    min-height: 100vh;
    margin: 0; padding: 0;
    overflow-y: auto;
}
#page {
    min-height: calc(100vh - 40px);
    position: relative;
}
footer {
    position: fixed;
    bottom: 0; left: 0;
    width: 100%;
    background-color: white;
    color: #1F1D3E;
    text-align: center;
    font-size: 12px;
    border-top: 2px solid #512554;
    height: 25px; line-height: 25px;
    z-index: 9000;
    box-shadow: 0 -1px 3px rgba(0,0,0,0.1);
}
</style>
    
</head>

<body onload="BodyOnLoad()">

<div id="page">

    <div id="header">
        <?php include_once("cm_header.php"); ?>
    </div>

    <div id="content">

        <div class="cargando"><span class="texto">iContel</span></div>

        <!-- Capas requeridas por kickoff.js -->
        <!-- (Se dejan ocultas por compatibilidad) -->

        <!-- MENÚ AJAX -->
        <?php include_once("menu_modulos.php"); ?>

        <!-- CONTENEDOR PARA LOS MÓDULOS AJAX -->
        <div id="modulo-contenedor">
            <div class="cargando"><span class="texto">iContel</span></div>
        </div>

    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Cargar módulo inicial → TAREAS
    loadModulo('cm_tareas_pendientes.php');

    // Activar ordenamiento al cargar el primer módulo
    if (typeof activarSortEnTablas === "function") {
        activarSortEnTablas();
    }
});
</script>

<?php include_once("../footer/footer_oscuro.php"); ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const c = document.querySelector('.cargando');
    if (c) c.classList.add('ocultar');
});
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    loadModulo('cm_tareas_pendientes.php');
});
</script>

    
</body>
</html>
