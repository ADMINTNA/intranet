<?php
    $estemes_desde = primer_dia_mes();
	$estemes_hasta = ultimo_dia_mes();
	$mesanterior_desde = primer_dia_mes_anterior();	
	$mesanterior_hasta = ultimo_dia_mes_anterior();	
	function ultimo_dia_mes() { 
	  $month = date('m');
	  $year = date('Y');
	  $day = date("d", mktime(0,0,0, $month+1, 0, $year));
	  return date('Y-m-d H:i:s', mktime(23,59,59, $month, $day, $year));
	}
	function primer_dia_mes() {
	  $month = date('m');
	  $year = date('Y');
 	  return date('Y-m-d H:i:s', mktime(0,0,0, $month, 1, $year));
	}	
	function ultimo_dia_mes_anterior() { 
	  $month = date('m');
	  $year = date('Y');
	  $day = date("d", mktime(0,0,0, $month, 0, $year));
	  return date('Y-m-d H:i:s', mktime(23,59,59, $month-1, $day, $year));
	}
	function primer_dia_mes_anterior() {
	  $month = date('m');
	  $year = date('Y');
	  return date('Y-m-d H:i:s', mktime(0,0,0, $month-1, 1, $year));
	}	
    function gestionarDebugMode() {
        // Asegúrate de iniciar sesión si no está iniciada
        // modo de uso <br>
        // https://intranet.icontel.cl/cdr/index.php?error=1 Activa debug
        // https://intranet.icontel.cl/cdr/index.php?error=0 desactiva debug

        // require_once "includes/functions.php";
        // gestionarDebugMode();


        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

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