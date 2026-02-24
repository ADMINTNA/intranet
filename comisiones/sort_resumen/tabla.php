<?php
// ==========================================================
// /comisiones/sort_resumen/tabla.php
// Tabla Resumen – Informe de Comisiones
// Autor: Mauricio Araneda
// Fecha: 2025-11-05
// Codificación: UTF-8 sin BOM
// ==========================================================

// ==========================================
// Configuración global de sesión para Comisiones
// ==========================================
session_name('icontel_intranet_sess');

// Asegurar cookie válida para todos los subdominios
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '.icontel.cl');

// Seguridad
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fechas para el título
$desde = date('d-m-Y', strtotime($_SESSION['com_inicio']));
$hasta = date('d-m-Y', strtotime($_SESSION['com_fin']));



// Query original que venía de la tabla principal
 $query = $_SESSION['query'] . " GROUP BY vc.fac_vendedor, vc.fac_estado ORDER BY vc.fac_vendedor"; 
// $_SESSION['agrupar'] . $_SESSION['orden'];

include_once "config.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include_once("../../meta_data/meta_data.html"); ?>
    <title>Servicios Activos por Cliente</title>

    <link href='style.css' rel='stylesheet' type='text/css'>
    <script src='jquery-3.3.1.min.js' type='text/javascript'></script>
    <script src='script.js' type='text/javascript'></script>

    <style type="text/css">
        iframe {
            border: none;
            border-collapse: collapse;
            padding: 0;
            margin: 0;
            display: block;
            width: 100%;
            height: 250px;
        }
        table {
            border: none;
            color: black;
            font-size: 10px;
            border-collapse: collapse;
        }
        th, td {
            padding: 4px;
            font-size: 12px;
        }
        th {
            background-color: #1F1D3E;
            color: white;
        }
        body {
            margin: 0;
            padding: 0;
            font-size: 10px;
            background-color: #FFFFFF;
            color: #1F1D3E;
        }
        table tbody tr:nth-child(odd) {
            background: #F6F9FA;
        }
        table tbody tr:nth-child(even) {
            background: #FFFFFF;
        }
        table thead {
            background: #444;
            color: #fff;
            font-size: 18px;
        }
    </style>

    <script type="text/javascript">
        function exportToExcel(tableId) {
            let tableData = document.getElementById(tableId).outerHTML;
            tableData = tableData.replace(/<A[^>]*>|<\/A>/g, ""); 
            tableData = tableData.replace(/<input[^>]*>|<\/input>/gi, "");

            let a = document.createElement('a');
            a.href = `data:application/vnd.ms-excel, ${encodeURIComponent(tableData)}`;
            a.download = 'Comisiones_' + getRandomNumbers() + '.xls';
            a.click();
        }
        function getRandomNumbers() {
            let dateObj = new Date();
            let dateTime = `${dateObj.getHours()}${dateObj.getMinutes()}${dateObj.getSeconds()}`;
            return `${dateTime}${Math.floor((Math.random().toFixed(2)*100))}`;
        }
    </script>

</head>

<body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300">
<div class='container'>
    <input type='hidden' id='sort' value='asc'>

    <!-- TABLA PRINCIPAL (resumen por vendedor y tipo) -->
    <table id="empTable" name="empTable" align="center" width="750px" border='1' cellpadding='10'>
        <tr>
            <th>#</th>
            <th>Ejecutiv@</th>
            <th>Tipo</th>
            <th>Neto UF</th>
            <th>Costo UF</th>
            <th>Margen UF</th>
            <th>Neto Comi UF</th>
            <th>Comi UF</th>
            <th>Comi SGV UF</th>
            <th></th>
        </tr>

        <?php include_once("tabla_datos.php"); ?>
    </table>

    <br><br><br><br>

    <!-- SEGUNDO CUADRO (resumen.php) -->
    <iframe style="margin: 0;" src="resumen.php"></iframe>

</div>
</body>
</html>
