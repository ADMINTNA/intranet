<?php
// ==========================================================
// AJAX Endpoint: Cargar información del cliente
// ==========================================================
header('Content-Type: text/html; charset=UTF-8');
error_reporting(0); // Suprimir errores para AJAX

// Incluir configuración
$config_path = realpath(__DIR__ . '/../config.php');
if ($config_path && file_exists($config_path)) {
    include_once($config_path);
} else {
    echo '<p style="color:red;">Error: No se pudo cargar config.php</p>';
    exit;
}

$empresa = $_GET['empresa'] ?? '';
if (empty($empresa)) {
    echo '<p style="color:red;">No se especificó empresa</p>';
    exit;
}

// Ejecutar la búsqueda de empresa (esto genera $datos_completos)
ob_start();
include_once(__DIR__ . '/../busca_empresa.php');
ob_end_clean();

// Generar HTML de la tabla
?>
<div class="contenedor-scroll-empresa">
    <table>
        <thead>
            <tr>
                <th width="25%">Empresa</th>
                <th width="10%">Ejecutiv@</th>
                <th width="15%">Contacto</th>
                <th width="13%">Teléfono</th>
                <th width="10%">eMail</th>
                <th width="10%">Tipo</th>
                <th width="5%">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $datos_completos ?? '<tr><td colspan="7" style="text-align:center;">No se encontraron datos</td></tr>'; ?>
        </tbody>
    </table>
</div>
