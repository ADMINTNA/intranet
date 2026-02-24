<?php include_once("../includes/config.php"); ?>
<!doctype html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado"/>
    <meta name="Keywords" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cablead">
    <meta name="author" content="iConTel S.p.A.">
    <meta name="subject" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado">
    <meta NAME="Classification" content="TNA Solutions, Enlaces, Internet, ISP, WISP, Diseño, Seguridad Informatica, Desarrollo de Sistemas, Redes, Aplicaciones Web">
    <meta name="Geography" content="Chile">
    <meta name="Language" content="Spanish">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/favicon/favicon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <meta http-equiv="content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Expires" content="never">
    <meta name="Copyright" content="iConTel S.p.A.">
    <meta name="Designer" content="iConTel S.p.A.">
    <meta name="Publisher" content="iConTel S.p.A.">
    <meta name="Revisit-After" content="7 days">
    <meta name="distribution" content="Global">
    <meta name="city" content="Santiago">
    <meta name="country" content="Chile">
    <!-- index para los robots-->
    <meta name="robots" content="index,follow" />
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <!-- OpenGraph metadata-->
    <meta property="og:locale" content="es_LA" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="iContel Telecomunicaciones" />
    <meta property="og:description" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado"/>
    <meta property="og:url" content="https://www.icontel.cl/index.php" />
    <meta property="og:site_name" content="Icontel Telecomunicaciones" />
    <meta property="og:image" content="https://www.icontel.cl/favicon/logo.png" />
    <meta property="fb:admins" content="FB-AppID"/>
    <meta name="twitter:card" content="summary"/>
    <meta name="twitter:description" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado"/>
    <meta name="twitter:title" content="iConTel Telecomunicaciones"/>
    <meta name="twitter:site" content="iContel S.p.A."/>
    <meta name="twitter:creator" content="iConTel Telecomunicaciones"/>
    <link rel="canonical" href="https://www.icontel.cl/index.php" />
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.icontel.cl/favicon/favicon-16x16.png">    
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.icontel.cl/favicon/favicon-32x32.png">    
    <link rel="icon" type="image/png" sizes="57x57" href="https://www.icontel.cl/favicon/favicon-57x57.png">
    <link rel="icon" type="image/png" sizes="60x60" href="https://www.icontel.cl/favicon/favicon-60x60.png">
    <link rel="icon" type="image/png" sizes="72x72" href="https://www.icontel.cl/favicon/favicon-72x72.png">
    <link rel="icon" type="image/png" sizes="76x76" href="https://www.icontel.cl/favicon/favicon-76x76.png">
    <link rel="icon" type="image/png" sizes="96x96" href="https://www.icontel.cl/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="114x114" href="https://www.icontel.cl/favicon/favicon-114x114.png">
    <link rel="icon" type="image/png" sizes="120x120" href="https://www.icontel.cl/favicon/favicon-120x120.png">
    <link rel="icon" type="image/png" sizes="144x144" href="https://www.icontel.cl/favicon/favicon-144x144.png">
    <link rel="icon" type="image/png" sizes="152x152" href="https://www.icontel.cl/favicon/favicon-152x152.png">
    <link rel="icon" type="image/png" sizes="180x180" href="https://www.icontel.cl/favicon/favicon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="https://www.icontel.cl/favicon/favicon-192x192.png">
    <link rel="manifest" href="https://www.icontel.cl/favicon/manifest.json">
    <title>Servicios Recurrentes por Cliente</title>
        <link href='style.css' rel='stylesheet' type='text/css'>
        <script src='jquery-3.3.1.min.js' type='text/javascript'></script>
        <script src='script.js' type='text/javascript'></script>
<script type="application/x-javascript">    
function exportToExcel(tableId){
    let tableData = document.getElementById(tableId).outerHTML;
    tableData = tableData.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
    tableData = tableData.replace(/<input[^>]*>|<\/input>/gi, ""); //remove input params

    let a = document.createElement('a');
    a.href = `data:application/vnd.ms-excel, ${encodeURIComponent(tableData)}`
    a.download = 'ServicioVigentes_' + getRandomNumbers() + '.xls'
    a.click()
}
function getRandomNumbers() {
    let dateObj = new Date()
    let dateTime = `${dateObj.getHours()}${dateObj.getMinutes()}${dateObj.getSeconds()}`

    return `${dateTime}${Math.floor((Math.random().toFixed(2)*100))}`
}        

</script>
     <style type="text/css">
        table {
               border: none;
               color: #1F1D3E;
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
         body{
            margin:0;
            padding:0px;
            margin-left: 0px;
            margin-top: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
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
        table {
          border-collapse: collapse;
        }            
    </style>
</head>
<body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" >
    <div class='container'>
        <input type='hidden' id='sort' value='asc'>
         <table id="tblToExcl" name="tblToExcl" width='100%' id='empTable' border='1' cellpadding='10'>
            <tr>
                <th>#</span></th>
                <th><span onclick='sortTable("cliente");'>Cliente</span></th>
                <th><span onclick='sortTable("producto");'>Producto</span></th>
                <th><span onclick='sortTable("proveedor");'>Proveedor</span></th>
                <th><span onclick='sortTable("codigo_servicio");'>Código de Servicio</span></th>
                <th><span onclick='sortTable("categoria");'>Categoria</span></th>
                <th><span onclick='sortTable("valor");'>Valor UF</span></th>
                <th><span onclick='sortTable("costo");'>Costo UF</a></th>
                <th><span onclick='sortTable("recurrencia");'>Recurrencia</a></th>
            </tr>
            <?php 
            session_start();
            $query =  $_SESSION["query"]." ORDER BY cliente ASC";
            $conn = DbConnect("tnaoffice_suitecrm");
            $result = mysqli_query($conn,$query);
            $ptr = 0;
            $tot_valor = 0;
            $tot_costo = 0;
            $cliente = Array();
            while($row = mysqli_fetch_array($result)){
    $ptr ++;    
    $cliente      = $row['cliente'];
    $producto     = $row['producto'];
    $proveedor    = $row['proveedor'];
    $codigo_servicio = $row['codigo_servicio'];    
    $categoria    = $row['categoria'];
    $valor        = $row['valor'];
    $costo        = $row['costo'];
    $recurrencia  = $row['recurrencia'];
    $tot_valor    += $valor;
    $tot_costo    += $costo;
                ?>    <tr>
                    <td align="right"><?php echo $ptr; ?></td>
                    <td><?php echo $cliente; ?></td>
                    <td><?php echo $producto; ?></td>
                    <td><?php echo $proveedor; ?></td>
                    <td><?php echo $codigo_servicio; ?></td>
                    <td><?php echo $categoria; ?></td>
                    <td align="right"><?php echo number_format($valor, 2, ',', '.') ?></td>
                    <td align="right"><?php echo number_format($costo, 2, ',', '.'); ?></td>
                    <td><?php echo $recurrencia; ?></td>
                </tr>
            <?php
            }
            $conn->close(); 
            ?>        <tr>
                <th colspan="6" align="left">Totales </th>
                <th align="right"><?php echo number_format($tot_valor, 2, ',', '.'); ?></th>
                <th align="right"><?php echo number_format($tot_costo, 2, ',', '.'); ?></th>
                <th> </th>
            </tr>
        
        </table>
        <div class="row">
            <input type="button" onClick="exportToExcel('tblToExcl')" value="Export to Excel" />
		</div>
        <br><br>
        <br><br>
    </div>
</body>
</html>
