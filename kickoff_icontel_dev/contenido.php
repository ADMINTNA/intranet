<?php
require_once __DIR__ . '/session_core.php';

// Oculta solo los errores tipo Notice
error_reporting(E_ALL & ~E_NOTICE);

// Opcional: desactiva la visualizaciÃ³n de errores en pantalla
ini_set('display_errors', '0');

$_SESSION['donde'] ="KickOff";
 
// print_r($_SESSION);
	//	error_reporting(E_ALL);
	//	ini_set('display_errors', '1');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
	<?PHP  	
		 include_once("config.php"); 
		 include_once("security_groups.php"); 	
            //if(isset($_SESSION['sg_id'])) echo "Session = {$_SESSION['sg_id']}";
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
            $i = 1;
            $cuantos = count($grupos);            
            while ($i <= $cuantos) {
                if($grupos[$i]['id'] == $sg_id) {
                    $sg_name=$grupos[$i]['name'];
                }
                $i++;
            }		
		echo "<script>
				var sg_id = '$sg_id';
				var sg_name = '$sg_name';
			  </script>";

		include_once("/meta_data/meta_data.html"); 
	?>   
    <title>Cuadro de Mando</title>
    <style type="text/css">
       table {
               border: none;
               font-size: 12px;
               border-collapse: collapse;
           }   
          th, td {
              padding: 2px;
              font-size: 14px;
         }
         th {
            background-color: midnightblue; 
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
		/* unvisited link */
		a:link {
		  color: grey;
		}
		/* visited link */
		a:visited {
		  color: gray;
		}
		/* mouse over link */
		a:hover {
		  color: lightskyblue;
		  font-size: larger;
		  font-weight: bold;
		}
		/* selected link */
		a:active {
		  color: blue;
		}	
		.link { color: #FF0000; } /* CSS link color (red) */
		.link:hover { color: #00FF00; } /* CSS link hover (green) */		
		iframe {
			margin:0;
			padding:0;
			height:100%;
			margin-left: 0px;
			margin-top: 0px;
			margin-right: 0px;
			margin-bottom: 0px;
			display:block;
			width:100%;
			border:none; 
		}
		label {
		  display: block;
		  width: 130px;
		}
		button {cursor: pointer;}
	</style>
		
   <!--meta http-equiv="refresh" content="300;url=https://intranet.icontel.cl/kickoff/"-->     
    <script language="JavaScript">
        var ptr=0   
       function autoSubmit() {
            var formObject = document.forms['form_select'];
            formObject.submit();
        }
    </script>  
    </head>
    <body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" onLoad="carga_capas()">
	<?PHP		
		$ventas = "/ Ventas / -..Ghislaine / -..MAO / -..Natalia / -..Monica / -..Raquel";
		$operaciones = "/ -..Bryan / Operaciones / -..Alex /";
		$sac    = "/ Servicio al Cliente / -..Maria JosÃ© / -..DAM /";
		$admin  = "/ -..MAM";
		$proveedores  = "/ -..MAO";
 	//	include_once("cm_header.php");
        include_once("cm_casos_abiertos.php"); 
	    if(strpos($proveedores, $sg_name)) include_once("cm_casos_abiertos_sujeto_a_cobro.php");	
		if(strpos($ventas, $sg_name) or strpos($admin, $sg_name) )  include_once("cm_cobranza_comercial.php");	
		if(strpos($ventas, $sg_name)) include_once("cm_clientes_potenciales.php");
        include_once("cm_tareas_pendientes.php");
        if($sg_name != "Soporte tecnico") include_once("cm_oportunidades_abiertas.php"); 
		if(strpos($ventas.$operaciones, $sg_name) ) include_once("cm_oportunidades_en_Demo.php");		
		if(strpos($ventas, $sg_name) && $sg_name != "-..MAO" ) include_once("cm_oportunidades_Archivadas.php");		
		if(strpos($sac, $sg_name) ) include_once("cm_cobranza_comercial.php");				
		if(strpos($sac, $sg_name)){
 			include_once("cm_casos_abiertos_seguimiento.php");			
			include_once("cm_casos_abiertos_congelados.php");			
		}
		if(strpos($admin, $sg_name)){include_once("cm_ordenes_de_compra_pendientes.php"); }
		
    ?>
	<br><br>
    </body>   
</html>

  