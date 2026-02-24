<?php
// ==========================================================
// AJAX Endpoint: Cargar portal de pago
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

// Necesitamos el account_id y rut de la búsqueda de empresa
$conn = DbConnect($db_sweet);
$sql = "CALL tnasolut_sweet.searchbyempresa('%".$empresa."%')";
$result = $conn->query($sql);

$account_id = '';
$rut = '';

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $account_id = $row["id"];
    $rut = str_replace([' ', '.'], "", $row["rut"]);
}
$conn->close();

// Incluir el código de Duemint
$dumit_por_vencer = 0;
$dumit_vencida = 0;
$dumit_portal = '#';
$endummit = false;

if (!empty($account_id) && !empty($rut)) {
    ob_start();
    include(__DIR__ . '/../busca_duemint.php');
    ob_end_clean();
}

// Generar HTML del portal de pago
?>
<div class="payment-portal">
    <div class="payment-card warning">
        <div class="payment-label">Por Vencer</div>
        <div class="payment-amount">$<?php echo number_format($dumit_por_vencer, 0, ',', '.'); ?></div>
    </div>
    
    <div class="payment-card danger">
        <div class="payment-label">Vencido</div>
        <div class="payment-amount">$<?php echo number_format($dumit_vencida, 0, ',', '.'); ?></div>
    </div>
    
    <div class="payment-card">
        <div class="payment-label">Cuenta Corriente</div>
        <a href="<?php echo $dumit_portal; ?>" target="_blank" class="payment-link">
            <?php if (!$endummit) { ?>
                ⚠️ NO EN DUEMINT
            <?php } else { ?>
                Ver Detalle Completo →
            <?php } ?>
        </a>
    </div>
</div>
