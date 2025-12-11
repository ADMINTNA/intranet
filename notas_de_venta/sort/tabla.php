<?php include "config.php";
    // activo mostrar errores
      error_reporting(E_ALL);
     ini_set('display_errors', '1');
    session_start();
?>
<!doctype html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Notas de Ventas</title>
    <script src='jquery-3.3.1.min.js' type='text/javascript'></script>
    <link href='style.css' rel='stylesheet' type='text/css'>
    <script src="script.js" type="text/javascript"></script>
    <link href='suma_selected_col_cel.css' rel='stylesheet' type='text/css'>
    <script src="suma_selected_col_cel.js" type="text/javascript"></script>
    <link rel="stylesheet" href="filtro_columna.css">
    <script src="filtro_columna.js"></script></head>
     
<body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" >
	<div class='container'>
		<input type='hidden' id='sort' value='asc'>
		<table width='100%' id='empTable' border='1' cellpadding='10'>
          <thead>
             <!-- cabecera de títulos -->
			<tr>
				<th>#</span></th>
				<th width="4%"><span onclick='sortTable("nv_num");'    >NV N°</span></th>
				<th width="4%"><span onclick='sortTable("fac_num");'   >Fac Nª</span></th>
				<th width="6"><span onclick='sortTable("fac_tipo");'   >Fac Tipo</span></th>
			 	<th width="6%"><span onclick='sortTable("fac_fecha");' >Fac Fecha</span></th>
				<th width="4%"><span onclick='sortTable("cot_num");'   >Cot Nª</span></th>
				<th width="6%"><span onclick='sortTable("cot_estado");'>Cot Estado</span></th>
				<th><span onclick='sortTable("descripcion");'          >Descripción</span></th>
				<th><span onclick='sortTable("cliente");'              >Cliente</span></th>
				<th width="4%"><span onclick='sortTable("moneda");'     >Moneda</span></th>
				<th width="5%"><span onclick='sortTable("monto");'      >Monto</span></th>
				<th width="10%"><span onclick='sortTable("usuario");'               >Usuario</span> 
                    &nbsp;&nbsp;
                    <input type="button" onClick="exportToExcel('empTable')" value="Export" style="font-size: 10px; padding: 2px 6px;" />
                </th>   
            </tr>
           </thead>
          <tbody>
            <?php 
                $query =  $_SESSION["query"]." ORDER BY nv_num DESC";
                $conn = DbConnect("tnasolut_sweet");
                $result = mysqli_query($conn,$query);
                $ptr = 0;

                while($row = mysqli_fetch_array($result)){
                    $ptr ++;    
                    $fac_url       = $row["fac_url"];
                    $cot_url       = $row["cot_url"];
                    $cli_url       = $row["cli_url"];
                    $nv_num        = $row["nv_num"];
                    $fac_num       = $row['fac_num'];
                    $fac_fecha = date("d-m-Y", strtotime($row['fac_fecha']));
                    //$fac_fecha     = $row['fac_fecha'];
                    $cot_num       = $row['cot_num'];
                    $cot_estado    = $row['cot_estado'];
                    $descripcion   = $row['descripcion'];
                    $cliente       = $row['cliente'];
                    $fac_tipo      = $row["fac_tipo"];
                    $moneda        = $row["moneda"];
                    $monto         = $row["monto"];
                    $usuario       = $row["usuario"];
                    ?>
                    <tr>
                        <td><?php echo $ptr; ?></td>
                        <td><?php echo $nv_num; ?></td>
                        <td><a target="_blank" href="<?php echo $fac_url; ?>"><?php echo $fac_num; ?></a></td>
                        <td align="center"><?php echo $fac_tipo; ?></td>
                        <td align="center"><?php echo $fac_fecha; ?></td>
                        <td><a target="_blank" href="<?php echo $cot_url; ?>"><?php echo $cot_num; ?></a></td>
                        <td><?php echo $cot_estado; ?></td>
                        <td><?php echo $descripcion; ?></td>
                        <td><?php echo $cliente; ?></td>
                        <td align="center"><?php echo $moneda; ?></td>
                        <td align="right"><?php echo number_format($monto,2); ?></td>
                        <td><?php echo $usuario; ?></td>
                    </tr>
            <?php
            } ?>
          </tbody>
        </table>
     </div>
<script> // Script inicial de suma_selected_col_cel y filtro_columna
  // Si tu tabla es #empTable:
  initSumaSelectedColCel('#empTable');
  // ✅ con primera columna sin filtro:
  initFiltroColumna('#empTable', { excludeFirstColumn: true });

  // ❌ si quieres filtros en todas:
  // initFiltroColumna('#empTable');

  // Si tienes varias tablas:
  // initSumaSelectedColCel('#tabla1');
  // initSumaSelectedColCel('#tabla2');
</script>
</body>
</html>