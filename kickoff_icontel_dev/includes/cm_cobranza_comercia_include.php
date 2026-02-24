<?php
// ==========================================================
// Cobranza Comercial - iContel / TNA Group
// kickoff_icontel/includes/cm_cobranza_comercia_include.php
// Descripción: Genera tabla usando SP search_by_status_min_docs
// Autor: Mauricio Araneda (Refactorizado 2025-12-16)
// Codificación: UTF-8 sin BOM
// ==========================================================

mb_internal_encoding("UTF-8");

// ----------------------------------------------------------
// Conexión a base icontel_clientes para el SP
// ----------------------------------------------------------
$conn = DbConnect($db_clientes);
if (!$conn) {
    die("<b>Error:</b> No se pudo conectar a la base icontel_clientes.");
}
$conn->set_charset('utf8mb4');


// ==========================================================
// 1) LISTA DINÁMICA DE ESTADOS DESDE SWEET
//    (MISMA API USADA EN DUEMINT)
// ==========================================================
function obtenerListaEstadoSweet()
{
    $url = "https://sweet.icontel.cl/custom/tools/api_dropdown.php?list=Estatus_financiero";

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

// Cargar solo una vez
$LISTA_ESTADO_SWEET = obtenerListaEstadoSweet();


// ==========================================================
// 2) FUNCIÓN SELECT (render del combo)
// ==========================================================
function selectSweetEstado($estadoActual, $lista)
{
    $html  = "<div class='estado-container'>";
    $html .= "<select class='estado-sweet'>";

    foreach ($lista as $item) {

        $key   = htmlspecialchars($item['key'], ENT_QUOTES, 'UTF-8');
        $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');

        $selected = (strcasecmp(trim($estadoActual), trim($key)) === 0) ? "selected" : "";

        $html .= "<option value='{$key}' {$selected}>{$label}</option>";
    }

    $html .= "</select><span class='estado-icono'></span></div>";
    return $html;
}


// ==========================================================
// 3) EJECUTAR SP DE DUEMINT
//    CALL icontel_clientes.search_by_status_min_docs(3, 0, '', '', 60)
//    Columnas: rut_cliente, nombre_cliente, id_cuenta, 
//              estado_financiero_sweet, cometario_estado_financiero,
//              estado, nombre_estado, num_docs, monto_total, 
//              url_cliente, fecha_referencia, dias_ref, dias_vencidos_calc
// ==========================================================
$sql = "CALL icontel_clientes.search_by_status_min_docs(3, 0, '', '', 60)";
$result = $conn->query($sql);

if (!$result) {
    die("<b>Error SP:</b> " . $conn->error);
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$result->close();
$conn->next_result();  // Liberar resultados del SP
$conn->close();


// ==========================================================
// 4) TOTALES
// ==========================================================
$total_docs  = 0;
$total_monto = 0;

foreach ($rows as $r) {
    $total_docs  += (int)$r['num_docs'];
    $total_monto += (float)$r['monto_total'];
}


// ==========================================================
// 5) CONSTRUIR TABLA HTML
// ==========================================================
$contenido = "";
$ptr = 0;

foreach ($rows as $row) {

    $ptr++;

    // Formateo de campos
    $rut        = htmlspecialchars($row["rut_cliente"]);
    $cliente    = htmlspecialchars($row["nombre_cliente"], ENT_QUOTES, 'UTF-8');
    $comentario = htmlspecialchars($row["cometario_estado_financiero"] ?? '', ENT_QUOTES, 'UTF-8');
    $tipo       = htmlspecialchars($row["nombre_estado"]);
    $numDocs    = (int)$row['num_docs'];
    $montoRaw   = (float)$row['monto_total'];
    $dias       = intval($row['dias_ref']);

    // Fecha formateada
    $fechaRef = ($row['fecha_referencia'] != '0000-00-00' && !empty($row['fecha_referencia']))
        ? date('d-m-Y', strtotime($row['fecha_referencia']))
        : '';

    // URL de Sweet
    $urlSweet = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=" .
                rawurlencode("index.php?module=Accounts&action=DetailView&record=" . $row['id_cuenta']);

    // URL de Duemint
    $urlDuemint = htmlspecialchars($row['url_cliente'] ?? '');

    // Color de días según vencimiento
    $colorDias = 'inherit';
    if ($dias >= 90)       $colorDias = '#FF3333';
    elseif ($dias >= 60)   $colorDias = '#FF8800';
    elseif ($dias >= 30)   $colorDias = '#FFCC00';
    elseif ($dias < 0)     $colorDias = '#33B5FF';

    // Clase de fila según estado
    $estado = strtolower(trim($row["estado_financiero_sweet"] ?? ''));
    switch ($estado) {
        case 'suspender':              $clase = 'estado-suspender'; break;
        case 'suspendido':             $clase = 'estado-suspendido'; break;
        case 'cobranza_comercial':     $clase = 'estado-cobranza'; break;
        case 'acuerdo_cobranza_comer': $clase = 'estado-acuerdo_cobranza_comer'; break;
        default:                       $clase = ''; break;
    }

    $contenido .= "<tr class='{$clase}'>
        <td>{$ptr}</td>

        <td style='text-align:left;'>{$rut}</td>

        <td style='text-align:left;'>
            <a target='_blank' 
               href='{$urlSweet}' 
               style='color:#1F1D3E; text-decoration:none; font-weight:bold;'>{$cliente}</a>
        </td>

        <td class='estado-sweet-cell' data-rut='{$rut}'>
            " . selectSweetEstado($row["estado_financiero_sweet"], $LISTA_ESTADO_SWEET) . "
        </td>

        <td class='comentario-cell' data-rut='{$rut}'>
            <div class='comentario-container'>
                <input type='text' class='comentario-input' value='{$comentario}'>
                <span class='comentario-icono'></span>
            </div>
        </td>

        <td style='text-align:center;'>{$tipo}</td>

        <td style='text-align:center;'>" . (
            !empty($urlDuemint)
            ? "<a href='{$urlDuemint}' target='_blank' style='text-decoration:none; color:inherit;'>{$numDocs}</a>"
            : $numDocs
        ) . "</td>

        <td style='text-align:right;'>$ " . number_format($montoRaw, 0, ',', '.') . "</td>

        <td style='text-align:center;'>{$fechaRef}</td>

        <td style='text-align:center; color:{$colorDias}; font-weight:bold;'>{$dias}</td>

        <td style='text-align:center;'>" . (
            !empty($urlDuemint)
            ? "<a href='{$urlDuemint}' target='_blank'>🔗</a>"
            : ""
        ) . "</td>

    </tr>";
}

// Agregar fila de totales
$contenido .= "<tr style='background:#512554; color:white; font-weight:bold;'>
    <td colspan='6' align='right'>Totales:</td>
    <td style='text-align:center;'>{$total_docs}</td>
    <td style='text-align:right;'>$ " . number_format($total_monto, 0, ',', '.') . "</td>
    <td colspan='3'>&nbsp;</td>
</tr>";
?>
