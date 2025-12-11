<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
<?PHP include_once("../../meta_data/meta_data.html"); ?>   
    <title>Productos iConTel</title>
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
            padding:4px;
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
        <?php
      // activo mostrar errores
    // error_reporting(E_ALL);
    //ini_set('display_errors', '1');
    
    include_once("./includes/config.php");    
    // var_dump($_POST);        
    if(isset($_POST['nombre']))         $nombre     = $_POST['nombre'];    
    if(isset($_POST['variante']))       $variante   = $_POST['variante'];    
    if(isset($_POST['descripcion']))    $desripcion = $_POST['descripcion'];    
    if(isset($_POST['categoria']))      $categoria  = $_POST['categoria'];    
    If(!empty($nombre))         $cuales  = " && pr.name                    like '%".$nombre."%'";
    if(!empty($variante))       $cuales .= " && pr.part_number             like '%".$variante."%'";
    if(!empty($descripcion))    $cuales .= " && pr.description             like '%".$descripcion."%'";
    if(!empty($categoria))      $cuales .= " && pr.aos_product_category_id like '%".$categoria."%'";
    $sql = "SELECT pr.id 			as id,
                   pr.name			as producto,
                   pr.description 	as descripcion,
                   pr.part_number	as numero_parte,
                   pr.price 		as valor,
                   ca.name 			as categoria,
                   ca.description  	as categ_descrip,
                   CASE
                        WHEN pr.`type` = 'Good' 	THEN 'Equipo' 
                        WHEN pr.`type` = 'Service' 	THEN 'Servicio' 
                        ELSE 'SIN INFORMACION'
                   END								as tipo
            FROM aos_products as pr 
            LEFT JOIN aos_product_categories as ca on pr.aos_product_category_id = ca.id
            WHERE !pr.deleted
               && !ca.deleted";
     $order .= " ORDER BY pr.name ASC";
     $sql .= $cuales.$order;
     $url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAOS_Products%26offset%3D1%26stamp%3D1647912995043611800%26return_module%3DAOS_Products%26action%3DDetailView%26record%3D";      

      //echo $sql."<br><br>";  
        // me conecto a la Base de Datos
            $conn = DbConnect("tnasolut_sweet");
            $result = $conn->query($sql);
            $ptr=0;
            if($result->num_rows > 0)  { 
                while($row = $result->fetch_assoc()) {
                  $ptr ++;    
                  $contenido .= "<tr>";
                  $contenido .= "<td>".$ptr."</td>";
                  $contenido .= '<td><a target="_blank" href="'.$url.$row["id"].'">'.$row{"producto"}.'</a></td>';
                  $contenido .= "<td>".$row["numero_parte"]."</td>";
                  $contenido .= "<td align='right'>".number_format($row["valor"],2)."</td>";
                  $contenido .= "<td>".$row["descripcion"]."</td>";
                  $contenido .= "<td>".$row["categoria"]."</td>";
                  $contenido .= "</tr>";
                }
            } else {
              $contenido = "<tr><td colspan='9'>No se encontraron datos.</td></tr>";
            }
            $conn->close(); 
        ?>
        <table align="center" width="100%">
              <tr align="center" style="color: white;background-color: #1F1D3E;">
                  <td colspan="3" align="left" valign="top" rowspan="1"><img src="./images/logo_icontel_azul.jpg"  height="100" alt=""/></td>
                  <td colspan="10" align="center" valign="bottom"><h1>Listado de Productos</h1></td>
                </tr>
                <tr align="left">
                    <th> # </th>
                    <th>Producto</th>
                    <th>Variante</th>
                    <th>Valor</th>
                    <th width="50%">Descripción</th>
                    <th>Categoría</th>
                </tr>
                 <?PHP echo $contenido; ?>
        </table>
        <br><br>
    </body> 
</html>

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
  










    
