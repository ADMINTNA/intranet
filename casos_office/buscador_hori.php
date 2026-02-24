<?PHP include_once("./includes/functions.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
     <?php include_once("../meta_data/meta_data.html"); ?>
    <title>Buscador Casos iContel</title>
    <script type="text/javascript">
    function estemes() {
        document.getElementById('fechadesde').value = "<?php echo $estemes_desde; ?>"; 
        document.getElementById('fechahasta').value = "<?php echo $estemes_hasta; ?>"; 
    }
    function mesanterior() {
        document.getElementById('fechadesde').value = "<?php echo $mesanterior_desde; ?>"; 
        document.getElementById('fechahasta').value = "<?php echo $mesanterior_hasta; ?>"; 
    }

    </script>     
    <style type="text/css">
        .table_alarmas{
               border: none;
               color: #1F1D3E;
               color: white;
               font-size: 15px;
               border-collapse: collapse;
               background-color: #19173C;
               border-collapse: collapse;

           }   
          th, td {
              padding: 5px;
         }
         body{
            margin:0;
            padding:0;
            margin-left: 0px;
            margin-top: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
            font-size: 18px;
            background-color: #FFFFFF;
            color: #1F1D3E;
        }
        table {
          padding: 0;
          margin: 0;    
          border-collapse: collapse;
        }     

    input[type='radio']:after {
            width: 15px;
            height: 15px;
            border-radius: 15px;
            top: -2px;
            left: -1px;
            position: relative;
            background-color: #1F1D3E;
            content: '';
            display: inline-block;
            visibility: visible;
            border: 2px solid white;
        }

        input[type='radio']:checked:after {
            width: 15px;
            height: 15px;
            border-radius: 15px;
            top: -2px;
            left: -1px;
            position: relative;
            background-color: white;
            content: '';
            display: inline-block;
            visibility: visible;
            border: 2px solid white;
        }    

    </style>
    <?php  date_default_timezone_set("America/Santiago"); ?>     
</head>
<body>
	 <form action="busqueda_session.php" method="post" target="_blank">
		<table border="0" style="font-size: small;">
		  <tbody>
			<tr>
			  <td width="6%" align="center">Nª<br /><input name="numero" type="text" id="numero" size="5" value="" /></td>
			  <td align="center" width="4%"><input type="radio" name="mes" value="estemes" onclick="estemes();" /> <br />
		      Este <br />Mes</td>
			  <td align="center" width="7%"><input type="radio" name="mes" value="mesant" onclick="mesanterior();"/>
			    <br />
		      Mes<br />Anterior </td>
			  <td width="22%" align="center">Categorìa<br /><select name="categoria" id="categoria">
				<option value = ''>&nbsp;</option>
				  <option value = 'Cableado'>Cableado</option>
				  <option value = 'Enlace'>Enlace</option>
				  <option value = 'enlace_caido'>Enlace Caído</option>
				  <option value = 'facturacion'>Facturación</option>
				  <option value = 'Fuera_de_horario'>Fuera de Horario</option>
				  <option value = 'Hosting'>Hosting / Correos</option>
				  <!--option value = 'Nuevo_requerimiento'>Nuevo Requerimiento / Oportunidad</option-->
				  <option value = 'Otros'>Otros</option>
				  <option value = 'Soporte'>Soporte</option>
				  <option value = 'Soporte_contrato_mensual'>Soporte Contrato Mensual</option>
				  <option value = 'Sujeto_a_cobro'>Sujeto a Cobro</option>
				  <option value = 'Telefonia'>Telefonía</option>
				  <option value = 'termino_contrato'>Término de Contrato</option>
			  </select></td>
				<td width="11%" align="center">Razón Social<br /><input name="empresa" type="text" id="empresa" size="10" value="" /></td>
				<td width="11%" align="center">Usuario Asignado<br /><br /><input name="usuario" type="text" id="usuario" size="10" value="" /></td>
				<td width="10%" align="center"><label><input type="radio" name="estado" value="cerrados" /> Cerrados </label><br />
                    <label><input type="radio" name="estado" value="abiertos" required="required" checked="checked" /> Abiertos </label><br />
                <label><input type="radio" name="estado" value="todos" /> Todos</label></td>
				<td width="12%" align="center">Código de Servicio<br /><br /><input name="codservicio" type="text" id="codservicio" size="10" value="" /></td>
				<td width="17%" align="center" style="background-color: #1F1D3E;color: white;">
				  <input style="font-size: 12px;" type="submit" value="Buscar en Sistemas" /> <br />	
				  <input style="font-size: 10px;" type="reset" value="Limpiar" />
				</td>
			</tbody>
			</table>
    </form>                             
	
	
	
	
	
</body>    
</html>
