<?php
// include_once("api_bsale_functions.php");

// Conexión a la BD Sweet
$conn = DbConnect("tnasolut_sweet");
$sql = "CALL searchactiveservicesbyaccountid('{$account_id}')";
$result = $conn->query($sql);

$prod_id = Array();
$cantidad_servicios = 0;

function getColorStyle($estado) {
    $estado = strtoupper(trim($estado));

    if (in_array($estado, ["GENERAR BAJA", "SUSPENDER", "SUSPENDIDO"])) {
        return 'color: red; background-color: white';
    }
    if (in_array($estado, ["EN TRASLADO", "POSIBLE TRASLADO"])) {
        return 'color: orange; background-color: white';
    }
    if (in_array($estado, ["EN INSTALACION", "EN DEMO"])) {
        return 'color: blue; background-color: white';
    }
    if (in_array($estado, ["DE BAJA", "TRASLADADO"])) {
        return 'color: lightgray; background-color: white';
    }
    return '';
}

$totales_por_estado = [];

if ($result->num_rows > 0) {    
    while($row = $result->fetch_assoc()) {
        $cantidad_servicios++; // Contador de servicios

        while ($conn->more_results() && $conn->next_result()) {
            if ($r = $conn->store_result()) {
                $r->free();
            }
        }

        if (!empty($row["nv_bsale"])) {
            $sqlDoc = "
                SELECT urlPublicView, urlPdf
                FROM icontel_clientes.cron_bsale_documents
                WHERE num_doc = '" . $conn->real_escape_string($row["nv_bsale"]) . "'
                  AND TRIM(UPPER(tipo_doc)) = 'NOTA DE VENTA'
                ORDER BY fecha_emision DESC
                LIMIT 1
            ";
            $qDoc = $conn->query($sqlDoc);
            if ($qDoc && $qDoc->num_rows > 0) {
                $doc = $qDoc->fetch_assoc();
                $row["url_nv_bsale_view"] = $doc["urlPublicView"];
                $row["url_nv_bsale_pdf"] = $doc["urlPdf"];
            }
            if ($qDoc) {
                $qDoc->free();
            }
        }

        $row["documentos_contrato"] = [];

        if (!empty($row["coti_num"])) {
           // $qContratoDocs = $conn->query($sqlContratoDocs);
            $qContratoDocs = $conn->query("CALL documentos_adjuntos_por_cotizacion('" . $conn->real_escape_string($row['coti_num']) . "')");
            if ($qContratoDocs && $qContratoDocs->num_rows > 0) {
                while ($doc = $qContratoDocs->fetch_assoc()) {
                    $row["documentos_contrato"][] = $doc;
                }
            }

            if ($qContratoDocs) {
                $qContratoDocs->free();
            }
        }

        $estado = strtoupper(trim($row["coti_estado"]));
        if (!isset($totales_por_estado[$estado])) {
            $totales_por_estado[$estado] = 0;
        }
        $totales_por_estado[$estado] += floatval($row["produ_valor"]);

        $style = getColorStyle($row["coti_estado"]);

        echo '<tr valign="top" style="' . $style . '">';
?>
        <td><a href="<?php echo $row["url_new_caso"]; ?>" target="_blank"><img src="../../images/ticket.png" height="20" alt=""/></a></td>
        <td align="center"><?php echo number_format($row["produ_cantidad"]); ?></td>
        <td><?php echo $row["coti_estado"]; ?></td>
        <td><?php echo $row["produ_nombre"]; ?></td>
        <td>
          <?php
          if (!empty($row["documentos_contrato"])) {
              foreach ($row["documentos_contrato"] as $doc) {
                ?>
                <a href="<?php echo htmlspecialchars($doc["url_documento"]); ?>"
                    target="_blank"
                    title="Descargar <?php echo htmlspecialchars($doc["documento_nombre"]); ?>"
                    style="display:inline-block; margin-left:3px;">
                    <img src="../images/documento.png"
                    alt="PDF: <?php echo htmlspecialchars($doc["documento_nombre"]); ?>"
                    height="20">
                </a>
                <?php
              }
          }
          ?>
        </td>
        <td><?php echo $row["dir_instalacion"]; ?></td>
        <td><?php echo $row["produ_proveedor"]; ?></td>
        <td><?php echo $row["codigo_servicio"]; ?></td>
        <td style="white-space: nowrap; vertical-align: middle; text-align: left;">
          <?php echo $row["fecha_contrato"]; ?>
        </td>
        <td><?php echo $row["duracion_contrato"]; ?></td>
        <?php
            $meses = (int)$row["meses"];
            $meses_abs = abs($meses);
            if ($meses >= 0) {
                $contr_cliente = "<span style='color:red;'>Vencido ({$meses_abs})</span>";
            } elseif ($meses >= -3) {
                $contr_cliente = "<span style='color:orange;'>Por Vencer ({$meses_abs})</span>";
            } else {
                $contr_cliente = "<span style='color:green;'>Vigente ({$meses_abs})</span>";
            }
        ?>
        <td><?php echo $contr_cliente; ?></td>
        <td>
            
          <!--  $row["url_nv_bsale_pdf"] para mostrar pdf
            $row["url_nv_bsale_view"]  para solo ver -->
            
          <?php if (!empty($row["url_nv_bsale_view"])): ?>
            <a href="<?php echo $row["url_nv_bsale_view"]; ?>" target="_blank"><?php echo $row["nv_bsale"]; ?></a>
          <?php else: ?>
            <?php echo $row["nv_bsale"]; ?>
          <?php endif; ?>
        </td>
        <td><a href="<?php echo $row["url_coti"]; ?>" target="_blank"><?php echo $row["coti_num"]; ?></a></td>
        <td><a href="<?php echo $row["url_opor"]; ?>" target="_blank"><?php echo $row["opor_num"]; ?></a></td>
        <td align="right"><?php echo number_format($row["produ_valor"], 2, ',', ' '); ?></td>
    </tr>
<?php
    }

// Fila final resumen como sticky footer, incluyendo cantidad de servicios
echo '<tfoot>';
echo '<tr>';
echo '<td colspan="14" style="
    position: sticky;
    bottom: 0;
    background-color: #f3f3f3;
    text-align: center;
    padding: 2px 4px;
    line-height: 1.2;
    color: #555;
    border-top: 1px solid #ddd;
    font-size: 15px;
    font-weight: bold;
">';
    echo '<strong>Totales por estado: ' . $cantidad_servicios . ' Items &nbsp;&nbsp;';
foreach ($totales_por_estado as $estado => $total) {
    echo '<span style="margin-right: 20px;">'
        . htmlspecialchars($estado) 
        . ': UF ' 
        . number_format($total, 2, ',', '.') 
        . '</span>';
}
echo '</strong></td>';
echo '</tr>';
echo '</tfoot>';
}

$conn->close(); 
?>