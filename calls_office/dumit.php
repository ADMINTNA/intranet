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
    $ani_final = "\"%".$anix."%\"";
// ------ SQL Consulta Servicios Activos --------
    $server   = "localhost";
    $user     = "tnasolut_data_studio";
    $password = "P3rf3ct0.,";
    $dbname   = "tnasolut_sweet";
    // Create connection
    $conn = new mysqli($server, $user, $password, $dbname);
    mysqli_query("SET NAMES 'utf8'");
    mysql_set_charset("UTF8", $conn);

    // Check connection
    if ($conn->connect_error) {
      die("No me pude conectar a base de datos: " . $conn->connect_error);
    }

    // $sql = "SELECT name, phone_office FROM `accounts` WHERE REPLACE(REPLACE(`phone_office`,\" \",\"\"),\"+\",\"\") LIKE ".$ani_final. " && !`deleted`";

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
    ca.name 								as produ_categoria
    FROM aos_quotes 					as q	FORCE INDEX(aos_quotes_stage)
    LEFT JOIN accounts 					as a 	ON a.id = q.billing_account_id
    LEFT JOIN accounts_cstm 			as ac 	ON ac.id_c = a.id
    LEFT JOIN aos_products_quotes 		as p 	ON p.parent_id = q.id
    LEFT JOIN aos_products 				as pr 	ON p.product_id = pr.id
    LEFT JOIN aos_product_categories 	as ca 	ON pr.aos_product_category_id = ca.id
    WHERE q.stage = 'Closed Accepted' 
    && REPLACE(REPLACE(a.phone_office,' ',''),'+','') LIKE '%{$anix}%'
    && p.parent_type = 'AOS_Quotes' 
    && !q.deleted   
    && !a.deleted  
    && !p.deleted  
    && !pr.deleted  
    && !ca.deleted  
    ";
    $result = $conn->query($sql);
    if ( ($result->num_rows > 0)  && (strlen($anix)>8) ) {    
   // if ( ($result->num_rows > 0) ) {
      $ptr = 1;  
      while($row = $result->fetch_assoc()) {
       if($ptr == 1) {
           $estado = $row["estado"];
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
        $titulo = "<td colspan=\"6\" ".$style.">".date("d-m-Y H:i:s").": Llamada desde ".$row["telefono"]." de <a href='".$row["url"]."' target=\"_blank\">".$row["razon_social"]."</a>"." - Rut: ".$row["rut"]."&nbsp;&nbsp;[&nbsp;".$estado."&nbsp;]</td>";
           
          ?>
        <div class="excel2007">
           <table border="1" align="center" class="ExcelTable2007">
              <tbody>
               <tr>
                   <?php echo $titulo; ?>
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
    ?>
</body>
</html>

    