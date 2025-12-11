<?php
// ==========================================================
// /casos/buscador_unico.php - iContel / TNA Group
// Buscador integrado de Casos con orden y exportación
// Autor: Mauricio Araneda
// Fecha: 2025-11-20
// Codificación: UTF-8 sin BOM
// ==========================================================

header("Content-Type: text/html; charset=UTF-8");
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', '1');

// ==========================================================
// Conexión UTF-8 correcta
// ==========================================================
function DbConnect($dbname) {
    $server   = "localhost";
    $user     = "tnasolut_data_studio";
    $password = "P3rf3ct0.,";

    $conn = new mysqli($server, $user, $password, $dbname);
    if ($conn->connect_errno) {
        die("Error conectando a MySQL ($dbname): " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

// ==========================================================
// Recibir parámetros
// ==========================================================
$numero         = $_POST['numero']        ?? '';
$categoria      = $_POST['categoria']     ?? '';
$empresa        = $_POST['empresa']       ?? '';
$codservicio    = $_POST['codservicio']   ?? '';
$texto_avance   = $_POST['texto_avance']  ?? '';
$estado         = $_POST['estado']        ?? '';

// ==========================================================
// Construcción del WHERE
// ==========================================================
$where = "WHERE c.deleted = 0 
          AND a.deleted = 0 
          AND cc.categoria_c != 'Soporte_contrato_mensual'";

if (!empty($numero)) {
    $where .= " AND c.case_number = '" . addslashes($numero) . "'";
} else {

    if (!empty($categoria)) {
        $where .= " AND cc.tipo_caso_c LIKE '%" . addslashes($categoria) . "%'";
    }
    if (!empty($empresa)) {
        $where .= " AND a.name LIKE '%" . addslashes($empresa) . "%'";
    }
    if (!empty($codservicio)) {
        $where .= " AND cc.codigo_servicio_c LIKE '%" . addslashes($codservicio) . "%'";
    }
    if (!empty($estado)) {
        if ($estado === 'cerrados') {
            $where .= " AND c.state LIKE '%closed%'";
        } elseif ($estado === 'abiertos') {
            $where .= " AND c.state NOT LIKE '%closed%'";
        }
    }
    if (!empty($texto_avance)) {
        $av = htmlspecialchars(str_replace("'", "", $texto_avance), ENT_QUOTES, 'UTF-8');
        $where .= " AND (
            cc.avances_1_c LIKE '%$av%' OR
            cc.avances_2_c LIKE '%$av%' OR
            cc.avances_3_c LIKE '%$av%' OR
            cc.avances_4_c LIKE '%$av%' OR
            c.description LIKE '%$av%'
        )";
    }
}

// ==========================================================
// Consulta principal
// ==========================================================
$query = "
    SELECT 
        c.id,
        cc.responsable_c        AS responsable,
        cc.categoria_c          AS categoria,
        cc.proveedor_c          AS proveedor,
        c.case_number           AS numero,
        c.name                  AS asunto,
        c.state                 AS estado,
        a.name                  AS cliente,
        c.date_entered          AS f_creacion,
        cc.codigo_servicio_c    AS codigo_servicio,
        CONCAT_WS(' ', uu.first_name, uu.last_name) AS creado_por,
        c.created_by            AS u_creation,
        c.date_modified         AS f_modifica,
        IF(c.state='Closed',
            TIMEDIFF(c.date_modified, c.date_entered),
            TIMEDIFF(NOW(), c.date_entered)
        ) AS antiguedad,
        cc.horas_sin_servicio_c AS horas,
        CONCAT_WS(' ', u.first_name, u.last_name) AS usuario
    FROM cases AS c
    JOIN tnasolut_sweet.cases_cstm AS cc ON cc.id_c = c.id
    JOIN tnasolut_sweet.accounts   AS a  ON a.id = c.account_id
    JOIN tnasolut_sweet.users      AS u  ON u.id = c.assigned_user_id
    JOIN tnasolut_sweet.users      AS uu ON uu.id = c.created_by
    $where
    ORDER BY numero DESC
";

$conn = DbConnect("tnasolut_sweet");
$result = $conn->query($query);
if (!$result) {
    echo "<h2 style='color:red;'>Error SQL</h2>";
    echo "<pre>" . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8') . "</pre>";
    exit;
}

// ==========================================================
// Función formato fecha
// ==========================================================
function horacl($fecha) {
    return date("d-m-Y H:i", strtotime($fecha));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Casos Sweet – Búsqueda Única</title>
    <link rel="stylesheet" href="../css/filtro_columna.css">
    <link rel="stylesheet" href="css/buscador_unico.css?v=2">
    <script src="../js/jquery-3.3.1.min.js"></script>
    <!--script src="../js/sort_columna.js"></script>
    <script src="../js/filtro_columnas.js"></script-->
</head>
<body>

<!-- ==========================================================
     TABLA ÚNICA CON HEAD + FILTROS + TBODY (estructura correcta)
     ========================================================== -->
<table id="empTable" class="tabla-master">
<thead>
    <!-- LÍNEA 1 — LOGO + TÍTULO -->
    <tr class="header-top">
        <td rowspan="3" width="200">
            <img src="../images/logo_icontel_azul.jpg" height="90" alt="logo">
        </td>
        <td colspan="14" align="center">
            <div class="titulo-general">Buscador de Casos en Sweet</div>
            <div class="subtitulo-general">(Click en columnas para ordenar — Filtros activos)</div>
        </td>
    </tr>

    <!-- LÍNEA 2 — ENCABEZADOS -->
    <tr class="wato-header">
        <th >#</th>
        <th onclick="sortColumn(1)">Número</th>
        <th onclick="sortColumn(2)">Asunto</th>
        <th onclick="sortColumn(3)">Estado</th>
        <th onclick="sortColumn(4)">Responsable</th>
        <th onclick="sortColumn(5)">Cliente</th>
        <th onclick="sortColumn(6)">Asignado a</th>
        <th onclick="sortColumn(7)">Categoría</th>
        <th onclick="sortColumn(8)">F.Creación</th>
        <th onclick="sortColumn(9)">Creado Por</th>
        <th onclick="sortColumn(10)">F.Modif.</th>
        <th onclick="sortColumn(11)">Antigüedad</th>
        <th onclick="sortColumn(12)">Horas s/Servicio</th>
        <th onclick="sortColumn(13)">Proveedor</th>
        <th onclick="sortColumn(14)">Código Servicio</th>
    </tr>

    <!-- LÍNEA 3 — FILTROS -->
    <tr class="filters">
        <?php for ($i=0; $i<15; $i++): ?>
            <th><input type="text" placeholder="🔍"></th>
        <?php endfor; ?>
    </tr>

</thead>
<tbody>
<?php 
$contador = 1;
$url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26action%3DDetailView%26record=";

while ($row = $result->fetch_assoc()):
?>
<tr>
    <td><?= $contador++ ?></td>
    <td><a target="_blank" href="<?= $url . $row['id'] ?>"><?= htmlspecialchars($row['numero']) ?></a></td>
    <td><?= htmlspecialchars($row['asunto']) ?></td>
    <td><?= htmlspecialchars($row['estado']) ?></td>
    <td><?= htmlspecialchars($row['responsable']) ?></td>
    <td><?= htmlspecialchars($row['cliente']) ?></td>
    <td><?= htmlspecialchars($row['usuario']) ?></td>
    <td><?= htmlspecialchars($row['categoria']) ?></td>
    <td><?= horacl($row['f_creacion']) ?></td>
    <td><?= htmlspecialchars($row['creado_por']) ?></td>
    <td><?= horacl($row['f_modifica']) ?></td>
    <td><?= htmlspecialchars($row['antiguedad']) ?></td>
    <td><?= htmlspecialchars($row['horas']) ?></td>
    <td><?= htmlspecialchars($row['proveedor']) ?></td>
    <td><?= htmlspecialchars($row['codigo_servicio']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<script>
// ==========================================================
// ORDENAR COLUMNAS (sortColumn) — compatible con tabla unificada
// ==========================================================
function sortColumn(colIndex) {
    const table = document.getElementById('empTable');
    const tbody = table.tBodies[0];
    const rows = Array.from(tbody.querySelectorAll('tr'));

    const prevCol = parseInt(table.dataset.sortCol || -1);
    const prevDir = table.dataset.sortDir || 'asc';

    let dir = 'asc';
    if (prevCol === colIndex && prevDir === 'asc') {
        dir = 'desc';
    }

    rows.sort((a, b) => {
        const aText = (a.cells[colIndex]?.innerText || '').trim();
        const bText = (b.cells[colIndex]?.innerText || '').trim();

        const aNum = parseFloat(aText.replace(/[^0-9.-]+/g, ''));
        const bNum = parseFloat(bText.replace(/[^0-9.-]+/g, ''));
        const numCompare = (!isNaN(aNum) && !isNaN(bNum));

        let comp;
        if (numCompare) comp = aNum - bNum;
        else comp = aText.localeCompare(bText, 'es', { sensitivity: 'base' });

        return dir === 'asc' ? comp : -comp;
    });

    rows.forEach(r => tbody.appendChild(r));

    table.dataset.sortCol = colIndex;
    table.dataset.sortDir = dir;
}

// ==========================================================
// FILTRAR POR COLUMNA — fila de filtros en THEAD
// ==========================================================
function initColumnFilters() {
    const table = document.getElementById('empTable');
    const tbody = table.tBodies[0];
    const filterInputs = table.querySelectorAll('thead tr.filters input');

    filterInputs.forEach((input, colIndex) => {
        input.addEventListener('input', () => {

            const filters = Array.from(filterInputs).map(e =>
                e.value.toLowerCase().trim()
            );

            Array.from(tbody.rows).forEach(row => {
                let visible = true;

                filters.forEach((filtro, i) => {
                    if (!filtro) return;
                    const txt = (row.cells[i]?.textContent || '').toLowerCase();
                    if (!txt.includes(filtro)) visible = false;
                });

                row.style.display = visible ? '' : 'none';
            });

        });
    });
}

document.addEventListener('DOMContentLoaded', initColumnFilters);
</script>

<?php include_once("../footer/footer.php"); ?>
</body>
</html>
