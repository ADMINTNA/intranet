<?php
    include_once("config.php"); 
    $conn = DbConnect($db_sweet);
    $sql = "CALL `security_groups`()";                        
    $result = $conn->query($sql);
    $ptr=0;
    $grupos = array();
    if($result->num_rows > 0)  { 
        $select = '<select name="sg" onChange="autoSubmit();"><br>';
        $select .= '<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Seleccione</option><br>';           
        
        while($row = $result->fetch_assoc()) {
            $ptr ++; 
            $grupos[$ptr]['name'] = $row["name"];            
            $grupos[$ptr]['id']   = $row["id"];
            $select .= '<option value="'.$row["id"].'">'.$row["name"].'</option><br>';           
        }
        $select .= '</select><br>';
    }
    $conn->close();
    unset($result);
    unset($conn);   
  //  echo $select;
?>

