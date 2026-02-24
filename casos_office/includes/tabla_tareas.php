        <?php
            $conn = DbConnect($db_sweet);
            $sql = "CALL Kick_Off_Operaciones_Tareas_Abiertas"; 
            $resultado = $conn->query($sql);
            $ptr=0;
            $contenido = "";
            if($resultado->num_rows > 0)  { 
                while($lin = $resultado->fetch_assoc()) {
                  $ptr ++; 
                  switch ($lin["prioridad"]){    
                  case "1 URGENTE ESCALADO":
                    $contenido .= '<tr style="color: red" >';
                    break;
                  case "2 URGENTE":
                    $contenido .= '<tr style="color: orangered" >';
                    break;
                 case "3 Alta":
                    $contenido .= '<tr style="color: orange" >';
                    break;
                  case "4 Baja":
                    $contenido .= '<tr style="color: green" >';
                    break;
                  default:
                    $contenido .= '<tr>';                     
                  }   
                  $contenido .= "<td>".$ptr."</td>";
                  $contenido .= "<td>".$lin["prioridad"]."</td>";
                  $contenido .= "<td>".$lin["usuario"]."</td>";                    
                  $contenido .= "<td>".$lin["estado"]."</td>";
                  $contenido .= '<td><a target="_blank" href="'.$lin["url"].'">'.$lin{"tarea"}.'</a></td>';
                  switch ($lin["origen"]){ 
                  case "Cases":
                        $contenido .= "<td> Nº de caso y url  </td>";
                        $contenido .= "<td>CASO</td>";
                        break;
                  case "Opportunities":
                        $contenido .= "<td> Nº de Oportunidad y url  </td>";
                        $contenido .= "<td>OPORTUNIDAD</td>";
                        break;
                  case "Accounts":
                        $contenido .= "<td> Nº de cuenta y url  </td>";
                        $contenido .= "<td>CUENTA</td>";
                        break;
                  default:
                        $contenido .= "<td> </td>";
                        $contenido .= "<td> </td>";
                  }   
 
                    
                  $contenido .= "<td>".$lin["f_creacion"]."</td>";
                  $contenido .= "<td>".$lin["f_modifica"]."</td>";
                  $contenido .= "</tr>";
                }
            } else {
              $contenido = "<tr><td colspan='9'>No se encontraron Tareas pendientes.</td></tr>";
            }
            $conn->close(); 
            unset($resultado);
            unset($conn);
        ?>
        <table align="center" width="100%">
          <tr align="center" style="color: white;background-color: #1F1D3E;">
              <td colspan="9" align="center" valign="bottom"><h1>Soporte: Tareas Abiertas</h1></td>
            </tr>
            <tr align="left">
                <th> # </th>
                <th width="9%">Prioridad</th>
                <th width="9%">Asignado a</th>                    
                <th width="9%">Estado</th>
                <th>Asunto</th>
                <th width="9%">Origen Nº</th>
                <th width="9%">Origen Tipo</th>
                <!--th>Estado</th-->
                <!--th>Razón Social</th-->
                <th width="9%">Fecha Creación</th>
                <th width="9%">Fecha Modificación</th>
            </tr>
             <?PHP echo $contenido; ?>
        </table>   
