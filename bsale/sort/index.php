<?php
//=====================================================
// /intranet/bsale/sort/index.php
// Redirige a tabla.php si la sesión es válida y contiene query
// Autor: Mauricio Araneda
// Actualizado: 09-11-2025
//=====================================================

include_once(__DIR__ . '/../includes/security_check.php'); // Protege acceso

// ⚠️ Validación extra opcional: si no existe query previa, salir
if (empty($_SESSION['query'])) {
    echo "<p style='color: red; font-weight: bold; padding: 20px; font-size: 16px;'>
    ⚠️ Error: no se encontró una consulta previa. Por favor realiza la búsqueda desde <a href='/bsale/busqueda.php'>Bsale</a>.
    </p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notas de Ventas Bsale – TNA Group</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include_once("../../meta_data/meta_data.html"); ?>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #ffffff;
        }

        header {
            background-color: #1F1D3E;
            color: white;
            padding: 10px;
            border: none;
        }

        iframe {
            display: block;
            width: 100%;
            height: calc(100vh - 160px); /* Ajusta según header+footer */
            border: none;
            overflow: auto;
        }

        .table-container {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

<!-- 🔷 HEADER -->
<header>
    <table width="100%" border="0">
        <tr>
            <td width="200" valign="top" align="left">
                <img src="../images/logo_icontel_azul.jpg" height="80" alt="Logo iConTel"/>
            </td>
            <td>
                <table width="100%" height="100%" border="0">
                    <tr>
                        <td align="center" style="font-size: 20px; font-weight: bold;">
                            Notas de Venta y Facturas en Bsale
                      </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size: 12px;">
                            (haz clic en los títulos de columnas para ordenar)
                      </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</header>

<!-- 🔶 IFRAME CON RESULTADOS -->
<div class="table-container">
    <iframe src="tabla.php"></iframe>    
</div>

<!-- 🔻 FOOTER CORPORATIVO -->
<?php include_once("../../footer/footer.php"); ?>

</body>
</html>