<?php
function fquery($conn, $sql) {
	if (!$result = mysqli_query($conn, $sql)) {
			echo "Lo sentimos, este sitio web está experimentando problemas.";
			echo "Error: La ejecución de la consulta falló debido a: \n";
			echo "Query: " . $sql . "\n";
			echo "Errno: " . $mysqli->errno . "\n";
			echo "Error: " . $mysqli->error . "\n";
			exit;
	}
	return($result)	;	
}



?>