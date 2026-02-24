<?PHP // config.php datos de configuraciones y generales
      // activo mostrar errores
     // error_reporting(E_ALL);
     // ini_set('display_errors', '1');>
    $db_sweet  = "tnaoffice_suitecrm";
    $db_dumit  = "tnasolut_Bsale";       
    date_default_timezone_set("America/Santiago");
    $hoy = date("d-m-Y H:i:s");

//////// Funciones //////////////
function DbConnect($dbname){
    $server   = "localhost";
    $user     = "data_studio";
    $password = "1Ngr3s0.,";
    // me conecto a la Base de Datos
    $conn = new mysqli($server, $user, $password);
    if ($conn->connect_error) { die("No me pude conectar a servidor localhost: " . $conn->connect_error); }
    $dummy = mysqli_set_charset ($conn, "utf8");    
    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) { die ('No se puede usar '.$dbname.' : ' . mysql_error()); }
    return($conn);
}
function busca_datos($rut) {
    global $account_id, $ani, $contacto, $count;
    $ptr        = 0;
    $datos      = array();
    $id         = "";
    $no_mostrar = 0;
    $url_empresa = 'https://sweet.tnaoffice.cl/index.php?module=Accounts&action=DetailView&record=';
    // me conecto a la Base de Datos
    $conn = DbConnect("tnaoffice_suitecrm");
    $sql = "CALL tnaoffice_suitecrm.searchbyrut('%".$rut."%')";   

    $result = $conn->query($sql);
    //echo $result->num_rows;
    if ($result->num_rows > 0) {
        $contacto = Array();
        while ($row = $result->fetch_assoc()){
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

            $url_rut = 'https://intranet.icontel.cl/calls_office/index.php?rut=' . $row["rut"];

            if (!empty($row["id_contacto"])) {
                $url_contacto = 'https://sweet.tnaoffice.cl/index.php?action=DetailView&module=Contacts&record=' . $row["id_contacto"];
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

            if (!$no_mostrar) {
                 $datos_completos .= "<td ".$style.">".$count.".- <a href='".$url_empresa.$row["id"]."' target=\"_blank\">".$row["razon_social"]."</a><br>".
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
    }
    $conn->close(); 
    return($datos_completos);
}

function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

    if(strlen($phoneNumber) == 11) {
        $countryCode = substr($phoneNumber, 0, 2);
        $first = substr($phoneNumber, 2, 1);
        switch($first){
         case 9:
            $region   = substr($phoneNumber, 2, 1);
            $primeros = substr($phoneNumber, 3, 4);
            $ultimos  = substr($phoneNumber, 7, 4);
            break;
         case 2:
            $region   = substr($phoneNumber, 2, 1);
            $primeros = substr($phoneNumber, 3, 4);
            $ultimos  = substr($phoneNumber, 7, 4);
            break;
         default:
            $region   = substr($phoneNumber, 2, 2);
            $primeros = substr($phoneNumber, 4, 3);
            $ultimos  = substr($phoneNumber, 7, 4);
        }
        $phoneNumber = '+'.$countryCode.' ('.$region.') '.$primeros.' '.$ultimos;
    }
    return $phoneNumber;
} 

/**
 * Busca el documento adjunto a un contrato relacionado a una cotización.
 * Retorna un array con la información o null si no hay documento.
 *
 * @param mysqli $conn Conexion MySQLi activa.
 * @param string $cotizacion_numero Número de la cotización (por ejemplo '7273').
 * @return array|null
 */
function busca_doc_de_contrato($conn, $coti_num) {
    $coti_num_esc = $conn->real_escape_string($coti_num);

    $sql = "
        SELECT
            c.id AS contrato_id,
            c.name AS contrato_nombre,
            d.id AS documento_id,
            d.document_name AS documento_nombre,
            r.id AS revision_id,
            r.filename AS archivo_nombre,
            CONCAT(
                'https://sweet.icontel.cl/index.php?preview=yes&entryPoint=download&id=',
                r.id,
                '&type=Documents'
            ) AS url_documento
        FROM
            aos_quotes q
            INNER JOIN opportunities o
                ON o.id = q.opportunity_id AND o.deleted = 0
            INNER JOIN aos_contracts c
                ON c.opportunity_id = o.id AND c.deleted = 0
            INNER JOIN aos_contracts_documents cd
                ON cd.aos_contracts_id = c.id AND cd.deleted = 0
            INNER JOIN documents d
                ON d.id = cd.documents_id AND d.deleted = 0
            INNER JOIN document_revisions r
                ON r.id = d.document_revision_id AND r.deleted = 0
        WHERE
            q.number = '$coti_num_esc'
            AND q.deleted = 0
        ORDER BY
            r.date_entered DESC
    ";

    $result = $conn->query($sql);

    if ($result === false) {
        echo "❌ Error en la consulta: " . $conn->error;
        return null;
    }

    if ($result->num_rows > 0) {
        $docs = [];
        while ($row = $result->fetch_assoc()) {
            $docs[] = $row;
        }
        return $docs;
    }

    return null;
}
