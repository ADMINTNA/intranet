<?php
// ==========================================================
// /intranet/cta_cte_duemint/config.php
// Configuración de base de datos para consulta Duemint
// ==========================================================

session_name('icontel_intranet_sess');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Conecta a la base de datos especificada
 * @param string $dbname Nombre de la base de datos
 * @return mysqli Conexión a la base de datos
 */
function DbConnecta($dbname) {
    $server   = "localhost";
    $user     = "data_studio";
    $password = "1Ngr3s0.,";
    
    // Crear conexión
    $conn = new mysqli($server, $user, $password);
    
    if ($conn->connect_error) {
        die("No me pude conectar a servidor localhost: " . $conn->connect_error);
    }
    
    // Configurar charset UTF-8
    $dummy = mysqli_set_charset($conn, "utf8");
    
    // Seleccionar base de datos
    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) {
        die('No se puede usar ' . $dbname . ' : ' . mysqli_error($conn));
    }
    
    return $conn;
}

/**
 * Limpia y formatea un RUT
 * @param string $rut RUT a limpiar
 * @return string RUT limpio sin puntos ni espacios
 */
function limpiarRut($rut) {
    $limpia_rut = array(' ', '.');
    return str_replace($limpia_rut, "", $rut);
}

/**
 * Formatea un número como moneda chilena
 * @param float $value Valor a formatear
 * @return string Valor formateado
 */
function formatCurrency($value) {
    return '$' . number_format($value, 0, ',', '.');
}
?>
