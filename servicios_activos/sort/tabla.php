<?php 
	session_start();
	include_once("config.php"); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <?PHP include_once("../../meta_data/meta_data.html"); ?>
    <title>Servicios Activos por Cliente</title>
  		<link rel="stylesheet" href="../css/tabla.css"> 		
        <script src='jquery-3.3.1.min.js' type='text/javascript'></script>
        <script src='script.js' type='text/javascript'></script>
 </head>
    <body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" >
        <div class='container'>
            <input type='hidden' id='sort' value='asc'>			
            <table id="empTable" name="empTable" width='100%' border='1' cellpadding='10'>
                <tr>
                    <th>#</span></th>
                    <th width="23%"><span onclick='sortTable("cliente");'>Cliente</span></th>
                    <th><span onclick='sortTable("producto");'>Producto / Servicio</span></th>
                    <th><span onclick='sortTable("codigo");'>Variante</span></th>
                    <th>
						<span onclick='sortTable("coti_numero");'>Nº Coti</span>
					</th>
                    <th><span onclick='sortTable("proveedor");'>Proveedor</span></th>
                    <th><span onclick='sortTable("codigo_servicio");'>C&oacute;digo de Servicio</span></th>
                    <th width="7%"><span onclick='sortTable("categoria");'>Categor&iacute;a</span></th>
                    <th><span onclick='sortTable("valor");'>Valor UF</span></th>
                    <th><span onclick='sortTable("costo");'>Costo UF</span></th>
                    <th><span onclick='sortTable("recurrencia");'>Recurrencia&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onClick="exportToExcel('empTable')" value="Export to Excel" /></span></th>
               </tr>
               <?php 
					include_once("tabla_datos.php"); // muestra datos
			   ?>
            </table><br><br>
             <br><br>     
        </div>
    </body>
</html>