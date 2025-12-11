<?php



require('../includes/config.php');

require('../includes/functions.php');



$id_clie = $_POST['id_clie'];



$sql = "SELECT id_app FROM app_x_cliente where id_clie = " . $id_clie ;

$apps = mysqli_query($conn, $sql);

$strJson='{"id_apps":[';

while($valores = mysqli_fetch_array($apps)){

    $strJson .= '"' . $valores["id_app"] . '",';

}



$strJson = substr($strJson,0,strlen($strJson) - 1);

$strJson .= ']}';



//echo $strJson;

$myJSON = json_encode($strJson);

//echo $myJSON;

echo $strJson;



mysqli_free_result($apps);

//mysqli_free_result($result);

mysqli_close($conn);



?>