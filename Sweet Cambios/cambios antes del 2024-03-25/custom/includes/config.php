<?php
   // activo mostrar errores
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    $default_assigned_user_id = "1";
    $script="";
    $hoy = date("d-m-Y H:i:s");
    $fecha_est_Resolucion = strtotime ( '+4 hour' , strtotime ($hoy) );
    $fecha_est_Resolucion = date ( 'Y-m-d' , $fecha_est_Resolucion);
    $hora = date("H:i", strtotime($hoy));
    $dia  = date('N', strtotime($hoy));
    if($dia >=1 and $dia <=5) { if($hora >= "09:00" and $hora <= "18:00") $habil =1; } else $habil = 0;
    // ---------------------------- Funciones ---------------------------------------
    function sweet_query_product($product_id){ // recupera datos del producto de aos_products_quote
        global $db;
        $sql = "SELECT * FROM aos_products_quotes WHERE id='$produ_id'";
        $result=$db->query($sql);
        $row = $db->fetchByAssoc($result);
        if($row)  { 
            $servicio =  array('nombre'=>trim($row['name']), 'proveedor'=>trim($row['account_name']), 'codigo_servicio'=> trim($row['codigo_servicio']) );
        } else $servicio = "";
        return($servicio); // devuelve array con nombre del producto, el proveedor yel codigo de servicio
    }
    function sweet_query_account_contact($account_id){
        global $db;
        $sql2 = "SELECT co.id			as contact_id,
                        co.first_name   as contact_nombre,
                        co.last_name	as contact_apellido
            FROM accounts_contacts	    as ac
            LEFT JOIN contacts		    as co ON co.id = ac.contact_id
            WHERE `account_id` LIKE '".$account_id."'";
        $select = "<select name='contacto_id' id='contacto_id'>\n";
        $result = $db->query($sql2);
        while($row = $result->fetch_assoc()) {
        $select .= "<option value='".$row['contact_id']."'>".$row['contact_nombre']." ".$row['contact_apellido']."\n";    
        }
        $select .= "<option value = ' '> </option>\n<select>\n"; 
        return($select);
    }

