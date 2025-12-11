<?php 
//=====================================================
// /intranet/bsale/sort/tabla.php
// Lista ordenable de NV y Facturas Bsale
// Autor: Mauricio Araneda
// Actualizado: 09-11-2025
//=====================================================

include_once(__DIR__ . '/../includes/security_check.php');
include_once("config.php");

// Mostrar errores críticos (pero sin notices)
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', '1');

// Verificar que existe una query en sesión
if (empty($_SESSION['query'])) {
    die("<p style='color:red; font-weight:bold;'>⚠️ Error: no se encontró una consulta previa en la sesión.</p>");
}

// Conectar a base de datos
$conn = DbConnect("icontel_clientes");
if (!$conn) {
    die("<p style='color:red; font-weight:bold;'>❌ Error de conexión a la base de datos.</p>");
}

// Ejecutar consulta
$query = $_SESSION["query"] . " ORDER BY cbd.fecha_emision DESC";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("<p style='color:red; font-weight:bold;'>❌ Error al ejecutar la consulta: " . mysqli_error($conn) . "</p>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>NV y Facturas Bsale</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Scripts de ordenamiento y filtros -->
  <script src="jquery-3.3.1.min.js"></script>
  <script src="script.js"></script>
  <script src="sort_table.js"></script>
  <script src="suma_selected_col_cel.js"></script>
  <script src="filtro_columna.js"></script>

  <!-- Estilos visuales y de filtros -->
  <link href="style.css" rel="stylesheet">
  <link href="suma_selected_col_cel.css" rel="stylesheet">
  <link href="filtro_columna.css" rel="stylesheet">

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #FFFFFF;
      color: #1F1D3E;
    }
    .container {
      padding: 20px;
    }
    th {
      cursor: pointer;
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 6px 10px;
      text-align: left;
    }
    th:hover {
      background-color: #f0f0f0;
    }
  </style>
</head><body>
<div class='container'>
  <input type='hidden' id='sort' value='asc'>
  <table width='100%' id='empTable' border='1' cellpadding='10'>
    <thead>
      <tr>
        <th>#</th>
        <th onclick='sortTable("tipo_doc");'>Tipo</th>
        <th onclick='sortTable("num_doc");'>Doc Nº</th>
        <th onclick='sortTable("fecha_emision");'>F. Emisión</th>
        <th onclick='sortTable("razon_social");'>Cliente</th>
        <th onclick='sortTable("rut_cliente");'>RUT</th>
        <th onclick='sortTable("id_moneda");'>Moneda</th>
        <th onclick='sortTable("valor_uf");'>Valor Neto</th>
        <th>PDF</th>
        <th>
          Ver&nbsp;&nbsp;
          <input type="button" onClick="exportToExcel('empTable')" value="Exportar" style="font-size: 10px; padding: 2px 6px;" />
        </th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $ptr = 0;
      while ($row = mysqli_fetch_array($result)) {
        $ptr++; 
      ?>
      <tr>
        <td><?= $ptr; ?></td>
        <td><?= htmlspecialchars($row["tipo_doc"]); ?></td>
        <td align="right"><?= $row["num_doc"]; ?></td>
        <td align="center"><?= date("d-m-Y", strtotime($row["fecha_emision"])); ?></td>
        <td><?= htmlspecialchars($row["razon_social"]); ?></td>
        <td align="right"><?= $row["rut_cliente"]; ?></td>
        <td align="center"><?= $row["id_moneda"]; ?></td>
        <td align="right"><?= number_format((float)$row["netAmount"], 2, ',', '.'); ?></td>
        <td><a target="_blank" href="<?= htmlspecialchars($row["urlPdf"]); ?>">PDF</a></td>
        <td><a target="_blank" href="<?= htmlspecialchars($row["urlPublicView"]); ?>">Ver</a></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  initSumaSelectedColCel('#empTable');
  initFiltroColumna('#empTable', {
    numericCols:  [2, 6],
    dateCols:     [3],
    excludedCols: [0, 7, 8]
  });
});
</script>
</body>
</html>