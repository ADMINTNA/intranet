<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <?php include_once("../meta_data/meta_data.html"); ?>
    <title>Buscador Tareas iContel</title>
</head>
<body >   
<?PHP 
    // activo mostrar errores
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');

 
		if(isset($_POST['asunto']))     $asunto     = $_POST['asunto'];    
        if(isset($_POST['cliente']))    $cliente    = $_POST['cliente'];    
        if(isset($_POST['categoria']))  $categoria  = $_POST['categoria'];    
        if(isset($_POST['prioridad']))  $prioridad  = $_POST['prioridad'];    
        if(isset($_POST['ejecutivo']))  $ejecutivo  = $_POST['ejecutivo'];    
        if(!empty($asunto))     $cuales  = " && t.name like '%".$asunto."%'";
        if(!empty($cliente))    $cuales .= " && ac.name like '%".$cliente."%'";
        if(!empty($categoria))  $cuales .= " && us.first_name like '%".$u_nombre."%'";
        if(!empty($u_nombre))   $cuales .= " && us.first_name like '%".$u_nombre."%'";
        if(!empty($u_apellido)) $cuales .= " && us.last_name like '%".$u_apellido."%'";
        if(!empty($estado))     $cuales .= " && op.sales_stage like '%".$estado."%'";
        
      $sql = "CREATE PROCEDURE `busca_tareas`(IN `cliente` VARCHAR(100))
select 
t.name 			as titulo,
u.user_name		as ejecutivo,
t.status		as estado,
t.parent_type	as origen,
t.priority		as prioridad,
a.name			as cliente,
date_format(CONVERT_TZ( t.date_entered, '+00:00', '-04:00' ),"%d/%m/%Y")  as f_creacion,

date_format(CONVERT_TZ( t.date_modified, '+00:00', '-04:00' ),"%d/%m/%Y") as f_modifica,
if(t.status != "Completed", DATEDIFF (NOW(),t.date_entered),"")  as dias 
from tasks 				as t
JOIN contacts 			as c  ON c.id = t.contact_id
JOIN accounts_contacts  as ac ON ac.contact_id = c.id
JOIN accounts			as a  ON a.id = ac.account_id
JOIN users				as u  ON u.id = t.assigned_user_id
WHERE 1 
order by t.name ASC, t.priority DESC ";
	
     require_once __DIR__ . '/../session_core.php';
// session_unset(); // ELIMINADO: Borraba la sesión completa (login + grupo)
     $_SESSION["query_op"] = $sql.$cuales;
     header('Location: ./sort/index.php');
?>
        <script type="text/javascript">
            window.location = "./sort/index.php";
        </script>  
     </body>
</html>
