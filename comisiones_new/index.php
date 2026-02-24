<?php
// ==========================================================
// /intranet/comisiones_new/index.php
// Menú de Búsqueda Modernizado - Sistema de Comisiones
// ==========================================================

require_once "config.php";

// Obtener listas para los selectores
$vendedores = busca_columna("CALL `ventas_vendedores_all`()", "vendedor");
$tipos_factura = busca_columna("CALL `ventas_tipo_factura_all`()", "tipo_factura");

// Fechas predefinidas (Lógica original)
$fecha_actual = new DateTime();
$fecha_actual->setTime(0, 0);
$fecha_fin = clone $fecha_actual;
$fecha_fin->setDate($fecha_fin->format('Y'), $fecha_fin->format('m'), 25);
$fecha_inicio = clone $fecha_actual;
$fecha_inicio->modify('first day of last month')
             ->setDate($fecha_inicio->format('Y'), $fecha_inicio->format('m'), 26);

$fecha_inicio_formato = $fecha_inicio->format('Y-m-d');
$fecha_fin_formato    = $fecha_fin->format('Y-m-d');

// Fechas para Mes Anterior (Ciclo 26 al 25)
$fecha_ant_inicio = clone $fecha_inicio;
$fecha_ant_inicio->modify('-1 month');
$fecha_ant_fin = clone $fecha_fin;
$fecha_ant_fin->modify('-1 month');

// Preselecciones estándar
$ejecutivos_pre = ["Ghislaine Rivera", "Natalia Diaz", "Raquel Maulen", "Rocio Tiznado"];
$tipos_pre = ["Anual", "Bienal", "Mensual", "Lista para Facturar", "Pendiente", "Unica"];
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comisiones iConTel</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="/intranet/favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <style>
        .choices__inner {
            border-radius: 8px !important;
            border: 1px solid var(--border-color) !important;
            background: var(--card-bg) !important;
            color: var(--text-color) !important;
            min-height: 40px !important;
            padding: 4px !important;
        }
        .choices__list--multiple .choices__item {
            background-color: var(--primary-color) !important;
            border: 1px solid var(--primary-color) !important;
            font-size: 0.75rem !important;
            padding: 1px 8px !important;
            border-radius: 100px !important; /* Estilo píldora más delicado */
            margin: 2px !important;
        }
        .search-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header-section">
            <div>
                <h1>Comisiones Analytics</h1>
                <p>Gestión y análisis de comisiones de venta</p>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="theme-switch" onclick="toggleTheme()">
                    <div class="icon active" id="theme-light">☀️</div>
                    <div class="icon" id="theme-dark">🌙</div>
                </div>
            </div>
        </header>

        <section class="card">
            <form method="post" action="informe.php" id="searchForm">
                <div class="search-grid">
                    <div class="form-group">
                        <label for="inicio">Fecha Desde</label>
                        <input type="text" name="inicio" id="inicio" value="<?= $fecha_inicio_formato ?>" class="flatpickr">
                    </div>
                    <div class="form-group">
                        <label for="fin">Fecha Hasta</label>
                        <input type="text" name="fin" id="fin" value="<?= $fecha_fin_formato ?>" class="flatpickr">
                    </div>
                    <div class="form-group">
                        <label for="ejecutivo">Ejecutivos</label>
                        <select id="ejecutivo" name="ejecutivo[]" multiple>
                            <option value="TODOS" <?= empty($ejecutivos_pre) ? 'selected' : '' ?>>--- TODOS ---</option>
                            <?php foreach ($vendedores as $v): ?>
                                <option value="<?= $v ?>" <?= in_array($v, $ejecutivos_pre) ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tipo_factura">Tipo Factura</label>
                        <select id="tipo_factura" name="tipo_factura[]" multiple>
                            <option value="TODOS" <?= empty($tipos_pre) ? 'selected' : '' ?>>--- TODOS ---</option>
                            <?php foreach ($tipos_factura as $t): ?>
                                <option value="<?= $t ?>" <?= in_array($t, $tipos_pre) ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 30px; display: flex; gap: 15px;">
                    <button type="submit" name="btn_search" value="1" class="btn">Generar Informe</button>
                    <button type="button" onclick="quickDate('<?= $fecha_inicio_formato ?>', '<?= $fecha_fin_formato ?>')" class="btn btn-secondary">Periodo Actual (26-25)</button>
                    <button type="button" onclick="quickDate('<?= $fecha_ant_inicio->format('Y-m-d') ?>', '<?= $fecha_ant_fin->format('Y-m-d') ?>')" class="btn btn-secondary">Mes Anterior</button>
                    <button type="reset" class="btn" style="background: #95a5a6;">Limpiar</button>
                </div>
            </form>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            // Inicializar Calendarios
            flatpickr(".flatpickr", {
                dateFormat: "Y-m-d",
                locale: "es",
                allowInput: true
            });

            // Inicializar Selectores Múltiples Modernos
            const choiceEjecutivo = new Choices('#ejecutivo', {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Seleccione ejecutivos...',
                searchPlaceholderValue: 'Buscar...'
            });

            const choiceTipo = new Choices('#tipo_factura', {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Seleccione tipos...',
                searchPlaceholderValue: 'Buscar...'
            });

            // Lógica de exclusividad mutua para "TODOS"
            function handleTodosExclusivity(instance, event) {
                const value = event.detail.value;
                const allValues = instance.getValue(true);

                if (value === 'TODOS') {
                    // Si se seleccionó TODOS, quitar el resto
                    allValues.forEach(val => {
                        if (val !== 'TODOS') instance.removeActiveItemsByValue(val);
                    });
                } else {
                    // Si se seleccionó otra cosa, quitar TODOS si estaba puesto
                    if (allValues.includes('TODOS')) {
                        instance.removeActiveItemsByValue('TODOS');
                    }
                }
            }

            document.getElementById('ejecutivo').addEventListener('addItem', (e) => handleTodosExclusivity(choiceEjecutivo, e));
            document.getElementById('tipo_factura').addEventListener('addItem', (e) => handleTodosExclusivity(choiceTipo, e));

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
            document.getElementById('inicio').value = d;
            document.getElementById('fin').value = h;
            // Usar requestSubmit() es más moderno o simplemente asegurar que el botón no se llame submit
            document.getElementById('searchForm').submit();
        }
    </script>
</body>
</html>
