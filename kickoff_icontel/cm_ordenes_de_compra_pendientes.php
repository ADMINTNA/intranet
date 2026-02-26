<?php
// ==========================================================
// KickOff AJAX — Órdenes de Compra Pendientes
// /kickoff_icontel/cm_ordenes_de_compra_pendientes.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

mb_internal_encoding("UTF-8");

// ------------------------------------------------------
// Cargar bootstrap AJAX (sesión + $sg_id + DbConnect)
// ------------------------------------------------------
require_once __DIR__ . "/ajax_bootstrap.php";

// Seguridad mínima
if ($sg_id === "" || $sg_name === "") {
    echo "<div style='padding:20px; color:red;'>❌ Error: sesión inválida.</div>";
    exit;
}

// ==========================================================
// LISTA DINÁMICA DE ESTADOS DESDE SWEET API
// ==========================================================
function obtenerListaEstadoFactura()
{
    $url = "https://sweet.icontel.cl/custom/tools/api_dropdown.php?list=quote_invoice_status_dom";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);

    $resp = curl_exec($ch);
    curl_close($ch);

    if (!$resp) {
        return [];
    }

    $json = json_decode($resp, true);
    return is_array($json) ? $json : [];
}

// Cargar lista una sola vez
$LISTA_ESTADO_FACTURA = obtenerListaEstadoFactura();

// ==========================================================
// FUNCIÓN SELECT (render del combo de Estado)
// ==========================================================
function selectEstadoFactura($estadoActual, $lista, $cotiId)
{
    $html  = "<div class='estado-container'>";
    $html .= "<select class='estado-factura' data-coti-id='{$cotiId}'>";

    foreach ($lista as $item) {
        $key   = htmlspecialchars($item['key'], ENT_QUOTES, 'UTF-8');
        $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');

        // Comparar de forma flexible (mayúsculas/minúsculas, espacios)
        $estadoNorm = strtolower(trim(str_replace('_', ' ', $estadoActual)));
        $keyNorm = strtolower(trim(str_replace('_', ' ', $key)));
        $labelNorm = strtolower(trim(str_replace('_', ' ', $item['label'])));
        
        $selected = ($estadoNorm === $keyNorm || $estadoNorm === $labelNorm) ? "selected" : "";

        $html .= "<option value='{$key}' {$selected}>{$label}</option>";
    }

    $html .= "</select><span class='estado-icono'></span></div>";
    return $html;
}

// ==========================================================
// FUNCIÓN INPUT DTE (render del input de Nº DTE)
// ==========================================================
function inputNumeroDte($numeroDte, $cotiId)
{
    $valor = htmlspecialchars($numeroDte ?? '', ENT_QUOTES, 'UTF-8');
    $html  = "<div class='dte-container'>";
    $html .= "<input type='text' class='numero-dte-input' value='{$valor}' data-coti-id='{$cotiId}'>";
    $html .= "<span class='dte-icono'></span></div>";
    return $html;
}

// ==========================================================
// FUNCIÓN PARA EXTRAER ID DE COTIZACIÓN DESDE URL
// ==========================================================
function extraerCotiId($url)
{
    // Decodificar URL por si tiene caracteres escapados
    $urlDecoded = urldecode($url);
    
    // Formato 1: record=XXXX (URL normal)
    if (preg_match('/record=([a-zA-Z0-9\-]+)/', $urlDecoded, $matches)) {
        return $matches[1];
    }
    
    // Formato 2: record%3D (URL encoded)
    if (preg_match('/record%3D([a-zA-Z0-9\-]+)/i', $url, $matches)) {
        return $matches[1];
    }
    
    return '';
}

// ------------------------------------------------------
// Conexión a SweetCRM
// ------------------------------------------------------
$conn = DbConnect($db_sweet);
$conn->set_charset("utf8mb4");

// Procedimiento almacenado
$sql = "CALL CM_Ordenes_de_Compra_Pendientes()";
$resultado = $conn->query($sql);

$ptr       = 0;
$contenido = "";
$muestra   = ($resultado && $resultado->num_rows > 0);

// ------------------------------------------------------
// Construcción de filas
// ------------------------------------------------------
if ($muestra) {

    while ($lin = $resultado->fetch_assoc()) {

        $ptr++;
        
        // Extraer ID de cotización - primero verificar si viene directo del SP
        $cotiId = isset($lin["coti_id"]) ? $lin["coti_id"] : '';
        
        // Si no viene directo, intentar extraer de la URL
        if (empty($cotiId) && isset($lin["url_coti"])) {
            $cotiId = extraerCotiId($lin["url_coti"]);
        }

        // ===============================
        //  COLOR POR ESTADO DE FACTURA
        // ===============================
        switch ($lin["coti_estado_factura"]) {

            case "DTE Pagado":

                if ($lin["opor_estado"] == "Facturado / Cerrado") {
                    $color = "color:orangered;";
                } else {
                    $color = "color:orange;";
                }
                break;

            default:
                $color = "color:#333;";
        }

        $contenido .= "<tr style='{$color}'>";

        // Celdas seguras
        $contenido .= "<td width='1%'>{$ptr}</td>";

       
        $contenido .= "<td width='1%'>" . htmlspecialchars($lin["coti_numero"]) . "</td>";

        $contenido .= '<td colspan="2"><a target="_blank" href="' .
                       htmlspecialchars($lin["url_coti"]) . '">' .
                       htmlspecialchars($lin["coti_titulo"]) . '</a></td>';

        // Estado como SELECT editable
        $contenido .= "<td class='estado-factura-cell'>" . 
                      selectEstadoFactura($lin["coti_estado_factura"], $LISTA_ESTADO_FACTURA, $cotiId) . 
                      "</td>";
        
        // Nº DTE como INPUT editable
        $contenido .= "<td class='numero-dte-cell'>" . 
                      inputNumeroDte($lin["numero_dte"], $cotiId) . 
                      "</td>";
        
        $contenido .= "<td align='right'>" . htmlspecialchars($lin["moneda"]) . "</td>";

        // Formato numérico según moneda
        if ($lin["moneda"] == "$") {
            $monto = number_format($lin["coti_neto"], 0);
        } else {
            $monto = number_format($lin["coti_neto"], 2);
        }

        $contenido .= "<td align='right'>{$monto}</td>";

        $contenido .= '<td align="right"><a target="_blank" href="' .
                       htmlspecialchars($lin["url_opor"]) . '">' .
                       htmlspecialchars($lin["opor_numero"]) . '</a></td>';

        $contenido .= "<td>" . htmlspecialchars($lin["op_nombre"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["opor_estado"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["account"]) . "</td>";

        $contenido .= "</tr>";
    }

} else {

    $contenido = "
        <tr>
            <td colspan='12' style='text-align:center; padding:12px; color:#666;'>
                ⚠️ No se encontraron Órdenes de Compra pendientes.
            </td>
        </tr>";

}

$conn->close();
unset($resultado);
unset($conn);

// URL nueva cotización
$url_nueva_cotizacion = "https://sweet.icontel.cl/index.php?module=AOS_Quotes&action=EditView";

?>
<link rel="stylesheet" href="css/cm_tareas_pendientes.css?v=<?=time()?>">
<link rel="stylesheet" href="css/cm_ordenes_de_compra.css?v=<?=time()?>">
<link rel="stylesheet" href="css/cm_cobranza_comercial.css?v=<?=time()?>">

<script>
// Definir ruta base para AJAX
window.KICKOFF_BASE_PATH = '/kickoff_icontel/';
</script>



<!-- ======================================================= -->
<!--  TABLA ÓRDENES DE COMPRA PENDIENTES — VERSIÓN AJAX       -->
<!-- ======================================================= -->

<style>
#Ordenes_de_Compra tr.subtit th { background:#512554 !important; color:#fff !important; }
</style>

<div class="tabla-scroll">
<table id="Ordenes_de_Compra" width="100%" cellspacing="0" cellpadding="0" border="0">

    <!-- Título -->
    <tr style="background:#512554; color:white;">
        <td colspan="10" class="titulo" style="padding:8px;">
            &nbsp;&nbsp;🧾 Órdenes de Compra Pendientes
        </td>
        <td colspan="2" align="right" style="padding-right:15px;">
            <a href="<?= $url_nueva_cotizacion ?>" 
               target="new"
               style="color:white; font-size:22px; text-decoration:none;"
               title="Crear Nueva Cotización">
               +
            </a>
        </td>
    </tr>

    <!-- Encabezado -->
    <tr class="subtit">
        <th width="1%">#</th>
        <th width="1%">N°</th>
        <th width="8%" colspan="2" style="white-space:nowrap">
            Asunto&nbsp;<input id="filtro-oc-asunto"
                type="text" placeholder="🔍"
                oninput="ocFilterAsunto(this.value)"
                style="width:80px!important;padding:2px 5px!important;border:1px solid rgba(255,255,255,0.6)!important;border-radius:4px;background:rgba(255,255,255,0.2)!important;color:#fff!important;font-size:11px;font-weight:400;outline:none;vertical-align:middle"><span
                id="filtro-oc-asunto-x"
                onclick="document.getElementById('filtro-oc-asunto').value='';ocFilterAsunto('')"
                title="Quitar filtro"
                style="display:none;cursor:pointer;color:#ffd600;font-weight:bold;font-size:13px;vertical-align:middle;margin-left:2px">✕</span>
            
        </th>
        <th width="2%">Estado</th>
        <th width="2%">N° DTE</th>
        <th width="1%" align="center">$</th>
        <th width="1%" align="center">Bruto</th>
        <th width="1%" align="right">OP #</th>
        <th width="8%">OP Nombre</th>
        <th width="2%">OP Etapa/Estado</th>
        <th width="8%">Proveedor</th>
    </tr>

    <?= $contenido ?>

</table>
</div>

<script>
function ocFilterAsunto(q){
    q=q.toLowerCase();
    var x=document.getElementById('filtro-oc-asunto-x');
    if(x) x.style.display=q?'inline':'none';
    document.querySelectorAll('#Ordenes_de_Compra tr').forEach(function(r){
        if(!r.querySelector('td')) return;
        var tds=r.querySelectorAll('td'), txt='';
        for(var i=1;i<Math.min(5,tds.length);i++){
            if(tds[i]&&tds[i].querySelector('a')){txt=tds[i].textContent.toLowerCase();break;}
        }
        if(!txt) for(var i=1;i<Math.min(5,tds.length);i++){
            if(tds[i]&&tds[i].textContent.trim()){txt=tds[i].textContent.toLowerCase();break;}
        }
        r.style.display=(!q||txt.includes(q))?'':'none';
    });
}
</script>

<script src="js/cm_ordenes_de_compra.js?v=<?=time()?>"></script>
