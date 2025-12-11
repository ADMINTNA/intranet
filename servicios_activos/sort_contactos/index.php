<?php
    if(isset($_POST["submit"]))
         // print_r($_POST)."<br><br>";
        if(isset($_POST["Recurrencia"])) {
            $Recurrencia = "(";
            $ptr = 0;
            foreach ($_POST['Recurrencia'] as $subject){
                if($ptr >0) $Recurrencia .= " OR ";
                $Recurrencia .= "c.etapa_cotizacion_c = '".$subject."'";
                $ptr ++;
            }    
            $Recurrencia .= ")<br>";
        }
        if(isset($_POST["Categoria"])) {
            $Categoria = "(";
            $ptr = 0;
            foreach ($_POST['Categoria'] as $subject){
                if($ptr >0) $Categoria .= " OR ";
                $Categoria .= "ca.name = '".$subject."'";
                $ptr ++;
            }    
            $Categoria .= ")<br>";
        }
        if(isset($_POST["Producto"])) {
            $Producto = "(";
            $ptr = 0;
            foreach ($_POST['Producto'] as $subject){
                if($ptr >0) $Producto .= " OR ";
                $Producto .= "p.name = '".$subject."'";
                $ptr ++;
            }    
            $Producto .= ")<br>";
        }
$sql = "SELECT<br>
UPPER(a.name) 							as razon_social,<br>
ac.rut_c 								as rut,<br>
case when q.currency_id = -99 then 'UF' else cu.name end as moneda,<br>
q.name 									as coti_nombre,<br>
q.number 								as coti_numero,<br>
fc.num_nota_venta1_c					as nv_bsale,<br>
p.name 									as produ_nombre,<br>
-- p.item_description 					as produ_descripcion,<br>
p.part_number 							as produ_codigo,<br>
p.product_qty 							as produ_cantidad,<br>
p.product_unit_price 					as produ_precio,<br>
p.product_qty * p.product_unit_price 	as produ_total,<br>
p.product_cost_price 					as produ_costo,<br>
p.account_name 							as produ_proveedor,<br>
p.codigo_servicio						as codigo_servicio,<br>
p.fecha_contrato 						as produ_fecha_contrato,<br>
p.duracion_contrato						as produ_vigencia,<br>
ca.name 								as produ_categoria,<br>
u.user_name 							as vendedor,<br>
(p.product_qty * p.product_unit_price) - p.product_cost_price 		as produ_margen,<br>
p.duracion_contrato - TIMESTAMPDIFF(MONTH, p.fecha_contrato, NOW()) as produ_meses_vence, <br>
CASE<br>
	WHEN c.etapa_cotizacion_c = 'cerrado_aceptado_cot' 			THEN 'MENSUAL' <br>
	WHEN c.etapa_cotizacion_c = 'Cerrado_aceptado_anual_cot' 	THEN 'ANUAL' <br>
	WHEN c.etapa_cotizacion_c = 'cerrado_aceptado_cli' 			THEN 'BIENAL' <br>
	WHEN c.etapa_cotizacion_c = 'cerrado_aceptado' 				THEN 'UNICA' <br>
	WHEN c.etapa_cotizacion_c = 'gasto' 						THEN 'GASTO' <br>
	WHEN c.etapa_cotizacion_c = 'en_traslado' 					THEN 'EN TRASLADO' <br> 
	WHEN c.etapa_cotizacion_c = 'posible_traslado' 				THEN 'POSIBLE TRASLADO' <br> 
	WHEN c.etapa_cotizacion_c = 'suspendido'					THEN 'SUSPENDIDO' <br>
    ELSE 'SIN INFORMACION' <br>
END									as coti_etapa <br>
FROM aos_quotes 					as q	FORCE INDEX(aos_quotes_stage)<br>
LEFT JOIN currencies 				as cu 	ON q.currency_id = cu.id <br>
LEFT JOIN accounts 					as a 	ON a.id = q.billing_account_id<br>
LEFT JOIN accounts_cstm 			as ac 	ON ac.id_c = a.id <br>
LEFT JOIN aos_products_quotes 		as p 	ON p.parent_id = q.id <br>
LEFT JOIN aos_quotes_cstm 			as c 	ON q.id = c.id_c <br>
LEFT JOIN aos_products 				as pr 	ON p.product_id = pr.id <br>
LEFT JOIN aos_product_categories 	as ca 	ON pr.aos_product_category_id = ca.id <br>
LEFT JOIN users			 			as u 	ON a.assigned_user_id = u.id <br>
LEFT JOIN aos_invoices 				as f 	ON f.quote_number = q.number <br>
LEFT JOIN aos_invoices_cstm			as fc	ON f.id = fc.id_c <br>
WHERE q.stage = 'Closed Accepted' <br>
&& p.parent_type = 'AOS_Quotes' <br>";
if(isset($Recurrencia)) $sql .= "&& ".$Recurrencia;
if(isset($Categoria)  ) $sql .= "&& ".$Categoria;
if(isset($Producto)   ) $sql .= "&& ".$Producto;
$sql .= "
&& !q.deleted   <br>
&& !a.deleted  <br>
&& !p.deleted  <br>
ORDER BY `razon_social` ASC <br>";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<?PHP include_once("../meta_data/meta_data.html"); ?>   
  <title>Casos iContel Telecomunicaciones</title>
   <style type="text/css">
   	html, body, div {
   		margin:0;
   		padding:0;
   		height:100%;
   		margin-left: 0px;
   		margin-top: 0px;
   		margin-right: 0px;
   		margin-bottom: 0px;
   	}
    table{
           border: none;
           color: #1F1D3E;
           color: white;
           font-size: 15px;
           border-collapse: collapse;
           background-color: #19173C;
           border-collapse: collapse;

       }   
   	iframe {
        border: none;
        border-collapse: collapse;
        padding: 0;
        margin: 0;
        display:block; 
        width:100%;  
        height: 80%;
    }
 	footer {
	  background-color: white;
	  position: absolute;
	  bottom: 0;
	  width: 100%;
	  height: 25px;
	  color: gray;
	  font-size: 12px;
	}
	/* unvisited link */
	a:link {
	  color: gray;
	}
	/* visited link */
	a:visited {
	  color: gray;
	}
	/* mouse over link */
	a:hover {
	  color: darkgrey;
	  font-size: 20px;
	  font-weight: bold;
	}
	/* selected link */
	a:active {
	  color: blue;
	}		
  </style>
  </head>
  <body>
   <table align="center" border="0" width="100%">
        <tr align="center" style="color: white;background-color: #1F1D3E;">
          <th width="200" height="130" valign="top" align="left"><img src="../images/logo_icontel_azul.jpg"  height="115" alt=""/></th>
          <td>
              <table width="100%" height="100%">
                  <tr height="90">
                      <th align="center" style="font-size: 20px;">Informe de Servicios Activos por Cliente</th>
                  </tr>
                  <tr>
                      <td align="center" style="font-size: 12px;">(click sobre los ti&aacute;tulos para ordenar)</td>
                  </tr>
              </table>
          </td>    
        </tr>
    </table>
  <iframe style="margin: 0 0 0 0;" src="tabla.php" ></iframe>
   </body>
<footer align="center">
℗®™&copy; Copyright <span id="Year"></span><b> iConTel </b>- <a href="tel:228409988">☎+56 2 2840 9988</a> - <a href="mailto: contacto@tnasolutions.cl?subject=Contacto desde Intranet iConTel.">📧contacto@tnasolutions.cl</a> - 🏠Badajoz 45, piso 17, Las Condes, Santiago, Chile.<br> 
	<script type="text/javascript"	>
		var d = new Date(); 
		document.getElementById("Year").innerHTML = d.getFullYear();
	</script>	
</footer>	
</html>
