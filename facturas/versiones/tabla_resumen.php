<?php if (!empty($resumen)): ?>
    <h2 style="text-align: center; margin-top: 30px;">Resumen de Cotizaciones</h2>
    <table class="resumen-table">
        <tr>
            <th class="resumen-title">Estado</th>
            <th class="resumen-title">Total de Cotizaciones</th>
            <th class="resumen-title">% DistribuciÃ³n Cantidad</th>
            <th class="resumen-title">Suma del Monto</th>
            <th class="resumen-title">% DistribuciÃ³n Monto</th>
        </tr>
        <?php 
        $total_cotizaciones = 0;
        $total_monto = 0.00;
        
        // Calcular totales generales
        foreach ($resumen as $row) {
            $total_cotizaciones += $row['total_cotizaciones'];
            $total_monto += $row['suma_monto'];
        }
        
        // Mostrar cada fila con porcentaje
        foreach ($resumen as $row): 
            $distribucion_cantidad = ($row['total_cotizaciones'] / $total_cotizaciones) * 100;
            $distribucion_monto = ($row['suma_monto'] / $total_monto) * 100;
        ?>
            <tr>
                <td><?= htmlspecialchars($row['Estado']); ?></td>
                <td><?= htmlspecialchars($row['total_cotizaciones']); ?></td>
                <td><?= number_format($distribucion_cantidad, 2); ?>%</td>
                <td class="monto"><?= number_format($row['suma_monto'], 2); ?></td>
                <td class="monto"><?= number_format($distribucion_monto, 2); ?>%</td>
            </tr>
        <?php endforeach; ?>
        
        <tr class="total-row">
            <td>Total General</td>
            <td><?= $total_cotizaciones; ?></td>
            <td>100%</td>
            <td class="monto"><?= number_format($total_monto, 2); ?></td>
            <td class="monto">100%</td>
        </tr>
    </table>
<?php else: ?>
    <p style="text-align: center; color: red;">No se encontraron cotizaciones para el rango de fechas seleccionado.</p>
<?php endif; ?>
