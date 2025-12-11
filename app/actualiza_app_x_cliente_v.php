<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Untitled Document</title>
</head>

<body>
<?php
require('includes/config.php');
require('includes/functions.php');

$id_clie = 2;

$id_clie = $_POST['id_clie'];
$userName= $_POST['userName'];

$query_clean = "update app_x_cliente set checked = 0 where id_clie = " . $id_clie  . "";
mysqli_query($conn, $query_clean);

$query = "update app_x_cliente set checked = 1 where id_clie = " . $id_clie  . " and id_app in (" ;
if(!empty($_POST['apps'])){
    // Ciclo para mostrar las casillas checked checkbox.
    foreach($_POST['apps'] as $selected){
    echo $selected."</br>";// Imprime resultados
    $query .= " '" . $selected . "' ,";
    }
}           
$query = substr($query,0,strlen($query) -1);
$query .= " ) " ;

//echo  $query;    
$save = mysqli_query($conn, $query);

mysqli_close($conn);

//exit(0);


echo '<script type="text/javascript">
window.location = "formulario_visibilidad.php?id=' . $id_clie . '&userName=' .$userName . '&save=true"
</script>';

?>
</body>
</html>