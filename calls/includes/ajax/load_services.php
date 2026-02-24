<?php
// ==========================================================
// AJAX Endpoint: Cargar servicios activos
// ==========================================================
header('Content-Type: text/html; charset=UTF-8');
error_reporting(0);

// Incluir configuración
include_once(__DIR__ . '/../config.php');

$empresa = $_GET['empresa'] ?? '';
if (empty($empresa)) {
    echo '<p style="color:red;">No se especificó empresa</p>';
    exit;
}

// Necesitamos el account_id de la búsqueda de empresa
$conn = DbConnect($db_sweet);
$sql = "CALL tnasolut_sweet.searchbyempresa('%".$empresa."%')";
$result = $conn->query($sql);

$account_id = '';

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $account_id = $row["id"];
}
$conn->close();

// Generar HTML de la tabla de servicios
?>
<div class="contenedor-scroll">
    <table>
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Cant.</th>
                <th width="6%">Estado</th>
                <th width="15%">Servicio</th>
                <th>Contrato Cliente</th>
                <th width="15%">Detalles de instalación</th>
                <th align="left">Proveedor</th>
                <th width="15%">Cód.Servicio</th>
                <th>Fecha</th>
                <th>Plazo</th>
                <th width="6%">Meses</th>
                <th>NV</th>
                <th>Coti_#</th>
                <th>Opor_#</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (!empty($account_id)) {
                include(__DIR__ . '/../busca_servicios_activos.php'); 
            } else {
                echo '<tr><td colspan="15" style="text-align:center; padding:20px;">No se encontraron servicios</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>
