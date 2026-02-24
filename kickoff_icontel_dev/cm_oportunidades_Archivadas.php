        <?php
            $conn = DbConnect($db_sweet);
            $sql = "CALL Oportunidades_en_Pausa()";       
            $resultado = $conn->query($sql);
            $ptr=0;
            $contenido = "";
			$muestra = $resultado->num_rows;
            if($resultado->num_rows > 0)  { 				
                while($lin = $resultado->fetch_assoc()) {
				$style = ' style="color: orange;" ';					
                  $ptr ++; 
                  switch ($lin["estado"]){    
                  case "1 Escalado Urgente":
                    $contenido .= '<tr style="color: red" >';
                    break;
                  case "2 Aceptadado, listo para Instalar":
                    $contenido .= '<tr style="color: orangered" >';
                    break;
                  case "3 Generar NV":
                    $contenido .= '<tr style="color: orange" >';
                    break;
                  case "Cotizar":
                    $contenido .= '<tr style="color: green" >';
                    break;
                  default:
                    $contenido .= '<tr>';                     
                  }   
                  $contenido .= "<td>".$ptr."</td>";
                  $contenido .= "<td>".$lin["numero"]."</td>";
                  $contenido .= '<td colspan="2"><a target="_blank" href="'.$lin["url_opor"].'">'.$lin["nombre"].'</a></td>';
                  $contenido .= "<td>".$lin["estado"]."</td>";
                  $contenido .= "<td>".$lin["asignado"]."</td>";
                  $contenido .= "<td>".$lin["f_creacion"]."</td>";
                  $contenido .= "<td>".$lin["f_modifica"]."</td>";
                  $contenido .= "<td align='right'>".$lin["dias"]."&nbsp;&nbsp;</td>";
                  $contenido .= "</tr>";
                }
            } else {
			  $style = "";
              $contenido = "<tr><td colspan='9'>No se encontraron Oportunidades Archivadas.</td></tr>";
            }
            $conn->close(); 
            unset($resultado);
            unset($conn);
			$td =  '<td colspan="11" align="left" valign="bottom" class="titulo" >&nbsp;&nbsp;💼 Oportunidades archivadas</td>
			<td colspan="1" align="right" valign="bottom" style="font-size: 20px; color: white; background-color: #512554;"><a href="'.$url_nueva_oportunidad.'" target="new">+</a>&nbsp;&nbsp;&nbsp;</td>';
				
				
        ?>
        <div class="tabla-scroll">
        <table id="oportunidades_archivadas" align="center" width="100%">
          <tr align="center" style="color: white;background-color: #512554;">
              <?PHP echo $td; ?>
            </tr>
            <tr align="left" class="subtit">
                <th class="subtitulo" with="1%"> # </th>
                <th class="subtitulo" width="5%">Número</th>
                <th class="subtitulo" width="30%" colspan="2">Asunto a</th>                    
                <th class="subtitulo" >Estado</th>
                <th class="subtitulo" width="11%">Asignado a</th>
                <th class="subtitulo" width="11%">Fecha Creación</th>
                <th class="subtitulo" width="11%">Fecha Modificación</th>               
				<th class="subtitulo" width="5"  align="right">Días&nbsp;&nbsp;</th>
            </tr>
             <?PHP echo $contenido; ?>
        </table>
        </div>   
		<div><button style="color: #512554; border: none" onclick="capa('oportunidades_archivadas')">Oportunidades en Pausa o Archivadas [Muestra/Oculta <?PHP echo $ptr; ?>]</button></div>
		<?PHP if(!$muestra) echo "<script>capa('oportunidades_archivadas');</script>"; ?>