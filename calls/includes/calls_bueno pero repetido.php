<html lang='en_us'>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
    <?php
    date_default_timezone_set("America/Santiago");
    if (isset($_POST['ani'])){
        $ani = $_POST['ani']; 
        $nombre  = $_POST['nombre']; 
    }
    if (isset($_GET['ani'])) {
        $ani    = $_GET['ani']; 
        $nombre = $_GET['nombre']; 
    }
    if(empty($ani)) exit(); 
    $anix = $ani;    
    $pos = strpos($ani,":");
    if($pos) $anix = substr($anix,$pos+1,strlen($anix)-$pos);
    $anix = str_replace(" ","", $anix);
    $anix = str_replace("+","", $anix);
    $ani_final = "%".$anix."%";

    $server   = "localhost";
    $user     = "tnasolut_data_studio";
    $password = "P3rf3ct0.,";
    $db_sweet  = "tnasolut_sweet";
    $db_dumit  = "tnasolut_Bsale";   
    $conn = new mysqli($server, $user, $password);
     if ($conn->connect_error) { die("No me pude conectar a servidor localhost: " . $conn->connect_error); }
    // va a buscar datos a sweet
    $bd_seleccionada = mysqli_select_db($conn, $db_sweet);
    if (!$bd_seleccionada) { die ('No se puede usar '.$db_sweet.' : ' . mysql_error()); }
    $sql = "SELECT 
    UPPER(a.name) 							as razon_social,
    a.id                                    as id,
    a.phone_office                          as telefono,
    ac.rut_c 								as rut,
    ac.estatusfinanciero_c 					as estado,
    p.name 									as produ_nombre,
    p.product_qty 							as produ_cantidad,
    p.account_name 							as produ_proveedor,
    p.codigo_servicio						as codigo_servicio,
    ca.name 								as produ_categoria,
    ct.first_name                           as nombre,
    ct.last_name                            as apellido
    FROM aos_quotes 					as q	FORCE INDEX(aos_quotes_stage)
    LEFT JOIN accounts 					as a 	ON a.id = q.billing_account_id
    LEFT JOIN accounts_contacts 		as co	ON a.id					= co.account_id
    LEFT JOIN contacts					as ct	ON co.contact_id		= ct.id
    LEFT JOIN accounts_cstm 			as ac 	ON ac.id_c = a.id
    LEFT JOIN aos_products_quotes 		as p 	ON p.parent_id = q.id
    LEFT JOIN aos_products 				as pr 	ON p.product_id = pr.id
    LEFT JOIN aos_product_categories 	as ca 	ON pr.aos_product_category_id = ca.id
    WHERE q.stage = 'Closed Accepted' 
    && ( REPLACE(REPLACE(a.phone_office,' ',''),'+','')   LIKE '{$ani_final}' OR
         REPLACE(REPLACE(ct.phone_home ,' ',''),'+','')   LIKE '{$ani_final}' OR
         REPLACE(REPLACE(ct.phone_mobile ,' ',''),'+','') LIKE '{$ani_final}' OR
         REPLACE(REPLACE(ct.phone_work ,' ',''),'+','')   LIKE '{$ani_final}' OR
         REPLACE(REPLACE(ct.phone_other ,' ',''),'+','')  LIKE '{$ani_final}' OR
         REPLACE(REPLACE(ct.phone_fax ,' ',''),'+','')    LIKE '{$ani_final}' )
    && p.parent_type = 'AOS_Quotes' 
    && !q.deleted   
    && !a.deleted  
    && !co.deleted  
    && !ct.deleted  
    && !p.deleted  
    && !pr.deleted  
    && !ca.deleted
    ";
    $result = $conn->query($sql);
    if ( ($result->num_rows > 0)  && (strlen($ani)>8) ) {    
      $ptr = 1;  
      while($row = $result->fetch_assoc()) {
       if($ptr == 1) {
            $filtro = array(' ','.');
            $rut = str_replace($filtro, "", $row["rut"]);
// Busco datos de Dumit
            $bd_seleccionada = mysqli_select_db($conn, $db_dumit);
            if (!$bd_seleccionada) { die ('No se puede usar '.$db_dumit.' : ' . mysql_error()); }
            $sql = "SELECT cron_duemint_documents.status        AS estado,
                           cron_duemint_documents.statusName    AS nom_estado,
                           0                                    AS dias,
                           count(cron_duemint_documents.number) AS num_doc,
                           sum(cron_duemint_documents.total)    AS monto,
                           cron_duemint_clients.url             AS wrl_cliente
                      FROM cron_duemint_documents, cron_duemint_clients
                     WHERE cron_duemint_documents.clientTaxid = '{$rut}'
                       AND cron_duemint_documents.clientTaxid = cron_duemint_clients.taxid
                       AND cron_duemint_documents.status        IN (1)
                       GROUP BY cron_duemint_documents.statusName
                    UNION ALL
                    SELECT cron_duemint_documents.status        AS estado,
                           cron_duemint_documents.statusName    AS nom_estado,
                           DATEDIFF((cron_duemint_documents.dueDate), DATE(NOW())) AS dias,
                           count(cron_duemint_documents.number) AS num_doc,
                           sum(cron_duemint_documents.amountDue) AS monto,
                           cron_duemint_clients.url              AS wrl_cliente
                      FROM cron_duemint_documents, cron_duemint_clients
                     WHERE cron_duemint_documents.clientTaxid = '{$rut}'
                       AND cron_duemint_documents.clientTaxid = cron_duemint_clients.taxid
                       AND cron_duemint_documents.status      IN (2, 3)
                       GROUP BY cron_duemint_documents.statusName";
            
             $dumit = $conn->query($sql);
               if ($dumit->num_rows > 0) { 
                    while($col = $dumit->fetch_assoc()) {
                       switch($col["estado"]) {
                       case 1:
                        $dumit_pagada = $col["monto"];
                        break;
                       case 2:
                        $dumit_por_vencer = $col["monto"];
                        break;
                       case 3:
                        $dumit_vencida =  $col["monto"];
                        break;
                       default:
                       } 
                       $dumit_portal = $col["wrl_cliente"];
                    }
               } else {
                 $dumit_pagada     = "No en Dumit";  
                 $dumit_por_vencer = "No en Dumit"; 
                 $dumit_vencida    = "No en Dumit";
                 $dumit_portal     = "https://www.duemint.com";
               }
// Fin Datos Dumit
           $estado = $row["estado"];
          // $estado = "De Baja";
           $url= 'https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAccounts%26offset%3D4%26stamp%3D1642464369091709700%26return_module%3DAccounts%26action%3DDetailView%26record%3D'.a.id;          
           switch($estado) {
           case "De Baja":
            $style = "style=\"background-color:red;color:yellow;font-size: medium;\""; 
                break;
           case "Suspendido":
            $style = "style=\"background-color:red;color:yellow;font-size: medium;\""; 
                break;
           case "Extrajudicial":
            $style = "style=\"background-color:red;color:yellow;font-size: medium;\""; 
                break;
           default:
            $style = "style=\"background-color:white;color:black;font-size: medium;\""; 
           } 
        $titulo = "<td colspan=\"6\" ".$style.">".date("d-m-Y H:i:s").": Llamada desde <a href=\"tel:{$ani}\">".formatPhoneNumber($ani)."</a> de <a href='".$row["url"]."' target=\"_blank\">".$row["razon_social"]."</a>"." - ".$row["nombre"]." ".$row["apellido"]." - Rut: ".$row["rut"]."&nbsp;&nbsp;[&nbsp;".$estado."&nbsp;]</td>";
           
          ?>
        <div class="excel2007">
           <table border="1" align="center" class="ExcelTable2007">
              <tbody>
               <tr>
                   <?php echo $titulo; ?>
               </tr> 
               <tr>
                   <td colspan="6">  
                        <table cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse"  width="100%">
                          <tbody>
                            <tr style="border: none;" align="center" >
                              <td width="15%" style="background-color:green;color:white;font-size: medium;border: none;">Por Vencer</td>
                              <td  width="15%" style="background-color:orangered;color:white;font-size: medium;border: none;">Vencido</td>
                              <td style="border: none;">Portal de Pago Cliente</td>
                            </tr>
                            <tr align="center">
                              <td style="background-color:green;color:white;font-size: medium;border: none;"><?php echo number_format($dumit_por_vencer); ?></td>
                              <td style="background-color:orangered;color:white;font-size: medium;border: none;"><?php echo number_format($dumit_vencida); ?></td>
                              <td style="border: none;"><a href="<?php echo $dumit_portal; ?>" target="_blank"><?php echo $dumit_portal; ?></a></td>
                            </tr>
                          </tbody>
                        </table>
                   </td>
               </tr>      
               <tr>
                  <th>Cliente</th>
                  <th>R.U.T.</th>
                  <th>Cant.</th>
                  <th>Servicio</th>
                  <th>Proveedor</th>
                  <th width="30%">Cod. Servicio</th>
                </tr>  
         <?PHP 
        $ptr ++;  
        }  
          echo "<tr>
              <td>".$row["razon_social"]."</td>
             <td>".$row["rut"]."</td>
              <td align=\"center\">".number_format($row["produ_cantidad"])."</td>
              <td>".$row["produ_nombre"]."</td>
              <td>".$row["produ_proveedor"]."</td>
              <td>".$row["codigo_servicio"]."</td>
             </tr>";

       }
        ?>
                </tbody>
            </table>
        </div>
        </br></br>
    <?php
    } else {
      echo date("d-m-Y H:i:s").":Llamada desde número telefónico <b>".$anix."</b> no encontrado en <a href='https://sweet.icontel.cl\' target='_blank'>Sweet CRM</a>.</br>";
    }
    $conn->close(); 
    
function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

    if(strlen($phoneNumber) == 11) {
        $countryCode = substr($phoneNumber, 0, 2);
        $first = substr($phoneNumber, 2, 1);
        switch($first){
         case 9:
            $region   = substr($phoneNumber, 2, 1);
            $primeros = substr($phoneNumber, 3, 4);
            $ultimos  = substr($phoneNumber, 7, 4);
            break;
         case 2:
            $region   = substr($phoneNumber, 2, 1);
            $primeros = substr($phoneNumber, 3, 4);
            $ultimos  = substr($phoneNumber, 7, 4);
            break;
         default:
            $region   = substr($phoneNumber, 2, 2);
            $primeros = substr($phoneNumber, 4, 3);
            $ultimos  = substr($phoneNumber, 7, 4);
        }
        $phoneNumber = '+'.$countryCode.' ('.$region.') '.$primeros.' '.$ultimos;
    }
    return $phoneNumber;
}    
    
    
    ?>
</body>
</html>

    