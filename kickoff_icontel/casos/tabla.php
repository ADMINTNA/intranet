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
		if($grupos[$i]['id'] == $sg_id) { $sg_name=$grupos[$i]['name']; }
		$i++;
	}
?>
<!doctype html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <?PHP include_once("/meta_data/meta_data.html"); ?>
	<title>Casos Abiertos iContel</title>
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
            a.download = 'Casos_' + getRandomNumbers() + '.xls'
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
                    <th>#</span></th>
                    <th width="5%"><span onclick='sortTable("prioridad_descr");'>Prioridad</span></th>
                    <th><span onclick='sortTable("id");'>N&uacute;mero</span></th>
                    <th><span onclick='sortTable("asunto");'>Asunto</span></th>
                    <th><span onclick='sortTable("estado");'>Estado</span></th>
                    <th><span onclick='sortTable("categoria");'>Categor&iacute;a</span></th>
                    <th><span onclick='sortTable("usuario");'>Asignado a</span></th>
                    <th><span onclick='sortTable("cliente");'>Cliente</span></th>
                    <th width="9%"><span onclick='sortTable("f_creacion");'>Fecha Creaci&oacute;n</span></th>
                    <th width="9%"><span onclick='sortTable("f_modifica");'>Fecha Modificaci&oacute;n<br><input type="button" onClick="exportToExcel('empTable')" value="Export to Excel" /></span></th>
                    <th  width="5"  align="right"><span onclick='sortTable("dias");'>D&iacute;as</span></th>
                </tr>
                <?php 
                    // require_once __DIR__ . '/../session_core.php';
                    // $query =  $_SESSION["query"]." ORDER BY producto DESC";
            		//$query = "CALL Kick_Off_Operaciones_Abiertos('".$sg_id."')"; 
					$ventas = "/ Ventas / --Ghislaine / --MAO /";
					$sac    = "/ Servicio al Cliente / --Maria JosÃ© / --DAM /";
					$query = 'SELECT c.id				as id,
						   cc.categoria_c	as categoria,
						   c.case_number 	as numero,
						   c.name			as asunto,
						   c.state			as estado, 
						   c.priority		as prioridad,
						   CASE
								WHEN c.priority = "P1"	THEN "1 URGENTE ESCALADO"
								WHEN c.priority = "P1E"	THEN "2 URGENTE"
								WHEN c.priority = "P2"	THEN "3 Alta"
								WHEN c.priority = "P3"	THEN "4 Baja"
								WHEN c.priority = "P4"	THEN "5 Seguimiento"
								WHEN c.priority = "P5"	THEN "6 Congelado"
								ELSE "PRIORIDAD NO ASIGNADA"
						   END AS prioridad_descr,         
						   a.name 			as cliente,
						   u.id				as id_usuario,
						   u.first_name 	as nombre,
						   u.last_name  	as apellido,
						   CONVERT_TZ( c.date_entered,  \'+00:00\', \'-04:00\' ) as f_creacion,
						   CONVERT_TZ( c.date_modified, \'+00:00\', \'-04:00\' ) as f_modifica,
						   DATEDIFF (NOW(), c.date_entered) 	 			 as dias, 
						   if(ISNULL( u.first_name), u.last_name,concat( u.first_name," ",u.last_name)) as usuario,
						   sg.name 			as departamento
					FROM `cases` as c
					JOIN cases_cstm as cc ON cc.id_c =  c.id
					JOIN accounts   as  a ON  a.id   = c.account_id
					JOIN users      as  u ON  u.id   = c.assigned_user_id
					JOIN securitygroups_users	as sgu ON  sgu.user_id = u.id
					JOIN securitygroups 		as sg  ON  sg.id = sgu.securitygroup_id 
					WHERE c.state != "Closed"
					&& sgu.securitygroup_id = "'.$sg_id.'"
 					&& !c.deleted
					&& !a.deleted  ';
					if(!strpos($sac, $sg_name)){ // si no es SAC
						$query .= '&& c.priority != "P4"
								   && c.priority != "P5"';
					}
					$_SESSION["query"] = $query;
					$query = $query . 'ORDER BY  prioridad_descr ASC, usuario ASC, dias DESC';					
                    $conn = DbConnect("tnasolut_sweet");
                    $result = mysqli_query($conn,$query);
                    $ptr = 0;
             		$url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26offset%3D1%26stamp%3D1644666990053569200%26return_module%3DCases%26action%3DDetailView%26record%3D";      
                    while($row = mysqli_fetch_array($result)){
	//print_r($row);					
                        $ptr ++;    
						switch ($row["prioridad"]){    
						case "P1E":
							$tr = '<tr style="color: red" >';
							break;
						case "P1":
							$tr = '<tr style="color: orangered" >';
							break;
						case "P2":
							$tr = '<tr style="color: orange" >';
							break;
						case "P3":
							$tr = '<tr style="color: green" >';
							break;
						default:
							$tr = '<tr>';                     
						}   
                        ?>
							<?PHP echo $tr; ?>
								<td><?php echo $ptr; ?></td>
								<td><?PHP echo $row["prioridad_descr"]; ?></td>
								<?PHP echo '<td><a target="_blank" href="'.$url.$row["id"].'">'.$row["numero"].'</a></td>'; ?>
								<td><?PHP echo $row["asunto"]; ?></td>
								<td><?PHP echo $row["estado"]; ?></td>
								<td><?PHP echo $row["categoria"]; ?></td>
								<td><?PHP echo $row["usuario"]; ?></td>
								<td><?PHP echo $row["cliente"]; ?></td>
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