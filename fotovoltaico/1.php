<?php
require_once './includes/config.php'; // Conexión y funciones de la base de datos
require_once './includes/presupuesto_functions.php'; // Funciones para presupuestos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Presupuestos</title>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
    <h1>Gestión de Presupuestos Fotovoltaicos</h1>

    <!-- Formulario para crear o editar presupuestos -->
    <h2>Crear/Editar Presupuesto</h2>
    <form method="POST" action="./includes/handle_presupuesto.php">
        <input type="hidden" name="accion" value="crear">
        <input type="hidden" name="id" id="presupuesto-id">

        <label>Cliente:</label>
        <input type="text" name="cliente" id="presupuesto-cliente" required><br>

        <label>Dirección:</label>
        <input type="text" name="direccion" id="presupuesto-direccion" required><br>

        <label>Consumo Mensual (kWh):</label>
        <input type="number" step="0.01" name="consumoMensual" id="presupuesto-consumoMensual" required><br>

        <label>Facturación Mensual (CLP):</label>
        <input type="number" step="0.01" name="facturacionMensual" id="presupuesto-facturacionMensual" required><br>

        <label>Horas Sol:</label>
        <input type="number" name="horasSol" id="presupuesto-horasSol" required><br>

        <label>Tamaño Panel (m²):</label>
        <input type="number" step="0.01" name="tamanoPanel" id="presupuesto-tamanoPanel" required><br>

        <label>Cantidad Paneles:</label>
        <input type="number" name="cantidadPaneles" id="presupuesto-cantidadPaneles" required><br>

        <label>m² Paneles:</label>
        <input type="number" step="0.01" name="m2Paneles" id="presupuesto-m2Paneles" required><br>

        <label>Potencia Panel (kWh):</label>
        <input type="number" step="0.01" name="potenciaPanel" id="presupuesto-potenciaPanel" required><br>

        <label>Rendimiento Panel (%):</label>
        <input type="number" step="0.01" name="rendimientoPanel" id="presupuesto-rendimientoPanel" required><br>

        <label>Cantidad Plantas:</label>
        <input type="number" name="cantidadPlantas" id="presupuesto-cantidadPlantas" required><br>

        <label>kWh Planta:</label>
        <input type="number" step="0.01" name="kWhPlanta" id="presupuesto-kWhPlanta" required><br>

        <label>kW Mes:</label>
        <input type="number" step="0.01" name="kWMes" id="presupuesto-kWMes" required><br>

        <label>Cumplimiento:</label>
        <input type="text" name="cumplimiento" id="presupuesto-cumplimiento" required><br>

        <label>Margen Comercial (%):</label>
        <input type="number" step="0.01" name="margenComercial" id="presupuesto-margenComercial" required><br>

        <button type="submit">Guardar</button>
    </form>

    <!-- Tabla de presupuestos -->
    <h2>Listado de Presupuestos</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($presupuestos as $presupuesto): ?>
                <tr>
                    <td><?= htmlspecialchars($presupuesto['id']) ?></td>
                    <td><?= htmlspecialchars($presupuesto['cliente']) ?></td>
                    <td><?= htmlspecialchars($presupuesto['direccion']) ?></td>
                    <td>
                        <form method="POST" action="./includes/handle_presupuesto.php" style="display:inline;">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= $presupuesto['id'] ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                        <button onclick="editarPresupuesto(<?= htmlspecialchars(json_encode($presupuesto)) ?>)">Editar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="./js/scripts.js"></script>
</body>
</html>
