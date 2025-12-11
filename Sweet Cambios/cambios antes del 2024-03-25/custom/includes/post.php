<?PHP
    if(isset($_POST['account_id']) and $_POST['account_id']!==''){ // Crea y muestra Nuevo Caso en Sweet
        try{
            $caso = BeanFactory::newBean('Cases');            
            $caso->name                         = $_POST['asunto'];  
            $caso->account_id                   = $_POST['account_id'];
            $caso->state                        = $_POST["estado"];
            $caso->priority                     = $_POST['prioridad'];
            $caso->description                  = $_POST['descripcion'];
            $caso->created_by                   = $current_user->id;  
            $caso->assigned_user_id             = $current_user->id;
            $caso->categoria_c                  = $_POST['categoria'];
            $caso->codigo_servicio_c            = $_POST['codigo_servicio'];
            $caso->contact_id_c                 = $_POST['contacto_id'];
            $caso->fecha_resolucion_estimada_c  = $_POST['fecharesol'];
            $caso->horario_c                    = $_POST['horario'];
            $caso->horas_sin_servicio_c         = $_POST["h_sin_servicio"];
            $caso->numero_ticket_c              = $_POST["numero_ticket"] ;
            $caso->proveedor_c                  = $_POST['proveedor'];
            $caso->responsable_c                = $_POST['responsable'];
            $caso->servicio_afectado_c          = $_POST['servicio_afectado'];
            $caso->tipo_caso_c                  = $_POST['casotipo'];
            echo $caso->categoria_c;
            $caso->save();
            SugarApplication::redirect('index.php?module=Cases&action=DetailView&record='.$caso->id); // muestra caso creado en sweet
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
?>