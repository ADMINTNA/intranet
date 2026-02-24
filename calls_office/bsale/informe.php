<?php
//=====================================================
// /bsale/informe.php
// Cierre de sesión seguro del sistema KickOff
// Autor: Mauricio Araneda
// Actualizado: 08-11-2025
//=====================================================

include_once(__DIR__ . '/../includes/security_check.php');
header('Content-Type: text/html; charset=UTF-8');

$num_doc  = isset($_GET['num_doc']) && $_GET['num_doc'] !== '' ? (int)$_GET['num_doc'] : null;
$tipo_key = $_GET['tipo'] ?? null;

$docs = callGetBsaleDocumento($num_doc, $tipo_key);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Resultados Bsale</title>

<!-- ✅ CSS EXTERNO -->
<link rel="stylesheet" href="css/informe.css">

<!-- ✅ MÓDULOS JS -->
<script src="js/tabla_orden.js"></script>
<script src="js/filtro_columna.js"></script>
</head>
<body>

<!-- 📄 ENCABEZADO FIJO -->
<header>📄 Resultados de Documentos Bsale</header>

<!-- 🧾 CONTENEDOR PRINCIPAL -->
<div class="table-container">
  <table id="tablaDocs">
    <thead>
      <tr>
        <th class="sortable">Tipo</th>
        <th class="sortable">N°</th>
        <th class="sortable">Fecha Emisión</th>
        <th class="sortable">Cliente</th>
        <th class="sortable">RUT</th>
        <th class="sortable" style="text-align:right;">UF</th>
        <th>PDF</th>
        <th>Ver</th>
      </tr>
      <tr class="filter-row">
        <th><input type="text" placeholder="Filtrar..."></th>
        <th><input type="text" placeholder="Filtrar..."></th>
        <th><input type="text" placeholder="Filtrar..."></th>
        <th><input type="text" placeholder="Filtrar..."></th>
        <th><input type="text" placeholder="Filtrar..."></th>
        <th><input type="text" placeholder="Filtrar..."></th>
        <th></th>
        <th></th>
      </tr>
    </thead>

    <tbody>
      <?php if (!empty($docs)): ?>
        <?php foreach ($docs as $d): ?>
        <tr>
          <td><?= q($d['tipo_doc']) ?></td>
          <td><?= q($d['num_doc']) ?></td>
          <td><?= fechaCorta($d['fecha_emision']) ?></td>
          <td><?= q($d['razon_social']) ?></td>
          <td><?= q($d['rut_cliente']) ?></td>
          <td align="right"><?= number_format($d['total_uf'], 2, ',', '.') ?></td>
          <td><a href="<?= q($d['urlPdf']) ?>" target="_blank">PDF</a></td>
          <td><a href="<?= q($d['urlPublicView']) ?>" target="_blank">Ver</a></td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="8" align="center">❌ No se encontraron documentos para los parámetros ingresados.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- 🧹 BARRA DE ACCIONES -->
<div class="action-bar">
  <button class="clear-btn" onclick="clearAllFilters()">🧹 Borrar filtros</button>
  <button class="help-btn" onclick="toggleHelp(true)">❓ Ayuda</button>
</div>

<!-- 🧭 CAPA DE AYUDA -->
<div id="helpOverlay">
  <div id="helpBox">
    <h4>🧭 Guía rápida de uso</h4>
    <ul style="list-style:none; padding-left:0;">
      <li><b>🔹 Ordenar columnas:</b><br>
          Clic en el nombre de la columna → ▲ ascendente, ▼ descendente.</li><br>
      <li><b>🔹 Filtros:</b><br>
          Escribe en los campos bajo el título.<br>
          <i>Ejemplos:</i><br>
          <code>mayor 100</code> &nbsp; <code>menor 500</code><br>
          <code>entre 100 y 2000</code><br>
          <code>entre 01-01-2024 y 31-03-2024</code></li><br>
      <li><b>🔹 Selección y suma:</b><br>
          - Arrastra verticalmente para seleccionar una columna.<br>
          - ⌘ (Mac) / Ctrl (Windows) + clic = celdas no contiguas.<br>
          - Botón <b>“Calcular selección”</b> → sumar o contar.</li>
    </ul>
    <button id="helpClose" onclick="toggleHelp(false)">Cerrar</button>
  </div>
</div>

<!-- ⚙️ FOOTER GLOBAL -->
<?php include "../../footer/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  initFiltrosPorColumna('tablaDocs');
  initOrdenColumnas('tablaDocs');
});

function toggleHelp(show) {
  document.getElementById('helpOverlay').style.display = show ? 'flex' : 'none';
}

function clearAllFilters() {
  document.querySelectorAll('.filter-row input').forEach(i => i.value = '');
  document.querySelectorAll('#tablaDocs tbody tr').forEach(r => r.style.display = '');
}
</script>

</body>
</html>
