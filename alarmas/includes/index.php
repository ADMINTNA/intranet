<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Documento sin título</title>
</head>

<body>

<?PHP    
    include_once("config.php");       
    $archivo = "../alarma.txt";
    $linea = leearchivo($archivo);
    if(strlen($linea) > 10) {
        $datos = explode("//", $linea); 
      // echo $hoy." Enviar mensaje a: {$datos[0]} que diga: {$datos[1]}";
     $conn = DbConnect("tnasolut_alarmas");
     $sql = "CALL buscausuario('{$datos[0]}')";
     $result = $conn->query($sql);
       if ($result->num_rows > 0) { 
           // debo enviar mensaje
           while($col = $result->fetch_assoc() ) {
                $status = "Ok";
                $destinatario = $col["mail"];
                $asunto    = 'Reporte de Nueva Alarma:'.$hoy.", ".$datos[1].".";
                $cuerpo    = "Hola ".$col["usuario"].$eol."Le informamos que el ".$hoy.": Se ha reportado la siguiente alarma <b>".$eol.$datos[1]."</b>".$eol;
                $headers = "MIME-Version: 1.0\r\n"; $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
                //dirección del remitente 
                $headers .= "From: Alarmas TNA <info@tnasolutions.cl>\r\n"; 
                if(!mail($destinatario,$asunto,$cuerpo,$headers)){
                    "Error no se ha podido eviar email a ".$col["mail"];
                    $status = "error";
                }
                $link = DbConnect("tnasolut_alarmas");
                    $sql = "CALL insertaevento(current_timestamp(),'".$col["usuario"]."','".$col["mail"]."', '".$datos[1]."', '".$status."')";
                    if(!$link->query($sql)) echo "error al insertar evento en la tabla tnasolut_alarmas.eventos";
                 $link->close(); 
                 
               break;
          }           
        } else {
            // no hago nada   
           echo "salí sin hacer nada.";
        }
       $conn->close(); 
    }   
    unlink($archivo);

?>    
     
</body>
</html>