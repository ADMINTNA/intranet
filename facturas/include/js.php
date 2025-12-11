<script>
// JavaScript Document
    const estadosLabels = <?= json_encode(array_keys($resumen)) ?>;
    const cantidadData = <?= json_encode(array_column($resumen, 'total')) ?>;
    const montoData = <?= json_encode(array_column($resumen, 'monto')) ?>;
    const colors = ['#4CAF50', '#FF9800', '#2196F3', '#FF5722', '#9C27B0', '#E91E63', '#00BCD4'];

    // Gráfico de Distribución de Cantidad de Cotizaciones por Estado
    const ctxCantidad = document.getElementById('chartCotizacionesPorEstado').getContext('2d');
    new Chart(ctxCantidad, {
        type: 'pie',
        data: {
            labels: estadosLabels,
            datasets: [{
                label: 'Distribución de Cantidad',
                data: cantidadData,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' },
                title: { display: true, text: 'Distribución de Cantidad de Cotizaciones por Estado' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) label += ': ';
                            label += context.raw;
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Distribución de Montos por Estado
    const ctxMonto = document.getElementById('chartMontoPorEstado').getContext('2d');
    new Chart(ctxMonto, {
        type: 'pie',
        data: {
            labels: estadosLabels,
            datasets: [{
                label: 'Distribución de Monto',
                data: montoData,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' },
                title: { display: true, text: 'Distribución de Montos por Estado' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) label += ': ';
                            label += context.raw;
                            return label;
                        }
                    }
                }
            }
        }
    });	

</script>