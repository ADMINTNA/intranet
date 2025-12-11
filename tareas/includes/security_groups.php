<?php
    $conn = DbConnect($db_sweet);
    $sql = "CALL `security_groups`()";                        
    $result = $conn->query($sql);

    $ptr = 0;
    $grupos = array();
    $select = '';

    if ($result && $result->num_rows > 0) { 
        $select = '<select name="sg" onChange="autoSubmit();">';
        $select .= '<option value="">Seleccione un grupo</option>';           
        
        while ($row = $result->fetch_assoc()) {
            $ptr++; 
            $grupos[$ptr]['name'] = $row["name"];            
            $grupos[$ptr]['id']   = $row["id"];
            $select .= '<option value="'.$row["id"].'">'.htmlspecialchars($row["name"]).'</option>';           
        }

        $select .= '</select>';

    } else {
        $select = '<p>No hay grupos disponibles.</p>';
    }

    $conn->close();
    unset($result);
    unset($conn);   

    //echo $select;
?>