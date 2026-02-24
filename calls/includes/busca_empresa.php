<?php 
include_once("config.php");    
date_default_timezone_set("America/Santiago");

if (isset($_POST['empresa'])) { $empresa = $_POST['empresa']; }
if (isset($_GET['empresa'])) { $empresa = $_GET['empresa']; }
if (empty($empresa)) exit();

$ptr        = 0;
$datos      = array();
$id         = "";
$no_mostrar = 0;

$url_empresa = 'https://sweet.icontel.cl/index.php?module=Accounts&action=DetailView&record=';
$conn = DbConnect($db_sweet);
$sql = "CALL tnasolut_sweet.searchbyempresa('%".$empresa."%')";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $contacto = Array();
    while ($row = $result->fetch_assoc()) {
        $ptr++;
        $contacto[$ptr]["id"] = $row["ct.id"];          
        $contacto[$ptr]["nombre"] = $row['nombre']." ".$row['apellido'];

        if ($tmp == $row["id"]) {
            $no_mostrar = 1;
        } else {
            $no_mostrar = 0;
        }

        if (empty($account_id)) {
            $ani = $row['office_tel'];           
            $ani = str_replace([" ","+"], "", $ani);
            $account_id = $row["id"]; 
            $contacto_id = $row["ct.id"];
            $rut = str_replace([' ', '.'], "", $row["rut"]);
        }

        $url_rut = 'https://intranet.icontel.cl/calls/index.php?rut=' . $row["rut"];

        if (!empty($row["id_contacto"])) {
            $url_contacto = 'https://sweet.icontel.cl/index.php?action=DetailView&module=Contacts&record=' . $row["id_contacto"];
            $nombre_contacto = "<a href='".$url_contacto."' target='_blank'>".$contacto[$ptr]["nombre"]."</a>";
        } else {
            $nombre_contacto = $contacto[$ptr]["nombre"];
        }    
        switch ($row["estado"]) {
            case "Baja":
                $style = "style=\"background-color:orange;color:yellow;\""; 
             case "Suspendido":
                $style = "style=\"background-color:orange;color:yellow;\""; 

            case "Extrajudicial":
                $style = "style=\"background-color:orange;color:yellow;\""; 
                break;
            default:
                $style = "style=\"background-color:white;color:grey;\""; 
        } 

        $datos_completos .= "<tr ".$style.">";
/*
        if (!$no_mostrar) {
            $datos_completos .= "<td ".$style."><a href='".$url_empresa.$row["id"]."' target=\"_blank\">".$row["razon_social"]."</a><br>".$row["tamano_empresa"]."</td><td colspan='7'>".$row["descripcion"]."</td></tr>
            <tr ".$style."><td></td><td>".$row["ejecutivo"]."</td>";
        } else {
            $datos_completos .= "<td></td><td></td>";
        }
*/
        
         if (!$no_mostrar) {
            $datos_completos .= "<td ".$style.">".$ptr.".- <a href='".$url_empresa.$row["id"]."' target=\"_blank\">".$row["razon_social"]."</a><br>".
            "<div style='display: flex; justify-content: space-between;'><span>".$row["tamano_empresa"]."</span>"."<span><a href='".$url_rut."' target=\"_blank\">".$row["rut"]."</a></span></div></td>";
            $datos_completos .= "<td colspan='6'>".$row["descripcion"]."</td></tr>
            <tr ".$style."><td></td><td>".$row["ejecutivo"]."</td>";            
        } else {
            $datos_completos .= "<td></td><td></td>";
        }
       
        
        // Teléfono con enlace tel:
        $telefono = trim($row['celular']);
        if ($telefono != "") {
            $hrefTel = preg_replace('/\s+/', '', $telefono);
            $telefonoHtml = "<a href=\"tel:$hrefTel\">".htmlspecialchars($telefono)."</a>";
        } else {
            $telefonoHtml = "";
        }

        // eMail con enlace mailto:
        $email = trim($row['email']);
        if ($email != "") {
            $emailHtml = "<a href=\"mailto:".htmlspecialchars($email)."\">".htmlspecialchars($email)."</a>";
        } else {
            $emailHtml = "";
        }

        $datos_completos .= "<td>".$nombre_contacto."</td>".
                            "<td>".$telefonoHtml."</td>".
                            "<td>".$emailHtml."</td>".
                            "<td>".$row['tipo_contacto']."</td>";

        if (!$no_mostrar) {
            $datos_completos .= "<td>".$row["estado"]."</td></tr>";
        } else {
            $datos_completos .= "<td></td></tr>";
        }

        $tmp = $row["id"];
    }
} else {
    echo date("d-m-Y H:i:s") . ": No se encontraron Datos con ese antecedente.<br>";
    exit();
}

$conn->close(); 
?>