<?php
// ==========================================
// Configuración global de sesión para Comisiones
// ==========================================
session_name('icontel_intranet_sess');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '.icontel.cl');
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "config.php";
// Fechas para el título
$desde = date('d-m-Y', strtotime($_SESSION['com_inicio']));
$hasta = date('d-m-Y', strtotime($_SESSION['com_fin']));

?>
<!DOCTYPE html>
<html>
<head>
    <title>Resumen Comisiones iContel Telecomunicaciones</title>
    <style type="text/css">
        table {
            border-collapse: collapse;
            font-size: 12px;
            margin: auto;
            
        }
        th {
            background-color: #1F1D3E;
            color: white;
            padding: 4px;
        }
        td {
            background-color: white;
            color: #1F1D3E;
            text-align: right;
            padding: 4px;
        }
        td:first-child, th:first-child {
            text-align: center;
        }
        td:nth-child(2), th:nth-child(2) {
            text-align: left;
        }
        .totales {
            background-color: #1F1D3E !important;
            color: white !important;
            font-weight: bold;
        }
        .oculta {
            display: none;
        }
.numeric {
    text-align: right;
    font-family: monospace; /* opcional, más legible */
}        
     </style>
</head>
<body>
<?php
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "
        SELECT vc.fac_vendedor,
               vc.fac_estado,
               SUM(vc.comision_uf) AS comision_uf,
               SUM(vc.comi_sgv_uf) AS comi_sgv_uf
        FROM ventas_comisiones AS vc
        WHERE vc.fac_fecha BETWEEN '{$_SESSION['com_inicio']}' AND '{$_SESSION['com_fin']}'";
    $query .= $_SESSION['vendedores'] .
        " GROUP BY vc.fac_vendedor, vc.fac_estado
          ORDER BY vc.fac_vendedor";
    $stmt = $pdo->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $summary = [];
    $sgv_summary = ['Unica' => 0, 'Recurrente' => 0, 'Total' => 0];

    foreach ($results as $row) {
        $vendedor = trim($row['fac_vendedor']);
        $estado = $row['fac_estado'];
        $comision_uf = (float)$row['comision_uf'];
        $comi_sgv_uf = (float)$row['comi_sgv_uf'];

        if (!isset($summary[$vendedor])) {
            $summary[$vendedor] = ['Unica' => 0, 'Recurrente' => 0, 'Total' => 0];
        }

        if ($estado === 'Unica') {
            $summary[$vendedor]['Unica'] += $comision_uf;
            $sgv_summary['Unica'] += $comi_sgv_uf;
        } else {
            $summary[$vendedor]['Recurrente'] += $comision_uf;
            $sgv_summary['Recurrente'] += $comi_sgv_uf;
        }

        $summary[$vendedor]['Total'] = $summary[$vendedor]['Unica'] + $summary[$vendedor]['Recurrente'];
    }

    $sgv_summary['Total'] = $sgv_summary['Unica'] + $sgv_summary['Recurrente'];

    echo "<table border='1' id='resumen'>
            <thead>
                <tr> 
                      <th colspan='5' align='center' style='font-size: 20px;'>&nbsp;Comisiones del {$desde} al {$hasta}&nbsp;</th>
                 </tr>
                <tr>
                    <th>Ejecutiv@</th>
                    <th>Unica UF</th>
                    <th>Recurrente UF</th>
                    <th>Total UF</th>
                    <th></th>
             </tr>
            </thead>
            <tbody>";

    foreach ($summary as $vendedor => $data) {
        if ($vendedor === 'Ghislaine Rivera') continue;
        
        echo "<tr>
                <td style='text-align: left;'>{$vendedor}</td>
                <td class='unica' style='text-align: right;'>".number_format($data['Unica'], 2, '.', '')."</td>

                <td class='recurrente'>".number_format($data['Recurrente'], 2, '.', '')."</td>
                <td class='total'>".number_format($data['Total'], 2, '.', '')."</td>
                <td><input type='checkbox' class='excluir-fila' onchange='toggleRow(this)' checked></td>
              </tr>";
    }

    echo "<tr>
            <td style='text-align: left;'>Ghislaine Rivera</td>
            <td class='unica' style='text-align: right;'>".number_format($sgv_summary['Unica'], 2, '.', '')."</td>

            <td >".number_format($sgv_summary['Recurrente'], 2, '.', '')."</td>
            <td class='total'>".number_format($sgv_summary['Total'], 2, '.', '')."</td>
            <td><input type='checkbox' class='excluir-fila' onchange='toggleRow(this)' checked></td>
         </tr>";

echo "<tr>
        <td style='background-color: #1F1D3E; color: white; font-weight: bold; font-size: 12px; text-align: left;'>Totales</td>
        <td id='totalUnica' style='background-color: #1F1D3E; color: white; font-weight: bold; font-size: 12px; text-align: right;'></td>
        <td id='totalRecurrente' style='background-color: #1F1D3E; color: white; font-weight: bold; font-size: 12px; text-align: right;'></td>
        <td id='totalGeneral' style='background-color: #1F1D3E; color: white; font-weight: bold; font-size: 12px; text-align: right;'></td>
        <td style='background-color: #1F1D3E; color: white; font-weight: bold; font-size: 12px;'></td>
      </tr>";
    echo "</tbody></table>";

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
<script>
function toggleRow(checkbox) {
    const fila = checkbox.closest('tr');
    if (!checkbox.checked) {
        fila.classList.add('oculta');
    } else {
        fila.classList.remove('oculta');
    }
    actualizarTotales();
}

function actualizarTotales() {
    let unica = 0, recurrente = 0, total = 0;
    const filas = document.querySelectorAll("#resumen tbody tr:not(.totales):not(.oculta)");

    filas.forEach(row => {
        const u = parseFloat(row.querySelector('.unica')?.textContent || 0);
        const r = parseFloat(row.querySelector('.recurrente')?.textContent || 0);
        const t = parseFloat(row.querySelector('.total')?.textContent || 0);
        unica += u;
        recurrente += r;
        total += t;
    });

    document.getElementById('totalUnica').textContent = unica.toFixed(2);
    document.getElementById('totalRecurrente').textContent = recurrente.toFixed(2);
    document.getElementById('totalGeneral').textContent = total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', actualizarTotales);
</script>
</body>
</html>
