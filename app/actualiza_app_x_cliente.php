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
// print_r($_POST);
// echo('<br>');

// print_r($_POST['radio_save']);
// echo('<br>');

// print_r($_POST['id_clie']);
// echo('<br>');

// print_r($_POST['nombre']);
// echo('<br>');

// print_r($_POST['rut']);
// echo('<br>');

// print_r($_POST['username']);
// echo('<br>');

// print_r($_POST['password']);
// echo('<br>'); 

if ($_POST['radio_save']== "new"){
    $query = "insert into clientes (rut,razon_social,username,password) values "; 
    $query .= "( '" . $_POST['rut'] . "','" . $_POST['nombre'] . "' , '" . $_POST['username'] ."' ,'" . $_POST['password'] . "')";
   

    $insert = mysqli_query($conn, $query);

    if ($insert) { //agregado
        
        $sql = "SELECT max(id) as id FROM clientes";
                    
                        $row = mysqli_query($conn, $sql);
                        while($valores = mysqli_fetch_array($row)){
                            $id_clie= $valores['id'];
                        }
                        echo ">>>" . $id_clie;

    }

} else if ($_POST['radio_save']== "update"){
    $id_clie = $_POST['id_clie'];
}

//exit(0);

/*$apps_id = $_POST['apps_id'];
$apps_id = unserialize(base64_decode($apps_id));
var_dump($apps_id);
echo('<br>');*/
$query_del = "delete from app_x_cliente where id_clie = " . $id_clie;
$query = "insert into app_x_cliente (id_clie, id_app, checked) values ";



if(!empty($_POST['apps'])){
    // Ciclo para mostrar las casillas checked checkbox.
    foreach($_POST['apps'] as $selected){
    echo $selected."</br>";// Imprime resultados
    $query .= " ( " . $id_clie . ", " . $selected . ", 1),";
    }
}           
/*echo('<br>');
echo  $query_del;
echo('<br>'); */
$query = substr($query,0,strlen($query) -1);
//echo  $query;


$delete = mysqli_query($conn, $query_del);

if ($delete) { //borrado
    
    $save = mysqli_query($conn, $query);

}


/*foreach ($apps_id as $clave=>$valor)
   		{
           echo "El valor de $clave es: $valor <br>";
           $query .= " (2 , " . $valor . "),";
          
        } */
//print_r ($result2);
/*$array = json_decode(htmlspecialchars_decode($_POST['array']));
print_r($array);


*/

mysqli_close($conn);

echo '<script type="text/javascript">
window.location = "formulario.php?save=true"
</script>';

?>
</body>
</html>