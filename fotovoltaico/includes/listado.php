<?php
require_once 'config.php';

// Obtener listado de soluciones
$soluciones = listarSoluciones();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Soluciones Fotovoltaicas</title>
</head>
<body>
    <h1>Gestión de Soluciones Fotovoltaicas</h1>

    <!-- Listado de soluciones -->
    <h2>Listado de Soluciones</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Dirección</th>
                <th>Fecha de Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($soluciones as $solucion): ?>
                <tr>
                    <td><?= $solucion['id'] ?></td>
                    <td><?= $solucion['cliente'] ?></td>
                    <td><?= $solucion['direccion'] ?></td>
                    <td><?= $solucion['fecha_creacion'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= $solucion['id'] ?>">
                            <button type="submit">Eliminar</button> 
                        </form>
                    </td>
                    <td><button class="cargar" data-id="<?= $solucion['id'] ?>">Cargar</button></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Formulario para mostrar y editar datos -->
    <h2>Formulario de Proyecto</h2>
    <form id="formProyecto">
        <label>Cliente: <input type="text" id="cliente" required></label><br>
        <label>Dirección: <input type="text" id="direccion" required></label><br>
        <label>Consumo Mensual: <input type="number" step="0.01" id="consumoMensual" required></label><br>
        <label>Facturación Mensual: <input type="number" step="0.01" id="facturacionMensual" required></label><br>
        <label>Horas Sol: <input type="number" step="0.01" id="horasSol" required></label><br>
        <label>Potencia Panel: <input type="number" step="0.01" id="potenciaPanel" required></label><br>
        <label>Rendimiento Panel: <input type="number" step="0.01" id="rendimientoPanel" required></label><br>
        <label>Tamaño Panel: <input type="number" step="0.01" id="tamanoPanel" required></label><br>
        <label>Trámite SEC: <input type="number" step="0.01" id="tramiteSec"></label><br>
        <label>SEC: <input type="checkbox" id="sec"></label><br>
        <label>Tipo Cálculo: <input type="text" id="tipoCalculo" required></label><br>
        <label>Cantidad Inversores: <input type="number" id="cantidadInversores" required></label><br>
        <label>Estructura: <input type="number" step="0.01" id="estructura"></label><br>
        <label>Costo Inversor: <input type="number" step="0.01" id="costoInversor" required></label><br>
        <label>Cantidad Paneles: <input type="number" id="cantidadPaneles" required></label><br>
        <label>kWh Planta: <input type="number" step="0.01" id="kWhPlanta" required></label><br>
        <label>kW Día: <input type="number" step="0.01" id="kWDia" required></label><br>
        <label>Cumplimiento: <input type="number" step="0.01" id="cumplimiento" required></label><br>
    </form>

    <script src="index.js"></script>
</body>
</html>
