<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notas de ventas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include_once("../../meta_data/meta_data.html"); ?>
    <!--link rel="stylesheet" href="style.css" type="text/css" /-->
    <style>
        iframe {
          display: block;
          width: 100%;
          height: calc(100vh - 160px); /* Ajusta según alto de header + footer */
          border: none;
          overflow: auto;
        }
    </style>
</head>
<body>
<header style="background-color: #1F1D3E; color: white; padding: 10px; border: none;">
    <table width="100%" border="0">
        <tr>
            <td width="200" valign="top" align="left">
                <img src="../images/logo_icontel_azul.jpg" height="80" alt="Logo iConTel"/>
            </td>
            <td>
                <table width="100%" height="100%" border="0">
                    <tr>
                        <td align="center" style="font-size: 20px;">Notas de Ventas de BSale en Sweet</td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size: 12px;">(click sobre los títulos para ordenar)</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</header>    <!-- Contenido con iframe -->
    <div class="table-container">
         <iframe src="tabla.php"></iframe>    
    </div>

    <!-- Footer -->
    <?php include_once("../../footer/footer.php"); ?>

</body>
</html>