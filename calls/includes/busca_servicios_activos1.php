<?PHP // include va a buscar servicios activos a Sweet por id de la cuenta y los despliega por linea
    $conn = DbConnect("tnasolut_sweet");
     $sql = "CALL searchactiveservicesbyaccountid('{$account_id}')";  
    $result = $conn->query($sql);
    $prod_id = Array();
    if ($result->num_rows > 0) {    
         $ptr = 1;  
        $prod_id[$ptr] = $row["produ_id"];
        while($row = $result->fetch_assoc()) {
            $url = "https://sweet.icontel.cl/index.php?entryPoint=NuevoCaso&produ_id=".urldecode($row['produ_id'])."&account_id=".urldecode($account_id);
			$url_coti = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAOS_Quotes%26action%3DDetailView%26record%3D" . $row['coti_id'];		
			$url_opor = "https://sweet.icontel.cl/index.php?module=Opportunities&action=DetailView&record=" . $row['opor_id'];
            $tr = '<tr valign="top"';			
				if($row["coti_estado"] == "EN TRASLADO" OR $row["coti_estado"] == "POSIBLE TRASLADO") $tr .= 'style="color: orange; background-color: white"'; 
				if($row["coti_estado"] == "EN INSTALACION" ) $tr .= 'style="color: blue; background-color: white"'; 
				if($row["coti_estado"] == "DE BAJA") 		 $tr .= 'style="color: lightgray; background-color: white"'; 
				if($row["coti_estado"] == "GENERAR BAJA") 	 $tr .= 'style="color: red; background-color: white"'; 
			$tr .= '>';
            echo $tr;    
            ?>    
                <td><a href="<?PHP echo $url; ?>" target="_blank"><img src="../../images/ticket.png" height="25" alt=""/></a></td>
                <td align="center"><?PHP echo number_format($row["produ_cantidad"]); ?></td>
                <td><?PHP echo $row["produ_nombre"]; ?></td>
                <td align="right"><?PHP echo number_format($row["produ_valor"], 2, ',', ' '); ?></td>
                <td ><a href="<?PHP echo $url_coti; ?>" target="_blank"><?PHP echo $row["coti_num"]; ?></td>
                <td ><a href="<?PHP echo $url_opor; ?>" target="_blank"><?PHP echo $row["opor_num"]; ?></td>
                <td colspan=""><?PHP echo $row["coti_estado"]; ?></td>
                <td colspan=""><?PHP echo $row["produ_proveedor"]; ?></td>
                <td colspan=""><?PHP echo $row["codigo_servicio"]; ?></td>                            
                <td colspan=""><?PHP echo $row["dir_instalacion"]; ?></td>
                <td colspan=""><?PHP echo $row["fecha_contrato"]; ?></td>
                <td colspan=""><?PHP echo $row["duracion_contrato"]; ?></td>
                <td colspan=""><?PHP echo $row["meses"]; ?></td>               
                <td colspan=""><?PHP echo $row["nv_bsale"]; ?></td>               
            </tr>
            <?PHP
        }
    } 
    $conn->close(); 
?>
 