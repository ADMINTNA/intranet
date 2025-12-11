<?PHP // config.php datos de configuraciones y generales
    // activo mostrar errores
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    $db_sweet  = "tnasolut_alarmas";
    date_default_timezone_set("America/Santiago");
    $eol = "<br>";
    $hoy = date('d-m-Y H:i:s');

//////// Funciones //////////////
function DbConnect($dbname){
    $server   = "localhost";
    $user     = "tnasolut_data_studio";
    $password = "P3rf3ct0.,";
    // me conecto a la Base de Datos
    $conn = new mysqli($server, $user, $password);
    if ($conn->connect_error) { die("No me pude conectar a servidor localhost: " . $conn->connect_error); }
    $dummy = mysqli_set_charset ($conn, "utf8");    
    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) { die ('No se puede usar '.$dbname.' : ' . mysql_error()); }
    return($conn);
}
function leearchivo($path){
    $file = fopen($path,"r");
        echo $linea = fgets($file);
    fclose($file);  
    return $linea;
}    


?>