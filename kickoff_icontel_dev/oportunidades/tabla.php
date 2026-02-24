<?php include "config.php";
    // activo mostrar errores
     // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    require_once __DIR__ . '/../session_core.php';
	$sg_id = $_GET['sg_id'];
	if (isset($_POST['sg']))   { 
		$sg_id   = $_POST['sg'];
		$_SESSION['sg_id'] = $sg_id;
	}
	if (isset( $_GET['sg']))   { 
		$sg_id   =  $_GET['sg']; 
		$_SESSION['sg_id'] = $sg_id;
	}
	if(!isset($sg_id)){
		if(isset($_SESSION['sg_id'])) {
			$sg_id = $_SESSION['sg_id'];
		} else {
			$sg_id   = "a03a40e8-bda8-0f1b-b447-58dcfb6f5c19"; // soporte
			$sg_name = "Soporte Soporte tecnico";
			$_SESSION['sg_id'] = $sg_id;                   
		}
	} 
	include_once("security_groups.php"); 
	$i = 1;
	$cuantos = count($grupos);            
	while ($i <= $cuantos) {
		if($grupos[$i]['id'] == $sg_id) {
			$sg_name=$grupos[$i]['name'];
		}
		$i++;
	}
?>
<!doctype html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <?PHP include_once("/meta_data/meta_data.html"); ?>
	<title>Oportunidades iContel</title>
    <link href='style.css' rel='stylesheet' type='text/css'>
    <script src='jquery-3.3.1.min.js' type='text/javascript'></script>
    <script src='script.js' type='text/javascript'></script>
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
			padding:0px;
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
    <script type="text/javascript">
        function exportToExcel(tableId){
            let tableData = document.getElementById(tableId).outerHTML;
            tableData = tableData.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
            tableData = tableData.replace(/<input[^>]*>|<\/input>/gi, ""); //remove input params

            let a = document.createElement('a');
            a.href = `data:application/vnd.ms-excel, ${encodeURIComponent(tableData)}`
            a.download = 'Oportunidades_' + getRandomNumbers() + '.xls'
            a.click()
        }
        function getRandomNumbers() {
            let dateObj = new Date()
            let dateTime = `${dateObj.getHours()}${dateObj.getMinutes()}${dateObj.getSeconds()}`

            return `${dateTime}${Math.floor((Math.random().toFixed(2)*100))}`
        }        
    </script>     	 
    </head>
    <body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" >
        <div class='container'>
            <input type='hidden' id='sort' value='asc'>
            <table width='100%' id='empTable' border='1' cellpadding='10'>
                <tr>
                    <th width="2%">#</span></th>
                    <th width="3%"><span onclick='sortTable("numero");'>NÃºmero</span></th>
                    <th width="19%"><span onclick='sortTable("cuenta");'>Cliente</span></th>
                    <th width="25%"><span onclick='sortTable("nombre");'>Asunto</span></th>
                    <th><span onclick='sortTable("estado");'>Estado</span></th>
                    <th><span onclick='sortTable("asignado");'>Asignado</span></th>
                    <th width="9%"><span onclick='sortTable("f_creacion");'>Fecha CreaciÃ³n</span></th>
                    <th width="9%"><span onclick='sortTable("f_modifica");'>Fecha Modifica<br><input type="button" onClick="exportToExcel('empTable')" value="Export to Excel" /></span></th>						
                    <th  width="5"  align="right"><span onclick='sortTable("dias");'>DÃ­as</span></th>
                </tr>
                <?php 
					$query = 'select 
						op.id					as id,
						oc.numero_oportunidad_c as numero,
						op.name 				as nombre,
						case
							when op.sales_stage = "esperafact"  		then "Esperando Factibilidad"
							when op.sales_stage = "Facturacion" 		then "3 Generar NV"
							when op.sales_stage = "Firmar_Contrato"		then "Firmar Contrato o Anexo"
							when op.sales_stage = "Levantamiento" 		then "Levantamiento"
							when op.sales_stage = "pendiente_enlace" 	then "Pendiente Enlace Proveedor"
							when op.sales_stage = "Proposal/Price Quote" then "Seguimiento"
							when op.sales_stage = "Prospecting" 		then "Prospecto"
							when op.sales_stage = "Proyecto" 			then "Instalacion"
							when op.sales_stage = "proyectodemo" 		then "Proyecto Demo"
							when op.sales_stage = "Recepcion" 			then "Solicitar Recepcion Conforme"
							when op.sales_stage = "Value Proposition" 	then "Cotizar"
							when op.sales_stage = "Waiting" 			then "En Pausa En Espera"
							when op.sales_stage = "AceptadoCliente" 	then "2 Aceptadado, listo para Instalar"
							when op.sales_stage = "Escalado" 			then "1 Escalado Urgente"
							when op.sales_stage = "facturar" 			then "Lista para Facturar"
							when op.sales_stage = "Pre_Instalacion_cliente" then "Pre InstalaciÃ³n"
							when op.sales_stage = "Pre_Instalacion" 	then "Pre InstalaciÃ³n"
						end 					as estado,
						ac.name 				as cuenta,
						if(us.first_name IS NULL,us.last_name,CONCAT(us.first_name," ",us.last_name)) as asignado,
						CONVERT_TZ( op.date_entered,  \'+00:00\', \'-04:00\' ) as f_creacion,
						CONVERT_TZ( op.date_modified, \'+00:00\', \'-04:00\' ) as f_modifica,
						DATEDIFF (NOW(), op.date_entered) 	  as dias, 
						sgu.securitygroup_id 	as security_id,
						sg.name 				as security_group
						from opportunities 			as 	op 
						join accounts_opportunities as	ao 	on ao.opportunity_id = op.id 
						JOIN accounts 				AS	ac	ON ac.id = ao.account_id 
						join opportunities_cstm 	as	oc  on oc.id_c = op.id 
						join users 					as	us  on us.id = op.assigned_user_id 
						JOIN securitygroups_users	as sgu ON sgu.user_id = us.id
						JOIN securitygroups 		as sg  ON sg.id = sgu.securitygroup_id
						where 1
						&& sgu.securitygroup_id = "'.$sg_id.'"
						&& op.sales_stage  != "Facturado"
						&& op.sales_stage  != "Closed Lost"
						&& op.sales_stage  != "Dado_de_Baja"
						&& op.sales_stage  != "Duplicada_reemplazada"
						&& op.sales_stage  != "Needs Analysis"
						&& op.sales_stage  != "Archivado_Ventas"
						&& op.sales_stage  != "Waiting"
						&& !op.deleted 
						-- && !us.deleted
						&& !ac.deleted 
						&& !ao.deleted 
						&& !sg.deleted ';
					$_SESSION["query"] = $query;
					$query .= ' order by estado ASC, dias DESC';	
                    $conn = DbConnect("tnasolut_sweet");
                    $result = mysqli_query($conn,$query);
                    $ptr = 0;
           			$url = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DOpportunities%26action%3DDetailView%26record%3D"; 
                    while($row = mysqli_fetch_array($result)){
	//print_r($row);					
                        $ptr ++;    
						  switch ($row["estado"]){    
						  case "1 Escalado Urgente":
							$tr = '<tr style="color: red" >';
							break;
						  case "2 Aceptadado, listo para Instalar":
							$tr = '<tr style="color: orangered" >';
							break;
						  case "3 Generar NV":
							$tr = '<tr style="color: orange" >';
							break;
						  case "Cotizar":
							$tr = '<tr style="color: green" >';
							break;
						  default:
							$tr .= '<tr>';                     
						  }   
                        ?>
						<?PHP echo $tr; ?>
							<td><?php echo $ptr; ?></td>
							<td><?PHP echo $row["numero"]; ?></td>
							<td><?PHP echo $row["cuenta"]; ?></td>
                 			<?PHP echo '<td><a target="_blank" href="'.$url.$row["id"].'">'.$row["nombre"].'</a></td>'; ?>
							<td><?PHP echo $row["estado"]; ?></td>
							<td><?PHP echo $row["asignado"]; ?></td>
							<td><?PHP echo $row["f_creacion"]; ?></td>
							<td><?PHP echo $row["f_modifica"]; ?></td>
							<td align='right'><?PHP echo $row["dias"]; ?>&nbsp;&nbsp;</td>
						</tr>
					<?php 
					} 
				?>
            </table><br><br>
        </div>
    </body>
</html>