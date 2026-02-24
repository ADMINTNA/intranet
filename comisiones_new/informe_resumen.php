<?php
// ==========================================================
// /intranet/comisiones_new/informe_resumen.php
// Reporte Resumen de Comisiones Modernizado
// ==========================================================

require_once "config.php";

// 1) Recuperar Variables de Sesión (Ya preparadas por informe.php o index.php)
$fecha_ini = $_SESSION['com_inicio'] ?? date('Y-m-d');
$fecha_fin = $_SESSION['com_fin'] ?? date('Y-m-d');
$ejecutivos = $_SESSION['com_ejecutivos'] ?? [];
$tipos_factura = $_SESSION['com_tipos'] ?? [];

// 2) Preparar SQL de Agregación
$vendedores_sql = genera_condicion($ejecutivos, "vc.fac_vendedor");
$tipo_sql = genera_condicion($tipos_factura, "vc.fac_estado");

// Asegurar que la tabla temporal existe (por si se entra directo al resumen)
$query_tmp = "CALL ventas_comisiones_periodo('{$fecha_ini}', '{$fecha_fin}')";
recrea_base_comisiones($query_tmp);

$sql_resumen = "
    SELECT  
        vc.fac_vendedor, 
        vc.fac_estado,
        SUM(vc.neto_uf) AS neto_uf,
        SUM(vc.costo_uf) AS costo_uf,
        SUM(vc.neto_uf) - SUM(vc.costo_uf) AS margen_uf,
        SUM(vc.neto_comi_uf) AS neto_comi_uf,
        SUM(vc.comision_uf) AS comision_uf,
        SUM(vc.comi_sgv_uf) AS comi_sgv_uf
    FROM ventas_comisiones AS vc
    WHERE vc.fac_fecha BETWEEN '{$fecha_ini}' AND '{$fecha_fin}'
    $vendedores_sql
    $tipo_sql
    GROUP BY vc.fac_vendedor, vc.fac_estado
    ORDER BY vc.fac_vendedor, vc.fac_estado
";

$res_data = $con->query($sql_resumen);

$rows = [];
$summary2 = []; // Para la segunda tabla (Unica vs Recurrente)
$summary_chart = []; // Para el gráfico por tipo
$sgv_summary = ['Unica' => 0, 'Recurrente' => 0];

if ($res_data) {
    while ($row = $res_data->fetch_assoc()) {
        $rows[] = $row;
        
        $vendedor = trim($row['fac_vendedor']);
        $estado = $row['fac_estado'];
        $comision_uf = (float)$row['comision_uf'];
        $comi_sgv_uf = (float)$row['comi_sgv_uf'];

        if (!isset($summary2[$vendedor])) {
            $summary2[$vendedor] = ['Unica' => 0, 'Recurrente' => 0];
        }

        if ($estado === 'Unica') {
            $summary2[$vendedor]['Unica'] += $comision_uf;
            $sgv_summary['Unica'] += $comi_sgv_uf;
        } else {
            $summary2[$vendedor]['Recurrente'] += $comision_uf;
            $sgv_summary['Recurrente'] += $comi_sgv_uf;
        }

        // Datos para el gráfico por tipo
        $summary_chart[$estado] = ($summary_chart[$estado] ?? 0) + $comision_uf;
    }
}

// Asegurar que Ghislaine esté en la segunda tabla si tiene SGV
if (!isset($summary2['Ghislaine Rivera'])) {
    $summary2['Ghislaine Rivera'] = ['Unica' => 0, 'Recurrente' => 0];
}
// Asignar el acumulado de SGV a Ghislaine en la segunda tabla
$summary2['Ghislaine Rivera']['Unica'] = $sgv_summary['Unica'];
$summary2['Ghislaine Rivera']['Recurrente'] = $sgv_summary['Recurrente'];

?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Comisiones - iConTel</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="/intranet/favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-grid { display: grid; grid-template-columns: 1fr; gap: 30px; margin-top: 20px; }
        .btn-toggle { background: var(--secondary-color); color: white; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
        .table-resumen { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-resumen th { background: var(--primary-color); color: white; padding: 10px; text-align: left; font-size: 0.85rem; }
        .table-resumen td { padding: 10px; border-bottom: 1px solid rgba(0,0,0,0.05); font-size: 0.9rem; }
        [data-theme="dark"] .table-resumen td { border-bottom: 1px solid rgba(255,255,255,0.05); }
        .tfoot-total { background: var(--primary-color); color: white; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header-section no-print">
            <div>
                <h1>Resumen de Comisiones</h1>
                <p>Periodo: <?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="theme-switch" onclick="toggleTheme()">
                    <div class="icon active" id="theme-light">☀️</div>
                    <div class="icon" id="theme-dark">🌙</div>
                </div>
                <a href="informe.php" class="btn-toggle">Ver Analítico</a>
                <a href="index.php" class="btn btn-secondary">Nueva Búsqueda</a>
                <button onclick="window.print()" class="btn">PDF</button>
            </div>
        </header>

        <div class="card no-print" style="margin-bottom: 30px;">
            <h3 style="margin-top:0">Distribución de Comisiones por Tipo</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="typeChart"></canvas>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Tabla 1: Detalle por Ejecutivo y Tipo -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin:0">1. Detalle por Ejecutivo y Estado (<?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>)</h3>
                    <button onclick="exportToExcel('tblResumen1', 'Resumen_Detalle')" class="btn no-print" style="padding: 5px 15px; font-size: 0.8rem;">Excel</button>
                </div>
                <div class="table-container">
                    <table id="tblResumen1" class="table-resumen">
                        <thead>
                            <tr>
                                <th>Ejecutivo</th>
                                <th>Estado</th>
                                <th class="text-right">Venta (UF)</th>
                                <th class="text-right">Costo (UF)</th>
                                <th class="text-right">Margen (UF)</th>
                                <th class="text-right">Neto Comi (UF)</th>
                                <th class="text-right">Comisión (UF)</th>
                                <th class="text-right">SGV (UF)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $t1 = ['v'=>0, 'c'=>0, 'm'=>0, 'nc'=>0, 'co'=>0, 'sgv'=>0];
                            foreach($rows as $row): 
                                $t1['v'] += $row['neto_uf'];
                                $t1['c'] += $row['costo_uf'];
                                $t1['m'] += $row['margen_uf'];
                                $t1['nc'] += $row['neto_comi_uf'];
                                $t1['co'] += $row['comision_uf'];
                                $t1['sgv'] += $row['comi_sgv_uf'];
                            ?>
                                <tr>
                                    <td><?= $row['fac_vendedor'] ?></td>
                                    <td><span class="badge"><?= $row['fac_estado'] ?></span></td>
                                    <td class="text-right"><?= formatDecimal($row['neto_uf']) ?></td>
                                    <td class="text-right"><?= formatDecimal($row['costo_uf']) ?></td>
                                    <td class="text-right"><?= formatDecimal($row['margen_uf']) ?></td>
                                    <td class="text-right"><?= formatDecimal($row['neto_comi_uf']) ?></td>
                                    <td class="text-right" style="font-weight:600; color:var(--secondary-color)"><?= formatDecimal($row['comision_uf']) ?></td>
                                    <td class="text-right"><?= formatDecimal($row['comi_sgv_uf']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="tfoot-total">
                                <td colspan="2">TOTALES</td>
                                <td class="text-right"><?= formatDecimal($t1['v']) ?></td>
                                <td class="text-right"><?= formatDecimal($t1['c']) ?></td>
                                <td class="text-right"><?= formatDecimal($t1['m']) ?></td>
                                <td class="text-right"><?= formatDecimal($t1['nc']) ?></td>
                                <td class="text-right"><?= formatDecimal($t1['co']) ?></td>
                                <td class="text-right"><?= formatDecimal($t1['sgv']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Tabla 2: Resumen Ejecutivo (Unica vs Recurrente) -->
            <div class="card" style="max-width: 800px; margin: 0 auto; width: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin:0">2. Resumen General (Unica vs Recurrente) (<?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>)</h3>
                    <button onclick="exportToExcel('tblResumen2', 'Resumen_General')" class="btn no-print" style="padding: 5px 15px; font-size: 0.8rem;">Excel</button>
                </div>
                <div class="table-container">
                    <table id="tblResumen2" class="table-resumen">
                        <thead>
                            <tr>
                                <th>Ejecutiv@</th>
                                <th class="text-right">Unica UF</th>
                                <th class="text-right">Recurrente UF</th>
                                <th class="text-right">Total UF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $t2 = ['u'=>0, 'r'=>0, 't'=>0];
                            // Quitamos a Ghislaine de la lista principal para ponerla al final (como en el original)
                            $ghislaine_data = $summary2['Ghislaine Rivera'] ?? ['Unica'=>0, 'Recurrente'=>0];
                            unset($summary2['Ghislaine Rivera']);
                            
                            foreach($summary2 as $vend => $data): 
                                $total = $data['Unica'] + $data['Recurrente'];
                                $t2['u'] += $data['Unica'];
                                $t2['r'] += $data['Recurrente'];
                                $t2['t'] += $total;
                            ?>
                                <tr>
                                    <td style="font-weight: 500;"><?= $vend ?></td>
                                    <td class="text-right"><?= formatDecimal($data['Unica']) ?></td>
                                    <td class="text-right"><?= formatDecimal($data['Recurrente']) ?></td>
                                    <td class="text-right" style="font-weight:600; color:var(--secondary-color)"><?= formatDecimal($total) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <!-- Fila de Ghislaine (SGV) -->
                            <?php 
                                $total_ghi = $ghislaine_data['Unica'] + $ghislaine_data['Recurrente'];
                                $t2['u'] += $ghislaine_data['Unica'];
                                $t2['r'] += $ghislaine_data['Recurrente'];
                                $t2['t'] += $total_ghi;
                            ?>
                            <tr style="border-top: 2px solid rgba(0,0,0,0.1)">
                                <td style="font-weight: 600;">Ghislaine Rivera</td>
                                <td class="text-right"><?= formatDecimal($ghislaine_data['Unica']) ?></td>
                                <td class="text-right"><?= formatDecimal($ghislaine_data['Recurrente']) ?></td>
                                <td class="text-right" style="font-weight:600; color:var(--secondary-color)"><?= formatDecimal($total_ghi) ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="tfoot-total">
                                <td>TOTALES</td>
                                <td class="text-right"><?= formatDecimal($t2['u']) ?></td>
                                <td class="text-right"><?= formatDecimal($t2['r']) ?></td>
                                <td class="text-right"><?= formatDecimal($t2['t']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // 1. Gráfico por Tipo (Doughnut) - Ejecutar primero para mayor seguridad
            const canvas = document.getElementById('typeChart');
            if (canvas) {
                const typeCtx = canvas.getContext('2d');
                new Chart(typeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: <?= json_encode(array_keys($summary_chart)) ?>,
                        datasets: [{
                            data: <?= json_encode(array_values($summary_chart)) ?>,
                            backgroundColor: [
                                '#3498db', '#2ecc71', '#e67e22', '#e74c3c', '#9b59b6', '#f1c40f', '#1abc9c'
                            ],
                            borderWidth: 0,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    font: { family: "'Inter', sans-serif", size: 12 },
                                    boxWidth: 15,
                                    padding: 15
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let val = context.parsed || 0;
                                        return context.label + ': ' + val.toLocaleString('es-CL', {minimumFractionDigits: 2}) + ' UF';
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }

            // 2. DataTables para ambas tablas
            const dtConfig = {
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                "pageLength": 25,
                "searching": false,
                "info": false,
                "paging": false
            };
            
            $('#tblResumen1').DataTable(dtConfig);
            $('#tblResumen2').DataTable(dtConfig);

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
            a.download = 'ResumenComisiones_' + new Date().getTime() + '.xls';
            a.click();
        }
    </script>
</body>
</html>
