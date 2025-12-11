<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado"/>
    <meta name="Keywords" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cablead">
    <meta name="author" content="iConTel S.p.A.">
    <meta name="subject" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado">
    <meta NAME="Classification" content="TNA Solutions, Enlaces, Internet, ISP, WISP, Diseño, Seguridad Informatica, Desarrollo de Sistemas, Redes, Aplicaciones Web">
    <meta name="Geography" content="Chile">
    <meta name="Language" content="Spanish">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/favicon/favicon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <meta http-equiv="content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Expires" content="never">
    <meta name="Copyright" content="iConTel S.p.A.">
    <meta name="Designer" content="iConTel S.p.A.">
    <meta name="Publisher" content="iConTel S.p.A.">
    <meta name="Revisit-After" content="7 days">
    <meta name="distribution" content="Global">
    <meta name="city" content="Santiago">
    <meta name="country" content="Chile">
    <!-- index para los robots-->
    <meta name="robots" content="index,follow" />
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <!-- OpenGraph metadata-->
    <meta property="og:locale" content="es_LA" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="iContel Telecomunicaciones" />
    <meta property="og:description" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado"/>
    <meta property="og:url" content="https://www.icontel.cl/index.php" />
    <meta property="og:site_name" content="Icontel Telecomunicaciones" />
    <meta property="og:image" content="https://www.icontel.cl/favicon/logo.png" />
    <meta property="fb:admins" content="FB-AppID"/>
    <meta name="twitter:card" content="summary"/>
    <meta name="twitter:description" content="iConTel, Telecomunicaciones, Telefonia, VoIP, Enlaces, Internet, Asesoria, ISP, WISP, Seguridad, Informatica, Desarrollo, Redes, Soporte, CCTV, Cloud, Hosting, Collocate, Cableado"/>
    <meta name="twitter:title" content="iConTel Telecomunicaciones"/>
    <meta name="twitter:site" content="iContel S.p.A."/>
    <meta name="twitter:creator" content="iConTel Telecomunicaciones"/>
    <link rel="canonical" href="https://www.icontel.cl/index.php" />
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.icontel.cl/favicon/favicon-16x16.png">    
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.icontel.cl/favicon/favicon-32x32.png">    
    <link rel="icon" type="image/png" sizes="57x57" href="https://www.icontel.cl/favicon/favicon-57x57.png">
    <link rel="icon" type="image/png" sizes="60x60" href="https://www.icontel.cl/favicon/favicon-60x60.png">
    <link rel="icon" type="image/png" sizes="72x72" href="https://www.icontel.cl/favicon/favicon-72x72.png">
    <link rel="icon" type="image/png" sizes="76x76" href="https://www.icontel.cl/favicon/favicon-76x76.png">
    <link rel="icon" type="image/png" sizes="96x96" href="https://www.icontel.cl/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="114x114" href="https://www.icontel.cl/favicon/favicon-114x114.png">
    <link rel="icon" type="image/png" sizes="120x120" href="https://www.icontel.cl/favicon/favicon-120x120.png">
    <link rel="icon" type="image/png" sizes="144x144" href="https://www.icontel.cl/favicon/favicon-144x144.png">
    <link rel="icon" type="image/png" sizes="152x152" href="https://www.icontel.cl/favicon/favicon-152x152.png">
    <link rel="icon" type="image/png" sizes="180x180" href="https://www.icontel.cl/favicon/favicon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="https://www.icontel.cl/favicon/favicon-192x192.png">
    <link rel="manifest" href="https://www.icontel.cl/favicon/manifest.json">
    <title>Buscador Casos iContel</title>
</head>
<body >   
<?PHP 
    // activo mostrar errores
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');

    include_once("../../meta_data/meta_data.html"); 
    if(isset($_POST['numero']))    $numero = $_POST['numero'];
    if(!empty($numero)) {
        $cuales = " && c.case_number = '".$numero."'";
    } else {
        if(isset($_POST['fechadesde'])) $fechadesde = $_POST['fechadesde'];    
        if(isset($_POST['fechahasta'])) $fechahasta = $_POST['fechahasta'];    
        if(isset($_POST['categoria']))  $categoria  = $_POST['categoria'];    
        if(isset($_POST['empresa']))    $empresa    = $_POST['empresa'];    
        if(isset($_POST['usuario']))    $usuario    = $_POST['usuario'];    
        if(isset($_POST['proveedor']))  $proveedor  = $_POST['proveedor'];    
        if(isset($_POST['codservicio'])) $codigo_servicio  = $_POST['codservicio'];    
        if(isset($_POST['estado']))     $estado     = $_POST['estado']; 
        if(isset($_POST['creadopor']))  $creadopor  = $_POST['creadopor']; 
        If(!empty($fechadesde) && !empty($fechahasta)) $cuales = " && c.date_entered BETWEEN '".$fechadesde."' AND '".$fechahasta."'";
        if(!empty($categoria))  $cuales .= " && cc.categoria_c   like '%".$categoria."%'";
        if(!empty($proveedor))  $cuales .= " && cc.proveedor_c   like '%".$proveedor."%'";       
        if(!empty($codigo_servicio))  $cuales .= " && cc.codigo_servicio_c   like '%".$codigo_servicio."%'";
        if(!empty($empresa))    $cuales .= " && a.name           like '%".$empresa."%'";
        if(!empty($estado)) {
            if($estado == "cerrados") $cuales .= " && c.state like '%closed%'"; 
            if($estado == "abiertos") $cuales .= " && c.state NOT like '%closed%'"; 
        }
        if(!empty($usuario))   $cuales .= " && concat( u.first_name,' ',u.last_name) like '%".$usuario."%'";    
        if(!empty($creadopor)) $cuales .= " && concat( uu.first_name,' ',uu.last_name) like '%".$creadopor."%'";            
    }    
    $sql = "SELECT c.id			       as id,
               cc.responsable_c        as responsable,
               cc.categoria_c		   as categoria,
               cc.proveedor_c          as proveedor, 
               c.case_number 		   as numero,
               c.name				   as asunto,
               c.state				   as estado,       
               a.name 				   as cliente,
               c.date_entered		   as f_creacion,
               cc.codigo_servicio_c    as codigo_servicio,
               if(ISNULL( uu.first_name), uu.last_name,concat( uu.first_name,' ',uu.last_name)) as creado_por,  
               c.created_by            as u_creation, 
               c.date_modified		   as f_modifica,
               if( c.state='Closed',TIMEDIFF(c.date_modified ,c.date_entered),TIMEDIFF(NOW(),c.date_entered) ) as antiguedad,
               cc.horas_sin_servicio_c as horas,       
               if(ISNULL( u.first_name), u.last_name,concat( u.first_name,' ',u.last_name)) as usuario   
               FROM `cases` as c
               JOIN tnasolut_sweet.cases_cstm as cc  ON cc.id_c =  c.id
               JOIN tnasolut_sweet.accounts   as  a  ON  a.id   = c.account_id
               JOIN tnasolut_sweet.users      as  u  ON  u.id  = c.assigned_user_id                              
               JOIN tnasolut_sweet.users      as  uu ON  uu.id = c.created_by
               WHERE !c.deleted && !a.deleted && cc.categoria_c NOT like 'Soporte_contrato_mensual'";
     session_start();
     session_unset();
     $_SESSION["query"] = $sql.$cuales;
     header('Location: ./sort/index.php');
?>
        <script type="text/javascript">
            window.location = "./sort/index.php";
        </script>  
     </body>
</html>
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
  










    
