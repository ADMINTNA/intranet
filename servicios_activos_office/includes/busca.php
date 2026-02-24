<!doctype html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	 <?PHP include_once("/meta_data/meta_data.html"); ?>   
	 <title>Servicios Activos iContel</title>
        <link href='css/style.css' rel='stylesheet' type='text/css'>
        <script src='jquery-3.3.1.min.js' type='text/javascript'></script>
        <script src='script.js' type='text/javascript'></script>
        <script src='../../js/excel_export.js' type='text/javascript'></script>
    </head>
    <body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" >
		<div class='container'>
			<?php 
				$categorias = busca_categorias();
				$sql = "CALL searchbyactiveservicebyfamily()";       
				$conn = DbConnect("tnaoffice_suitecrm");
				$result = $conn->query($sql);
				if ($result->num_rows > 0)   {          
					$cliente = Array();					
					$costo 	 = Array();
					$ptr = 0;
					$actual = " "; ?>
				<table align="center">
					<tr>
						<td>
					<table width="100%" align="center" border="0" id="cabecera" name="cabecera">
						<tr align="center" style="color: white;background-color: #1F1D3E;">
						  <th colspan = "2" width="200" valign="top" align="left"><img src="./images/logo_icontel_azul.jpg"  height="60" alt=""/></th>
						  <th colspan="7">
							  <table width="100%" height="100%" border="0">
								  <tr>
									  <th align="center" style="font-size: 20px;">Servicios Activos por Cliente<br><input type="button" onClick="exportToExcel('tblData','Servicios_Familia_')" value="Export to Excel" /></th>
									  <th align="right"><a href="javascript:history.back()"><img src="../images/volver_azul.png"  style="background-color:blue;" width="30" height="30" alt="Volver"/> </a></th>

								  </tr>
							  </table>
						  </th>
						</tr>
					</table>
					<table width="100%" align="center" border="0" id="tblData" name="tblData">
						<tr> 
							<th width="1%">#</th>
							<th align ="left">Cliente</th>
							<th align ="left">Asignado a</th>
							<th align ="left">RUT</th>
							<th align ="left">Direcci&oacute;n</th>
							<th align ="left">Ciudad</th>
							<th align ="left">Tel&eacute;fono</th>
							<?PHP foreach($categorias as $value){ ?>
								<th colspan="2" width="80">
									<table width="100%" border="0">
									  <tbody>
										<tr>
										  <th colspan="2" align="center" ><?PHP echo $value; ?></th>
										</tr>
										<tr>
										  <th align="right">VTA</th>
										  <th align="right">CTO</th>
										</tr>
									  </tbody>
									</table>									
							    </th>							
							<?php } ?> 
							<th></th>
							<th></th>
						</tr>
						<?PHP
						$x = 100;
						while($row = $result->fetch_assoc()) { // ----------- Comienzo de loop recorrido
							if($actual !=  $row['razon_social']){
								$ptr ++;
								$cliente[$ptr]['cliente'] = $row['razon_social'];								
								$costo[$ptr]['cliente'] = $row['razon_social'];
								for ($i = $x; $i < count($categorias); $i++) { echo "<td> </td><td> </td>"; }
								$actual = $row['razon_social'];
								echo "</tr>
									  <tr><td>".$ptr."</td>
										  <td>".$cliente[$ptr]['cliente']."</td>
										  <td>".$row['asignado']."</td>
										  <td>".$row['rut']."</td>
										  <td>".$row['direccion']."</td>
										  <td>".$row['ciudad']."</td>
										  <td>".$row['fono']."</td>";
								$x=0;
							}
							$cliente[$ptr][$row['produ_categoria']] = $row['produ_total'];
							$costo[$ptr][$row['produ_categoria']] = $row['produ_costo'];
							for ($i = $x; $i <= count($categorias); $i++) {
								if($categorias[$i]==$row['produ_categoria']) {
									echo "<td align='right'>". number_format($cliente[$ptr][$row['produ_categoria']], 2, ',', '.')."</td>";
									echo "<td align='right' style='color: red'>". number_format($costo[$ptr][$row['produ_categoria']], 2, ',', '.')."</td>";
									$cateorias[$i]['total'] += $cliente[$ptr][$row['produ_categoria']];
									$costos[$i]['total']    += $costo[$ptr][$row['produ_categoria']];
									$totalfinal += $cliente[$ptr][$row['produ_categoria']];
									$totalcosto += $costo[$ptr][$row['produ_categoria']];
									break;
								} else { echo "<td> </td><td></td>"; }
							}  
							$x = $i+1;
						}                                       // ----------- fin de loop recorrido
						for ($i = $x; $i < count($categorias); $i++) { echo "<td> </td><td> </td>"; }
						for ($i = 0; $i < count($categorias); $i++) {
							$total += $cateorias[$i]['total']; 
							$totalc += $costos[$i]['total']; 
						}
						echo "</tr>
							  <tr >
							  	<th></th>
								<th align ='right'> VTA ".number_format($total, 2, ',', '.')."<br>CTO ".number_format($totalc, 2, ',', '.')."
								</th>
								<th><th>
								<th></th>
								<th></th>";    
						for ($i = 0; $i < count($categorias); $i++) { 
							echo "<th align='right'>".number_format($cateorias[$i]['total'], 2, ',', '.')."</th>"; 
							echo "<th align='right'>".number_format($costos[$i]['total'], 2, ',', '.')."</th>"; 
						}
					echo "<th></th><th></th></tr></table>" ;
					echo"	</td>
					</tr>
				</table>";	
				} 
				$conn->close(); 
			?>
			<BR><BR>
		</div>
	</body>                    
</html>

        
        
