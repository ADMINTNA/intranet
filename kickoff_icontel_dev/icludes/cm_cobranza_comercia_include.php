<?PHP
    $conn = DbConnect($db_sweet);
    if (!$conn) { die("<b>Error:</b> No se pudo conectar a Sweet."); }
    $conn->set_charset('utf8mb4');

    function obtenerListaSweet($listName) {
        $url = "https://sweet.icontel.cl/custom/tools/get_dropdown.php?list=" . urlencode($listName);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        $resp = curl_exec($ch);
        curl_close($ch);
        if (!$resp) return [];
        $json = json_decode($resp, true);
        return (is_array($json) ? $json : []);
    }

    function selectSweetEstado($estadoActual, $lista) {
        $html  = "<div class='estado-container'>";
        $html .= "<select class='estado-sweet'>";
        foreach ($lista as $item) {
            $key   = htmlspecialchars($item['key'], ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');
            $sel = (strcasecmp(trim($estadoActual), trim($key)) === 0) ? "selected" : "";
            $html .= "<option value='{$key}' {$sel}>{$label}</option>";
        }
        $html .= "</select><span class='estado-icono'></span></div>";
        return $html;
    }

    $LISTA_ESTADO_SWEET = obtenerListaSweet("Estatus_financiero");

    $sql = "SELECT 
        CONCAT(
            'https://sweet.icontel.cl/index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DAccounts%26action%3DDetailView%26record%3D', ac.id
        ) AS url_cuenta,
        ac.name AS cliente,
        CONCAT(us.first_name, ' ', us.last_name) AS ejecutivo,
        acc.estatusfinanciero_c AS estado,
        acc.comentario_estado_c AS comentario,
        DATE_FORMAT(CONVERT_TZ(ac.date_modified, '+00:00', '-04:00'), '%d/%m/%Y') AS fecha_modif,
        DATEDIFF(NOW(), ac.date_modified) AS dias,
        REPLACE(REPLACE(TRIM(acc.rut_c), '.', ''), ' ', '') AS rut_limpio,
        duemint.estado_duemint,
        duemint.nom_estado_duemint,
        duemint.dias_duemint,
        duemint.num_doc_duemint,
        duemint.monto_duemint
    FROM tnasolut_sweet.accounts AS ac
    JOIN tnasolut_sweet.accounts_cstm AS acc ON acc.id_c = ac.id
    JOIN tnasolut_sweet.users AS us ON us.id = ac.assigned_user_id
    LEFT JOIN (
        SELECT 
            d.clientTaxId AS rut,
            d.status AS estado_duemint,
            d.statusName AS nom_estado_duemint,
            CASE
                WHEN d.status = 1 THEN COALESCE(DATEDIFF(DATE(NOW()), MAX(d.paidDate)), 0)
                WHEN d.status = 2 THEN COALESCE(MIN(DATEDIFF(d.dueDate, DATE(NOW()))), 0)
                WHEN d.status = 3 THEN COALESCE(DATEDIFF(DATE(NOW()), MIN(d.dueDate)), 0)
                WHEN d.status = 4 THEN COALESCE(DATEDIFF(DATE(NOW()), MAX(d.issueDate)), 0)
                WHEN d.status = 5 THEN COALESCE(DATEDIFF(DATE(NOW()), MAX(d.issueDate)), 0)
                ELSE 0
            END AS dias_duemint,
            COUNT(d.number) AS num_doc_duemint,
            SUM(d.total) AS monto_duemint
        FROM icontel_clientes.cron_duemint_documents AS d
        WHERE d.status = 3
        GROUP BY d.clientTaxId, d.status, d.statusName
    ) AS duemint 
    ON duemint.rut = REPLACE(REPLACE(TRIM(acc.rut_c), '.', ''), ' ', '')
    WHERE acc.estatusfinanciero_c IN (
        'cobranza_comercial',
        'acuerdo_cobranza_comer',
        'suspender',
        'Suspendido',
        'retencion_posible_baja'
    )
    ORDER BY acc.estatusfinanciero_c DESC, duemint.estado_duemint ASC";

    $result = $conn->query($sql);
    $contenido = "";
    $ptr = 0;

    while ($row = $result->fetch_assoc()) {

        $ptr++;
        $estado = strtolower(trim($row["estado"]));

        switch ($estado) {
            case 'suspender':              $clase = 'estado-suspender'; break;
            case 'suspendido':             $clase = 'estado-suspendido'; break;
            case 'cobranza_comercial':     $clase = 'estado-cobranza'; break;
            case 'acuerdo_cobranza_comer': $clase = 'estado-acuerdo_cobranza_comer'; break;
            default:                       $clase = ''; break;
        }

        $rut = htmlspecialchars($row["rut_limpio"]);
        $cliente = htmlspecialchars($row["cliente"], ENT_QUOTES, 'UTF-8');
        $comentario = htmlspecialchars($row["comentario"] ?? '', ENT_QUOTES, 'UTF-8');

        $contenido .= "<tr class='{$clase}'>
            <td>{$ptr}</td>
            <td style='text-align:left;'>
                <a target='_blank' href='" . htmlspecialchars($row["url_cuenta"]) . "' style='color:#1F1D3E; text-decoration:none;'>{$cliente}</a>
            </td>
            <td class='estado-sweet-cell' data-rut='{$rut}'>" . selectSweetEstado($row["estado"], $LISTA_ESTADO_SWEET) . "</td>
            <td class='comentario-cell' data-rut='{$rut}'>
                <div class='comentario-container'>
                    <input type='text' class='comentario-input' value='{$comentario}'>
                    <span class='comentario-icono'></span>
                </div>
            </td>
            <td style='text-align:right;'>$ " . number_format($row["monto_duemint"], 0, ',', '.') . "</td>
            <td>" . (int)$row["num_doc_duemint"] . "</td>
            <td>" . (int)$row["dias_duemint"] . "</td>
            <td>" . htmlspecialchars($row["ejecutivo"], ENT_QUOTES, 'UTF-8') . "</td>
            <td>" . htmlspecialchars($row["fecha_modif"]) . "</td>
            <td>" . (int)$row["dias"] . "</td>
        </tr>";
    }

    $conn->close();

?>