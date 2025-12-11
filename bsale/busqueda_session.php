<?php
//=====================================================
// /intranet/bsale/busqueda_session.php
// Busca Notas de venta de BSale
// Autor: Mauricio Araneda
// Actualizado: 09-11-2025
//=====================================================

session_start(); // Asegura que se pueda usar $_SESSION

// Recibir variables desde POST de forma segura
$numero   = isset($_POST['numero']) ? trim($_POST['numero']) : null;
$tipo_doc = isset($_POST['tipo'])   ? strtolower(trim($_POST['tipo'])) : null;

// Normalizar tipo de documento
$tipo = null;
if ($tipo_doc === 'fac') {
    $tipo = 'FACTURA ELECTRONICA';
} elseif ($tipo_doc === 'nv') {
    $tipo = 'NOTA DE VENTA';
}

// Construcción del SQL con condiciones opcionales
$sql = "
    SELECT 
        cbd.id_bsale,
        cbd.tipo_doc,
        cbd.num_doc,
        cbd.fecha_emision,
        cbd.fecha_vencimiento,
        cbd.razon_social,
        cbd.rut AS rut_cliente,
        cbd.direccion,
        cbd.comuna,
        cbd.ciudad,
        cbd.id_moneda,
        cbd.valor_uf,
        cbd.total_uf,
        cbd.neto_uf,
        cbd.iva_uf,
        cbd.netAmount,
        cbd.totalAmount AS total_pesos,
        cbd.urlPdf,
        cbd.urlPublicView,
        cbd.state AS estado
    FROM icontel_clientes.cron_bsale_documents AS cbd
    WHERE (" . ($numero ? "cbd.num_doc = '" . addslashes($numero) . "'" : "1") . ")
      AND (" . ($tipo   ? "cbd.tipo_doc = '" . addslashes($tipo) . "'"   : "1") . ")
";

// Guardar la query en sesión
$_SESSION["query"] = $sql;

// Redirigir al visor de resultados
header('Location: sort/index.php');
exit;
?>