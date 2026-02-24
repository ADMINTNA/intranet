<?php
// ==========================================================
// /intranet/bsale/busqueda_resultado.php
// Resultados directos de búsqueda BSale con formato TNA
// Autor: Mauricio Araneda
// Actualizado: 09-11-2025
// ==========================================================
include_once(__DIR__ . '/../includes/security_check.php');
include_once('config.php');

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', '1');

// --- Recibir parámetros ---
$numero   = $_REQUEST['numero'] ?? null;
$tipo_doc = strtolower($_REQUEST['tipo'] ?? '');

// --- Normalizar tipo ---
if ($tipo_doc === 'fac') {
    $tipo = 'FACTURA ELECTRONICA';
} elseif ($tipo_doc === 'nv') {
    $tipo = 'NOTA DE VENTA';
} else {
    $tipo = null;
}

// --- Construir condiciones ---
$condiciones = [];
if ($numero) $condiciones[] = "cbd.num_doc = '" . addslashes($numero) . "'";
if ($tipo)   $condiciones[] = "cbd.tipo_doc = '" . addslashes($tipo) . "'";
$where = $condiciones ? implode(" AND ", $condiciones) : "1";

// --- Query final ---
 echo $query = "
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
    WHERE $where
    ORDER BY cbd.fecha_emision DESC
";

$conn = DbConnect("icontel_clientes");
$result = mysqli_query($conn, $query);
if (!$result) {
    die("<p style='color:red; font-weight:bold;'>❌ Error SQL: " . mysqli_error($conn) . "</p>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Notas de Venta y Facturas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Estilos y scripts -->
    <link href="../css/sort_columna.css" rel="stylesheet" />
    <script src="../js/sort_columna.js"></script>    
    <link href="../css/tna.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/filtro_columnas.css" />
    <script src="../js/filtro_columnas.js"></script>
</head>

<body>

<!-- 🔷 HEADER -->
<header>
 <!-- 🧾 ENCABEZADO -->
  <table align="center" border="0" width="100%">
    <tr align="center" style="color: white; background-color: #1F1D3E;">
      <th width="200" height="115" valign="top" align="left">
        <img src="../images/logo_icontel_azul.jpg" height="115" alt="Logo iContel"/>
      </th>
      <td>
        <table width="100%" height="100%">
          <tr height="90">
            <th align="center" style="font-size: 20px;">
              Notas de Venta y Facturas en BSale
            </th>
          </tr>
          <tr>
            <td align="center" style="font-size: 12px;">
              (Click sobre los Títulos para ordenar)
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</header>
 
<!-- 📊 CONTENEDOR DE TABLA -->
<table id="empTable">
<?php if (mysqli_num_rows($result) == 0): ?>
  <p style="color: lightcoral; text-align: center; padding: 20px;">
    <b>⚠️ No se encontraron resultados para los criterios ingresados.</b>
  </p>
<?php else: ?>
  <table id="tablaDuemint">
    <thead>
      <tr>
        <th class="sortable" data-col="ptr">#</th>
        <th class="sortable" data-col="tipo_doc">Tipo</th>
        <th class="sortable" data-col="num_doc">Doc Nº</th>
        <th class="sortable" data-col="fecha_emision">F. Emisión</th>
        <th class="sortable" data-col="razon_social">Cliente</th>
        <th class="sortable" data-col="rut_cliente">RUT</th>
        <th class="sortable" data-col="id_moneda">Moneda</th>
        <th class="sortable" data-col="netAmount">Valor Neto</th>
        <th>PDF</th>
        <th>Ver</th>
      </tr>
      <tr class="filters">
        <th><input class="filter-input" placeholder="#" /></th>
        <th><input class="filter-input" placeholder="Tipo" /></th>
        <th><input class="filter-input" placeholder="Doc Nº" /></th>
        <th><input class="filter-input" placeholder="F. Emisión" /></th>
        <th><input class="filter-input" placeholder="Cliente" /></th>
        <th><input class="filter-input" placeholder="RUT" /></th>
        <th><input class="filter-input" placeholder="Moneda" /></th>
        <th><input class="filter-input" placeholder="Valor Neto" /></th>
        <th></th>
        <th></th>
      </tr>
    </thead>    
<tbody>
      <?php $ptr = 0; while ($row = mysqli_fetch_array($result)): $ptr++; ?>
      <tr>
        <td><?= $ptr ?></td>
        <td><?= htmlspecialchars($row["tipo_doc"]) ?></td>
        <td align="right"><?= $row["num_doc"] ?></td>
        <td align="center"><?= date("d-m-Y", strtotime($row["fecha_emision"])) ?></td>
        <td align="left"    ><?= htmlspecialchars($row["razon_social"]) ?></td>
        <td align="right"><?= $row["rut_cliente"] ?></td>
        <td align="center"><?= $row["id_moneda"] ?></td>
        <td align="right"><?= number_format((float)$row["netAmount"], 2, ',', '.') ?></td>
        <td><a target="_blank" href="<?= htmlspecialchars($row["urlPdf"]) ?>">PDF</a></td>
        <td><a target="_blank" href="<?= htmlspecialchars($row["urlPublicView"]) ?>">Ver</a></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php endif; ?>
</div>

<!-- 🧩 INICIALIZADORES -->
    
<script>
    document.getElementById("btnLimpiarFiltros").addEventListener("click", function () {
      const inputs = document.querySelectorAll("#empTable .filter-input");
      inputs.forEach(input => input.value = "");
      const evt = new Event("input");
      inputs[0].dispatchEvent(evt); // Forzar re-filtrado
    });
  });
</script>
<!-- 🔻 FOOTER CORPORATIVO -->
<footer>
  &#9786; &copy;&reg;&trade; Copyright <span id="Year"></span> <b>iConTel</b> – 
  ☎ <a href="tel:228409988">+56 2 2840 9988</a> – 
  📧 <a href="mailto:contacto@icontel.cl">contacto@icontel.cl</a> – 
  🏠 Badajoz 45, piso 17, Las Condes, Santiago, Chile.
  <script>
    document.getElementById("Year").textContent = new Date().getFullYear();
  </script>
</footer>
<!-- 🔧 BARRA DE BOTONES DE FILTRO -->
<div id="barra-botones">
  <button id="btnLimpiarFiltros">🔄 Limpiar filtros</button>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    initFiltroColumna('#empTable', {
      numericCols:  [2, 6], // puedes ajustar según columnas numéricas
      dateCols:     [3],    // columna de fecha
      excludedCols: [0, 8, 9] // columnas donde no quieres filtro (#, PDF, Ver)
    });
  });
</script>
</body>
</html>