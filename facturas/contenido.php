<?php include_once(__DIR__ . "/include/include.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Distribución de Facturas</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
        <script src='jquery-3.3.1.min.js' type='text/javascript'></script>
        <script src='./js/script.js' type='text/javascript'></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script type="text/javascript">
	function exportToExcel(tableId){
		let tableData = document.getElementById(tableId).outerHTML;
		tableData = tableData.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
		tableData = tableData.replace(/<input[^>]*>|<\/input>/gi, ""); //remove input params

		let a = document.createElement('a');
		a.href = `data:application/vnd.ms-excel, ${encodeURIComponent(tableData)}`
		a.download = 'Facturas_' + getRandomNumbers() + '.xls'
		a.click()
	}
	function getRandomNumbers() {
		let dateObj = new Date()
		let dateTime = `${dateObj.getHours()}${dateObj.getMinutes()}${dateObj.getSeconds()}`

		return `${dateTime}${Math.floor((Math.random().toFixed(2)*100))}`
	}        

</script>
<style>
.sortable {
    cursor: pointer;
    user-select: none;
}
.sortable:hover {
    background-color: #6a2d6d !important;
}
.sort-indicator {
    margin-left: 5px;
    font-size: 0.8em;
}
.sort-indicator.asc::after {
    content: ' ▲';
}
.sort-indicator.desc::after {
    content: ' ▼';
}
</style>
    </head>
<body>

<form action="" method="POST" style="text-align: center; margin-bottom: 10px;">
    <table>
        <tr><th colspan="6">Filtros de Facturas</th></tr>
        <tr>
            <td align="left">
                <label>Fecha Inicial:<br>
                    <input type="date" name="start_date" value="<?= h($start_date) ?>"></label><br>
                <label>Fecha Final:<br>
                    <input type="date" name="end_date" value="<?= h($end_date) ?>"></label>
            </td>
            <td>
                <label>Usuarios:</label>
                <select name="user[]" multiple>
                    <?php foreach ($usuarios as $usuario): if ($usuario==='') continue; ?>
                        <option value="<?= h($usuario) ?>" <?= in_array($usuario, $selected_users) ? 'selected' : '' ?>>
                            <?= h($usuario) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <label>Estados:</label>
                <select name="estado[]" multiple>
                    <?php foreach ($estados as $estado): ?>
                        <option value="<?= h($estado) ?>" <?= in_array($estado, $selected_estados) ? 'selected' : '' ?>>
                            <?= $estado === '' ? '(Vacío)' : h($estado) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <label>Estado OP:</label>
                <select name="estado_op[]" multiple>
                    <?php foreach ($estados_op as $estado_op): ?>
                        <option value="<?= h($estado_op) ?>" <?= in_array($estado_op, $selected_estados_op) ? 'selected' : '' ?>>
                            <?= $estado_op === '' ? '(Vacío)' : h($estado_op) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <button type="submit" name="filtrar">Filtrar</button>
                <button style="background-color: #64C2C8" type="submit" name="limpiar_filtros">Limpiar Filtros</button>
            </td>
        </tr>
        <tr>
            <td colspan="5" align="left" style="padding: 10px;">
                <label><strong>Columnas Visibles:</strong></label><br>
                <label style="margin-right: 10px;"><input type="checkbox" class="col-toggle" data-column="1" checked> Fac</label>
                <label style="margin-right: 10px;"><input type="checkbox" class="col-toggle" data-column="2" checked> Cot</label>
                <label style="margin-right: 10px;"><input type="checkbox" class="col-toggle" data-column="3" checked> Opor</label>
                <label style="margin-right: 10px;"><input type="checkbox" class="col-toggle" data-column="4" checked> NV</label>
                <label style="margin-right: 10px;"><input type="checkbox" class="col-toggle" data-column="5" checked> Título</label>
                <label style="margin-right: 10px;"><input type="checkbox" class="col-toggle" data-column="6" checked> Fecha</label>
                <label style="margin-right: 10px;"><input type="checkbox" class="col-toggle" data-column="7" checked> Moneda</label>
                <label style="margin-right: 10px;"><input type="checkbox" class="col-toggle" data-column="8" checked> Monto</label>
                <label style="margin-right: 10px;"><input type="checkbox" class="col-toggle" data-column="9" checked> Estado</label>
                <label style="margin-right: 10px;"><input type="checkbox" class="col-toggle" data-column="10" checked> Ejecutiv@</label>
                <label style="margin-right: 10px;"><input type="checkbox" class="col-toggle" data-column="11" checked> Estado OP</label>
            </td>
        </tr>
    </table>
</form>

<!-- Tabla Principal -->
<?php if (!empty($facturas)): ?>
    
    <div style="max-height: 400px; overflow-y: auto; margin: 0 auto; width: 95%;">
      <table id="empTable" style="border-collapse: collapse; width: 100%; text-align: center; border: 1px solid #ddd;">
          <thead>
              <tr style="background-color: #f2f2f2;">
                  <th style="position: sticky; top: 0; background-color: #512554; z-index: 2;">#</th>
                  <th data-column="1" data-sort-type="text" class="sortable" style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Fac<span class="sort-indicator"></span></th>
                  <th data-column="2" data-sort-type="text" class="sortable" style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Cot<span class="sort-indicator"></span></th>
                  <th data-column="3" data-sort-type="text" class="sortable" style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Opor<span class="sort-indicator"></span></th>
                  <th data-column="4" data-sort-type="text" class="sortable" style="position: sticky; top: 0; background-color: #512554; z-index: 2;">NV<span class="sort-indicator"></span></th>
                  <th data-column="5" data-sort-type="text" class="sortable" style="position: sticky; top: 0; background-color: #512554; z-index: 2; text-align:left;">Título<span class="sort-indicator"></span></th>
                  <th data-column="6" data-sort-type="date" class="sortable" style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Fecha_Fac<span class="sort-indicator"></span></th>
                  <th data-column="7" data-sort-type="text" class="sortable" style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Moneda<span class="sort-indicator"></span></th>
                  <th data-column="8" data-sort-type="number" class="sortable" style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Monto(UF)<span class="sort-indicator"></span></th>
                  <th data-column="9" data-sort-type="text" class="sortable" style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Estado<span class="sort-indicator"></span></th>
                  <th data-column="10" data-sort-type="text" class="sortable" style="position: sticky; top: 0; background-color: #512554; z-index: 2;">Ejecutiv@<span class="sort-indicator"></span></th>
                  <th data-column="11" data-sort-type="text" class="sortable" style="position: sticky; top: 0; background-color: #512554; z-index: 2;"><input type="button" onClick="exportToExcel('empTable')" value="📊 Exportar" /><br>Estado OP<span class="sort-indicator"></span></th>
              </tr>
          </thead>
          <tbody>
            <?php $line_number = 1; foreach ($facturas as $row): ?>
                <tr>
                    <td><?= $line_number++; ?></td>
                    <td data-column="1">
                        <a href="<?= h($row['url_fac']); ?>" target="_blank" rel="noopener">
                            <?= h($row['numero_fac']); ?>
                        </a>
                    </td>
                    <td data-column="2">
                        <a href="<?= h($row['url_cot']); ?>" target="_blank" rel="noopener">
                            <?= h($row['numero_cot']); ?>
                        </a>
                    </td>
                    <td data-column="3">
                        <a href="<?= h($row['url_opor']); ?>" target="_blank" rel="noopener">
                            <?= h($row['numero_op']); ?>
                        </a>
                    </td>
                    <td data-column="4">
                        <a href="<?= h($row['url_nv']); ?>" target="_blank" rel="noopener">
                            <?= h($row['numeo_nv']); ?>
                        </a>
                    </td>
                    <td data-column="5" style="text-align: left;"><?= h($row['titulo']); ?></td>
                    <td data-column="6"><?= fmt_fecha($row['fecha']); ?></td>
                    <td data-column="7"><?= h($row['Moneda']); ?></td>
                    <td data-column="8" style="text-align: right"><?= number_format((float)$row['Monto'], 2); ?></td>
                    <td data-column="9" style="text-align: left;"><?= h($row['Estado']); ?></td>
                    <td data-column="10" style="text-align: left"><?= h($row['Usuario']); ?></td>
                    <td data-column="11"><?= h($row['op_estado']); ?></td>
                </tr>
            <?php endforeach; ?>
          </tbody>
      </table>
    </div>
<?php else: ?>
    <p style="text-align: center; color: red;">No se encontraron facturas con los filtros aplicados.</p>
<?php endif; ?>

<br>

<!-- Tabla Resumen -->
<?php ksort($resumen); ?>
<table style="margin: 0 auto; border-collapse: collapse; width: 60%; text-align: center; border: 1px solid #ddd;">
    <tr>
        <td colspan="5">Tabla Resumen de Facturas por Estado</td>
    </tr>
    <tr style="background-color: #f2f2f2;">
        <th>Estado</th>
        <th>Cantidad</th>
        <th>%</th>
        <th>Monto (UF)</th>
        <th>%</th>
    </tr>
    <?php 
    $total_cantidad = 0;
    $total_monto = 0.00;
    foreach ($resumen as $estado => $datos): 
        $total_cantidad += $datos['total'];
        $total_monto    += $datos['monto'];
    ?>
        <tr>
            <td><?= h($estado); ?></td>
            <td><?= (int)$datos['total']; ?></td>
            <td><?= number_format($datos['porcentaje_cantidad'], 2); ?>%</td>
            <td style="text-align: right;"><?= number_format($datos['monto'], 2); ?></td>
            <td><?= number_format($datos['porcentaje_monto'], 2); ?>%</td>
        </tr>
    <?php endforeach; ?>
    <tr style="background-color: #f2f2f2; font-weight: bold;">
        <td>Total</td>
        <td><?= $total_cantidad; ?></td>
        <td>100%</td>
        <td style="text-align: right;"><?= number_format($total_monto, 2); ?></td>
        <td>100%</td>
    </tr>
</table>

<!-- Gráfico -->
<div style="width: 800px; margin: 0 auto;">
    <canvas id="chartFacturasPorEstado"></canvas>
</div>

<script>
    var ctx = document.getElementById('chartFacturasPorEstado').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($resumen)); ?>,
            datasets: [{
                label: 'Cantidad',
                data: <?= json_encode(array_column($resumen, 'total')); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Facturas por Estado',
                    font: { size: 18 }
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const STORAGE_KEY = 'facturas_visible_columns';
    
    // Cargar preferencias guardadas
    function loadColumnPreferences() {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            return JSON.parse(saved);
        }
        // Por defecto, todas las columnas visibles
        return {1:true, 2:true, 3:true, 4:true, 5:true, 6:true, 7:true, 8:true, 9:true, 10:true, 11:true};
    }
    
    // Guardar preferencias
    function saveColumnPreferences(prefs) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs));
    }
    
    // Aplicar visibilidad de columnas
    function applyColumnVisibility(columnNum, visible) {
        const selector = `[data-column="${columnNum}"]`;
        document.querySelectorAll(selector).forEach(el => {
            el.style.display = visible ? '' : 'none';
        });
    }
    
    // Inicializar
    const prefs = loadColumnPreferences();
    const toggles = document.querySelectorAll('.col-toggle');
    
    toggles.forEach(toggle => {
        const colNum = parseInt(toggle.dataset.column);
        toggle.checked = prefs[colNum] !== false;
        applyColumnVisibility(colNum, toggle.checked);
        
        toggle.addEventListener('change', function() {
            prefs[colNum] = this.checked;
            saveColumnPreferences(prefs);
            applyColumnVisibility(colNum, this.checked);
        });
    });
});
</script>

<script>
// Column Sorting
document.addEventListener('DOMContentLoaded', function() {
    const STORAGE_KEY = 'facturas_sort_preferences';
    const table = document.getElementById('empTable');
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    
    // Cargar preferencias
    function loadSortPreferences() {
        const saved = localStorage.getItem(STORAGE_KEY);
        return saved ? JSON.parse(saved) : { column: null, direction: 'asc' };
    }
    
    // Guardar preferencias
    function saveSortPreferences(column, direction) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({ column, direction }));
    }
    
    // Obtener valor de celda para ordenamiento
    function getCellValue(row, columnIndex) {
        const cell = row.cells[columnIndex];
        if (!cell) return '';
        return cell.textContent.trim() || cell.innerText.trim();
    }
    
    // Comparar valores según tipo
    function compareValues(a, b, sortType, direction) {
        let aVal = a;
        let bVal = b;
        
        if (sortType === 'number') {
            aVal = parseFloat(aVal.replace(/,/g, '')) || 0;
            bVal = parseFloat(bVal.replace(/,/g, '')) || 0;
        } else if (sortType === 'date') {
            aVal = new Date(aVal).getTime() || 0;
            bVal = new Date(bVal).getTime() || 0;
        } else {
            aVal = aVal.toLowerCase();
            bVal = bVal.toLowerCase();
        }
        
        if (aVal < bVal) return direction === 'asc' ? -1 : 1;
        if (aVal > bVal) return direction === 'asc' ? 1 : -1;
        return 0;
    }
    
    // Ordenar tabla
    function sortTable(columnIndex, sortType, direction) {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((rowA, rowB) => {
            const aVal = getCellValue(rowA, columnIndex);
            const bVal = getCellValue(rowB, columnIndex);
            return compareValues(aVal, bVal, sortType, direction);
        });
        
        rows.forEach(row => tbody.appendChild(row));
        
        rows.forEach((row, index) => {
            row.cells[0].textContent = index + 1;
        });
    }
    
    // Actualizar indicadores visuales
    function updateSortIndicators(activeColumn, direction) {
        document.querySelectorAll('.sort-indicator').forEach(indicator => {
            indicator.className = 'sort-indicator';
        });
        
        if (activeColumn) {
            const indicator = activeColumn.querySelector('.sort-indicator');
            if (indicator) {
                indicator.classList.add(direction);
            }
        }
    }
    
    // Event listeners para headers
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function() {
            const columnIndex = Array.from(this.parentElement.children).indexOf(this);
            const sortType = this.dataset.sortType || 'text';
            const currentDirection = this.dataset.currentDirection || 'asc';
            const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            
            sortTable(columnIndex, sortType, newDirection);
            this.dataset.currentDirection = newDirection;
            updateSortIndicators(this, newDirection);
            saveSortPreferences(columnIndex, newDirection);
        });
    });
    
    // Aplicar ordenamiento inicial si hay preferencias guardadas
    const prefs = loadSortPreferences();
    if (prefs.column !== null) {
        const headers = Array.from(table.querySelectorAll('th'));
        const header = headers[prefs.column];
        if (header && header.classList.contains('sortable')) {
            const sortType = header.dataset.sortType || 'text';
            sortTable(prefs.column, sortType, prefs.direction);
            header.dataset.currentDirection = prefs.direction;
            updateSortIndicators(header, prefs.direction);
        }
    }
});
</script>
</body>
</html>
