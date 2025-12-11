<?php
// ---------------------------- Funciones ---------------------------------------
    function sweet_query_product($id){ // recupera datos del producto de aos_products_quote
        global $db;
        $sql = "SELECT apc.name	   as nombre,
                   apc.account_name        as proveedor,
                   apc.codigo_servicio     as codigo_servicio,
                   ca.name 			       as categoria
            FROM aos_products_quotes 		 as apc
            LEFT JOIN aos_products			 as pr on apc.product_id = pr.id 
            LEFT JOIN aos_product_categories as ca on pr.aos_product_category_id = ca.id
            WHERE apc.id = '".$id."'
               && !apc.deleted
               && !pr.deleted
               && !ca.deleted";
        // $sql = "SELECT * FROM aos_products_quotes WHERE id='".$id."'";
        $result=$db->query($sql);
        $row = $db->fetchByAssoc($result);
        if($row)  { 
            $servicio =  array('nombre'             =>$row['nombre'], 
                               'proveedor'          =>$row['proveedor'], 
                               'codigo_servicio'    =>$row['codigo_servicio'],                                
                               'categoria'          =>$row['categoria'] 
                              );
        } else $servicio = "";
        return($servicio); // devuelve array con nombre del producto, el proveedor y el codigo de servicio
    }
    function sweet_query_account_contact($id){
        global $db;
        $sql2 = "SELECT co.id			as contact_id,
                        co.first_name   as contact_nombre,
                        co.last_name	as contact_apellido
            FROM accounts_contacts	    as ac
            LEFT JOIN contacts		    as co ON co.id = ac.contact_id
            WHERE `account_id` LIKE '".$id."'";
        $select = "<select name='contacto_id' id='contacto_id'>\n";
        $result = $db->query($sql2);
        while($row = $result->fetch_assoc()) {
        $select .= "<option value='".$row['contact_id']."'>".$row['contact_nombre']." ".$row['contact_apellido']."\n";    
        }
        $select .= "<option value = ' '> </option>\n<select>\n"; 
        return($select);
    }
    function normaliza_proveedor($proveedor){
        $proveedor = strtoupper($proveedor);
        switch(true){
            case(strpos($proveedor, "GTD") !== FALSE):
                $proveedor = "GTD_Teleductos"; 
                break;
            case(strpos($proveedor,"FINET") !== FALSE):
                $proveedor = "Finet";
                break;
            case(strpos($proveedor,"CENTURY") !== FALSE):
                $proveedor = "Lumen";
                break;
            case (strpos($proveedor, "MOVISTAR") !== FALSE):
                $proveedor = "Movistar_Chile"; 
                break;
            case(strpos($proveedor, "SZNET") !== FALSE):
                $proveedor = "SZNET";
                break;
            case(strpos($proveedor, "TNA SOL") !== FALSE):
                $proveedor = "TNA Solutions";
                break;
            case(strpos($proveedor, "UFINET") !== FALSE):
                $proveedor = "UfiNet";
                break;
            default:
                $servicio["proveedor"] = "TNA Solutions";
        }
        return($proveedor);       
    }
?>