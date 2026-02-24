<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
<?PHP include_once("../../meta_data/meta_data.html"); ?>   
    <title>Casos iConTel</title>
    <style type="text/css">
        table {
               border: none;
               color: #1F1D3E;
               color: black;
               font-size: 10px;
               border-collapse: collapse;
           }   
          th, td {
              padding: 4px;
              font-size: 12px;
         }
         th {
            background-color: #1F1D3E; 
            color: white;
         }
         body{
            margin:0;
            padding:4px;
            margin-left: 0px;
            margin-top: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
            font-size: 10px;
            background-color: #FFFFFF;
            color: #1F1D3E;
        }
        table tbody tr:nth-child(odd) {
            background: #F6F9FA;
        }
        table tbody tr:nth-child(even) {
            background: #FFFFFF;
        }
        table thead {
          background: #444;
          color: #fff;
          font-size: 18px;
        }
        table {
          border-collapse: collapse;
        }            
    </style>
    </head>
    <body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" >
        <?php
      // activo mostrar errores
    // error_reporting(E_ALL);
    //ini_set('display_errors', '1');
    
            include_once("./includes/config.php");    
            date_default_timezone_set("America/Santiago");
    if(isset($_POST['fechadesde'])) $fechadesde = $_POST['fechadesde'];    
    if(isset($_POST['fechahasta'])) $fechahasta = $_POST['fechahasta'];    
    if(isset($_POST['categoria']))  $categoria  = $_POST['categoria'];    
    if(isset($_POST['empresa']))    $empresa    = $_POST['empresa'];    
    if(isset($_POST['usuario']))    $usuario    = $_POST['usuario'];    
    if(isset($_POST['proveedor']))  $proveedor  = $_POST['proveedor'];    
    if(isset($_POST['estado']))     $estado     = $_POST['estado']; 
    if(isset($_POST['ordenar']))    $ordenar    = $_POST['ordenar']; 
    if(isset($_POST['creadopor']))  $creadopor  = $_POST['creadopor']; 
    If(!empty($fechadesde) && !empty($fechahasta)) $cuales = " && c.date_entered BETWEEN '".$fechadesde."' AND '".$fechahasta."'";
    if(!empty($categoria))  $cuales .= " && cc.categoria_c   like '%".$categoria."%'";
    if(!empty($proveedor))  $cuales .= " && cc.proveedor_c   like '%".$proveedor."%'";
    if(!empty($empresa))    $cuales .= " && a.name           like '%".$empresa."%'";
    if(!empty($estado)) {
        if($estado == "cerrados") $cuales .= " && c.state like '%closed%'"; 
        if($estado == "abiertos") $cuales .= " && c.state NOT like '%closed%'"; 
    }
    if(!empty($usuario))   $cuales .= " && concat( u.first_name,' ',u.last_name) like '%".$usuario."%'";    
    if(!empty($creadopor)) $cuales .= " && concat( uu.first_name,' ',uu.last_name) like '%".$creadopor."%'";
    echo $ordenar."<br>";    
    switch ($ordenar) {
    case "fechacreacion":
        $orden = "f_creacion DESC";
        break;    
    case "categoria":
        $orden = "categoria ASC";
        break;    
    case "cliente":
        $orden = "cliente ASC";
        break;    
    case "proveedor":
        $orden = "proveedor ASC";
        break;    
    case "usuario":
        $orden = "usuario ASC";
        break;   
    case "creado_por":
        $orden = "creado_por ASC";
        break;   
    case "estado":
        $orden = "estado ASC";
        break;   
    case "antiguedad":
        $orden = "antiguedad DESC";
        break;   
    case "horas":
        $orden = "horas_sin_servicio DESC";
        break;   
    default:
        $orden = "numero ASC";    
    }
            
    $sql = " SELECT c.id			   as id,
               cc.categoria_c		   as categoria,
               cc.proveedor_c          as proveedor, 
               c.case_number 		   as numero,
               c.name				   as asunto,
               c.state				   as estado,       
               a.name 				   as cliente,
               c.date_entered		   as f_creacion,
               if(ISNULL( uu.first_name), uu.last_name,concat( uu.first_name,' ',uu.last_name)) as creado_por,  
               c.created_by            as u_creation, 
               c.date_modified		   as f_modifica,
               if( c.state='Closed',TIMEDIFF(c.date_modified ,c.date_entered),TIMEDIFF(NOW(),c.date_entered) ) as antiguedad,
               cc.horas_sin_servicio_c as horas_sin_servicio,       
               if(ISNULL( u.first_name), u.last_name,concat( u.first_name,' ',u.last_name)) as usuario   
               FROM `cases` as c
               JOIN tnaoffice_suitecrm.cases_cstm as cc  ON cc.id_c =  c.id
               JOIN tnaoffice_suitecrm.accounts   as  a  ON  a.id   = c.account_id
               JOIN tnaoffice_suitecrm.users      as  u  ON  u.id  = c.assigned_user_id                              
               JOIN tnaoffice_suitecrm.users      as  uu ON  uu.id = c.created_by
               WHERE !c.deleted && !a.deleted";
     $order .= " ORDER BY ".$orden;
     echo $sql .= $cuales.$order;
     $url = "https://sweet.icontel.cl/?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DCases%26offset%3D1%26stamp%3D1644666990053569200%26return_module%3DCases%26action%3DDetailView%26record%3D";      

      //echo $sql."<br><br>";  
        // me conecto a la Base de Datos
            $conn = DbConnect("tnaoffice_suitecrm");
            $result = $conn->query($sql);
            $ptr=0;
            if($result->num_rows > 0)  { 
                while($row = $result->fetch_assoc()) {
                  $ptr ++;    
                  $contenido .= "<tr>";
                  $contenido .= "<td>".$ptr."</td>";
                  $contenido .= "<td>".$row["categoria"]."</td>";
                  $contenido .= '<td><a target="_blank" href="'.$url.$row["id"].'">'.$row{"numero"}.'</a></td>';
                  $contenido .= "<td>".$row["asunto"]."</td>";
                  $contenido .= "<td>".$row["estado"]."</td>";
                  $contenido .= "<td>".$row["cliente"]."</td>";
                  $contenido .= "<td>".$row["usuario"]."</td>";
                  $contenido .= "<td>".$row["f_creacion"]."</td>";
                  $contenido .= "<td>".$row["creado_por"]."</td>";                  
                  $contenido .= "<td>".$row["f_modifica"]."</td>";
                  $contenido .= "<td>".$row["antiguedad"]."</td>";
                  $contenido .= "<td align='center'>".$row["horas_sin_servicio"]."</td>";
                  $contenido .= "<td align='left'>".$row["proveedor"]."</td>";
                  $contenido .= "</tr>";
                }
            } else {
              $contenido = "<tr><td colspan='9'>No se encontraron datos con la categoría= ".$categoria."</td></tr>";
            }
            $conn->close(); 
        ?>
        <table align="center" width="100%">
              <tr align="center" style="color: white;background-color: #1F1D3E;">
                  <td colspan="3" align="left" valign="top" rowspan="1"><img src="./images/logo_icontel_azul.jpg"  height="100" alt=""/></td>
                  <td colspan="10" align="center" valign="bottom"><h1>Informe de Casos ordenado por <?php echo $orden; ?></h1></td>
                </tr>
                <tr align="left">
                    <th> # </th>
                    <th>Categoría</th>
                    <th>Número</th>
                    <th>Asunto</th>
                    <th>Estado</th>
                    <th>Cliente</th>
                    <th>Asignado a</th>
                    <th width="">Fecha Creación</th>
                    <th>Creado por</th>
                    <th width="">Fecha Modificación</th>
                    <th>Antiguedad<br>Creáción</th>
                    <th>Horas sin<br>Servicio</th>
                    <th>Proveedor</th>
                </tr>
                 <?PHP echo $contenido; ?>
        </table>
        <br><br>
    </body> 
</html>

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
  










    
