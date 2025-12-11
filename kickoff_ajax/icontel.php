<?php
// ==========================================================
// KickOff AJAX / icontel.php – Entorno de Laboratorio AJAX
// ==========================================================

// -------------------------
// SESIÓN UNIFICADA
// -------------------------
session_name('icontel_intranet_sess');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '.icontel.cl');
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Cargar configuración
include_once("config.php");

// =======================================================
// AUTENTICACIÓN CONFIGURABLE
// =======================================================
// Verificar si se debe usar autenticación de SuiteCRM
if (defined('USE_SWEET_AUTH') && USE_SWEET_AUTH === true) {
    
    // ===== AUTENTICACIÓN VÍA SUITECRM =====
    require_once('includes/sweet_auth.php');
    
    // Verificar si el usuario está autenticado en SuiteCRM
    if (!SweetAuth::isAuthenticated()) {
        // No hay sesión de Sweet → redirigir a Sweet para login
        $return_url = 'https://intranet.icontel.cl/kickoff_ajax/icontel.php';
        header('Location: ' . SweetAuth::getLoginUrl($return_url));
        exit;
    }
    
    // Usuario autenticado en Sweet → crear sesión de Kickoff
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        SweetAuth::createKickoffSession();
    }
    
} else {
    
    // ===== AUTENTICACIÓN TRADICIONAL =====
    // =======================================================
    // DESARROLLO AJAX – Permitir acceso directo para mAo
    // SIN romper la seguridad del KickOff original
    // =======================================================
    if (
        basename(dirname(__FILE__)) === 'kickoff_ajax' &&
        ($_SESSION['usuario'] ?? '') === 'Mauricio'
    ) {
        // ✔ Acceso permitido en entorno AJAX
    } else {
        // Validación normal del KickOff original
        if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {

            session_unset();
            session_destroy();

            if (isset($_COOKIE['icontel_intranet_sess'])) {
                setcookie('icontel_intranet_sess', '', time() - 3600, '/', '.icontel.cl', true, true);
            }
            if (isset($_COOKIE['rememberme'])) {
                setcookie('rememberme', '', time() - 3600, '/', '.icontel.cl', true, true);
            }

            echo "<script>
                if (window.top !== window.self) {
                    window.top.location.href = 'https://intranet.icontel.cl/index.php?error=session_expired';
                } else {
                    window.location.href = 'https://intranet.icontel.cl/index.php?error=session_expired';
                }
            </script>";
            exit;
        }
    }
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

<h2>🔍 Debug de Sesión — KickOff</h2>
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
</script>";




// Metadatos estándar
include_once("meta_data/meta_data.html");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>KickOff AJAX</title>
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

        <!-- ========================================================== -->
        <!-- CAPAS PARA BOTONES DEL HEADER -->
        <!-- ========================================================== -->
        
        <DIV hidden ID="capa_casos" style="background-color: darkblue; color: white;">
            <?PHP include_once("../casos/index.php"); ?>
        </DIV>
        
        <DIV hidden ID="capa_iconos" style="background-color: white; color: white;">
            <iframe src="../app/menu.php"></iframe>
        </DIV>
        
        <DIV hidden ID="capa_buscadores" style="background-color: white; color: white;">
            <iframe src="./buscadores/index.php"></iframe>
        </DIV>

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
    const c = document.querySelectorAll('.cargando');
    c.forEach(el => el.classList.add('ocultar'));
});
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    loadModulo('cm_tareas_pendientes.php');
});
</script>

    
</body>
</html>
