<?php 
// ==========================================================
// /intranet/oportunidades/sort/tabla.php
// Resultado de la búsqueda de oportunidades
// Autor: Mauricio Araneda
// Fecha: 2025-11-18
// Codificación: UTF-8 sin BOM
// ==========================================================

// ⚠️ IMPORTANTE: nada de HTML antes de esto
session_name('icontel_intranet_sess');
session_start();

// Cabeceras
header('Content-Type: text/html; charset=utf-8');

// Config y funciones
include "config.php";
include_once("../../includes/funciones.php");

// Debug controlado por sesión
$DEBUG = (!empty($_SESSION["debug"]) && $_SESSION["debug"] === true);

// ==========================================================
// 🛑 VALIDAR QUE EXISTA LA QUERY
// ==========================================================
if (empty($_SESSION["query_op"])) {
    ?>
    <h2 style="color:red; text-align:center;">❌ No existe consulta activa de Oportunidades</h2>
    <p style="text-align:center;">Debe volver al buscador y ejecutar una búsqueda.</p>
    <p style="text-align:center;">
        <a href="../buscadores/oportunidades.php">🔍 Volver al buscador</a>
    </p>
    <?php
    exit;
}

// Query final segura
$query = $_SESSION["query_op"] . " ORDER BY op_numero DESC";

// Debug sólo si $_SESSION['debug']==true
if ($DEBUG) {
    echo "<pre style='font-size:10px;color:#555;background:#EEE;border:1px solid #CCC;padding:8px;'>";
    echo "🔍 DEBUG SQL OPORTUNIDADES\n\n" . $query;
    echo "</pre>";
}

// ==========================================================
// 🔗 Conexión BD
// ==========================================================
$conn = DbConnect("tnasolut_sweet");

if (!$conn) {
    die("<h3 style='color:red;'>❌ Error al conectar con la base de datos</h3>");
}

// Ejecutar SQL
$result = mysqli_query($conn, $query);

if (!$result) {

    if ($DEBUG) {
        echo "<h3 style='color:red;'>❌ Error SQL en oportunidades</h3>";
        echo "<pre>".mysqli_error($conn)."</pre>";
    }

    exit("<h3 style='color:red;text-align:center;'>❌ Error ejecutando la consulta</h3>");
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once("../../meta_data/meta_data.html"); ?>
    <title>Oportunidades iContel</title>

    <link href='style.css' rel='stylesheet' type='text/css'>
    <script src='jquery-3.3.1.min.js'></script>
    <script src='script.js'></script>

    <style>
        table { border-collapse: collapse; font-size: 12px; color:#1F1D3E; width:100%; }
        th { background:#1F1D3E; color:white; padding:6px; }
        td { padding:6px; }
        tr:nth-child(odd) { background:#F6F9FA; }
        tr:nth-child(even){ background:white; }
    </style>
</head>

<body>
<div class="container">
    <table id="empTable" border="1" cellpadding="10">
        <tr>
            <th>#</th>
            <th>Número</th>
            <th>Asunto</th>
            <th>Cliente</th>
            <th>Usuario</th>
            <th>Etapa</th>
            <th>Fecha Creación</th>
        </tr>
        <?php
        $ptr = 0;

        $url = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc="
             . "index.php%3Fmodule%3DOpportunities%26action%3DDetailView%26record%3D";

        while ($row = mysqli_fetch_assoc($result)) {

            $ptr++;
            $id      = $row["op_id"];
            $numero  = $row["op_numero"];
            $asunto  = $row["op_nombre"];
            $cliente = $row["op_cliente"];

            // Unificación del nombre del usuario
            $usuario = trim($row["u_nombre"] . " " . $row["u_apellido"]);

            $estado  = $row["op_estado"];
            $fecha   = horacl($row["op_fecha"]);
            ?>
            <tr>
                <td><?= $ptr ?></td>
                <td><a target="_blank" href="<?= $url.$id ?>"><?= $numero ?></a></td>
                <td><?= $asunto ?></td>
                <td><?= $cliente ?></td>
                <td><?= $usuario ?></td>
                <td><?= $estado ?></td>
                <td><?= $fecha ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>