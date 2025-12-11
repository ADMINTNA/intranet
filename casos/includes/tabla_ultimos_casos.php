        <?php
            $categoria="Ultimos Casos";
            $url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26offset%3D1%26stamp%3D1644666990053569200%26return_module%3DCases%26action%3DDetailView%26record%3D";      
            $conn = DbConnect($db_sweet);
            $sql = "CALL Kick_Off_Operaciones_Cerrados(3)"; 
            $resultado = $conn->query($sql);
            $ptr=0;
            $contenido = "";
            if($resultado->num_rows > 0)  { 
                while($lin = $resultado->fetch_assoc()) {
                  $ptr ++; 
                  switch ($lin["prioridad"]){    
                  case "P1E":
                    $contenido .= '<tr style="color: red" >';
                    break;
                  case "P1":
                    $contenido .= '<tr style="color: orangered" >';
                    break;
                  case "P2":
                    $contenido .= '<tr style="color: orange" >';
                    break;
                  case "P33":
                    $contenido .= '<tr style="color: green" >';
                    break;
                  default:
                    $contenido .= '<tr>';                     
                  }   
                  $contenido .= "<td>".$ptr."</td>";
                  $contenido .= "<td>".$lin["categoria"]."</td>";
                  $contenido .= '<td><a target="_blank" href="'.$url.$lin["id"].'">'.$lin{"numero"}.'</a></td>';
                  $contenido .= "<td>".$lin["asunto"]."</td>";
                  $contenido .= "<td>".$lin["estado"]."</td>";
                  $contenido .= "<td>".$lin["nombre"]." ".$lin["apellido"]."</td>";                    
                  $contenido .= "<td>".$lin["cliente"]."</td>";
                  $contenido .= "<td>".$lin["f_creacion"]."</td>";
                  $contenido .= "<td>".$lin["f_modifica"]."</td>";
                  $contenido .= "</tr>";
                }
            } else {
              $contenido = "<tr><td colspan='9'>No se encontraron datos con la categoría= ".$categoria."</td></tr>";
            }
            $conn->close(); 
            unset($resultado);
            unset($conn);
        ?>
        <table align="center" width="100%">
          <tr align="center" style="color: white;background-color: #1F1D3E;">
              <td colspan="9" align="center" valign="bottom"><h1>Soporte: Casos Cerrados los 3 últimos Días</h1></td>
            </tr>
            <tr align="left">
                <th> # </th>
                <th>Categoría</th>
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
