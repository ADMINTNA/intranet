<?php
// ==========================================================
// /intranet/cdr_new/recargas.php
// Reporte Global de Recargas Modernizado
// ==========================================================

require_once "config.php";
require_once "includes/functions.php";

$desde = $_SESSION['desde'] ?? primer_dia_mes();
$hasta = $_SESSION['hasta'] ?? ultimo_dia_mes();
$todos = $_POST['todos'] ?? 0;

if (isset($_POST['desde'])) $desde = $_POST['desde'];
if (isset($_POST['hasta'])) $hasta = $_POST['hasta'];

// Obtener lista de clientes con planes activos
$sql_clientes = "SELECT id_client, razon_social, rut, minutos_plan, slm FROM clientes WHERE slm > 0 ORDER BY razon_social ASC";
$res_clientes = $con->query($sql_clientes);
$clientes_lista = [];
while($row = $res_clientes->fetch_assoc()) {
    $clientes_lista[] = $row;
}

$report_data = [];
$totals = [
    'llamadas' => 0,
    'minutos' => 0,
    'valor' => 0,
    'plan_min' => 0,
    'plan_val' => 0,
    'recargas_cant' => 0,
    'recargas_val' => 0
];

if (isset($_POST['procesar'])) {
    foreach ($clientes_lista as $cli) {
        $id = $cli['id_client'];
        
        $query_cdr = "SELECT 
                        count(*) as total_llamadas,
                        sum(duration/60) as total_minutos,
                        sum(cost) as total_pesos
                    FROM cdr 
                    USE INDEX (IX_CallsIDClientCallStart)
                    WHERE id_client = $id AND (call_start BETWEEN '$desde' AND '$hasta')";
        $res_cdr = $con->query($query_cdr);
        $cdr = $res_cdr->fetch_assoc();
        
        $llamadas = $cdr['total_llamadas'] ?? 0;
        $minutos = $cdr['total_minutos'] ?? 0;
        $valor = $cdr['total_pesos'] ?? 0;
        $plan_min = $cli['minutos_plan'];
        $slm = $cli['slm'];
        $plan_val = $plan_min * $slm;
        
        // Lógica de recargas
        $recargas = 0;
        $v_recargas = 0;
        $diff = $plan_val - $valor;
        if ($diff < 0) {
            $recarga_unidades = ($plan_min > 500) ? 1000 : (($plan_min > 100) ? 500 : 100);
            $recargas = ceil(abs($diff) / ($recarga_unidades * $slm));
            $v_recargas = $recargas * $recarga_unidades * $slm * 1.20;
        }

        $row_data = [
            'id' => $id,
            'nombre' => $cli['razon_social'],
            'rut' => $cli['rut'],
            'llamadas' => $llamadas,
            'minutos' => $minutos,
            'valor' => $valor,
            'plan_min' => $plan_min,
            'plan_val' => $plan_val,
            'recargas' => $recargas,
            'v_recargas' => $v_recargas
        ];

        // Filtro: Solo recargas o todos
        if ($todos == 1 || $recargas > 0 || ($plan_min == 0 && $llamadas > 0)) {
            $report_data[] = $row_data;
            
            // Acumular totales
            $totals['llamadas'] += $llamadas;
            $totals['minutos'] += $minutos;
            $totals['valor'] += $valor;
            $totals['plan_min'] += $plan_min;
            $totals['plan_val'] += $plan_val;
            $totals['recargas_cant'] += $recargas;
            $totals['recargas_val'] += $v_recargas;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Global Recargas - Modern</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <style>
        .revisar { color: #e74c3c; font-weight: bold; }
        .success { color: #27ae60; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header-section no-print">
            <div>
                <h1>Global Recargas Report</h1>
                <p>Resumen detallado de consumos y recargas por cliente</p>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="theme-switch" onclick="toggleTheme()">
                    <div class="icon active" id="theme-light">☀️</div>
                    <div class="icon" id="theme-dark">🌙</div>
                </div>
                <a href="index.php" class="btn btn-secondary">Buscador CDR</a>
                <button onclick="window.print()" class="btn">PDF</button>
            </div>
        </header>

        <section class="card no-print">
            <form method="post" class="search-form" id="searchForm">
                <input type="hidden" name="procesar" value="1">
                <div class="form-group">
                    <label for="desde">Desde</label>
                    <input type="text" name="desde" id="desde" value="<?= $desde ?>" class="flatpickr">
                </div>
                <div class="form-group">
                    <label for="hasta">Hasta</label>
                    <input type="text" name="hasta" id="hasta" value="<?= $hasta ?>" class="flatpickr">
                </div>
                <div class="form-group">
                    <label>Filtro</label>
                    <div style="display: flex; gap: 10px; padding: 10px;">
                        <label><input type="radio" name="todos" value="0" <?= ($todos == 0) ? 'checked' : '' ?>> Solo Recargas</label>
                        <label><input type="radio" name="todos" value="1" <?= ($todos == 1) ? 'checked' : '' ?>> Todos</label>
                    </div>
                </div>
                <button type="submit" name="procesar" value="1">Procesar Reporte</button>
                <button type="button" onclick="quickDate('<?= primer_dia_mes() ?>', '<?= ultimo_dia_mes() ?>')" class="btn-secondary">Este Mes</button>
                <button type="button" onclick="quickDate('<?= primer_dia_mes_anterior() ?>', '<?= ultimo_dia_mes_anterior() ?>')" class="btn-secondary">Mes Anterior</button>
            </form>
        </section>

        <?php if (!empty($report_data)): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="label">Total Llamadas</span>
                    <span class="value"><?= number_format($totals['llamadas'], 0) ?></span>
                </div>
                <div class="stat-card">
                    <span class="label">Total Minutos</span>
                    <span class="value"><?= formatDecimal($totals['minutos']) ?></span>
                </div>
                <div class="stat-card">
                    <span class="label">Total Consumo</span>
                    <span class="value"><?= formatCurrency($totals['valor']) ?></span>
                </div>
                <div class="stat-card" style="border-top-color: #f1c40f;">
                    <span class="label">Total Recargas $</span>
                    <span class="value"><?= formatCurrency($totals['recargas_val']) ?></span>
                </div>
            </div>

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin:0">Detalle de Clientes</h3>
                    <button onclick="exportToExcel('tblRecargas')" class="btn no-print" style="padding: 5px 15px; font-size: 0.8rem;">Excel</button>
                </div>
                <div class="table-container">
                    <table id="tblRecargas" class="display">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Razón Social</th>
                            <th>RUT</th>
                            <th class="text-right">Llamadas</th>
                            <th class="text-right">Minutos</th>
                            <th class="text-right">Valor $</th>
                            <th class="text-right">Plan Min</th>
                            <th class="text-right">Plan $</th>
                            <th class="text-right">Recargas</th>
                            <th class="text-right">Recargas $</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($report_data as $idx => $row): 
                            // Highlight row if client has consumption but no plan
                            $highlight_row = ($row['valor'] > 0 && $row['plan_min'] == 0) ? 'revisar' : '';
                        ?>
                            <tr class="<?= $highlight_row ?>">
                                <td class="text-center"><?= $idx + 1 ?></td>
                                <td><?= $row['nombre'] ?> (<?= $row['id'] ?>)</td>
                                <td><?= $row['rut'] ?></td>
                                <td class="text-right"><?= number_format($row['llamadas'], 0) ?></td>
                                <td class="text-right"><?= formatDecimal($row['minutos']) ?></td>
                                <td class="text-right"><?= formatCurrency($row['valor']) ?></td>
                                <td class="text-right"><?= number_format($row['plan_min'], 0) ?></td>
                                <td class="text-right"><?= formatCurrency($row['plan_val']) ?></td>
                                <td class="text-right <?= ($row['recargas'] > 0) ? 'revisar' : '' ?>"><?= number_format($row['recargas'], 0) ?></td>
                                <td class="text-right"><?= formatCurrency($row['v_recargas']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot style="background: #f8f9fa; font-weight: bold;">
                        <tr>
                            <td colspan="3" class="text-right">TOTALES</td>
                            <td class="text-right"><?= number_format($totals['llamadas'], 0) ?></td>
                            <td class="text-right"><?= formatDecimal($totals['minutos']) ?></td>
                            <td class="text-right"><?= formatCurrency($totals['valor']) ?></td>
                            <td class="text-right"><?= number_format($totals['plan_min'], 0) ?></td>
                            <td class="text-right"><?= formatCurrency($totals['plan_val']) ?></td>
                            <td class="text-right"><?= number_format($totals['recargas_cant'], 0) ?></td>
                            <td class="text-right"><?= formatCurrency($totals['recargas_val']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function() {
            flatpickr(".flatpickr", {
                enableTime: true,
                dateFormat: "Y-m-d H:i:S",
                locale: "es",
                allowInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0 && !instance.config.inline) {
                        instance.close();
                        instance.element.form.submit();
                    }
                }
            });

            $('#tblRecargas').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "pageLength": 50,
                "order": [[1, "asc"]]
            });

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
            const current = document.documentElement.getAttribute('data-theme');
            setTheme(current === 'light' ? 'dark' : 'light');
        }

        function quickDate(d, h) {
            document.getElementById('desde').value = d;
            document.getElementById('hasta').value = h;
            document.getElementById('searchForm').submit();
        }

        function exportToExcel(tableId) {
            let tableData = document.getElementById(tableId).outerHTML;
            let a = document.createElement('a');
            a.href = `data:application/vnd.ms-excel, ${encodeURIComponent(tableData)}`;
            a.download = 'Recargas_Global_' + new Date().getTime() + '.xls';
            a.click();
        }
    </script>
</body>
</html>
