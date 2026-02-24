<?php
// ... tu código de la página ...
// functions.php incluye funciones de uso comun en la aplicación
function gestionarDebugMode()
{
    // Asegúrate de iniciar sesión si no está iniciada
    // modo de uso <br>
    // https://intranet.icontel.cl/cdr/index.php?error=1 Activa debug
    // https://intranet.icontel.cl/cdr/index.php?error=0 desactiva debug
    
    // require_once "includes/functions.php";
    // gestionarDebugMode();
    
    
    // Activar si viene ?error=1
    if (isset($_GET['error']) && $_GET['error'] == '1') {
        $_SESSION['debug_mode'] = true;
    }

    // Desactivar si viene ?error=0
    if (isset($_GET['error']) && $_GET['error'] == '0') {
        unset($_SESSION['debug_mode']);
    }

    // Activar error reporting según sesión
    if (isset($_SESSION['debug_mode']) && $_SESSION['debug_mode'] === true) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        error_reporting(E_ALL & ~E_NOTICE);
    }
}
?>