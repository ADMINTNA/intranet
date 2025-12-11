<?php 
// ==========================================================
// /intranet/tareas/sort/index.php
// Muestra el resultado de la busqueda de tareas
// Autor: Mauricio Araneda
// Fecha: 2025-11-18
// Codificación: UTF-8 sin BOM
// ==========================================================

// ⚠️ IMPORTANTE: NADA DE HTML ANTES DE ESTO
session_name('icontel_intranet_sess');
session_start();

header('Content-Type: text/html; charset=utf-8');
include "config.php";
    // activo mostrar errores
     // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    session_start();

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
	include_once("../includes/security_groups.php"); 
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
    <?PHP //&include_once("/meta_data/meta_data.html"); ?>
	<title>Tareas iContel</title>
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
            a.download = 'Tareas_' + getRandomNumbers() + '.xls'
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
                    <th width="6%"><span onclick='sortTable("prioridad");'>Prioridad</span></th>
                    <th><span onclick='sortTable("usuario");'>Asignado</span></th>
                    <th><span onclick='sortTable("estado");'>Estado</span></th>
                    <th><span onclick='sortTable("categoria");'>Categoria</span></th>
                    <th><span onclick='sortTable("tarea");'>Asunto</span></th>
                    <th><span onclick='sortTable("usuario");'>Origen Tipo a</span></th>
                    <th><span onclick='sortTable("numero");'>Origen Nª</span></th>
                    <th width="9%"><span onclick='sortTable("f_creacion");'>Fecha Creación</span></th>
                    <th width="9%"><span onclick='sortTable("f_modifica");'>Fecha Modifica<br><input type="button" onClick="exportToExcel('empTable')" value="Export to Excel" /></span></th>
                    <th  width="5"  align="right"><span onclick='sortTable("dias");'>Días</span></th>
                </tr>
                <?php 
 				    $query =  $_SESSION["query_tareas"]. ' ORDER BY  prioridad ASC, usuario ASC, dias DESC';
					$ventas = "/ Ventas / --Ghislaine / --MAO /";
					$sac    = "/ Servicio al Cliente / --Maria José / --DAM /";				
                    $conn = DbConnect("tnasolut_sweet");
                    $result = mysqli_query($conn,$query);
                    $ptr = 0;
             		$url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26offset%3D1%26stamp%3D1644666990053569200%26return_module%3DCases%26action%3DDetailView%26record%3D";  
					$url_caso = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26action%3DDetailView%26record%3D";      
					$url_opor = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DOpportunities%26action%3DDetailView%26record%3D";  
					$url_cuenta = "https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAccounts%26action%3DDetailView%26record%3D";
		
                    while($row = mysqli_fetch_array($result)){					
                        $ptr ++;    
						switch ($row["prioridad"]){    
						case "1 URGENTE ESCALADO":
							$tr = '<tr style="color: red" >';
							break;
						case "2 URGENTE":
							$tr = '<tr style="color: orangered" >';
							break;
						case "3 Alta":
							$tr .= '<tr style="color: orange" >';
							break;
						case "4 Baja":
							$tr = '<tr style="color: green" >';
							break;
						default:
							$tr .= '<tr>';                     
						}   
                        ?>
						<?PHP echo $tr; ?>
							<td><?php echo $ptr; ?></td>
							<td><?PHP echo $row["prioridad"]; ?></td>
							<td><?PHP echo $row["usuario"]; ?></td>
							<td><?PHP echo $row["estado"]; ?></td>
							<td><?PHP echo $row["categoria"]; ?></td>
							<?PHP echo '<td><a target="_blank" href="'.$row["url"].'">'.$row{"tarea"}.'</a></td>'; 
							switch ($row["origen"]){ 
							case "Cases":
								$url ="";
								$td  = "<td>CASO</td>";
								$td .= '<td><a target="_blank" href="'.$url_caso.$row["origen_id"].'">'.$row{"numero"}.'</a></td>';                        
								break;
							case "Opportunities":
								$td  = "<td>OPORTUNIDAD</td>";
								$td .= '<td><a target="_blank" href="'.$url_opor.$row["origen_id"].'">'.$row{"numero"}.'</a></td>';                        
							   break;
							case "Accounts":
								$td  = "<td>CUENTA</td>";
								$td .= '<td><a target="_blank" href="'.$url_cuenta.$row["origen_id"].'">'.$row{"numero"}.'</a></td>';                        
								break;
							default:
								$td  = "<td> </td>";
								$td .= "<td> </td>";
							}
							echo $td;  
							?>
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