<?PHP 
date_default_timezone_set("America/Santiago");
global $db;
global $current_user;
$default_assigned_user_id = "1";
$script="";
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['account_id']) and $_POST['account_id']!==''){
         try{
            $caso = BeanFactory::newBean('Cases');
            $caso->name =  $_POST['asunto'];  
            $caso->account_id = $_POST['account_id'];
            $caso->servicio_afectado_c = $_POST['servicio_afectado'];
            $caso->proveedor_c = $_POST['proveedor'];
            $caso->codigo_servicio_c=$_POST['codigo_servicio'];
            $caso->fecha_resolucion_estimada_c = $_POST['fecharesol'];
            $caso->state = $_POST['estado'];
            $caso->responsable_c = $_POST['responsable'];
            $caso->contact_id_c = $_POST['contacto_id'];
            $caso->priority = $_POST['prioridad'];
            $caso->horario_c = $_POST['horario'];
            $caso->tipo_caso_c = $_POST['casotipo'];
            $caso->categoria_c = $_POST['categoria'];
            $caso->description = $_POST['descripcion'];
            $caso->created_by = $current_user->id;  
            $caso->assigned_user_id = $current_user->id;
            $caso->save();
            SugarApplication::redirect('index.php?module=Cases&action=DetailView&record='.$caso->id);
        }catch(Exception $e){
            echo "<div class='alert alert-danger' role='alert'><p> Error en la creacion del caso </p></div>".$e->getMessage();
        }
    }else{
        echo "<div class='alert alert-danger' role='alert'>
        No se pudo crear el Caso revisar datos ".$_POST['product_id'];
        echo "<p>Product id: ".$_POST['product_id']."</p>";
        echo "<p>Account id: ".$_POST['account_id']."</p></div>";
        //header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
} elseif(!empty($_GET['account_id'])){
        $hoy = date("d-m-Y H:i:s");
        $fecha_est_Resolucion = strtotime ( '+4 hour' , strtotime ($hoy) );
        $fecha_est_Resolucion = date ( 'Y-m-d' , $fecha_est_Resolucion);
        $hora = date("H:i", strtotime($hoy));
        $dia  = date('N', strtotime($hoy));
        if($dia >=1 and $dia <=5) { if($hora >= "09:00" and $hora <= "18:00") $habil =1; } else $habil = 0;
        $produ_id = $_GET["produ_id"];
        $account_id = $_GET["account_id"];
        global $db;
        //$query_aos_products_quotes = "CALL aos_products_quotes_by_id('$produ_id')";
        $sql = "SELECT * FROM aos_products_quotes WHERE id='$produ_id'";
        $result=$db->query($sql);
        $row = $db->fetchByAssoc($result);
        if($row)  { 
            $servicio =  array('nombre'=>trim($row['name']), 'proveedor'=>trim($row['account_name']), 'codigo_servicio'=> trim($row['codigo_servicio']) );
        } else $servicio = "";
        $sql2 = "SELECT co.id			as contact_id,
                        co.first_name	as contact_nombre,
                        co.last_name		as contact_apellido
                FROM accounts_contacts	as ac
                LEFT JOIN contacts		as co ON co.id = ac.contact_id
                WHERE `account_id` LIKE '".$account_id."'";
        $select = "<select name='contacto_id' id='contacto_id'>\n";
        $result = $db->query($sql2);
        while($row = $result->fetch_assoc()) {
          $select .= "<option value='".$row['contact_id']."'>".$row['contact_nombre']." ".$row['contact_apellido']."\n";    
        }
        $select .= "<option value = ' '> </option>\n<select>\n";    
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
     <html xmlns="http://www.w3.org/1999/xhtml">
     <head>
     <?php include_once("../meta_data/meta_data.html"); ?>
    <title>Crea nuevo Caso iContel</title>
    <style type="text/css">
        .table_alarmas{
               border: none;
               color: #1F1D3E;
               color: white;
               font-size: 15px;
               border-collapse: collapse;
               background-color: #19173C;
               border-collapse: collapse;

           }   
          th, td {
              padding: 5px;
         }
         body{
            margin:0;
            padding:0;
            margin-left: 0px;
            margin-top: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
            font-size: 18px;
            background-color: #FFFFFF;
            color: #1F1D3E;
        }
        table {
          padding: 0;
          margin: 0;    
          border-collapse: collapse;
        }     

    input[type='radio']:after {
            width: 15px;
            height: 15px;
            border-radius: 15px;
            top: -2px;
            left: -1px;
            position: relative;
            background-color: #1F1D3E;
            content: '';
            display: inline-block;
            visibility: visible;
            border: 2px solid white;
        }
        .input_read_only {
            background-color: #1F1D3E;
            color: white;
            border: none
        }

        input[type='radio']:checked:after {
            width: 15px;
            height: 15px;
            border-radius: 15px;
            top: -2px;
            left: -1px;
            position: relative;
            background-color: white;
            content: '';
            display: inline-block;
            visibility: visible;
            border: 2px solid white;
        }    

    </style>
    </head>
    <body>
    <div align="center">
       <table border="1">
            <tr align="center" style="color: white;background-color: #1F1D3E;">
              <td valign="top" rowspan="2"><img src="./images/logo_icontel_azul.jpg"  height="115" alt=""/></td>
              <td width="" colspan="1" rowspan="1" valign="top" style="border: none">
                 <table align="center" width="100%" style="vertical-align: top;" border="1" >
                      <!-- Titulo del menú o informe -->
                      <tr style="background-color: #1F1D3E;color: white;">  
                          <td>
                              <table width="100%">
                                  <tr>
                                    <th align="center" style="font-size: 20px;">Crea Caso en Sweet</th>
                                  </tr>
                              </table>
                          </td>
                      </tr>
                      <tr align="center">
                         <td >
                         <!-- Contenido Principal del menú o informe -->     
                             <form method="post" target="_self">
                                 <input type="hidden" id="account_id" name="account_id" 
                                        value="<?PHP echo $account_id; ?>">
                                 <input type="hidden" id="servicio_afectado" name="servicio_afectado" 
                                        value="<?PHP echo $servicio['nombre']; ?>">                             
                                 <input type="hidden" name="proveedor" id="proveedor" size="30" 
                                        value="<?PHP echo $servicio['proveedor']; ?>">
                                 <input type="hidden" name="codigo_servicio" id="codigo_servicio" size="30" 
                                        value="<?PHP echo $servicio['codigo_servicio']; ?>">
                                 <input type="hidden" name="fecharesol" id="fecharesol" size="30" 
                                        value="<?PHP echo $fecha_est_Resolucion; ?>">
                                 <input type="hidden" name="estado" id="estado" size="30" value="Abierto">
                                 <input type="hidden" name="responsable" id="responsable" size="30" value="validando">   
                                <table border="1" align="center">
                                  <tbody>
                                    <tr>
                                    <tr>
                                      <td width="">Asunto</td>
                                      <td colspan="3"><input  name="asunto" type="text" id="asunto" size="88"></td>
                                    </tr>
                                    <tr>
                                    <tr>
                                      <td width="">Contacto</td>
                                      <td><?PHP echo $select; ?></td>
                                     <td>Prioridad<br></td>
                                      <td>
                                        <select name="prioridad" id="prioridad">
                                             <option value = 'Alta'>Alta</option>
                                             <option value = 'Media'>Media</option> 
                                             <option value = 'Baja' selected>Baja</option>
                                        </select>   
                                      </td>
                                    </tr>
                                    <tr>
                                      <td>Horario<br></td>                                    
                                      <td> <input type="radio" id="horario" name="horario" value="Horario habil" 
                                                  <?PHP if($habil) echo "checked"; ?> > Habil 
                                           <input type="radio" id="horario" name="horario" value="Fuera de horario"
                                                  <?PHP if(!$habil) echo "checked"; ?> > Fuera de Horario
                                      </td>
                                      <td>Caso Tipo<br></td>
                                      <td>
                                        <select name="casotipo" id="casotipo">
                                             <option value = 'Continuidad operacional' selected>Continuidad Operacional</option>
                                             <option value = 'Sujeto a Cobro'>Sujeto a Cobro</option> 
                                             <option value = 'Termino de servicio'>Término de Servicio</option>
                                        </select>                                 
                                      </td>
                                    </tr>
                                    <tr>
                                      <td>Categoría<br></td>
                                      <td>
                                        <select name="categoria" id="categoria">
                                            <option value = 'Cableado' selected>Cableado</option>
                                            <option value = 'Enlace'>Enlace</option> 
                                            <option value = 'Enlace Caido'>Enlace Caido</option>
                                            <option value = 'Facturacion'>Facturacion</option>
                                            <option value = 'Fuera de Horario'>Fuera de Horario</option>
                                            <option value = 'Hosting / Correos'>Hosting / Correos</option>
                                            <option value = 'Nuevo requerimiento / oportunidad'>Nuevo requerimiento / oportunidad</option>
                                            <option value = 'Otros'>Otros</option>
                                            <option value = 'Soporte'>Soporte</option>
                                            <option value = 'Soporte contrato mensual'>Soporte contrato mensual</option>
                                            <option value = 'Sujeto a cobre'>Sujeto a cobro</option>
                                            <option value = 'Telefonia'>Telefonia</option>                                         
                                            <option value = 'Termino de contrato'>Termino de contrato</option>
                                        </select>
                                      </td>
                                      <td colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr style="background-color: #1F1D3E;color: white;">  
                                      <td colspan="4" align="left">Descripción:<br>
                                          <textarea id="descripcion" name="descripcion" rows="5" cols="80"><?PHP echo $hoy." : "; ?></textarea></td>
                                    </tr>
                                    <tr style="background-color: #1F1D3E;color: white;">  
                                      <td colspan="2" align="center"><input style="font-size: 10px;" type="reset" value="Limpiar" /></td>
                                      <td colspan="2" align="center"><input style="font-size: 12px;" type="submit" value="Crear Caso" /></td>
                                    </tr>                                  
                                    <!--tr>
                                      <td align="left" colspan="2"><input style="color: darkslategrey;" type="reset" value="Limpiar" /></td>
                                      <td align="right" colspan="2"><input style="color: darkslategrey;" type="submit" value="Crear Caso" /></td>
                                    </tr-->
                                  </tbody>
                                </table>
                            </form>                             
                         <!-- FINContenido Principal del menú o informe -->                                            
                         </td> 
                      </tr>
                 </table> 
              </td>   
            </tr>
            <tr style="background:#CFCFCF;">
              <td height="10" colspan="2"></td>
            </tr>
        </table> 
       </div>
       </body>    
    </html>
<?PHP
} else {
    header('Location: ../../index.php');
    exit;
}
?>