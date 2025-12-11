<?php
// archivo: ./includes/presupuesto_functions.php

function guardarPresupuesto($datos) {
    $conn = new mysqli('localhost', 'usuario', 'contrasena', 'base_de_datos');

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO presupuestos (cliente, direccion, consumoMensual, facturacionMensual, horasSol, tamanoPanel, cantidadPaneles, m2Paneles, potenciaPanel, rendimientoPanel, cantidadPlantas, kWhPlanta, kWMes, cumplimiento, margenComercial) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiididdddiiidi",
        $datos['cliente'],
        $datos['direccion'],
        $datos['consumoMensual'],
        $datos['facturacionMensual'],
        $datos['horasSol'],
        $datos['tamanoPanel'],
        $datos['cantidadPaneles'],
        $datos['m2Paneles'],
        $datos['potenciaPanel'],
        $datos['rendimientoPanel'],
        $datos['cantidadPlantas'],
        $datos['kWhPlanta'],
        $datos['kWMes'],
        $datos['cumplimiento'],
        $datos['margenComercial']
    );

    if ($stmt->execute()) {
        echo "Presupuesto guardado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

function eliminarPresupuesto($id) {
    $conn = new mysqli('localhost', 'usuario', 'contrasena', 'base_de_datos');

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM presupuestos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Presupuesto eliminado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
