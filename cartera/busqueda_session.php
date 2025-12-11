<?PHP
require_once __DIR__ . '/session_config.php';
// ... tu código de la página ...
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <?php include_once("../meta_data/meta_data.html"); ?>
    <title>Buscador Cartera iContel</title>
</head>
<body >   
	<?PHP 
		// activo mostrar errores
		// error_reporting(E_ALL);
		// ini_set('display_errors', '1');
		$cuales = '';
		if(isset($_POST['sincontactos']))   $sincontactos   = $_POST['sincontactos'];    
		if(isset($_POST['cliente']))    	$cliente    	= $_POST['cliente'];    
		if(isset($_POST['tipo']))       	$tipo       	= $_POST['tipo'];    
		if(isset($_POST['estado']))     	$estado     	= $_POST['estado'];    
		if(isset($_POST['direccion']))  	$direccion  	= $_POST['direccion'];    
		if(isset($_POST['ciudad']))     	$ciudad     	= $_POST['ciudad'];    
		if(isset($_POST['ejecutivo']))  	$ejecutivo  	= $_POST['ejecutivo'];    
		if(isset($_POST['contacto']))   	$contacto   	= $_POST['contacto'];    
		if(!empty($cliente))    $cuales .= " && a.name like '%".$cliente."%'";
		if(!empty($tipo))       $cuales .= " && a.account_type like '%".$tipo."%'";
		if(!empty($estado))     $cuales .= " && ac.estatusfinanciero_c like '%".$estado."%'";
		if(!empty($direccion))  $cuales .= " && a.billing_address_street like '%".$direccion."%'";
		if(!empty($ciudad))     $cuales .= " && a.billing_address_city like '%".$ciudad."%'";
		// if(!empty($ejecutivo))  $cuales .= " && concat(u.first_name,' ',u.last_name) like '%".$ejecutivo."%'";
    
       // Manejo del array de ejecutivos
        if (!empty($ejecutivo)) {
            if (is_array($ejecutivo)) {
                // Limpiar cada valor del array para evitar SQL Injection
                $ejecutivo = array_map('addslashes', $ejecutivo);
                // Convertir el array en una lista separada por comas para la consulta SQL
                $ejecutivos_in = "'" . implode("','", $ejecutivo) . "'";
                $cuales .= " && (concat(u.first_name,' ',u.last_name) IN ($ejecutivos_in) OR u.user_name IN ($ejecutivos_in))";
            } else {
                $ejecutivo = addslashes($ejecutivo);
                $cuales .= " && (concat(u.first_name,' ',u.last_name) LIKE '%$ejecutivo%' OR u.user_name LIKE '%$ejecutivo%')";
            }
        }
 		if(!empty($contacto))   $cuales .= " && concat(ct.first_name,' ',ct.last_name) like '%".$contacto."%'";	
	 	$sql = "SELECT 	a.name			as cuenta,
			a.account_type 				as cuenta_tipo,
			ac.estatusfinanciero_c 		as cuenta_estado,
			a.billing_address_street 	as cuenta_direccion,
			a.billing_address_city		as cuenta_ciudad,
			a.phone_office				as cuenta_telefono,
			ac.rut_c					as cuenta_rut,
            COALESCE(NULLIF(CONCAT(u.first_name, ' ', u.last_name), ' '), u.user_name) AS vendedor,	
			concat(ct.first_name, ' ',ct.last_name) as contacto_nombre,
			CASE
				WHEN ct.lead_source = 'Amigo' 						THEN 'Amigo' 
				WHEN ct.lead_source = 'Existing Customer' 			THEN 'Contacto Existente' 
				WHEN ct.lead_source = 'contacto_principal' 			THEN 'Contacto Principal' 
				WHEN ct.lead_source = 'contacto_tecnico_principal' 	THEN 'Contacto Técnico Principal' 
				WHEN ct.lead_source = 'contacto_tecnico_secundario' THEN 'Contacto Técnico Secundario' 
				WHEN ct.lead_source = 'de_baja' 					THEN 'De Baja' 
				WHEN ct.lead_source = 'Employee' 					THEN 'Empleado' 
				WHEN ct.lead_source = 'facturacion_proveedores' 	THEN 'Facturación y Proveedores' 
				WHEN ct.lead_source = 'representante_legal' 		THEN 'Representante Legal' 
				WHEN ct.lead_source = 'Proveedor' 					THEN 'Proveedor' 
				ELSE 'Otro'
			END							as contacto_tipo,	
			ct.lead_source				as contacto_tipo2,
			ct.phone_mobile				as contacto_celular,
			ct.phone_work				as contacto_fono,
			ea.email_address			as contacto_email
			FROM accounts 					as a
			LEFT JOIN accounts_cstm 		as ac 	ON ac.id_c = a.id
			LEFT JOIN users			 		as u 	ON a.assigned_user_id = u.id
			LEFT JOIN accounts_contacts		as co 	ON a.id	= co.account_id
			LEFT JOIN contacts				as ct 	ON co.contact_id = ct.id
			LEFT JOIN email_addr_bean_rel 	as er 	ON ct.id = er.bean_id
			LEFT JOIN email_addresses 		as ea	ON er.email_address_id = ea.id
			WHERE !a.deleted
			&& !ct.deleted
			&& !ea.deleted
			&& !er.deleted ";
			$groupby = " GROUP BY contacto_nombre, cuenta";
			if($sincontactos) {$groupby = " GROUP BY cuenta";}
		 $sql .= $cuales.$groupby;
  		 $order = " ORDER BY cuenta ASC";
		 $_SESSION["query"] = $sql;
		 header('Location: ./sort/index.php');
	?>
	<script type="text/javascript">
		window.location = "./sort/index.php";
	</script>  
</body>
</html>
