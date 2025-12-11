        <?php
            include_once("config.php"); 
            if (isset($_POST['categoria']))   { $categoria   = $_POST['categoria']; }
            if (isset( $_GET['categoria']))   { $categoria   =  $_GET['categoria']; }
            if(!isset($categoria)) exit("Error: Campo Categoría buscada está vacío.<br>");
            //$contacto = strtolower($nombre)." ".strtolower($apellido);
            // me conecto a la Base de Datos
            $url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26offset%3D1%26stamp%3D1644666990053569200%26return_module%3DCases%26action%3DDetailView%26record%3D";      
            $conn = DbConnect1($db_sweet);
            $sql = "CALL searchopencasesdetail('".$categoria."')";  
            if($categoria == "kickoff")     $sql = "CALL Kick_Off_Operaciones_Abiertos()";                        
            if($categoria == "todos")       $sql = "CALL searchopencasesdetailall()";            
            if($categoria == "Ultimos Casos") {$sql = "CALL Kick_Off_Operaciones_Cerrados(3)"; }
            if($categoria == "usuarios") $sql = "CALL searchopencasesusers()";   
            if($categoria == "En Seguimiento") $sql = "CALL searchcasesenseguimientodetalle()";
            if($categoria == "Congelado") $sql = "CALL searchcasescongeladodetalle()";
            $result = $conn->query($sql);
            $ptr=0;
            $contenido = "";
            if($result->num_rows > 0)  { 
                while($row = $result->fetch_assoc()) {
                  $ptr ++; 
                  switch ($row["prioridad"]){    
                  case "P1E":
                    $contenido .= '<tr style="color: red" >';
                    break;
                  case "P1":
                    $contenido .= '<tr style="color: orangered" >';
                    break;
                  case "P2":
                    $contenido .= '<tr style="color: orange" >';
                    break;
                  case "P3":
                    $contenido .= '<tr style="color: green" >';
                    break;
                  default:
                    $contenido .= '<tr>';                     
                  }   
                  $contenido .= "<td>".$ptr."</td>";
                  if($categoria == "kickoff") {
                  $contenido .= "<td>".$row["prioridad_descr"]."</td>";                      
                  } else {
                  $contenido .= "<td>".$row["categoria"]."</td>";
                  }
                  $contenido .= '<td><a target="_blank" href="'.$url.$row["id"].'">'.$row{"numero"}.'</a></td>';
                  $contenido .= "<td>".$row["asunto"]."</td>";
                  $contenido .= "<td>".$row["estado"]."</td>";
                  $contenido .= "<td>".$row["nombre"]." ".$row["apellido"]."</td>";                    
                  $contenido .= "<td>".$row["cliente"]."</td>";
                  $contenido .= "<td>".$row["f_creacion"]."</td>";
                  $contenido .= "<td>".$row["f_modifica"]."</td>";
                  $contenido .= "</tr>";
                }
            } else {
              $contenido = "<tr><td colspan='9'>No se encontraron datos con la categoría= ".$categoria."</td></tr>";
            }
            $conn->close();
            unset($result);
            unset($conn);
        ?>
        <table align="center" width="100%">
          <tr align="center" style="color: white;background-color: #1F1D3E;">
              <td colspan="3" align="left" valign="top" rowspan="1"><img src="../images/logo_icontel_azul.jpg"  height="100" alt=""/></td> 
              <?PHP
              if($categoria == "kickoff"){
                echo '<td colspan="4" align="center" valign="bottom"><h1>Soporte: Casos Abiertos</h1></td>';
              } else {
                ?><td colspan="6" align="center" valign="bottom"><h1>Casos Abiertos de la Categoría:&nbsp; <?php echo $categoria; ?></h1></td> <?PHP  
              }
              ?>
               </tr>
            <tr align="left">
                <th> # </th>
                <?php 
                    if($categoria == "kickoff") echo "<th>Prioridad</th>";
                    else echo "<th>Categoria</th>";
                ?>
                <th>Número</th>
                <th>Asunto</th>
                <th>Estado</th>
                <th>Asignado a</th>                    
                <th>Razón Social</th>
                <th width="9%">Fecha Creación</th>
                <th width="9%">Fecha Modificación</th>
            </tr>
             <?PHP echo $contenido; ?>
        </table>   
