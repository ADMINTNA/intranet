<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Documento sin tÃ­tulo</title>
</head>

<body>
	
<?php 
   // activo mostrar errores
  //  error_reporting(E_ALL);
 //  ini_set('display_errors', '1');


$fruits = array("d" => "lemon", "a" => "orange", "b" => "banana", "c" => "apple");
asort($fruits);
foreach ($fruits as $key => $val) {
    echo "$key = $val\n";
}
?>	
?>	
	
	
	
</body>
</html>