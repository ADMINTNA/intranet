<?PHP
// ==========================================================
// /intranet/tareas/sort/index.php
// Muestra el resultado de la busqueda de tareas
// Autor: Mauricio Araneda
// Fecha: 2025-11-18
// Codificación: UTF-8 sin BOM
// ==========================================================

// ⚠️ IMPORTANTE: NADA DE HTML ANTES DE ESTO
session_name('icontel_intranet_sess');
session_start();

header('Content-Type: text/html; charset=utf-8');

    // activo mostrar errores
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <?php include_once("../meta_data/meta_data.html"); ?>
    <title>Buscador Tareas iContel</title>
</head>
<body >   
<?PHP 
	if(isset($_POST['asunto']))     $asunto     = $_POST['asunto'];    
	if(isset($_POST['cliente']))    $cliente    = $_POST['cliente'];    
	if(isset($_POST['categoria']))  $categoria  = $_POST['categoria'];    
	if(isset($_POST['estado']))  	$estado  	= $_POST['estado'];    
	if(isset($_POST['prioridad']))  $prioridad  = $_POST['prioridad'];    
	if(isset($_POST['ejecutivo']))  $ejecutivo  = $_POST['ejecutivo'];    
	if(!empty($asunto))     $cuales  = " && t.name 			like '%".$asunto."%'";
	if(!empty($cliente))    $cuales .= " && ca.name 		like '%".$cliente."%'";
	if(!empty($categoria))  $cuales .= " && tc.categoria_c 	like '%".$categoria."%'";
	if(!empty($estado))   	$cuales .= " && t.status 		like '%".$estado."%'";
	if(!empty($prioridad))  $cuales .= " && t.priority 		like '%".$prioridad."%'";
	if(!empty($ejecutivo))  $cuales .= " && u.user_name 	like '%".$ejecutivo."%'";    
    $sql = 'SELECT 
    CONCAT("https://sweet.icontel.cl/index.php?action=DetailView&module=Tasks&record=", t.id) AS url, 
    t.name AS tarea, 
    CASE 
        WHEN t.parent_type = "Cases" THEN ca.case_number
        WHEN t.parent_type = "Opportunities" THEN oc.numero_oportunidad_c
        ELSE "N/A"
    END AS numero, 
    t.parent_type AS origen, 
    t.parent_id AS origen_id, 
    CONVERT_TZ(t.date_entered, "+00:00", "-04:00") AS f_creacion, 
    CONVERT_TZ(t.date_modified, "+00:00", "-04:00") AS f_modifica, 
    CASE 
        WHEN t.status != "Completed" THEN DATEDIFF(NOW(), t.date_entered)
        ELSE DATEDIFF(t.date_modified, t.date_entered) 
    END AS dias, 
    CASE 
        WHEN t.status = "Atrasada" THEN "Atrasada"
        WHEN t.status = "Rendicion" THEN "Rendición"
        WHEN t.status = "Not Started" THEN "No Iniciada"
        WHEN t.status = "Reasignada" THEN "Reasignada"
        WHEN t.status = "tarea_creada" THEN "Tareas Creadas"
        WHEN t.status = "Aprobar_Hora_Extra" THEN "Aprobar Hora"
        WHEN t.status = "Hora_Extra_Cerrada" THEN "Hora Aprobada"
        WHEN t.status = "In Progress" THEN "En Progreso"
        WHEN t.status = "movil_solicitado" THEN "Movil Solicitado"
        WHEN t.status = "Completed" THEN "Completada"
        ELSE "Estado no asignado"
    END AS estado, 
    UCASE(tc.categoria_c) AS categoria, 
    CASE 
        WHEN t.priority = "URGENTE_E" THEN "1 URGENTE ESCALADO"
        WHEN t.priority = "URGENTE" THEN "2 URGENTE"
        WHEN t.priority = "High" THEN "3 Alta"
        WHEN t.priority = "Low" THEN "4 Baja"
        ELSE "PRIORIDAD NO ASIGNADA"
    END AS prioridad, 
    u.user_name AS usuario
FROM 
    tasks t
JOIN 
    tasks_cstm tc ON tc.id_c = t.id
JOIN 
    users u ON u.id = t.assigned_user_id
JOIN 
    securitygroups_users sgu ON sgu.user_id = u.id
JOIN 
    securitygroups sg ON sg.id = sgu.securitygroup_id
LEFT JOIN 
    cases ca ON ca.id = t.parent_id AND t.parent_type = "Cases"
LEFT JOIN 
    opportunities op ON op.id = t.parent_id AND t.parent_type = "Opportunities"
LEFT JOIN 
    opportunities_cstm oc ON oc.id_c = op.id
WHERE 
    !t.deleted ';

 	$_SESSION["query_tareas"] = $sql.$cuales;
    header('Location: sort/index.php');
?>
        <script type="text/javascript">
            window.location = "sort/index.php";
        </script>  
     </body>
</html>
