<?php
var_dump($_POST);
	echo "<br><br>";
 
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
    if ($accion === 'crear') {
        $datos = [
            'cliente' => $_POST['cliente'],
            'direccion' => $_POST['direccion'],
            'consumoMensual' => $_POST['consumoMensual'],
            'facturacionMensual' => $_POST['facturacionMensual'],
            'horasSol' => $_POST['horasSol'],
            'tamanoPanel' => $_POST['tamanoPanel'],
            'cantidadPaneles' => $_POST['cantidadPaneles'],
            'm2Paneles' => $_POST['m2Paneles'],
            'potenciaPanel' => $_POST['potenciaPanel'],
            'rendimientoPanel' => $_POST['rendimientoPanel'],
            'cantidadPlantas' => $_POST['cantidadPlantas'],
            'kWhPlanta' => $_POST['kWhPlanta'],
            'kWMes' => $_POST['kWMes'],
            'cumplimiento' => $_POST['cumplimiento'],
            'margenComercial' => $_POST['margenComercial'],
        ];
        guardarPresupuesto($datos);
    } elseif ($accion === 'eliminar') {
        eliminarPresupuesto($_POST['id']);
    }
}

header("Location: ../index.php");
exit;
?>
