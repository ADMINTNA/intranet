<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
<?PHP include_once("../../meta_data/meta_data.html"); ?>   
    <title>Casos iConTel</title>
    <style type="text/css">
        table {
               border: none;
               color: #1F1D3E;
               color: black;
               font-size: 12px;
               border-collapse: collapse;
           }   
          th, td {
              padding: 2px;
              font-size: 14px;
         }
         th {
            background-color: #1F1D3E; 
            color: white;
         }
         body{
            margin:0;
            padding:0;
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
            include_once("config.php");    
            date_default_timezone_set("America/Santiago");
            if (isset($_POST['categoria']))   { $categoria   = $_POST['categoria']; }
            if (isset( $_GET['categoria']))   { $categoria   =  $_GET['categoria']; }
            if(!isset($categoria)) exit("Error: Campo Categoría buscada está vacío.<br>");
            //$contacto = strtolower($nombre)." ".strtolower($apellido);
            // me conecto a la Base de Datos
            $url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26offset%3D1%26stamp%3D1644666990053569200%26return_module%3DCases%26action%3DDetailView%26record%3D";      
            $conn = DbConnect($db_sweet);
            $sql = "CALL searchopencasesdetail('".$categoria."')";  
            if($categoria == "todos")    $sql = "CALL searchopencasesdetailall()";
            if($categoria == "usuarios") $sql = "CALL searchopencasesusers()";   
            $result = $conn->query($sql);
            $ptr=0;
            if($result->num_rows > 0)  { 
                while($row = $result->fetch_assoc()) {
                  $ptr ++;    
                  $contenido .= "<tr>";
                  $contenido .= "<td>".$ptr."</td>";
                  $contenido .= "<td>".$row["categoria"]."</td>";
                  $contenido .= '<td><a target="_blank" href="'.$url.$row["id"].'">'.$row{"numero"}.'</a></td>';
                  $contenido .= "<td>".$row["asunto"]."</td>";
                  $contenido .= "<td>".$row["estado"]."</td>";
                  $contenido .= "<td>".$row["cliente"]."</td>";
                  $contenido .= "<td>".$row["nombre"]." ".$row["apellido"]."</td>";
                  $contenido .= "<td>".$row["f_creacion"]."</td>";
                  $contenido .= "<td>".$row["f_modifica"]."</td>";
                  $contenido .= "</tr>";
                }
            } else {
              $contenido = "<tr><td colspan='9'>No se encontraron datos con la categoría= ".$categoria."</td></tr>";
            }
            $conn->close(); 
        ?>
        <table align="center" width="100%">
              <tr align="center" style="color: white;background-color: #1F1D3E;">
                  <td colspan="3" align="left" valign="top" rowspan="1"><img src="../images/logo_icontel_azul.jpg"  height="100" alt=""/></td>
                  <td colspan="6" align="center" valign="bottom"><h1>Casos Abiertos de la Categoría:&nbsp; <?php echo $categoria; ?></h1></td>
                </tr>
                <tr align="left">
                    <th> # </th>
                    <th>Categoría</th>
                    <th>Número</th>
                    <th>Asunto</th>
                    <th>Estado</th>
                    <th>Razón Social</th>
                    <th>Asignado a</th>
                    <th width="9%">Fecha Creación</th>
                    <th width="9%">Fecha Modificación</th>
                </tr>
                 <?PHP echo $contenido; ?>
        </table>
    </body> 
</html>

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
  