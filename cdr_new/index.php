<?php
// ==========================================================
// /intranet/cdr_new/index.php
// Buscador de CDR Modernizado V2 (Graphs & Interactive)
// ==========================================================

require_once "config.php";
require_once "includes/functions.php";

// Manejo de fechas y cliente en sesión
if (isset($_POST['desde'])) $_SESSION['desde'] = $_POST['desde'];
if (isset($_POST['hasta'])) $_SESSION['hasta'] = $_POST['hasta'];
if (isset($_POST['cliente'])) $_SESSION['cliente'] = $_POST['cliente'];

if (!isset($_SESSION['desde'])) $_SESSION['desde'] = primer_dia_mes();
if (!isset($_SESSION['hasta'])) $_SESSION['hasta'] = ultimo_dia_mes();

$desde = $_SESSION['desde'];
$hasta = $_SESSION['hasta'];
$id_cliente = $_SESSION['cliente'] ?? null;

// Valores para botones rápidos
$estemes_desde = primer_dia_mes();
$estemes_hasta = ultimo_dia_mes();
$mesanterior_desde = primer_dia_mes_anterior();	
$mesanterior_hasta = ultimo_dia_mes_anterior();	

// Obtener lista de clientes
$sql_clientes = "SELECT id_client, razon_social FROM clientes WHERE slm > 0 ORDER BY razon_social ASC";
$res_clientes = $con->query($sql_clientes);

$data_ready = false;
$stats = [
    'llamadas' => 0,
    'minutos' => 0,
    'consumo' => 0,
    'plan' => 0,
    'plan_val' => 0,
    'consumo_extra' => 0,
    'recargas' => 0,
    'valor_recarga' => 0,
    'cliente_nombre' => '',
    'rut' => ''
];

$graph_data = [
    'dates' => [],
    'costs' => [],
    'types' => [],
    'type_counts' => []
];

if ($id_cliente && isset($_POST['buscar'])) {
    $data_ready = true;
    
    // Obtener datos del cliente
    $stmt = $con->prepare("SELECT razon_social, rut, minutos_plan, slm FROM clientes WHERE id_client = ?");
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $res_client = $stmt->get_result();
    $client_info = $res_client->fetch_assoc();
    
    $stats['cliente_nombre'] = $client_info['razon_social'];
    $stats['rut'] = $client_info['rut'];
    $stats['plan'] = $client_info['minutos_plan'];
    $slm = $client_info['slm'];
    $stats['plan_val'] = $stats['plan'] * $slm;

    // Obtener Totales
    $query_totals = "SELECT 
                        count(*) as total_llamadas,
                        sum(duration/60) as total_minutos,
                        sum(cost) as total_pesos
                    FROM cdr 
                    USE INDEX (IX_CallsIDClientCallStart)
                    WHERE id_client = $id_cliente AND (call_start BETWEEN '$desde' AND '$hasta')";
    $res_totals = $con->query($query_totals);
    $row_totals = $res_totals->fetch_assoc();
    
    $stats['llamadas'] = $row_totals['total_llamadas'] ?? 0;
    $stats['minutos'] = $row_totals['total_minutos'] ?? 0;
    $stats['consumo'] = $row_totals['total_pesos'] ?? 0;
    
    if ($stats['consumo'] > $stats['plan_val']) {
        $stats['consumo_extra'] = $stats['consumo'] - $stats['plan_val'];
    }
    
    $recarga_base = ($stats['plan'] > 500) ? 1000 : (($stats['plan'] > 100) ? 500 : 100);
    $diff = $stats['plan_val'] - $stats['consumo'];
    if ($diff < 0) {
        $stats['recargas'] = ceil(abs($diff) / ($recarga_base * $slm));
        $stats['valor_recarga'] = $stats['recargas'] * $recarga_base * $slm * 1.20;
    }

    // Datos para Gráficos
    // Tendencia Diaria
    $query_daily = "SELECT DATE(call_start) as dia, sum(cost) as costo 
                    FROM cdr 
                    WHERE id_client = $id_cliente AND (call_start BETWEEN '$desde' AND '$hasta')
                    GROUP BY dia ORDER BY dia ASC";
    $res_daily = $con->query($query_daily);
    while($d = $res_daily->fetch_assoc()) {
        $graph_data['dates'][] = $d['dia'];
        $graph_data['costs'][] = round($d['costo'], 0);
    }

    // Distribución por Tipo
    $query_types = "SELECT tariffdesc as tipo, count(*) as cant 
                    FROM cdr 
                    WHERE id_client = $id_cliente AND (call_start BETWEEN '$desde' AND '$hasta')
                    GROUP BY tipo ORDER BY cant DESC LIMIT 5";
    $res_types = $con->query($query_types);
    while($t = $res_types->fetch_assoc()) {
        $graph_data['types'][] = $t['tipo'];
        $graph_data['type_counts'][] = $t['cant'];
    }

    // Obtener Detalle de Llamadas
    $query_calls = "SELECT 
                        call_start as inicio,
                        caller_id as origen,
                        called_number as destino,
                        duration/60 as duracion,
                        cost as valor,
                        tariffdesc as tipo,
                        call_rate as tarifa
                    FROM cdr 
                    WHERE id_client = $id_cliente AND (call_start BETWEEN '$desde' AND '$hasta')
                    ORDER BY call_start DESC";
    $res_calls = $con->query($query_calls);
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CDR Dashboard - iConTel</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="container">
        <header class="header-section no-print">
            <div>
                <h1>CDR Analytics</h1>
                <p>Monitoreo inteligente de trafico telefónico</p>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="theme-switch" onclick="toggleTheme()">
                    <div class="icon active" id="theme-light">☀️</div>
                    <div class="icon" id="theme-dark">🌙</div>
                </div>
                <a href="recargas.php" class="btn btn-secondary">Recargas</a>
                <button onclick="window.print()" class="btn">PDF</button>
            </div>
        </header>

        <section class="card no-print">
            <form method="post" class="search-form" id="searchForm">
                <input type="hidden" name="buscar" value="1">
                <div class="form-group" style="flex: 2;">
                    <label for="cliente">Cliente</label>
                    <select name="cliente" id="cliente" onchange="this.form.submit()">
                        <option value="">Seleccione un cliente...</option>
                        <?php while($row = $res_clientes->fetch_assoc()): ?>
                            <option value="<?= $row['id_client'] ?>" <?= ($id_cliente == $row['id_client']) ? 'selected' : '' ?>>
                                <?= $row['razon_social'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="desde">F. Inicio</label>
                    <input type="text" name="desde" id="desde" value="<?= $desde ?>" class="flatpickr">
                </div>
                <div class="form-group">
                    <label for="hasta">F. Término</label>
                    <input type="text" name="hasta" id="hasta" value="<?= $hasta ?>" class="flatpickr">
                </div>
                <button type="submit" name="buscar" value="1">Analizar</button>
                <button type="button" onclick="quickDate('<?= $estemes_desde ?>', '<?= $estemes_hasta ?>')" class="btn-secondary">Este Mes</button>
                <button type="button" onclick="quickDate('<?= $mesanterior_desde ?>', '<?= $mesanterior_hasta ?>')" class="btn-secondary">Mes Anterior</button>
            </form>
        </section>

        <?php if ($data_ready): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="label">Llamadas Totales</span>
                    <span class="value"><?= number_format($stats['llamadas'], 0) ?></span>
                </div>
                <div class="stat-card">
                    <span class="label">Minutos Consumidos</span>
                    <span class="value"><?= formatDecimal($stats['minutos']) ?></span>
                </div>
                <div class="stat-card <?= ($stats['consumo'] > 0 && $stats['plan'] == 0) ? 'alert-no-plan' : '' ?>">
                    <span class="label">Consumo en Pesos</span>
                    <span class="value"><?= formatCurrency($stats['consumo']) ?></span>
                    <?php 
                        $perc = ($stats['plan_val'] > 0) ? ($stats['consumo'] / $stats['plan_val']) * 100 : 0;
                        $class = ($perc > 100) ? 'danger' : (($perc > 80) ? 'warning' : '');
                    ?>
                    <div class="progress-pills">
                        <div class="progress-fill <?= $class ?>" style="width: min(100%, <?= $perc ?>%)"></div>
                    </div>
                </div>
                <div class="stat-card <?= ($stats['consumo'] > 0 && $stats['plan'] == 0) ? 'alert-no-plan' : '' ?>">
                    <span class="label">Plan Contratado</span>
                    <span class="value"><?= formatCurrency($stats['plan_val']) ?></span>
                    <?php if ($stats['consumo'] > 0 && $stats['plan'] == 0): ?>
                        <span style="color: #e74c3c; font-size: 0.85rem; margin-top: 8px; display: block;">⚠️ Sin plan definido</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dashboard-row">
                <div class="card">
                    <h3>Tendencia de Consumo Diaria</h3>
                    <canvas id="dailyChart" height="100"></canvas>
                </div>
                <div class="card">
                    <h3>Top Destinos</h3>
                    <canvas id="typeChart"></canvas>
                </div>
            </div>

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin:0">Detalle de Registros</h3>
                    <button onclick="exportToExcel('tblCalls')" class="btn no-print" style="padding: 5px 15px; font-size: 0.8rem;">Excel</button>
                </div>
                <div class="table-container">
                    <table id="tblCalls" class="display">
                        <thead>
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th class="text-right">Minutos</th>
                                <th class="text-right">Valor</th>
                                <th>Tipo</th>
                                <th class="text-right">Tarifa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($call = $res_calls->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($call['inicio'])) ?></td>
                                    <td><?= $call['origen'] ?></td>
                                    <td><?= $call['destino'] ?></td>
                                    <td class="text-right"><?= formatDecimal($call['duracion']) ?></td>
                                    <td class="text-right"><?= formatCurrency($call['valor']) ?></td>
                                    <td><?= $call['tipo'] ?></td>
                                    <td class="text-right"><?= formatCurrency($call['tarifa']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function() {
            // Inicializar Calendarios
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

            // Inicializar DataTables
            $('#tblCalls').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "pageLength": 25,
                "order": [[0, "desc"]]
            });

            // Inicializar Gráficos si hay datos
            <?php if ($data_ready): ?>
            // Daily Chart
            new Chart(document.getElementById('dailyChart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode($graph_data['dates']) ?>,
                    datasets: [{
                        label: 'Gasto en $',
                        data: <?= json_encode($graph_data['costs']) ?>,
                        borderColor: '#E67E22',
                        backgroundColor: 'rgba(230, 126, 34, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            // Type Chart
            new Chart(document.getElementById('typeChart'), {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($graph_data['types']) ?>,
                    datasets: [{
                        data: <?= json_encode($graph_data['type_counts']) ?>,
                        backgroundColor: ['#1F1D3E', '#E67E22', '#34495e', '#27ae60', '#e74c3c']
                    }]
                },
                options: { responsive: true }
            });
            <?php endif; ?>

            // Recuperar Tema
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
            a.download = 'CDR_Analytics_' + new Date().getTime() + '.xls';
            a.click();
        }
    </script>
</body>
</html>
