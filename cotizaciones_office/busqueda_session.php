<?PHP
     session_start();
     session_unset();
    // activo mostrar errores
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    $cuales = "";
//var_dump($_POST);
    if(isset($_POST['numero']))    $numero = $_POST['numero'];
    if(!empty($numero)) {
        $cuales = " AND aq.number	 = '".$numero."'";
    } else {
        if(isset($_POST['asunto']))      $asunto      = $_POST['asunto'];    
        if(isset($_POST['cliente']))     $cliente     = $_POST['cliente'];    
        if(isset($_POST['oportunidad'])) $oportunidad = $_POST['oportunidad'];    
        if(isset($_POST['ejecutivo']))   $ejecutivo   = $_POST['ejecutivo'];    
        if(isset($_POST['etapa']))       $etapa       = $_POST['etapa'];    
        if(isset($_POST['producto']))    $producto    = $_POST['producto'];    
        if(!empty($asunto))     $cuales  = " AND aq.name 		like '%".$asunto."%'";
        if(!empty($cliente))    $cuales .= " AND ac.name        like '%".$cliente."%'";
        if(!empty($oportunidad)) $cuales .= " AND opc.numero_oportunidad_c = '".$oportunidad."'";
        if(!empty($ejecutivo))  $cuales .= " AND u.user_name 	like '%".$ejecutivo."%'";
        if(!empty($etapa))      $cuales .= " AND aqc.etapa_cotizacion_c like '%".$etapa."%'";
        if(!empty($producto))   $cuales .= " AND apq.name       like '%".$producto."%'";
        
    } 
    $_SESSION["cuales"] = $cuales;
    header('Location: ./sort/index.php');
    exit();
