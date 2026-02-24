<?php
// ==========================================================
// duemint/tabla.php
// TABLA DE CLIENTES DUEMINT - FORMATO TNA
// Autor: Mauricio Araneda
// ==========================================================

header("Content-Type: text/html; charset=UTF-8");
mb_internal_encoding("UTF-8");

include_once("config.php");

// Conexión
$conn = DbConnect("icontel_clientes");

// Parámetros GET
$p_rut           = isset($_GET['rut']) ? trim($_GET['rut']) : '';
$p_nombre        = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
$p_status        = isset($_GET['status']) ? intval($_GET['status']) : 0;
$p_min_docs      = isset($_GET['min_docs']) ? intval($_GET['min_docs']) : 0;
$p_dias_status   = isset($_GET['dias_status']) ? intval($_GET['dias_status']) : 0;
$p_dias_vencidos = isset($_GET['dias_vencidos']) ? intval($_GET['dias_vencidos']) : 0;

$p_rut = str_replace(['.', ' '], '', strtoupper($p_rut));

// ==========================================================
// 🔥 FUNCIÓN: Cargar lista desde Sweet dinámicamente
// ==========================================================
function obtenerListaSweet()
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

// ==========================================================
// 🔥 FUNCIÓN: Generar <select> de Estado Sweet
// ==========================================================
function estadoSweetSelect($estadoActual, $lista)
{
    $html = "
    <div class='estado-container' style='display:flex; justify-content:flex-start; width:100%;'>
        <select class='estado-sweet' 
                style='text-align:left; text-align-last:left; width:95%; padding-left:4px;'>
    ";

    foreach ($lista as $item) {
        $key   = htmlspecialchars($item['key'],   ENT_QUOTES, 'UTF-8');
        $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');

        $sel = (strcasecmp($estadoActual, $key) === 0) ? "selected" : "";

        $html .= "<option value=\"$key\" $sel>$label</option>";
    }

    $html .= "</select><span class='estado-icono'></span></div>";

    return $html;
}

// ===============================================
// Cargar lista Sweet UNA SOLA VEZ (optimización)
// ===============================================
$listaEstadoSweet = obtenerListaSweet();

if (empty($listaEstadoSweet)) {
    echo "<p style='color:yellow;background:#512554;padding:8px;'>⚠️ Advertencia: Sweet no entregó la lista Estatus_financiero</p>";
}
// ==========================================================
// Ejecutar SP
// ==========================================================
  echo  $sql = sprintf(
    "CALL search_by_status_min_docs(%d, %d, '%s', '%s', %d)",
    $p_status,
    $p_min_docs,
    $conn->real_escape_string($p_rut),
    $conn->real_escape_string($p_nombre),
    $p_dias_vencidos
);

$result = $conn->query($sql);

if (!$result) {
    echo "<h3 style='color:red;text-align:center;'>❌ Error al ejecutar SP:</h3>";
    echo "<p style='color:red;text-align:center;'>" . $conn->error . "</p>";
    exit;
}

$rows = [];
$total_docs = 0;
$total_monto = 0;

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$result->close();
$conn->next_result();
$conn->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Informe Duemint</title>

<link rel="stylesheet" href="css/tabla.css">

</head>
<body>

<table id="tablaDuemint">
  <thead>

    <tr>
      <th>#</th>
      <th>RUT</th>
      <th width="15%">Razón Social</th>
      <th>Estado Sweet</th>
      <th width="15%">Comentario Estado</th>
      <th>Tipo</th>
      <th>Docs</th>
      <th>Monto Total</th>
      <th>Fecha Ref.</th>
      <th>Días</th>
      <th>Ver Duemint</th>
    </tr>

    <tr class="filters">
      <th></th>
      <th><input class="filter-input" data-type="text"   data-col="1" placeholder="RUT"></th>
      <th><input class="filter-input" data-type="text"   data-col="2" placeholder="Cliente"></th>
      <th><input class="filter-input" data-type="text"   data-col="3" placeholder="Estado"></th>
      <th><input class="filter-input" data-type="text"   data-col="4" placeholder="Comentario Estado"></th>
      <th><input class="filter-input" data-type="text"   data-col="5" placeholder="Tipo"></th>
      <th><input class="filter-input" data-type="number" data-col="6" placeholder=">100"></th>
      <th><input class="filter-input" data-type="number" data-col="7" placeholder=">100000"></th>
      <th><input class="filter-input" data-type="date"   data-col="8" placeholder="01-01-2024"></th>
      <th><input class="filter-input" data-type="number" data-col="9" placeholder="<30"></th>
      <th></th>
    </tr>

  </thead>

  <tbody>

<?php
$ptr = 0;

foreach ($rows as $r) {

    $ptr++;

    $numDocs   = (int)$r['num_docs'];
    $montoRaw  = (float)$r['monto_total'];
    $total_docs  += $numDocs;
    $total_monto += $montoRaw;

    $fechaRef = ($r['fecha_referencia'] != '0000-00-00' && !empty($r['fecha_referencia']))
        ? date('d-m-Y', strtotime($r['fecha_referencia']))
        : '';

    $fechaSort = ($fechaRef != '')
        ? date('Ymd', strtotime($r['fecha_referencia']))
        : '';

    $dias = intval($r['dias_ref']);
    $colorDias = 'white';

    if ($dias >= 90)       $colorDias = '#FF3333';
    elseif ($dias >= 60)   $colorDias = '#FF8800';
    elseif ($dias >= 30)   $colorDias = '#FFCC00';
    elseif ($dias < 0)     $colorDias = '#33B5FF';

    $urlSweet = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=" .
                rawurlencode("index.php?module=Accounts&action=DetailView&record=" . $r['id_cuenta']);

    $urlDmt = htmlspecialchars($r['url_cliente']);
    $comentario = $r['cometario_estado_financiero'] ?? '';

    echo "<tr>";

    echo "<td data-sort='{$ptr}'>{$ptr}</td>";
    echo "<td>" . htmlspecialchars($r['rut_cliente']) . "</td>";

    echo "<td style='text-align:left;'>
            <a class='cliente-link' href='{$urlSweet}' target='_blank'>" .
            htmlspecialchars($r['nombre_cliente']) .
            "</a>
          </td>";

    echo "<td class='estado-sweet-cell' data-rut='" . htmlspecialchars($r['rut_cliente']) . "'>" .
         estadoSweetSelect($r['estado_financiero_sweet'], $listaEstadoSweet) .
         "</td>";

    echo "<td style='text-align:left;'>
            <div style='display:flex; align-items:center; width:100%; gap:6px;'>
                <input type='text'
                       class='comentario-estado'
                       data-rut='" . htmlspecialchars($r['rut_cliente'], ENT_QUOTES) . "'
                       value='" . htmlspecialchars($comentario, ENT_QUOTES) . "'
                       style='width:85%; background-color:transparent; color:white; border:1px solid #512554; border-radius:4px; padding:2px 4px; font-size:12px;' />
                <span class='comentario-icono' style='width:14%; font-size:16px;'></span>
            </div>
          </td>";

    echo "<td>" . htmlspecialchars($r['nombre_estado']) . "</td>";
echo "<td data-sort='{$numDocs}' style='text-align:center;'>";
if (!empty($r['url_cliente'])) {
    echo "<a href='" . htmlspecialchars($r['url_cliente'], ENT_QUOTES) . "' 
             target='_blank' 
             style='text-decoration:none; color:inherit;'>";
    echo number_format($numDocs, 0, ',', '.');
    echo "</a>";
} else {
    echo number_format($numDocs, 0, ',', '.');
}
echo "</td>";
    echo "<td data-sort='{$montoRaw}' style='text-align:right;'>$ " . number_format($montoRaw, 0, ',', '.') . "</td>";
    echo "<td data-sort='{$fechaSort}'>{$fechaRef}</td>";
    echo "<td data-sort='{$dias}' style='color:{$colorDias};font-weight:bold;'>{$dias}</td>";
    echo "<td><a href='{$urlDmt}' target='_blank'>🔗 Abrir</a></td>";

    echo "</tr>";
}

?>
  </tbody>

  <tfoot>
    <tr>
      <td colspan="6" align="right"><b>Totales:</b></td>
      <td><?= number_format($total_docs, 0, ',', '.') ?></td>
      <td style="text-align:right;">$ <?= number_format($total_monto, 0, ',', '.') ?></td>
      <td colspan="3">&nbsp;</td>
    </tr>
  </tfoot>
</table>

<!-- Botones -->
<div id="barra-botones-local">
  <button id="btnLimpiarFiltros">🧹 Limpiar Filtros</button>
  <button id="btnExportarXLS">📊 Exportar a XLS</button>
</div>

<script src="js/tabla.js"></script>

<script>
// Limpiar filtros
function limpiarFiltros() {
  const filtros = document.querySelectorAll('.filter-input');
  filtros.forEach(f => {
    f.value = '';
    f.dispatchEvent(new Event('input'));
  });

  const btn = document.getElementById('btnLimpiarFiltros');
  btn.textContent = "✅ Filtros limpiados";

  setTimeout(() => btn.textContent = "🧹 Limpiar Filtros", 2000);

  window.scrollTo({ top: 0, behavior: "smooth" });
}

document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);
</script>

</body>
</html>
