<?php
// ==========================================================
// /intranet/comisiones_new/informe.php
// Reporte de Comisiones Modernizado (Graphs & Interactive)
// ==========================================================

require_once "config.php";

// 1) Recibir y Validar Inputs
$fecha_ini = $_POST["inicio"] ?? $_SESSION['com_inicio'] ?? date('Y-m-d');
$fecha_fin = $_POST["fin"] ?? $_SESSION['com_fin'] ?? date('Y-m-d');
$ejecutivos = $_POST["ejecutivo"] ?? $_SESSION['com_ejecutivos'] ?? [];
$tipos_factura = $_POST["tipo_factura"] ?? $_SESSION['com_tipos'] ?? [];

// Guardar en sesión
$_SESSION['com_inicio'] = $fecha_ini;
$_SESSION['com_fin'] = $fecha_fin;
$_SESSION['com_ejecutivos'] = $ejecutivos;
$_SESSION['com_tipos'] = $tipos_factura;

// 2) Preparar SQL
$vendedores_sql = genera_condicion($ejecutivos, "vc.fac_vendedor");
$tipo_sql = genera_condicion($tipos_factura, "vc.fac_estado");

$query_tmp = "CALL ventas_comisiones_periodo('{$fecha_ini}', '{$fecha_fin}')";
recrea_base_comisiones($query_tmp);

$sql_base = "
    SELECT  
        vc.fac_num, vc.coti_num, vc.fac_url, vc.coti_url, vc.fac_fecha,
        vc.fechacierre, vc.meta_uf, vc.cierre_uf, vc.cumplimiento, vc.comision,
        SUM(vc.neto_uf) AS neto_uf,
        SUM(vc.costo_uf) AS costo_uf,
        SUM(vc.neto_uf) - SUM(vc.costo_uf) AS margen_uf,
        SUM(vc.neto_comi_uf) AS neto_comi_uf,
        SUM(vc.comision_uf) AS comision_uf,
        SUM(vc.comi_sgv_uf) AS comi_sgv_uf,
        vc.fac_estado, vc.fac_cliente, vc.fac_vendedor
    FROM ventas_comisiones AS vc
    WHERE vc.fac_fecha BETWEEN '{$fecha_ini}' AND '{$fecha_fin}'
    $vendedores_sql
    $tipo_sql
    GROUP BY 
        vc.fac_num, vc.coti_num, vc.fac_url, vc.coti_url, vc.fac_fecha,
        vc.fechacierre, vc.meta_uf, vc.cierre_uf, vc.cumplimiento, vc.comision,
        vc.fac_estado, vc.fac_cliente, vc.fac_vendedor
    ORDER BY vc.fac_num DESC
";

$res_data = $con->query($sql_base);

// 3) Procesar Totales y Gráficos
$stats = [
    'facturacion' => 0,
    'comisiones' => 0,
    'margen' => 0,
    'comi_sgv' => 0
];

$graph_data = [
    'ejecutivos' => [],
    'valores' => []
];

$ejecutivo_totals = [];
$rows = [];

if ($res_data) {
    while ($row = $res_data->fetch_assoc()) {
        $rows[] = $row;
        $stats['facturacion'] += $row['neto_uf'];
        $stats['comisiones'] += $row['comision_uf'];
        $stats['margen'] += $row['margen_uf'];
        $stats['comi_sgv'] += $row['comi_sgv_uf'];

        $e = $row['fac_vendedor'];
        if (!isset($ejecutivo_totals[$e])) $ejecutivo_totals[$e] = 0;
        $ejecutivo_totals[$e] += $row['comision_uf'];
    }
}

arsort($ejecutivo_totals);
foreach ($ejecutivo_totals as $e => $v) {
    $graph_data['ejecutivos'][] = $e;
    $graph_data['valores'][] = round($v, 2);
}

?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Comisiones - iConTel</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="/intranet/favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <style>
        .stats-grid { grid-template-columns: repeat(4, 1fr); }
        @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>
<body>
    <div class="container">
        <header class="header-section no-print">
            <div>
                <h1>Informe Comisiones</h1>
                <p>Periodo: <?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="theme-switch" onclick="toggleTheme()">
                    <div class="icon active" id="theme-light">☀️</div>
                    <div class="icon" id="theme-dark">🌙</div>
                </div>
                <a href="informe_resumen.php" class="btn" style="background: var(--secondary-color);">Ver Resumen</a>
                <a href="index.php" class="btn btn-secondary">Nueva Búsqueda</a>
                <button onclick="window.print()" class="btn">PDF</button>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <span class="label">Facturación Total (UF)</span>
                <span class="value"><?= formatDecimal($stats['facturacion']) ?></span>
            </div>
            <div class="stat-card">
                <span class="label">Total Comisiones (UF)</span>
                <span class="value"><?= formatDecimal($stats['comisiones']) ?></span>
            </div>
            <div class="stat-card">
                <span class="label">Margen Total (UF)</span>
                <span class="value"><?= formatDecimal($stats['margen']) ?></span>
            </div>
            <div class="stat-card">
                <span class="label">Comisión SGV (UF)</span>
                <span class="value"><?= formatDecimal($stats['comi_sgv']) ?></span>
            </div>
        </div>

        <div class="dashboard-row no-print">
            <div class="card" style="grid-column: span 2;">
                <h3>Comisiones por Ejecutivo (UF)</h3>
                <canvas id="comiChart" height="80"></canvas>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin:0">Detalle de Comisiones (<?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>)</h3>
                <button onclick="exportToExcel('tblComi')" class="btn no-print" style="padding: 5px 15px; font-size: 0.8rem;">Excel</button>
            </div>
            <div class="table-container">
                <table id="tblComi" class="display">
                    <thead>
                        <tr>
                            <th>Fact N°</th>
                            <th>Coti N°</th>
                            <th>Fecha</th>
                            <th>Ejecutivo</th>
                            <th>Cliente</th>
                            <th class="text-right">Neto UF</th>
                            <th class="text-right">Margen UF</th>
                            <th class="text-right">Comisión %</th>
                            <th class="text-right">Comisión UF</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rows as $row): ?>
                            <tr>
                                <td><a href="<?= $row['fac_url'] ?>" target="_blank"><?= $row['fac_num'] ?></a></td>
                                <td><a href="<?= $row['coti_url'] ?>" target="_blank"><?= $row['coti_num'] ?></a></td>
                                <td><?= date('d/m/Y', strtotime($row['fac_fecha'])) ?></td>
                                <td><?= $row['fac_vendedor'] ?></td>
                                <td><?= $row['fac_cliente'] ?></td>
                                <td class="text-right"><?= formatDecimal($row['neto_uf']) ?></td>
                                <td class="text-right"><?= formatDecimal($row['margen_uf']) ?></td>
                                <td class="text-right"><?= $row['comision'] ?>%</td>
                                <td class="text-right" style="font-weight:600; color:var(--secondary-color)"><?= formatDecimal($row['comision_uf']) ?></td>
                                <td><span class="badge"><?= $row['fac_estado'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        </div>
    </div>

    <script>
        $(document).ready(function() {
            // DataTables
            $('#tblComi').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "pageLength": 50,
                "order": [[0, "desc"]]
            });

            // Gráfico Elegante
            const ctx = document.getElementById('comiChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, '#E67E22');
            gradient.addColorStop(1, '#D35400');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($graph_data['ejecutivos']) ?>,
                    datasets: [{
                        label: 'Comisión UF',
                        data: <?= json_encode($graph_data['valores']) ?>,
                        backgroundColor: gradient,
                        hoverBackgroundColor: '#F39C12',
                        borderRadius: 12
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(31, 29, 62, 0.9)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold', family: "'Inter', sans-serif" },
                            bodyFont: { size: 13, family: "'Inter', sans-serif" },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Comisión: ' + context.parsed.y.toLocaleString('es-CL', {minimumFractionDigits: 2}) + ' UF';
                                }
                            }
                        }
                    },
                    scales: { 
                        y: { 
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)',
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Tema
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
        });

        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            if(theme === 'dark') {
                $('#theme-light').removeClass('active');
                $('#theme-dark').addClass('active');
            } else {
                $('#theme-dark').removeClass('active');
                $('#theme-light').addClass('active');
            }
        }

        function toggleTheme() {
            setTheme(document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light');
        }

        function exportToExcel(tableId) {
            let tableData = document.getElementById(tableId).outerHTML;
            let a = document.createElement('a');
            a.href = `data:application/vnd.ms-excel, ${encodeURIComponent(tableData)}`;
            a.download = 'Comisiones_' + new Date().getTime() + '.xls';
            a.click();
        }
    </script>
</body>
</html>
